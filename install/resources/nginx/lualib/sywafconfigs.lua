--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 12:10
-- To change this template use File | Settings | File Templates.
--

local sywaftool = require("sywaftool")

local configs = {}
configs['DirRules'] = '/home/configs/nginx/lualib/wafrules/'
configs['DirLog'] = '/home/logs/nginx/'
configs['StatusCCDeny'] = true
configs['StatusWhiteUri'] = true
configs['StatusBlackUri'] = true
configs['StatusCookie'] = true
configs['StatusPost'] = true
configs['BlackFileExts'] = {['php'] = 1, ['jsp'] = 1}
configs['WhiteIps'] = {['127.0.0.1'] = 1}
configs['BlackIps'] = {['1.0.0.1'] = 1}
configs['CCCount'] = 240
configs['CCSeconds'] = 60
configs['ErrRspContentHtml'] = [[Please go~]]
configs['ErrRspContentJson'] = [[{"code":"22000","msg":"请求拒绝,请稍后再试","data":{}}]]
configs['WhiteUris'] = sywaftool.readRule(configs.DirRules .. 'white-uris')
configs['BlackUris'] = sywaftool.readRule(configs.DirRules .. 'black-uris')
configs['BlackUserAgents'] = sywaftool.readRule(configs.DirRules .. 'black-useragents')
configs['BlackGetArgs'] = sywaftool.readRule(configs.DirRules .. 'black-getargs')
configs['BlackCookies'] = sywaftool.readRule(configs.DirRules .. 'black-cookies')
configs['BlackPostArgs'] = sywaftool.readRule(configs.DirRules .. 'black-postargs')

return configs