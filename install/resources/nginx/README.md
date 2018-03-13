# lua
## 配置
    lua_ssl_trusted_certificate：用于PEM格式的Lua容器证书文件的绝对路径.当pg_ssl_verify或cassandra_ssl_verify被启用时,可使用此证书来验证Kong的数据库连接.详参 https://github.com/openresty/lua-nginx-module#lua_ssl_trusted_certificate.
    lua_ssl_verify_depth：通过由lua_ssl_trusted_certificate配置的Lua仓库,并使用服务器证书链来设置验证的深度(verification depth).这里包含了Kong数据库连接配置的证书.详参 https://github.com/openresty/lua-nginx-module#lua_ssl_verify_depth,默认值为1.
    lua_code_cache：禁用时,每个请求都将在单独的Lua VM实例中运行：所有Lua模块将从头开始加载,有助于在开发插件时进行重新编译和刷新.关闭此指令对性能有严重的影响.详参 https://github.com/openresty/lua-nginx-module#lua_code_cache,默认为开启on.
    lua_package_path：设置Lua模块搜索路径（LUA_PATH）.当自定义的插件为存储在默认路径或者在进行开发时,会比较有用.详参 https://github.com/openresty/lua-nginx-module#lua_package_path.
    lua_package_cpath：设置Lua C模块搜索路径（LUA_CPATH）.详参 https://github.com/openresty/lua-nginx-module#lua_package_cpath.
    lua_socket_pool_size：指定与每个远程服务器关联的每个cosocket连接池的大小.详参  https://github.com/openresty/lua-nginx-module#lua_socket_pool_size,默认值为30.

# Kong
## 配置
### Kong
    prefix：工作目录.相当于Nginx的根目录,其中包含临时文件和日志文件.每个Kong进程必须有一个单独的工作目录.默认为 /usr/local/kong.
    log_level：Nginx服务器的日志级别.日志可以在 <prefix> /logs/error.log 中找到.可用的配置参数,详见 http://nginx.org/en/docs/ngx_core_module.html#error_log.默认为notice.
    database：配置此节点来指定KONG使用哪个数据库（PostgreSQL或Cassandra）作为其数据存储.可选的数据库只有postgres和cassandra.默认为 postgres.
    custom_plugins：该节点用于加载的附加插件列表,插件名称用逗号分隔开.使用此属性可以加载用户自定义的、Kong未提供的插件.插件将从 kong.plugins {name}.* 形式的命名空间加载.默认为空,插件列表类似于my-plugin,hello-world,custom-rate-limiting的形式.
    anonymous_reports：发送匿名的KONG使用数据,如错误堆栈等,以帮助改善Kong.默认为开启on.

### Nginx
    proxy_listen：Kong用来接收HTTP请求的地址和端口.这是Kong服务的公共入口点,您的用户可以通过这个入口向KONG发送请求.可用的配置参数,详见  http://nginx.org/en/docs/http/ngx_http_core_module.html#listen.默认为 0.0.0.0:8000.
    proxy_listen_ssl：如果启用ssl,此配置就是KONG用来接收HTTP请求的地址和端口.默认值为 0.0.0.0:8443.
    admin_listen：Kong针对管理员提供的入口地址和端口.该API允许您配置和管理Kong,应该配置为持私的,并确保其是安全的.默认值为 0.0.0.0:8001.
    admin_listen_ssl：在启用ssl的情况下的管理员对kong进行管理的入口.默认值为 0.0.0.0:8444.
    nginx_worker_processes：配置Nginx服务开启后,可以产生的工作进程数.详情参考 http://nginx.org/en/docs/ngx_core_module.html#worker_processes.默认为 auto.
    nginx_daemon：配置Nginx是作为守护进程还是作为前台进程运行.主要用于开发或在Docker环境中运行Kong时.详见 http://nginx.org/en/docs/ngx_core_module.html#daemon.默认为on.
    mem_cache_size：数据库缓存大小的配置.可使用的单位是k和m,最小推荐值为几MB.默认值为 128M.
    ssl：确定Nginx是否应该在proxy_listen_ssl地址上侦听HTTP请求.如果禁用,Nginx将仅在proxy_listen上绑定,所有关于SSL的设置将被忽略.默认为开启on.
    ssl_cert：如果启用ssl,则为proxy_listen_ssl地址的SSL证书的绝对路径.如果没有指定并启用ssl,Kong将自动生成默认证书和密钥.
    ssl_cert_key：如果启用ssl,则为proxy_listen_ssl地址的SSL密钥的绝对路径.
    admin_ssl：配置Nginx是否监听admin_listen_ssl地址上的HTTP请求.如果禁用,Nginx将仅在admin_listen上绑定,所有SSL设置将被忽略.默认为开启on.
    admin_ssl_cert：如果启用了admin_ssl,则为admin_listen_ssl地址的SSL证书的绝对路径.如果没有指定,并且启用了admin_ssl,Kong将生成默认证书和密钥.
    admin_ssl_key：如果启用了admin_ssl,则为admin_listen_ssl地址的SSL密钥的绝对路径.
    upstream_keepalive：设置保存在每个工作进程的缓存中的上游服务器的连接池的最大数值.超过此值时,则将最近使用的连接关闭.

### Postgres
    pg_host：Postgres的服务器的主机地址
    pg_port：Postgres的服务器的端口
    pg_user：Postgres用户名
    pg_password：Postgres的用户密码
    pg_database：要连接的数据库实例名,必须存在
    pg_ssl：是否启用与服务器的SSL连接
    pg_ssl_verify：如果启用了pg_ssl,则切换服务器证书验证.请参阅lua_ssl_trusted_certificate设置.

### Cassandra
    cassandra_contact_points：集群名称列表,以逗号分隔
    cassandra_port：您的节点正在监听的端口
    cassandra_keyspace：您在群集中使用的密钥空间,如果不存在将被自动创建
    cassandra_consistency：设置读写操作的一致性
    cassandra_timeout：读写操作的超时设定,单位为毫秒ms
    cassandra_ssl：配置启用SSL连接
    cassandra_ssl_verify：如果启用cassandra_ssl,则切换服务器证书验证.请参阅lua_ssl_trusted_certificate设置
    cassandra_username：使用PasswordAuthenticator方案时的用户名
    cassandra_password：使用PasswordAuthenticator方案时的用户密码
    cassandra_consistency：读/写Cassandra群集时使用的一致性设置
    cassandra_lb_policy：在Cassandra群集中分发查询时使用的负载均衡策略.接受的值是RoundRobin和DCAwareRoundRobin.当且仅当您使用多数据中心集群时方可配置,此时,请同时配置cassandra_local_datacenter选项
    cassandra_local_datacenter：当使用DCAwareRoundRobin策略时,必须在KONG节点中指定本地集群的名称
    cassandra_repl_strategy：如果是首次创建密钥空间,请指定复制策略
    cassandra_repl_factor：指定SimpleStrategy的复制条件
    cassandra_data_centers：指定NetworkTopologyStrategy的数据中心

### 节点
    除了指向同一个数据库之外,每个Kong节点都必须加入同一个集群.Kong的节点工作在IP层（主机名不被支持,只有IP）,并且为平面网络拓扑,但数据中心之间没有任何NAT（网络地址转换）.一种常见的模式是在两个数据中心之间创建一个VPN,这样就不会违反平面网络原则.有关更多信息,请参阅  https://getkong.org/docs/latest/clustering/.
    cluster_listen：用于与群集中的其他节点通信的地址和端口.同一集群中的所有其他Kong节点必须能够通过TCP和UDP进行通信.只支持IPv4地址.默认值为 0.0.0.0:7946.
    cluster_listen_rpc：用于通过此节点上运行的代理与群集通信的地址和端口.仅包含此节点本地的TCP流量.默认为 127.0.0.1:7373.
    cluster_advertise：缺省情况下,cluster-listen地址通过集群发布.如果cluster_listen主机为“0.0.0.0”,则首个本地非环回IPv4地址将通告给其他节点.然而,在某些情况下（特别是NAT穿越）,可能存在无法绑定路由地址.该标志可以通知不同的地址来支持这一点(This flag enables advertising a different address to support this).
    cluster_encrypt_key：基于Base64编码的16字节密钥,用于加密集群通讯.
    cluster_keyring_file：指定一个文件来加载密钥环数据. Kong能够保持加密密钥同步并执行密钥回转.在密钥回转过程中,可能需要一段时间才能保留多个加密密钥,直到所有成员都收到新密钥为止.
    cluster_ttl_on_failure：当集群停止发送healthcheck ping（可能由节点或网络故障引起）时,节点的生存时间（单位为秒）.如果节点在到期之前无法发送新的healthcheck ping,则集群中的其他节点将停止重新连接到该节点的操作.建议至少60.默认为3600秒.
    cluster_profile：群集间ping和超时的时序配置文件.如果通过互联网使用lan或local的配置文件,时序限制越紧越容易产生风险.可配置的值为local,lan,wan,默认为wan.

### DNS解析器
    Kong会将主机名解析为SRV或A记录（按此顺序,CNAME记录将在该过程中被取消引用）.如果名称被解析为SRV记录,它还将通过从dns服务器接收的port字段内容来覆盖任何给定的端口号.对于ttl的持续时间,内部dns解析器将对通过dns记录中的条目的每个请求进行负载平衡.对于SRV记录,权重字段将被保留,但只会使用记录中最低优先级的字段条目.
    dns_resolver：由逗号分隔的服务器名称列表,每个条目的格式为 ipv4 [：port].如果未指定,将使用本地 resolv.conf 文件中的服务器名称.如果省略,则默认端口号为53.
    dns_hostsfile：hosts文件.该文件在启动时被读取一次,其在内存中是静态的.如果要修改此文件,必须重新加载服务.默认路径为 /etc/hosts.

# 参考
    //春哥博客
    http://blog.sina.com.cn/openresty
    //Kong专栏
    http://blog.csdn.net/column/details/18049.html
    //lua文档
    https://moonbingbing.gitbooks.io/openresty-best-practices/content/
    //lua工具库
    http://wiki.jikexueyuan.com/project/nginx-lua/lua.html