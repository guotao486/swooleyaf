--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 12:10
-- To change this template use File | Settings | File Templates.
--

local sywaftool = require("sywaftool")

local needTag = ''
local needContent = {}
local configs = {}
configs['DirRules'] = '/home/configs/nginx/lualib/wafrules/'
configs['DirLog'] = '/home/logs/nginx/'
configs['ErrRspContentHtml'] = [[Please go~]]
configs['ErrRspContentJson'] = [[{"code":"22000","msg":"请求拒绝,请稍后再试","data":{}}]]
configs['TotalTags'] = sywaftool.readRule(configs.DirRules .. 'total-tags')
for _, tag in pairs(configs['TotalTags']) do
    -- 黑名单-文件后缀
    needTag = tag .. 'BlackFileExts'
    configs[needTag] = {}
    needContent = sywaftool.readRule(configs.DirRules .. tag .. '/black-exts')
    for _, ext in pairs(needContent) do
        configs[needTag][ext] = 1
    end

    -- 白名单-ip
    needTag = tag .. 'WhiteIps'
    configs[needTag] = {}
    needContent = sywaftool.readRule(configs.DirRules .. tag .. '/white-ips')
    for _, ip in pairs(needContent) do
        configs[needTag][ip] = 1
    end

    -- 黑名单-ip
    needTag = tag .. 'BlackIps'
    configs[needTag] = {}
    needContent = sywaftool.readRule(configs.DirRules .. tag .. '/black-ips')
    for _, ip in pairs(needContent) do
        configs[needTag][ip] = 1
    end

    -- 白名单-uri
    needTag = tag .. 'WhiteUris'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/white-uris')

    -- 黑名单-uri
    needTag = tag .. 'BlackUris'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/black-uris')

    -- 黑名单-user agent
    needTag = tag .. 'BlackUserAgents'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/black-useragents')

    -- 黑名单-get args
    needTag = tag .. 'BlackGetArgs'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/black-getargs')

    -- 黑名单-cookies
    needTag = tag .. 'BlackCookies'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/black-cookies')

    -- 黑名单-post args
    needTag = tag .. 'BlackPostArgs'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/black-postargs')

    -- 白名单-hosts
    needTag = tag .. 'WhiteHosts'
    configs[needTag] = sywaftool.readRule(configs.DirRules .. tag .. '/white-hosts')

    -- CC攻击
    needContent = sywaftool.readRule(configs.DirRules .. tag .. '/deny-cc')
    for _, ccconfig in pairs(needContent) do
        local ccCount = tointeger(string.match(ccconfig,'(\d+)/'))
        local ccSeconds = tointeger(string.match(ccconfig,'/(\d+)'))
        if ccCount ~= nil and ccSeconds ~= nil and ccCount > 0 and ccSeconds > 0 then
            needTag = tag .. 'CCCount'
            configs[needTag] = ccCount
            needTag = tag .. 'CCSeconds'
            configs[needTag] = ccSeconds
        end
        break
    end
end

return configs