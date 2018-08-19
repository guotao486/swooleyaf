# -*- coding:utf-8 -*-
from fabric.api import *
from initcentos7.swooleyaf.configs import syDicts


class SyTool():
    # 初始化系统环境配置
    @staticmethod
    def initSystemEnv(envList):
        for eEnv in iter(envList):
            run('echo "%s" >> /etc/profile' % eEnv, False)
        run('source /etc/profile')

    # 配置基础环境
    @staticmethod
    def initSystem():
        run('yum -y install vim zip nss gcc gcc-c++ net-tools wget htop lsof unzip bzip2 curl-devel zlib-devel epel-release perl-ExtUtils-MakeMaker expat-devel gettext-devel openssl-devel iproute.x86_64 autoconf automake libtool make')
        run('wget -O /etc/yum.repos.d/CentOS-Base.repo http://mirrors.aliyun.com/repo/Centos-7.repo')
        run('yum -y update')
        run('systemctl enable firewalld')
        run('systemctl start firewalld')
        run('systemctl enable crond')
        run('systemctl start crond')
        with settings(warn_only=True):
            run('mkdir /home/configs')
            run('mkdir /home/logs')
            run('mkdir /usr/local/mysql')
            run('mkdir %s' % syDicts['path.package.remote'])

    # 开放防火墙端口
    @staticmethod
    def openPorts(portList):
        for ePort in iter(portList):
            run('firewall-cmd --zone=public --add-port=%s --permanent' % ePort, False)
        run('firewall-cmd --reload')

    # 配置git环境
    @staticmethod
    def installGit():
        run('yum -y remove git')
        with settings(warn_only=True):
            run('mkdir /usr/local/git')

        gitLocal = ''.join([syDicts['path.package.local'], '/resources/linux/git-2.10.2.tar.gz'])
        gitRemote = ''.join([syDicts['path.package.remote'], '/git-2.10.2.tar.gz'])
        put(gitLocal, gitRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -xzf git-2.10.2.tar.gz')
            run('cd git-2.10.2/ && make prefix=/usr/local/git all && make prefix=/usr/local/git install && cd ../ && rm -rf git-2.10.2/ && rm -rf git-2.10.2.tar.gz')
            run('git config --global user.name "%s"' % syDicts['git.user.name'])
            run('git config --global user.email "%s"' % syDicts['git.user.email'])

    # 配置nginx环境
    @staticmethod
    def installNginx():
        run('mkdir /home/logs/nginx')
        run('mkdir /home/configs/nginx')
        run('mkdir /home/configs/nginx/certs')
        run('mkdir /home/configs/nginx/cache')
        run('mkdir /home/configs/nginx/temp')
        run('mkdir /home/configs/nginx/modules')
        run('mkdir /home/configs/nginx/servers')
        run('mkdir /home/configs/nginx/streams')
        pcreLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/pcre-8.42.tar.gz'])
        pcreRemote = ''.join([syDicts['path.package.remote'], '/pcre-8.42.tar.gz'])
        put(pcreLocal, pcreRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/pcre')
            run('tar -zxvf pcre-8.42.tar.gz')
            run('cd pcre-8.42/ && ./configure --prefix=/usr/local/pcre && make && make install')
            run('rm -rf pcre-8.42/')

        zlibLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/zlib-1.2.11.tar.gz'])
        zlibRemote = ''.join([syDicts['path.package.remote'], '/zlib-1.2.11.tar.gz'])
        put(zlibLocal, zlibRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/zlib')
            run('tar -zxvf zlib-1.2.11.tar.gz')
            run('cd zlib-1.2.11/ && ./configure --prefix=/usr/local/zlib && make && make install')
            run('rm -rf zlib-1.2.11/')

        opensslLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/openssl-1.1.0i.tar.gz'])
        opensslRemote = ''.join([syDicts['path.package.remote'], '/openssl-1.1.0i.tar.gz'])
        put(opensslLocal, opensslRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/openssl')
            run('tar -zxvf openssl-1.1.0i.tar.gz')
            run('cd openssl-1.1.0i/ && ./config --prefix=/usr/local/openssl shared zlib && make && make install')
            run('rm -rf openssl-1.1.0i/')
            run('mv /usr/bin/openssl /usr/bin/openssl.old')
            run('mv /usr/include/openssl /usr/include/openssl.old')
            run('ln -s /usr/local/openssl/bin/openssl /usr/bin/openssl')
            run('ln -s /usr/local/openssl/include/openssl /usr/include/openssl')
            run('echo "/usr/local/openssl/lib" >> /etc/ld.so.conf')
            run('echo "/usr/local/lib64" >> /etc/ld.so.conf')
            run('ldconfig -v')

        libunwindLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/libunwind-1.1.tar.gz'])
        libunwindRemote = ''.join([syDicts['path.package.remote'], '/libunwind-1.1.tar.gz'])
        put(libunwindLocal, libunwindRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf libunwind-1.1.tar.gz')
            run('cd libunwind-1.1/ && CFLAGS=-fPIC ./configure --prefix=/usr && make CFLAGS=-fPIC && make CFLAGS=-fPIC install')
            run('rm -rf libunwind-1.1/')
            run('rm -rf libunwind-1.1.tar.gz')

        gperftoolsLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/gperftools-2.1.tar.gz'])
        gperftoolsRemote = ''.join([syDicts['path.package.remote'], '/gperftools-2.1.tar.gz'])
        put(gperftoolsLocal, gperftoolsRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf gperftools-2.1.tar.gz')
            run('cd gperftools-2.1/ && ./configure --prefix=/usr --enable-frame-pointers && make && make install')
            run('rm -rf gperftools-2.1/')
            run('rm -rf gperftools-2.1.tar.gz')
            run('echo "/usr/local/lib" > /etc/ld.so.conf.d/usr_local_lib.conf')
            run('/sbin/ldconfig')
            run('mkdir /tmp/tcmalloc')
            run('chmod 0777 /tmp/tcmalloc')

        luajitLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/LuaJIT-2.0.5.tar.gz'])
        luajitRemote = ''.join([syDicts['path.package.remote'], '/LuaJIT-2.0.5.tar.gz'])
        put(luajitLocal, luajitRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/luajit')
            run('tar -zxvf LuaJIT-2.0.5.tar.gz')
            run('cd LuaJIT-2.0.5/ && make && make install PREFIX=/usr/local/luajit')
            run('rm -rf LuaJIT-2.0.5/')
            run('rm -rf LuaJIT-2.0.5.tar.gz')

        ngxdevelLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/ngx_devel_kit-0.2.19.tar.gz'])
        ngxdevelRemote = ''.join([syDicts['path.package.remote'], '/ngx_devel_kit-0.2.19.tar.gz'])
        put(ngxdevelLocal, ngxdevelRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf ngx_devel_kit-0.2.19.tar.gz')
            run('mv ngx_devel_kit-0.2.19/ /home/configs/nginx/modules/ngx_devel_kit')
            run('rm -rf ngx_devel_kit-0.2.19.tar.gz')

        ngxluaLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/lua-nginx-module-0.10.13.tar.gz'])
        ngxluaRemote = ''.join([syDicts['path.package.remote'], '/lua-nginx-module-0.10.13.tar.gz'])
        put(ngxluaLocal, ngxluaRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf lua-nginx-module-0.10.13.tar.gz')
            run('mv lua-nginx-module-0.10.13/ /home/configs/nginx/modules/lua-nginx-module')
            run('rm -rf lua-nginx-module-0.10.13.tar.gz')

        ngxLualibLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/lualib.tar.gz'])
        ngxLualibRemote = ''.join([syDicts['path.package.remote'], '/lualib.tar.gz'])
        put(ngxLualibLocal, ngxLualibRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf lualib.tar.gz')
            run('mv lualib/ /home/configs/nginx/lualib')
            run('rm -rf lualib.tar.gz')

        ngxUpstreamCheckLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/nginx_upstream_check_module.tar.gz'])
        ngxUpstreamCheckRemote = ''.join([syDicts['path.package.remote'], '/nginx_upstream_check_module.tar.gz'])
        put(ngxUpstreamCheckLocal, ngxUpstreamCheckRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf nginx_upstream_check_module.tar.gz')
            run('mv nginx_upstream_check_module/ /home/configs/nginx/modules/')
            run('rm -rf nginx_upstream_check_module.tar.gz')

        ngxCachePurgeLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/ngx_cache_purge-2.3.tar.gz'])
        ngxCachePurgeRemote = ''.join([syDicts['path.package.remote'], '/ngx_cache_purge-2.3.tar.gz'])
        put(ngxCachePurgeLocal, ngxCachePurgeRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf ngx_cache_purge-2.3.tar.gz')
            run('mv ngx_cache_purge-2.3/ /home/configs/nginx/modules/ngx_cache_purge')
            run('rm -rf ngx_cache_purge-2.3.tar.gz')

        ngxHeaderMoreLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/headers-more-nginx-module-0.33.tar.gz'])
        ngxHeaderMoreRemote = ''.join([syDicts['path.package.remote'], '/headers-more-nginx-module-0.33.tar.gz'])
        put(ngxHeaderMoreLocal, ngxHeaderMoreRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf headers-more-nginx-module-0.33.tar.gz')
            run('mv headers-more-nginx-module-0.33/ /home/configs/nginx/modules/headers-more-nginx-module')
            run('rm -rf headers-more-nginx-module-0.33.tar.gz')

        nginxLocal = ''.join([syDicts['path.package.local'], '/resources/nginx/nginx-1.12.2.tar.gz'])
        nginxRemote = ''.join([syDicts['path.package.remote'], '/nginx-1.12.2.tar.gz'])
        put(nginxLocal, nginxRemote)
        with cd(syDicts['path.package.remote']):
            pcreDirRemote = ''.join([syDicts['path.package.remote'], '/pcre-8.42'])
            zlibDirRemote = ''.join([syDicts['path.package.remote'], '/zlib-1.2.11'])
            opensslDirRemote = ''.join([syDicts['path.package.remote'], '/openssl-1.1.0i'])
            run('mkdir /usr/local/nginx')
            run('tar -zxvf nginx-1.12.2.tar.gz')
            run('tar -zxvf pcre-8.42.tar.gz')
            run('tar -zxvf zlib-1.2.11.tar.gz')
            run('tar -zxvf openssl-1.1.0i.tar.gz')
            run('cd nginx-1.12.2/ && patch -p1 < /home/configs/nginx/modules/nginx_upstream_check_module/check_1.12.1+.patch && ./configure --prefix=/usr/local/nginx --with-pcre=%s --with-zlib=%s --with-openssl=%s --without-http_autoindex_module --without-http_ssi_module --with-http_ssl_module --with-http_stub_status_module --with-http_realip_module --with-http_gzip_static_module --with-http_v2_module --with-stream --with-stream_realip_module --with-stream_ssl_module --with-google_perftools_module --with-debug --add-module=/home/configs/nginx/modules/ngx_devel_kit --add-module=/home/configs/nginx/modules/lua-nginx-module --add-module=/home/configs/nginx/modules/nginx_upstream_check_module --add-module=/home/configs/nginx/modules/ngx_cache_purge --add-module=/home/configs/nginx/modules/headers-more-nginx-module --with-ld-opt="-Wl,-rpath,$LUAJIT_LIB" && make -j 4 && make install' % (pcreDirRemote, zlibDirRemote, opensslDirRemote))
            run('rm -rf nginx-1.12.2/')
            run('rm -rf nginx-1.12.2.tar.gz')
            run('rm -rf pcre-8.42/')
            run('rm -rf pcre-8.42.tar.gz')
            run('rm -rf zlib-1.2.11/')
            run('rm -rf zlib-1.2.11.tar.gz')
            run('rm -rf openssl-1.1.0i/')
            run('rm -rf openssl-1.1.0i.tar.gz')

        nginxConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/nginx.conf'])
        nginxConfRemote = '/usr/local/nginx/conf/nginx.conf'
        run('rm -rf %s' % nginxConfRemote)
        put(nginxConfLocal, nginxConfRemote)

        nginxApiConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/demoapi.conf'])
        nginxApiConfRemote = '/home/configs/nginx/servers/demoapi.conf'
        put(nginxApiConfLocal, nginxApiConfRemote)

        nginxOrderConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/a01order.conf'])
        nginxOrderConfRemote = '/home/configs/nginx/streams/a01order.conf'
        put(nginxOrderConfLocal, nginxOrderConfRemote)

        nginxServiceConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/a01services.conf'])
        nginxServiceConfRemote = '/home/configs/nginx/streams/a01services.conf'
        put(nginxServiceConfLocal, nginxServiceConfRemote)

        nginxUserConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/a01user.conf'])
        nginxUserConfRemote = '/home/configs/nginx/streams/a01user.conf'
        put(nginxUserConfLocal, nginxUserConfRemote)

        nginxFrontConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/demofront.conf'])
        nginxFrontConfRemote = '/home/configs/nginx/servers/demofront.conf'
        put(nginxFrontConfLocal, nginxFrontConfRemote)

        nginxFrontStaticConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/demofrontstatic.conf'])
        nginxFrontStaticConfRemote = '/home/configs/nginx/servers/demofrontstatic.conf'
        put(nginxFrontStaticConfLocal, nginxFrontStaticConfRemote)

        nginxDefaultServerConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/defaultserver.conf'])
        nginxDefaultServerConfRemote = '/home/configs/nginx/servers/defaultserver.conf'
        put(nginxDefaultServerConfLocal, nginxDefaultServerConfRemote)

        nginxServiceLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/nginx/nginx.service'])
        nginxServiceRemote = '/lib/systemd/system/nginx.service'
        put(nginxServiceLocal, nginxServiceRemote)
        run('chmod 754 %s' % nginxServiceRemote)
        run('systemctl enable nginx')

    # 配置PHP7环境
    @staticmethod
    def installPhp7():
        run('yum -y install gdb php-mcrypt libmcrypt libmcrypt-devel libxslt libxml2 libxml2-devel openssl openssl-devel curl-devel libcurl-devel libpng.x86_64 freetype.x86_64 libjpeg-turbo.x86_64 libjpeg-turbo-devel.x86_64 libjpeg-turbo-utils.x86_64 libpng-devel.x86_64 freetype-devel.x86_64 libjpeg-turbo-devel libmcrypt-devel mysql-devel openldap openldap-devel libtool-ltdl-devel.x86_64 gmp-devel')

        jpegLocal = ''.join([syDicts['path.package.local'], '/resources/php7/jpegsrc.v9.tar.gz'])
        jpegRemote = ''.join([syDicts['path.package.remote'], '/jpegsrc.v9.tar.gz'])
        put(jpegLocal, jpegRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/libjpeg')
            run('tar -zxvf jpegsrc.v9.tar.gz')
            run('cd jpeg-9/ && ./configure --prefix=/usr/local/libjpeg --enable-shared --enable-static && make && make install')
            run('ldconfig /usr/local/libjpeg/lib')
            run('rm -rf jpeg-9/')
            run('rm -rf jpegsrc.v9.tar.gz')

        freetypeLocal = ''.join([syDicts['path.package.local'], '/resources/php7/freetype-2.6.5.tar.bz2'])
        freetypeRemote = ''.join([syDicts['path.package.remote'], '/freetype-2.6.5.tar.bz2'])
        put(freetypeLocal, freetypeRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/freetype')
            run('tar -xjf freetype-2.6.5.tar.bz2')
            run('cd freetype-2.6.5/ && ./configure --prefix=/usr/local/freetype --enable-shared --enable-static && make && make install')
            run('ldconfig /usr/local/freetype/lib')
            run('rm -rf freetype-2.6.5/')
            run('rm -rf freetype-2.6.5.tar.bz2')

        php7Local = ''.join([syDicts['path.package.local'], '/resources/php7/php-7.1.21.tar.gz'])
        php7Remote = ''.join([syDicts['path.package.remote'], '/php-7.1.21.tar.gz'])
        put(php7Local, php7Remote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /tmp/swoolyaf')
            run('mkdir /home/configs/yaconf-cli')
            run('mkdir /home/configs/yaconf-fpm')
            run('mkdir /home/logs/seaslog-cli')
            run('mkdir /home/logs/seaslog-fpm')
            run('mkdir /usr/local/php7')
            run('tar -zxvf php-7.1.21.tar.gz')
            run('cd php-7.1.21/ && ./configure --prefix=/usr/local/php7 --exec-prefix=/usr/local/php7 --bindir=/usr/local/php7/bin --sbindir=/usr/local/php7/sbin --includedir=/usr/local/php7/include --libdir=/usr/local/php7/lib/php --mandir=/usr/local/php7/php/man --with-config-file-path=/usr/local/php7/etc --with-mysql-sock=/usr/local/mysql/mysql.sock --with-zlib=/usr/local/zlib --with-mhash --with-openssl --with-mysqli=shared,mysqlnd --with-pdo-mysql=shared,mysqlnd --with-iconv --enable-zip --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-xml --enable-pcntl --enable-bcmath --enable-mysqlnd --enable-sysvsem --with-mysqli --enable-embedded-mysqli  --with-pdo-mysql --enable-shmop --enable-mbregex --enable-mbstring --enable-ftp --enable-sockets --with-xmlrpc --enable-soap --without-pear --with-gettext --enable-session --with-curl --enable-opcache --enable-fpm --without-gdbm --enable-fileinfo --with-gmp && make && make install')

        php7CliIniLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/php7/php-cli.ini'])
        php7CliIniRemote = '/usr/local/php7/etc/php-cli.ini'
        put(php7CliIniLocal, php7CliIniRemote)
        php7FpmIniLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/php7/php-fpm-fcgi.ini'])
        php7FpmIniRemote = '/usr/local/php7/etc/php-fpm-fcgi.ini'
        put(php7FpmIniLocal, php7FpmIniRemote)
        php7FpmConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/php7/www.conf'])
        php7FpmConfRemote = '/usr/local/php7/etc/php-fpm.d/www.conf'
        put(php7FpmConfLocal, php7FpmConfRemote)
        php7FpmServiceLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/php7/php7-fpm.service'])
        php7FpmServiceRemote = '/lib/systemd/system/php7-fpm.service'
        put(php7FpmServiceLocal, php7FpmServiceRemote)
        run('chmod 754 /lib/systemd/system/php7-fpm.service')
        run('systemctl enable php7-fpm.service')

        php7DirRemote = ''.join([syDicts['path.package.remote'], '/php-7.1.21'])
        with cd(php7DirRemote):
            run('groupadd www')
            run('useradd -g www www -s /sbin/nologin')
            run('chown -R www /home/logs/seaslog-fpm')
            run('chgrp -R www /home/logs/seaslog-fpm')
            run('cp sapi/fpm/init.d.php-fpm /etc/init.d/php7-fpm')
            run('chmod +x /etc/init.d/php7-fpm')
            run('cp /usr/local/php7/etc/php-fpm.conf.default /usr/local/php7/etc/php-fpm.conf')
            run('cp -frp /usr/lib64/libldap* /usr/lib/')
            run('cd ext/ldap/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('cd ext/gd/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config --with-freetype-dir=/usr/local/freetype --with-jpeg-dir=/usr/local/libjpeg --with-zlib-dir=/usr/local/zlib --enable-gd-jis-conv --enable-gd-native-ttf && make && make install')

        # 扩展redis
        extRedisLocal = ''.join([syDicts['path.package.local'], '/resources/php7/redis-3.1.6.tgz'])
        extRedisRemote = ''.join([syDicts['path.package.remote'], '/redis-3.1.6.tgz'])
        put(extRedisLocal, extRedisRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/phpredis')
            run('tar -zxvf redis-3.1.6.tgz')
            run('cd redis-3.1.6/ && /usr/local/php7/bin/phpize && ./configure --prefix=/usr/local/phpredis --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf redis-3.1.6/')
            run('rm -rf redis-3.1.6.tgz')

        # 扩展imgick
        extImageMagickLocal = ''.join([syDicts['path.package.local'], '/resources/php7/ImageMagick-7.0.6-7.tar.gz'])
        extImageMagickRemote = ''.join([syDicts['path.package.remote'], '/ImageMagick-7.0.6-7.tar.gz'])
        put(extImageMagickLocal, extImageMagickRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/imagemagick')
            run('tar -zxvf ImageMagick-7.0.6-7.tar.gz')
            run('cd ImageMagick-7.0.6-7/ && ./configure --prefix=/usr/local/imagemagick --enable-shared --enable-lzw --without-perl --with-modules && make && make install')
            run('rm -rf ImageMagick-7.0.6-7/')
            run('rm -rf ImageMagick-7.0.6-7.tar.gz')
        extImagickLocal = ''.join([syDicts['path.package.local'], '/resources/php7/imagick-3.4.3.tgz'])
        extImagickRemote = ''.join([syDicts['path.package.remote'], '/imagick-3.4.3.tgz'])
        put(extImagickLocal, extImagickRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/imagick')
            run('tar -zxvf imagick-3.4.3.tgz')
            run('cd imagick-3.4.3/ && /usr/local/php7/bin/phpize && ./configure --prefix=/usr/local/imagick --with-php-config=/usr/local/php7/bin/php-config --with-imagick=/usr/local/imagemagick && make && make install')
            run('rm -rf imagick-3.4.3/')
            run('rm -rf imagick-3.4.3.tgz')

        # 扩展SeasLog
        extSeasLogLocal = ''.join([syDicts['path.package.local'], '/resources/php7/SeasLog-1.8.4.tgz'])
        extSeasLogRemote = ''.join([syDicts['path.package.remote'], '/SeasLog-1.8.4.tgz'])
        put(extSeasLogLocal, extSeasLogRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf SeasLog-1.8.4.tgz')
            run('cd SeasLog-1.8.4/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf SeasLog-1.8.4/')
            run('rm -rf SeasLog-1.8.4.tgz')

        # 扩展mongodb
        extMongodbLocal = ''.join([syDicts['path.package.local'], '/resources/php7/mongodb-1.3.4.tgz'])
        extMongodbRemote = ''.join([syDicts['path.package.remote'], '/mongodb-1.3.4.tgz'])
        put(extMongodbLocal, extMongodbRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf mongodb-1.3.4.tgz')
            run('cd mongodb-1.3.4/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf mongodb-1.3.4/')
            run('rm -rf mongodb-1.3.4.tgz')

        # 扩展msgpack
        extMsgpackLocal = ''.join([syDicts['path.package.local'], '/resources/php7/msgpack-2.0.2.tgz'])
        extMsgpackRemote = ''.join([syDicts['path.package.remote'], '/msgpack-2.0.2.tgz'])
        put(extMsgpackLocal, extMsgpackRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf msgpack-2.0.2.tgz')
            run('cd msgpack-2.0.2/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf msgpack-2.0.2/')
            run('rm -rf msgpack-2.0.2.tgz')

        # 扩展yac
        extYacLocal = ''.join([syDicts['path.package.local'], '/resources/php7/yac-2.0.2.tgz'])
        extYacRemote = ''.join([syDicts['path.package.remote'], '/yac-2.0.2.tgz'])
        put(extYacLocal, extYacRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf yac-2.0.2.tgz')
            run('cd yac-2.0.2/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf yac-2.0.2/')
            run('rm -rf yac-2.0.2.tgz')

        # 扩展yaconf
        extYaconfLocal = ''.join([syDicts['path.package.local'], '/resources/php7/yaconf-1.0.7.tgz'])
        extYaconfRemote = ''.join([syDicts['path.package.remote'], '/yaconf-1.0.7.tgz'])
        put(extYaconfLocal, extYaconfRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf yaconf-1.0.7.tgz')
            run('cd yaconf-1.0.7/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf yaconf-1.0.7/')
            run('rm -rf yaconf-1.0.7.tgz')

        # 扩展yaf
        extYafLocal = ''.join([syDicts['path.package.local'], '/resources/php7/yaf-3.0.7.tgz'])
        extYafRemote = ''.join([syDicts['path.package.remote'], '/yaf-3.0.7.tgz'])
        put(extYafLocal, extYafRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf yaf-3.0.7.tgz')
            run('cd yaf-3.0.7/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install')
            run('rm -rf yaf-3.0.7/')
            run('rm -rf yaf-3.0.7.tgz')

        # 扩展swoole
        extSwooleJemallocLocal = ''.join([syDicts['path.package.local'], '/resources/php7/jemalloc-4.5.0.tar.bz2'])
        extSwooleJemallocRemote = ''.join([syDicts['path.package.remote'], '/jemalloc-4.5.0.tar.bz2'])
        put(extSwooleJemallocLocal, extSwooleJemallocRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/jemalloc')
            run('tar -xjvf jemalloc-4.5.0.tar.bz2')
            run('cd jemalloc-4.5.0/ && ./configure --prefix=/usr/local/jemalloc --with-jemalloc-prefix=je_ && make -j 4 && make install')
            run('rm -rf jemalloc-4.5.0/')
            run('rm -rf jemalloc-4.5.0.tar.bz2')
        extSwooleHttp2Local = ''.join([syDicts['path.package.local'], '/resources/php7/nghttp2-1.26.0.tar.bz2'])
        extSwooleHttp2Remote = ''.join([syDicts['path.package.remote'], '/nghttp2-1.26.0.tar.bz2'])
        put(extSwooleHttp2Local, extSwooleHttp2Remote)
        with cd(syDicts['path.package.remote']):
            run('tar -xf nghttp2-1.26.0.tar.bz2')
            run('mv nghttp2-1.26.0/ /usr/local/nghttp2')
            run('cd /usr/local/nghttp2 && ./configure && make libdir=/usr/lib64 && make libdir=/usr/lib64 install')
            run('rm -rf nghttp2-1.26.0.tar.bz2')
        extSwooleLocal = ''.join([syDicts['path.package.local'], '/resources/php7/swoole-4.0.4.tgz'])
        extSwooleRemote = ''.join([syDicts['path.package.remote'], '/swoole-4.0.4.tgz'])
        put(extSwooleLocal, extSwooleRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf swoole-4.0.4.tgz')
            run('cd swoole-4.0.4/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config --with-jemalloc-dir=/usr/local/jemalloc --enable-openssl --enable-http2 && make && make install')
            run('rm -rf swoole-4.0.4/')
            run('rm -rf swoole-4.0.4.tgz')

    # 配置java环境
    @staticmethod
    def installJava():
        jdkLocal = ''.join([syDicts['path.package.local'], '/resources/java/jdk-8u131-linux-x64.tar.gz'])
        jdkRemote = ''.join([syDicts['path.package.remote'], '/jdk-8u131-linux-x64.tar.gz'])
        put(jdkLocal, jdkRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/java')
            run('tar -zxvf jdk-8u131-linux-x64.tar.gz')
            run('mv jdk1.8.0_131/ /usr/java/jdk1.8.0')
            run('rm -rf jdk-8u131-linux-x64.tar.gz')

    # 配置inotify环境
    @staticmethod
    def installInotify():
        inotifyLocal = ''.join([syDicts['path.package.local'], '/resources/linux/inotify-tools-3.14.tar.gz'])
        inotifyRemote = ''.join([syDicts['path.package.remote'], '/inotify-tools-3.14.tar.gz'])
        put(inotifyLocal, inotifyRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/inotify')
            run('tar -zxvf inotify-tools-3.14.tar.gz')
            run('cd inotify-tools-3.14/ && ./configure --prefix=/usr/local/inotify && make && make install')
            run('rm -rf inotify-tools-3.14/')
            run('rm -rf inotify-tools-3.14.tar.gz')
            run('mkdir /usr/local/inotify/symodules')
            run('touch /usr/local/inotify/symodules/change_service.txt')

    # 配置etcd环境
    @staticmethod
    def installEtcd():
        etcdLocal = ''.join([syDicts['path.package.local'], '/resources/linux/etcd-v3.2.7-linux-amd64.tar.gz'])
        etcdRemote = ''.join([syDicts['path.package.remote'], '/etcd-v3.2.7-linux-amd64.tar.gz'])
        put(etcdLocal, etcdRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf etcd-v3.2.7-linux-amd64.tar.gz')
            run('mv etcd-v3.2.7-linux-amd64/ /usr/local/etcd')
            run('cp /usr/local/etcd/etcd* /usr/local/bin')
            run('rm -rf etcd-v3.2.7-linux-amd64.tar.gz')

    # 配置redis环境
    @staticmethod
    def installRedis():
        redisLocal = ''.join([syDicts['path.package.local'], '/resources/redis/redis-3.2.11.tar.gz'])
        redisRemote = ''.join([syDicts['path.package.remote'], '/redis-3.2.11.tar.gz'])
        put(redisLocal, redisRemote)
        with cd(syDicts['path.package.remote']):
            run('mkdir /usr/local/redis')
            run('mkdir /etc/redis')
            run('tar -zxvf redis-3.2.11.tar.gz')
            run('mv redis-3.2.11/ /usr/local/redis/')
            run('cd /usr/local/redis/redis-3.2.11 && make && cd src/ && make install')
            redisServiceLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/redis/redis'])
            redisServiceRemote = '/etc/init.d/redis'
            put(redisServiceLocal, redisServiceRemote)
            run('chmod +x %s' % redisServiceRemote)
            redisConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/redis/6379.conf'])
            redisConfRemote = '/etc/redis/6379.conf'
            put(redisConfLocal, redisConfRemote)
            run('echo -e "\nbind 127.0.0.1 %s" >> %s' % (env.host, redisConfRemote), False)
            run('rm -rf redis-3.2.11.tar.gz')
            run('systemctl daemon-reload')
            run('chkconfig redis on')

    @staticmethod
    def installFtp():
        run('yum -y install pam pam-devel db4 de4-devel db4-uitls db4-tcl vsftpd')
        run('useradd vsftpd -s /sbin/nologin')
        ftpUserLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/ftp/vftpuser.txt'])
        ftpUserRemote = '/etc/vsftpd/vftpuser.txt'
        put(ftpUserLocal, ftpUserRemote)
        run('echo "" >> %s' % ftpUserRemote, False)
        run('db_load -T -t hash -f %s /etc/vsftpd/vftpuser.db' % ftpUserRemote)
        ftpdLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/ftp/vsftpd'])
        ftpdRemote = '/etc/pam.d/vsftpd'
        run('mv %s /etc/pam.d/vsftpd.back' % ftpdRemote)
        put(ftpdLocal, ftpdRemote)
        run('touch /etc/vsftpd/chroot_list')
        run('mkdir /etc/vsftpd/vsftpd_user_conf')
        run('echo "local_root=/home/jw" > /etc/vsftpd/vsftpd_user_conf/jw')
        ftpConfLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/ftp/vsftpd.conf'])
        ftpConfRemote = '/etc/vsftpd/vsftpd.conf'
        run('mv %s /etc/vsftpd/vsftpd.conf.back' % ftpConfRemote)
        put(ftpConfLocal, ftpConfRemote)
        run('systemctl enable vsftpd')
        run('systemctl restart vsftpd')

    @staticmethod
    def installMysql():
        run('rm -rf /etc/my.cnf')
        run('yum -y install make cmake libaio libaio-devel bison-devel ncurses-devel perl-Data-Dumpe')
        run('groupadd mysql && useradd -g mysql mysql -s /sbin/nologin')
        run('mkdir /usr/local/mysql/data && mkdir /home/logs/mysql && touch /home/logs/mysql/error.log && chown -R mysql /home/logs/mysql && chgrp -R mysql /home/logs/mysql')

        mysqlLocal = ''.join([syDicts['path.package.local'], '/resources/mysql/mysql-5.6.37.tar.gz'])
        mysqlRemote = ''.join([syDicts['path.package.remote'], '/mysql-5.6.37.tar.gz'])
        put(mysqlLocal, mysqlRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf mysql-5.6.37.tar.gz')
            run('cd mysql-5.6.37/ && cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql -DMYSQL_DATADIR=/usr/local/mysql/data -DSYSCONFDIR=/etc/my.cnf -DWITH_MYISAM_STORAGE_ENGINE=1 -DWITH_INNOBASE_STORAGE_ENGINE=1 -DMYSQL_UNIX_ADDR=/usr/local/mysql/mysql.sock -DMYSQL_TCP_PORT=3306 -DENABLED_LOCAL_INFILE=1 -DWITH_PARTITION_STORAGE_ENGINE=1 -DEXTRA_CHARSETS=all && make && make install')
            run('chown -R mysql:mysql /usr/local/mysql')

        mysqlConfigLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mysql/my.cnf'])
        mysqlConfigRemote = '/etc/my.cnf'
        put(mysqlConfigLocal, mysqlConfigRemote)
        with cd('/usr/local/mysql'):
            run('./scripts/mysql_install_db --user=mysql --basedir=/usr/local/mysql --datadir=/usr/local/mysql/data')

        run('echo "/usr/local/mysql/lib" > /etc/ld.so.conf.d/mysql.conf')
        run('ldconfig')

        mysqlServiceLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mysql/mysql.service'])
        mysqlServiceRemote = '/lib/systemd/system/mysql.service'
        put(mysqlServiceLocal, mysqlServiceRemote)
        run('chmod 754 %s' % mysqlServiceRemote)
        run('systemctl enable mysql')

        with cd(syDicts['path.package.remote']):
            run('rm -rf mysql-5.6.37/ && rm -rf mysql-5.6.37.tar.gz')

    @staticmethod
    def installMongodb():
        run('echo "never" > /sys/kernel/mm/transparent_hugepage/enabled && echo "never" > /sys/kernel/mm/transparent_hugepage/defrag')

        mongoLocal = ''.join([syDicts['path.package.local'], '/resources/mongodb/mongodb-linux-x86_64-rhel70-3.2.17.tgz'])
        mongoRemote = ''.join([syDicts['path.package.remote'], '/mongodb-linux-x86_64-rhel70-3.2.17.tgz'])
        put(mongoLocal, mongoRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf mongodb-linux-x86_64-rhel70-3.2.17.tgz')
            run('mv mongodb-linux-x86_64-rhel70-3.2.17/ /usr/local/mongodb')
            run('mkdir /usr/local/mongodb/data && mkdir /usr/local/mongodb/data/db && mkdir /usr/local/mongodb/data/logs')
            run('rm -rf mongodb-linux-x86_64-rhel70-3.2.17.tgz')

        mongoConfigLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mongodb/mongodb.conf'])
        mongoConfigRemote = '/usr/local/mongodb/mongodb.conf'
        put(mongoConfigLocal, mongoConfigRemote)

        # crontab任务对应的txt文件结束必须按回车键另起一行
        mongoCronLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mongodb/crontab.txt'])
        mongoCronRemote = ''.join([syDicts['path.package.remote'], '/crontab.txt'])
        put(mongoCronLocal, mongoCronRemote)
        run('crontab %s' % mongoCronRemote)
        run('rm -rf %s' % mongoCronRemote)
