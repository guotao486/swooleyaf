# 用途
- 防止sql注入,本地包含,部分溢出,fuzzing测试,xss,SSRF等web攻击
- 防止svn/备份之类文件泄漏
- 防止ApacheBench之类压力测试工具的攻击
- 屏蔽常见的扫描黑客工具,扫描器
- 屏蔽异常的网络请求
- 屏蔽图片附件类目录php执行权限
- 防止webshell上传
- 防止前端攻击

# 说明
## 环境要求
- ngx_lua version > 0.9.2
- luajit version > 2.0

## 使用说明
    nginx安装路径:/usr/local/nginx
    lualib路径:/home/configs/nginx/lualib
    vim /usr/local/nginx/conf/nginx.conf
        http {
            ...
            lua_code_cache on;
            lua_package_path "/home/configs/nginx/lualib/?.lua";
            lua_package_cpath "/home/configs/nginx/lualib/?.so";
            lua_need_request_body on;
            # 开启防御CC攻击必须设置
            lua_shared_dict sywafcachecc 10m;
            init_by_lua_file /home/configs/nginx/lualib/init.lua;
            limit_req_zone $cookie_sywaftoken zone=sywaftokens:10m rate=10r/s;
            ...
        }

        server {
            ...
            # 开启防火墙
            access_by_lua 'symodules.waf.checkWaf()';
            # 开启前端防攻击
            limit_req zone=sywaftokens burst=5 nodelay;
            access_by_lua 'symodules.waf.checkCookieToken()';
            ...
        }

# 配置介绍
## 文件路径
    /home/configs/nginx/lualib/sywafconfigs.lua

## 配置详解
- DirRules: 过滤规则存放目录,以/结尾
- DirLog: 日志存储目录,以/结尾
- SwitchLogAttack: 攻击日志记录开关 on:开启 off:关闭
- SwitchUrlDeny: url拦截开关 on:开启 off:关闭
- SwitchUrlRedirect: 拦截重定向开关 on:开启 off:关闭
- SwitchCookie: cookie过滤开关 on:开启 off:关闭
- SwitchPost: post过滤开关 on:开启 off:关闭
- SwitchWhiteUrl: url白名单过滤开关 on:开启 off:关闭
- SwitchCCDeny: cc攻击拦截开关 on:开启 off:关闭
- BlackFileExts: 文件后缀黑名单列表
- BlackIps: ip黑名单列表
- WhiteIps: ip白名单列表
- CCCount: CC攻击次数限制
- CCSeconds: CC攻击频率时间限制,单位为秒
- HtmlError: 攻击返回提示内容,必须用```[[```和```]]```包容

## 过滤规则介绍
    攻击日志格式:虚拟主机名_日期_attack.log

- args: 过滤get参数
- url: 过滤get请求url
- post: 过滤post请求
- whitelist: url白名单
- user-agent: 过滤user-agent

# 鸣谢
- 此WAF扩展基于loveshell的ngx_lua_waf开发
- 模块化ngx_lua_waf
- 添加前端基于cookie防攻击
- ngx_lua_waf的链接地址:https://github.com/loveshell/ngx_lua_waf
