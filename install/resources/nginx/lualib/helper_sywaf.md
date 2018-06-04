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
            lua_need_request_body on;
            lua_package_path "/home/configs/nginx/lualib/?.lua";
            lua_package_cpath "/home/configs/nginx/lualib/?.so";
            init_by_lua_file /home/configs/nginx/lualib/init.lua;
            ...
        }

        lua_shared_dict sywafcca01 20m;
        server {
            ...
            # 开启防火墙
            access_by_lua 'symodules.waf.checkWaf("a01")';
            # 开启前端防攻击
            access_by_lua 'symodules.waf.checkCCDeny("a01", "jb6hNP")';
            ...
        }

# 配置介绍
## 文件路径
    /home/configs/nginx/lualib/sywafconfigs.lua

## 配置详解
### 公共配置
- DirRules: 过滤规则存放目录,以/结尾
- DirLog: 日志存储目录,以/结尾
- TotalTags: 全体标识集合
- ErrRspContentHtml: html格式错误响应内容,必须用```[[```和```]]```包容
- ErrRspContentJson: json格式错误响应内容,必须用```[[```和```]]```包容

### 过滤规则配置
规则文件目录: DirRules/标识/<br/>
攻击日志格式: 日期_attack.log<br/>
规则文件介绍: <br/>
- black-cookies: cookie黑名单
- black-exts: 文件后缀黑名单
- black-getargs: get参数黑名单
- black-ip: ip黑名单
- black-postargs: post参数黑名单
- black-uris: uri黑名单
- black-useragents: user-agent黑名单
- deny-cc: cc攻击防御,配置格式为 120/60,120为最大请求次数,60为统计周期,单位为秒
- white-ip: ip白名单
- white-uris: uri白名单

# 鸣谢
- 此WAF扩展基于loveshell的ngx_lua_waf开发
- 模块化ngx_lua_waf
- 添加前端基于cookie防攻击
- ngx_lua_waf的链接地址:https://github.com/loveshell/ngx_lua_waf
