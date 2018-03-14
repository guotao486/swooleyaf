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
local OpenCCDeny = sywaftool.isOpen(configs.SwitchCCDeny)
local OpenWhiteUri = sywaftool.isOpen(configs.SwitchWhiteUri)
local OpenBlackUri = sywaftool.isOpen(configs.SwitchBlackUri)
local OpenCookie = sywaftool.isOpen(configs.SwitchCookie)
local OpenPost = sywaftool.isOpen(configs.SwitchPost)
local WhiteUris = sywaftool.readRule(configs.DirRules .. 'white-uris')
local BlackUris = sywaftool.readRule(configs.DirRules .. 'black-uris')
local BlackUserAgents = sywaftool.readRule(configs.DirRules .. 'black-useragents')
local BlackGetArgs = sywaftool.readRule(configs.DirRules .. 'black-getargs')
local BlackCookies = sywaftool.readRule(configs.DirRules .. 'black-cookies')
local BlackPostArgs = sywaftool.readRule(configs.DirRules .. 'black-postargs')

local function logWaf(method, url, data, rule)
    local msgs = {}
    local serverName = ngx.var.server_name
    table.insert(msgs, sytool.getClientIp())
    table.insert(msgs, " [")
    table.insert(msgs, ngx.localtime())
    table.insert(msgs, '] "')
    table.insert(msgs, method)
    table.insert(msgs, " ")
    table.insert(msgs, serverName)
    table.insert(msgs, url)
    table.insert(msgs, '" "')
    table.insert(msgs, data)

    local ua = ngx.var.http_user_agent
    if ua then
        table.insert(msgs, '" "')
        table.insert(msgs, ua)
        table.insert(msgs, '" "')
    else
        table.insert(msgs, '" - "')
    end
    table.insert(msgs, rule)
    table.insert(msgs, '"\n')
    local filename = configs.DirLog .. serverName .. "_" .. ngx.today() .. "_attack.log"
    sytool.log(filename, table.concat(msgs, ''))
end

local function sendErrorRsp(status, msgType)
    ngx.status = status
    if msgType == 'json' then
        ngx.header.content_type = "application/json;charset=UTF-8"
        ngx.say(configs.ErrRspContentJson)
    else
        ngx.header.content_type = "text/html;charset=UTF-8"
        ngx.say(configs.ErrRspContentHtml)
    end

    ngx.exit(ngx.status)
end

local function checkFileExt(ext)
    local extStr = string.lower(ext)
    if configs.BlackFileExts[extStr] ~= nil then
        logWaf('File-Ext', ngx.var.request_uri, "-", "file attack with ext " .. ext)
        sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
    end
end

local function checkPostArgs(data)
    if data ~= "" and #BlackPostArgs > 0 then
        for _, rule in pairs(BlackPostArgs) do
            if ngxMatch(ngxUnescapeUri(data), rule, "isjo") then
                logWaf('Post-Args', ngx.var.request_uri, data, rule)
                sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
            end
        end
    end
end

local function checkHeader()
    if ngx.var.http_Acunetix_Aspect or ngx.var.http_X_Scan_Memo then
        sendErrorRsp(ngx.HTTP_CLOSE, 'html')
    end
end

local function checkIp()
    local ip = sytool.getClientIp()
    if configs.WhiteIps[ip] ~= nil then
        return
    end

    if configs.BlackIps[ip] ~= nil then
        sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
    end
end

local function checkCCDeny()
    if OpenCCDeny then
        local token = ngx.md5(sytool.getClientIp() .. ngx.var.uri)
        local cachecc = ngx.shared.sywafcachecc
        local reqNum,_ = cachecc:get(token)
        if reqNum then
            if reqNum < configs.CCCount then
                cachecc:incr(token, 1)
            else
                sendErrorRsp(ngx.HTTP_OK, 'json')
            end
        else
            cachecc:set(token, 1, configs.CCSeconds)
        end
    end
end

local function checkUri()
    local nowUri = ngx.var.uri
    if OpenWhiteUri and #WhiteUris > 0 then
        for _, rule in pairs(WhiteUris) do
            if ngxMatch(nowUri, rule, "isjo") then
                return
            end
        end
    end

    if OpenBlackUri and #BlackUris > 0 then
        for _, rule in pairs(BlackUris) do
            if ngxMatch(nowUri, rule, "isjo") then
                logWaf('Uri', ngx.var.request_uri, "-", rule)
                sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
            end
        end
    end
end

local function checkUserAgent()
    if #BlackUserAgents > 0 then
        local ua = ngx.var.http_user_agent
        if ua ~= nil then
            for _, rule in pairs(BlackUserAgents) do
                if ngxMatch(ua, rule, "isjo") then
                    logWaf('User-Agent', ngx.var.request_uri, "-", rule)
                    sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
                end
            end
        end
    end
end

local function checkGetArgs()
    if #BlackGetArgs > 0 then
        local nowTable = {}
        local nowArgs = ngx.req.get_uri_args()
        for key, val in pairs(nowArgs) do
            local valStr = val
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
            end
            if valStr and type(valStr) ~= "boolean" then
                table.insert(nowTable, valStr)
            end
        end

        for eKey, eVal in pairs(nowTable) do
            for _, rule in pairs(BlackGetArgs) do
                if ngxMatch(ngxUnescapeUri(eVal), rule, "isjo") then
                    logWaf('Get-Args', ngx.var.request_uri, "-", rule)
                    sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
                end
            end
        end
    end
end

local function checkCookie()
    local ck = ngx.var.http_cookie
    if OpenCookie and #BlackCookies > 0 and ck then
        for _, rule in pairs(BlackCookies) do
            if ngxMatch(ck, rule, "isjo") then
                logWaf('Cookie', ngx.var.request_uri, "-", rule)
                sendErrorRsp(ngx.HTTP_FORBIDDEN, 'html')
            end
        end
    end
end

local function checkPost()
    local ngxReqMethod = ngx.req.get_method()
    if OpenPost and ngxReqMethod == "POST" then
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
                local data, err, partial = sock:receive(chunkSize)
                local eData = data or partial
                if not eData then
                    return
                end
                ngx.req.append_body(eData)
                checkPostArgs(eData)

                contentSize = contentSize + string.len(eData)
                local fileInfo = ngxMatch(eData, [[Content-Disposition: form-data;(.+)filename="(.+)\\.(.*)"]], 'ijo')
                if fileInfo then
                    checkFileExt(fileInfo[3])
                elseif ngxMatch(eData, "Content-Disposition:", 'isjo') then
                    checkPostArgs(eData)
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
                local eData = val
                if type(val) == "table" then
                    if type(val[1]) == "boolean" then
                        return
                    end
                    eData = table.concat(val, ", ")
                end
                if eData and type(eData) ~= "boolean" then
                    checkPostArgs(eData)
                    checkPostArgs(key)
                end
            end
        end
    end
end

local module = {}
module.tokenSecret = 'jb6hNP'

function module.checkWaf()
    checkHeader()
    checkIp()
    checkCCDeny()
    checkUri()
    checkUserAgent()
    checkGetArgs()
    checkCookie()
    checkPost()
end

function module.checkCookieToken()
    local nowToken = ngx.var.cookie_sywaftoken
    local newToken = tostring(ngx.crc32_short(module.tokenSecret .. ngx.var.remote_addr))
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