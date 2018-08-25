FROM base-php7
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="PHP7系统环境" image.version="1.0.0" image.desc="基于PHP7基础环境构建" image.tag="system-php7"
ENV SYDIR_DOWNLOAD /home/download
RUN yum -y install libxslt libxml2 libxml2-devel mysql-devel openldap openldap-devel gmp-devel
WORKDIR $SYDIR_DOWNLOAD

# php7
COPY ./resources/php7/php-7.1.21.tar.gz $SYDIR_DOWNLOAD/php-7.1.21.tar.gz
RUN mkdir /tmp/swoolyaf && mkdir /home/configs/yaconf-cli && mkdir /home/configs/yaconf-fpm && mkdir /home/logs/seaslog-cli && mkdir /home/logs/seaslog-fpm && mkdir /usr/local/php7
RUN tar -zxvf php-7.1.21.tar.gz
RUN cd php-7.1.21/ && ./configure --prefix=/usr/local/php7 --exec-prefix=/usr/local/php7 --bindir=/usr/local/php7/bin --sbindir=/usr/local/php7/sbin --includedir=/usr/local/php7/include --libdir=/usr/local/php7/lib/php --mandir=/usr/local/php7/php/man --with-config-file-path=/usr/local/php7/etc --with-mysql-sock=/usr/local/mysql/mysql.sock --with-zlib=/usr/local/zlib --with-mhash --with-openssl=/usr/local/openssl --with-mysqli=shared,mysqlnd --with-pdo-mysql=shared,mysqlnd --with-iconv --enable-zip --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-xml --enable-pcntl --enable-bcmath --enable-mysqlnd --enable-sysvsem --with-mysqli --enable-embedded-mysqli  --with-pdo-mysql --enable-shmop --enable-mbregex --enable-mbstring --enable-ftp --enable-sockets --with-xmlrpc --enable-soap --without-pear --with-gettext --enable-session --with-curl --enable-opcache --enable-fpm --without-gdbm --enable-fileinfo --with-gmp && make && make install
COPY ./configs/swooleyaf/php7/php-cli.ini /usr/local/php7/etc/php-cli.ini
COPY ./configs/swooleyaf/php7/php-fpm-fcgi.ini /usr/local/php7/etc/php-fpm-fcgi.ini
COPY ./configs/swooleyaf/php7/www.conf /usr/local/php7/etc/php-fpm.d/www.conf
COPY ./configs/swooleyaf/php7/php7-fpm.service /lib/systemd/system/php7-fpm.service
RUN chmod 754 /lib/systemd/system/php7-fpm.service
RUN groupadd www && useradd -g www www -s /sbin/nologin && chown -R www /home/logs/seaslog-fpm && chgrp -R www /home/logs/seaslog-fpm
RUN cp sapi/fpm/init.d.php-fpm /etc/init.d/php7-fpm && chmod +x /etc/init.d/php7-fpm
RUN cp /usr/local/php7/etc/php-fpm.conf.default /usr/local/php7/etc/php-fpm.conf
RUN cp -frp /usr/lib64/libldap* /usr/lib/
RUN cd php-7.1.21/ext/ldap/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN cd php-7.1.21/ext/gd/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config --with-freetype-dir=/usr/local/freetype --with-jpeg-dir=/usr/local/libjpeg --with-zlib-dir=/usr/local/zlib --enable-gd-jis-conv --enable-gd-native-ttf && make && make install
RUN rm -rf php-7.1.21/ && rm -rf php-7.1.21.tar.gz

# extension redis
COPY ./resources/php7/redis-4.1.1.tgz $SYDIR_DOWNLOAD/redis-4.1.1.tgz
RUN mkdir /usr/local/phpredis
RUN tar -zxvf redis-4.1.1.tgz
RUN cd redis-4.1.1/ && /usr/local/php7/bin/phpize && ./configure --prefix=/usr/local/phpredis --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf redis-4.1.1/ && rm -rf redis-4.1.1.tgz

# extension imagick
COPY ./resources/php7/imagick-3.4.3.tgz $SYDIR_DOWNLOAD/imagick-3.4.3.tgz
RUN mkdir /usr/local/imagick
RUN tar -zxvf imagick-3.4.3.tgz
RUN cd imagick-3.4.3/ && /usr/local/php7/bin/phpize && ./configure --prefix=/usr/local/imagick --with-php-config=/usr/local/php7/bin/php-config --with-imagick=/usr/local/imagemagick && make && make install
RUN rm -rf imagick-3.4.3/ && rm -rf imagick-3.4.3.tgz

# extension seaslog
COPY ./resources/php7/SeasLog-1.8.4.tgz $SYDIR_DOWNLOAD/SeasLog-1.8.4.tgz
RUN tar -zxvf SeasLog-1.8.4.tgz
RUN cd SeasLog-1.8.4/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf SeasLog-1.8.4/ && rm -rf SeasLog-1.8.4.tgz

# extension mongodb
COPY ./resources/php7/mongodb-1.5.2.tgz $SYDIR_DOWNLOAD/mongodb-1.5.2.tgz
RUN tar -zxvf mongodb-1.5.2.tgz
RUN cd mongodb-1.5.2/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf mongodb-1.5.2/ && rm -rf mongodb-1.5.2.tgz

# extension msgpack
COPY ./resources/php7/msgpack-2.0.2.tgz $SYDIR_DOWNLOAD/msgpack-2.0.2.tgz
RUN tar -zxvf msgpack-2.0.2.tgz
RUN cd msgpack-2.0.2/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf msgpack-2.0.2/ && rm -rf msgpack-2.0.2.tgz

# extension yac
COPY ./resources/php7/yac-2.0.2.tgz $SYDIR_DOWNLOAD/yac-2.0.2.tgz
RUN tar -zxvf yac-2.0.2.tgz
RUN cd yac-2.0.2/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf yac-2.0.2/ && rm -rf yac-2.0.2.tgz

# extension yaconf
COPY ./resources/php7/yaconf-1.0.7.tgz $SYDIR_DOWNLOAD/yaconf-1.0.7.tgz
RUN tar -zxvf yaconf-1.0.7.tgz
RUN cd yaconf-1.0.7/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf yaconf-1.0.7/ && rm -rf yaconf-1.0.7.tgz

# extension yaf
COPY ./resources/php7/yaf-3.0.7.tgz $SYDIR_DOWNLOAD/yaf-3.0.7.tgz
RUN tar -zxvf yaf-3.0.7.tgz
RUN cd yaf-3.0.7/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config && make && make install
RUN rm -rf yaf-3.0.7/ && rm -rf yaf-3.0.7.tgz

# extension swoole
COPY ./resources/php7/swoole-4.0.4.tgz $SYDIR_DOWNLOAD/swoole-4.0.4.tgz
RUN tar -zxvf swoole-4.0.4.tgz
RUN cd swoole-4.0.4/ && /usr/local/php7/bin/phpize && ./configure --with-php-config=/usr/local/php7/bin/php-config --with-jemalloc-dir=/usr/local/jemalloc --enable-openssl --enable-http2 && make && make install
RUN rm -rf swoole-4.0.4/ && rm -rf swoole-4.0.4.tgz