FROM base-nginx
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="nginx系统环境" image.version="1.0.0" image.desc="基于nginx基础环境构建" image.tag="system-nginx"
ENV SYDIR_DOWNLOAD /home/download
WORKDIR $SYDIR_DOWNLOAD

# 初始化目录
RUN mkdir /home/logs/nginx && mkdir /home/configs/nginx && mkdir /home/configs/nginx/certs && mkdir /home/configs/nginx/cache && mkdir /home/configs/nginx/temp && mkdir /home/configs/nginx/modules && mkdir /home/configs/nginx/servers && mkdir /home/configs/nginx/streams

# libunwind
COPY ./resources/nginx/libunwind-1.1.tar.gz $SYDIR_DOWNLOAD/libunwind-1.1.tar.gz
RUN tar -zxvf libunwind-1.1.tar.gz
RUN cd libunwind-1.1/ && CFLAGS=-fPIC ./configure --prefix=/usr && make CFLAGS=-fPIC && make CFLAGS=-fPIC install
RUN rm -rf libunwind-1.1/ && rm -rf libunwind-1.1.tar.gz

# gperftools
COPY ./resources/nginx/gperftools-2.1.tar.gz $SYDIR_DOWNLOAD/gperftools-2.1.tar.gz
RUN tar -zxvf gperftools-2.1.tar.gz
RUN cd gperftools-2.1/ && ./configure --prefix=/usr --enable-frame-pointers && make && make install
RUN rm -rf gperftools-2.1/ && rm -rf gperftools-2.1.tar.gz
RUN echo "/usr/local/lib" > /etc/ld.so.conf.d/usr_local_lib.conf && ldconfig && mkdir /tmp/tcmalloc && chmod 0777 /tmp/tcmalloc

# LuaJIT
COPY ./resources/nginx/LuaJIT-2.0.5.tar.gz $SYDIR_DOWNLOAD/LuaJIT-2.0.5.tar.gz
RUN mkdir /usr/local/luajit
RUN tar -zxvf LuaJIT-2.0.5.tar.gz
RUN cd LuaJIT-2.0.5/ && make && make install PREFIX=/usr/local/luajit
RUN rm -rf LuaJIT-2.0.5/ && rm -rf LuaJIT-2.0.5.tar.gz

# ngx_devel_kit
COPY ./resources/nginx/ngx_devel_kit-0.2.19.tar.gz $SYDIR_DOWNLOAD/ngx_devel_kit-0.2.19.tar.gz
RUN tar -zxvf ngx_devel_kit-0.2.19.tar.gz
RUN mv ngx_devel_kit-0.2.19/ /home/configs/nginx/modules/ngx_devel_kit
RUN rm -rf ngx_devel_kit-0.2.19.tar.gz

# lua-nginx-module
COPY ./resources/nginx/lua-nginx-module-0.10.13.tar.gz $SYDIR_DOWNLOAD/lua-nginx-module-0.10.13.tar.gz
RUN tar -zxvf lua-nginx-module-0.10.13.tar.gz
RUN mv lua-nginx-module-0.10.13/ /home/configs/nginx/modules/lua-nginx-module
RUN rm -rf lua-nginx-module-0.10.13.tar.gz

# nginx_upstream_check_module
COPY ./resources/nginx/nginx_upstream_check_module.tar.gz $SYDIR_DOWNLOAD/nginx_upstream_check_module.tar.gz
RUN tar -zxvf nginx_upstream_check_module.tar.gz
RUN mv nginx_upstream_check_module/ /home/configs/nginx/modules/
RUN rm -rf nginx_upstream_check_module.tar.gz

# ngx_cache_purge
COPY ./resources/nginx/ngx_cache_purge-2.3.tar.gz $SYDIR_DOWNLOAD/ngx_cache_purge-2.3.tar.gz
RUN tar -zxvf ngx_cache_purge-2.3.tar.gz
RUN mv ngx_cache_purge-2.3/ /home/configs/nginx/modules/ngx_cache_purge
RUN rm -rf ngx_cache_purge-2.3.tar.gz

# headers-more-nginx-module
COPY ./resources/nginx/headers-more-nginx-module-0.33.tar.gz $SYDIR_DOWNLOAD/headers-more-nginx-module-0.33.tar.gz
RUN tar -zxvf headers-more-nginx-module-0.33.tar.gz
RUN mv headers-more-nginx-module-0.33/ /home/configs/nginx/modules/headers-more-nginx-module
RUN rm -rf headers-more-nginx-module-0.33.tar.gz

# nginx
COPY ./resources/nginx/nginx-1.12.2.tar.gz $SYDIR_DOWNLOAD/nginx-1.12.2.tar.gz
RUN mkdir /usr/local/nginx
RUN tar -zxvf nginx-1.12.2.tar.gz
RUN tar -zxvf pcre-8.42.tar.gz
RUN tar -zxvf zlib-1.2.11.tar.gz
RUN tar -zxvf openssl-1.0.2p.tar.gz
RUN cd nginx-1.12.2/ && patch -p1 < /home/configs/nginx/modules/nginx_upstream_check_module/check_1.12.1+.patch && ./configure --prefix=/usr/local/nginx --with-pcre=$SYDIR_DOWNLOAD/pcre-8.42 --with-zlib=$SYDIR_DOWNLOAD/zlib-1.2.11 --with-openssl=$SYDIR_DOWNLOAD/openssl-1.0.2p --without-http_autoindex_module --without-http_ssi_module --with-http_ssl_module --with-http_stub_status_module --with-http_realip_module --with-http_gzip_static_module --with-http_v2_module --with-stream --with-stream_realip_module --with-stream_ssl_module --with-google_perftools_module --with-debug --add-module=/home/configs/nginx/modules/ngx_devel_kit --add-module=/home/configs/nginx/modules/lua-nginx-module --add-module=/home/configs/nginx/modules/nginx_upstream_check_module --add-module=/home/configs/nginx/modules/ngx_cache_purge --add-module=/home/configs/nginx/modules/headers-more-nginx-module --with-ld-opt="-Wl,-rpath,$LUAJIT_LIB" && make -j 4 && make install
RUN rm -rf nginx-1.12.2/ && rm -rf nginx-1.12.2.tar.gz
RUN rm -rf pcre-8.42/ && rm -rf pcre-8.42.tar.gz
RUN rm -rf zlib-1.2.11/ && rm -rf zlib-1.2.11.tar.gz
RUN rm -rf openssl-1.0.2p/ && rm -rf openssl-1.0.2p.tar.gz
RUN /usr/local/nginx/conf/nginx.conf
COPY ./configs/swooleyaf/nginx/nginx.conf /usr/local/nginx/conf/nginx.conf
COPY ./configs/swooleyaf/nginx/nginx.service /lib/systemd/system/nginx.service
RUN chmod 754 /lib/systemd/system/nginx.service && systemctl enable nginx