--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 12:31
-- To change this template use File | Settings | File Templates.
--

local configs = require("sywafconfigs")
local sytool = require("sytool")
local sywaftool = require("sywaftool")
local ngxMatch = ngx.re.find
local ngxUnescapeUri = ngx.unescape_uri
local OpenLogAttack = sywaftool.isOpen(configs.SwitchLogAttack)
local OpenUrlRedirect = sywaftool.isOpen(configs.SwitchUrlRedirect)
local OpenWhiteUrl = sywaftool.isOpen(configs.SwitchWhiteUrl)
local OpenUrlDeny = sywaftool.isOpen(configs.SwitchUrlDeny)
local OpenCookie = sywaftool.isOpen(configs.SwitchCookie)
local OpenCCDeny = sywaftool.isOpen(configs.SwitchCCDeny)
local OpenPost = sywaftool.isOpen(configs.SwitchPost)
local WhiteUrls = sywaftool.readRule(configs.DirRules .. 'whiteurl')
local BlackArgs = sywaftool.readRule(configs.DirRules .. 'args')
local BlackUrls = sywaftool.readRule(configs.DirRules .. 'url')
local BlackUserAgents = sywaftool.readRule(configs.DirRules .. 'user-agent')
local BlackCookies = sywaftool.readRule(configs.DirRules .. 'cookie')
local BlackPosts = sywaftool.readRule(configs.DirRules .. 'post')

local function logWaf(method, url, data, rule)
    if OpenLogAttack then
        local realIp = sytool.getClientIp()
        local ua = ngx.var.http_user_agent
        local serverName = ngx.var.server_name
        local time = ngx.localtime()
        local msg = ''
        if ua then
            msg = realIp .. " [" .. time .. "] \"" .. method .. " " .. serverName .. url .. "\" \"" .. data .. "\"  \"" .. ua .. "\" \"" .. rule .. "\"\n"
        else
            msg = realIp .. " [" .. time .. "] \"" .. method .. " " .. serverName .. url .. "\" \"" .. data .. "\" - \"" .. rule .. "\"\n"
        end
        local filename = configs.DirLog .. serverName .. "_" .. ngx.today() .. "_attack.log"
        sytool.log(filename, msg)
    end
end

local function sayHtml()
    if OpenUrlRedirect then
        ngx.header.content_type = "text/html;charset=UTF-8"
        ngx.status = ngx.HTTP_FORBIDDEN
        ngx.say(configs.HtmlError)
        ngx.exit(ngx.status)
    end
end

local function checkWhiteUrl()
    if OpenWhiteUrl then
        if #WhiteUrls > 0 then
            for _, rule in pairs(WhiteUrls) do
                if ngxMatch(ngx.var.uri, rule, "isjo") then
                    return true
                end
            end
        end
    end

    return false
end

local function checkFileExt(ext)
    local extStr = string.lower(ext)
    if configs.BlackFileExts[extStr] == nil then
        return true
    else
        logWaf('POST', ngx.var.request_uri, "-", "file attack with ext " .. ext)
        sayHtml()
        return false
    end
end

local function checkArgs()
    local nowTable = {}
    local nowArgs = ngx.req.get_uri_args()
    for key, val in pairs(nowArgs) do
        local valStr = ''
        if type(val) == 'table' then
            local et = {}
            for k, v in pairs(val) do
                if v == true then
                    table.insert(et, "")
                else
                    table.insert(et, v)
                end
            end
            valStr = table.concat(et, " ")
        else
            valStr = val
        end
        table.insert(nowTable, valStr)
    end

    for _, rule in pairs(BlackArgs) do
        for eKey, eVal in pairs(nowTable) do
            if eVal and type(eVal) ~= "boolean" and rule ~= "" and ngxMatch(ngxUnescapeUri(eVal), rule, "isjo") then
                logWaf('GET', ngx.var.request_uri, "-", rule)
                sayHtml()
                return true
            end
        end
    end
    return false
end

local function checkUrl()
    if OpenUrlDeny then
        for _, rule in pairs(BlackUrls) do
            if rule ~= "" and ngxMatch(ngx.var.request_uri, rule, "isjo") then
                logWaf('GET', ngx.var.request_uri, "-", rule)
                sayHtml()
                return true
            end
        end
    end
    return false
end

local function checkUserAgent()
    local ua = ngx.var.http_user_agent
    if ua ~= nil then
        for _, rule in pairs(BlackUserAgents) do
            if rule ~= "" and ngxMatch(ua, rule, "isjo") then
                logWaf('UA', ngx.var.request_uri, "-", rule)
                sayHtml()
                return true
            end
        end
    end
    return false
end

local function checkCookie()
    local ck = ngx.var.http_cookie
    if OpenCookie and ck then
        for _, rule in pairs(BlackCookies) do
            if rule ~= "" and ngxMatch(ck, rule, "isjo") then
                logWaf('Cookie', ngx.var.request_uri, "-", rule)
                sayHtml()
                return true
            end
        end
    end
    return false
end

local function checkCCDeny()
    if OpenCCDeny then
        local uri = ngx.var.uri
        local token = sytool.getClientIp() .. uri
        local cachecc = ngx.shared.sywafcachecc
        local reqNum, _ = cachecc:get(token)
        if reqNum then
            if reqNum < configs.CCCount then
                cachecc:incr(token, 1)
            else
                ngx.header.content_type = "application/json;charset=UTF-8"
                ngx.say('{"code":"22000","msg":"请求拒绝,请稍后再试","data":{}}')
                ngx.exit(200)
                return true
            end
        else
            cachecc:set(token, 1, configs.CCSeconds)
        end
    end
    return false
end

local function checkWhiteIp()
    local ip = sytool.getClientIp()
    if configs.WhiteIps[ip] ~= nil then
        return true
    end
    return false
end

local function checkBlackIp()
    local ip = sytool.getClientIp()
    if configs.BlackIps[ip] ~= nil then
        ngx.header.content_type = "text/html;charset=UTF-8"
        ngx.status = ngx.HTTP_FORBIDDEN
        ngx.exit(ngx.status)
        return true
    end
    return false
end

local function checkPostBody(data)
    for _, rule in pairs(BlackPosts) do
        if rule ~= "" and data ~= "" and ngxMatch(ngxUnescapeUri(data), rule, "isjo") then
            logWaf('POST', ngx.var.request_uri, data, rule)
            sayHtml()
            return true
        end
    end
    return false
end

local function checkPost()
    local ngxReqMethod = ngx.req.get_method()
    if ngxReqMethod == "POST" then
        local reqBoundary = sywaftool.getReqBoundary()
        if reqBoundary then
            local sock, err = ngx.req.socket()
            if not sock then
                return
            end
            ngx.req.init_body(131072)
            sock:settimeout(0)

            local chunkSize = 4096
            local contentLength = tonumber(ngx.req.get_headers()['content-length'])
            if contentLength < chunkSize then
                chunkSize = contentLength
            end

            local contentSize = 0
            while contentSize < contentLength do
                local data, err, partial = sock:receive(chunk_size)
                local eData = data or partial
                if not eData then
                    return
                end
                ngx.req.append_body(eData)
                if checkPostBody(eData) then
                    return true
                end

                contentSize = contentSize + string.len(eData)
                local fileInfo = ngxMatch(eData, [[Content-Disposition: form-data;(.+)filename="(.+)\\.(.*)"]], 'ijo')
                if fileInfo then
                    checkFileExt(fileInfo[3])
                elseif ngxMatch(eData, "Content-Disposition:", 'isjo') then
                    if checkPostBody(eData) then
                        return true
                    end
                end

                local leftSize = contentLength - contentSize
                if leftSize < chunkSize then
                    chunkSize = leftSize
                end
            end
            ngx.req.finish_body()
        else
            ngx.req.read_body()
            local nowArgs = ngx.req.get_post_args()
            if not nowArgs then
                return
            end
            for key, val in pairs(nowArgs) do
                local eData = ''
                if type(val) == "table" then
                    if type(val[1]) == "boolean" then
                        return
                    end
                    eData = table.concat(val, ", ")
                else
                    eData = val
                end
                if eData and type(eData) ~= "boolean" and checkPostBody(eData) then
                    checkPostBody(key)
                end
            end
        end
    end
end

local module = {}
module.tokenSecret = 'jb6YNPKe'

function module.checkWaf()
    if checkWhiteIp() then
    elseif checkBlackIp() then
    elseif checkCCDeny() then
    elseif ngx.var.http_Acunetix_Aspect then
        ngx.exit(444)
    elseif ngx.var.http_X_Scan_Memo then
        ngx.exit(444)
    elseif checkWhiteUrl() then
    elseif checkUserAgent() then
    elseif checkUrl() then
    elseif checkArgs() then
    elseif checkCookie() then
    elseif OpenPost then
        checkPost()
    else
        return
    end
end

function module.checkCookieToken()
    local nowToken = ngx.var.cookie_sywaftoken
    local newToken = ngx.md5(module.tokenSecret .. ngx.var.remote_addr)
    if nowToken ~= nil then
        if nowToken == newToken then
            return
        else
            ngx.exit(ngx.HTTP_FORBIDDEN)
        end
    else
        ngx.header['Set-Cookie'] = 'sywaftoken=' .. newToken
        return ngx.redirect(ngx.var.scheme .. '://' .. ngx.var.host .. ngx.var.uri)
    end
end

return module