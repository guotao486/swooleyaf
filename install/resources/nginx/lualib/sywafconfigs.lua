--
-- Created by IntelliJ IDEA.
-- User: jw
-- Date: 2018/3/12 0012
-- Time: 12:10
-- To change this template use File | Settings | File Templates.
--

local configs = {}
configs['DirRules'] = '/home/configs/nginx/lualib/wafconf/'
configs['DirLog'] = '/home/logs/nginx/'
configs['SwitchLogAttack'] = 'on'
configs['SwitchUrlDeny'] = 'on'
configs['SwitchUrlRedirect'] = 'on'
configs['SwitchCookie'] = 'on'
configs['SwitchPost'] = 'on'
configs['SwitchWhiteUrl'] = 'on'
configs['SwitchCCDeny'] = 'on'
configs['BlackFileExts'] = {['php'] = 1, ['jsp'] = 1}
configs['BlackIps'] = {['1.0.0.1'] = 1}
configs['WhiteIps'] = {['127.0.0.1'] = 1}
configs['CCCount'] = 240
configs['CCSeconds'] = 60
configs['HtmlError'] = [[Please go~]]

return configs