FROM base-common
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="PHP7基础环境" image.version="1.0.0" image.desc="基于全局基础环境构建" image.tag="base-php7"
ENV SYDIR_DOWNLOAD=/home/download
ENV CPPFLAGS="-I/usr/local/libjpeg/include -I/usr/local/freetype/include -I/usr/local/include -I/usr/local/zlib/include -I/usr/local/pcre/include"
ENV LDFLAGS="-L/usr/local/libjpeg/lib -L/usr/local/freetype/lib -L/usr/local/lib -L/usr/local/lib64 -L/usr/local/zlib/lib -L/usr/local/pcre/lib"
ENV LD_LIBRARY_PATH=$LD_LIBRARY_PATH:/usr/local/lib
ENV JAVA_HOME=/usr/java/jdk1.8.0
ENV CLASSPATH=.:$JAVA_HOME/jre/lib/rt.jar:$JAVA_HOME/lib/dt.jar:$JAVA_HOME/lib/tools.jar
ENV PATH=$PATH:/usr/local/git/bin:/usr/local/bin:$JAVA_HOME/bin:$JAVA_HOME/jre/bin
RUN echo "" >> /etc/profile && echo "export CPPFLAGS='-I/usr/local/libjpeg/include -I/usr/local/freetype/include -I/usr/local/include -I/usr/local/zlib/include -I/usr/local/pcre/include'" >> /etc/profile && echo "export LDFLAGS='-L/usr/local/libjpeg/lib -L/usr/local/freetype/lib -L/usr/local/lib -L/usr/local/lib64 -L/usr/local/zlib/lib -L/usr/local/pcre/lib'" >> /etc/profile && echo "export LD_LIBRARY_PATH=\$LD_LIBRARY_PATH:/usr/local/lib" >> /etc/profile && echo "export JAVA_HOME=/usr/java/jdk1.8.0" >> /etc/profile && echo "export CLASSPATH=.:\$JAVA_HOME/jre/lib/rt.jar:\$JAVA_HOME/lib/dt.jar:\$JAVA_HOME/lib/tools.jar" >> /etc/profile && echo "export PATH=\$PATH:/usr/local/git/bin:/usr/local/bin:\$JAVA_HOME/bin:\$JAVA_HOME/jre/bin" >> /etc/profile
RUN source /etc/profile
WORKDIR $SYDIR_DOWNLOAD

# git
COPY ./resources/linux/git-2.10.2.tar.gz $SYDIR_DOWNLOAD/git-2.10.2.tar.gz
RUN yum -y remove git
RUN mkdir /usr/local/git
RUN tar -xzf git-2.10.2.tar.gz
RUN cd git-2.10.2/ && ./configure --prefix=/usr/local/git && make all && make install
RUN rm -rf git-2.10.2/ && rm -rf git-2.10.2.tar.gz
RUN git config --global user.name "jiangwei" && git config --global user.email "jiangwei07061625@163.com"

# pcre
COPY ./resources/linux/pcre-8.42.tar.gz $SYDIR_DOWNLOAD/pcre-8.42.tar.gz
RUN mkdir /usr/local/pcre
RUN tar -zxvf pcre-8.42.tar.gz
RUN cd pcre-8.42/ && ./configure --prefix=/usr/local/pcre && make && make install && echo "/usr/local/pcre/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf pcre-8.42/ && rm -rf pcre-8.42.tar.gz

# zlib
COPY ./resources/linux/zlib-1.2.11.tar.gz $SYDIR_DOWNLOAD/zlib-1.2.11.tar.gz
RUN mkdir /usr/local/zlib
RUN tar -zxvf zlib-1.2.11.tar.gz
RUN cd zlib-1.2.11/ && ./configure --prefix=/usr/local/zlib && make && make install && echo "/usr/local/zlib/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf zlib-1.2.11/ && rm -rf zlib-1.2.11.tar.gz

# openssl
COPY ./resources/nginx/openssl-1.0.2p.tar.gz $SYDIR_DOWNLOAD/openssl-1.0.2p.tar.gz
RUN mkdir /usr/local/openssl
RUN tar -zxvf openssl-1.0.2p.tar.gz
RUN cd openssl-1.0.2p/ && ./config --prefix=/usr/local/openssl shared zlib && make && make install && echo "/usr/local/openssl/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf openssl-1.0.2p/ && rm -rf openssl-1.0.2p.tar.gz
RUN mv /usr/bin/openssl /usr/bin/openssl.old && mv /usr/include/openssl /usr/include/openssl.old && ln -s /usr/local/openssl/bin/openssl /usr/bin/openssl && ln -s /usr/local/openssl/include/openssl /usr/include/openssl

# nghtt2
COPY ./resources/linux/nghttp2-1.26.0.tar.bz2 $SYDIR_DOWNLOAD/nghttp2-1.26.0.tar.bz2
RUN tar -xf nghttp2-1.26.0.tar.bz2
RUN mv nghttp2-1.26.0/ /usr/local/nghttp2
RUN cd /usr/local/nghttp2 && ./configure && make && make install
RUN rm -rf nghttp2-1.26.0.tar.bz2

# jpeg
COPY ./resources/linux/jpegsrc.v9.tar.gz $SYDIR_DOWNLOAD/jpegsrc.v9.tar.gz
RUN mkdir /usr/local/libjpeg
RUN tar -zxvf jpegsrc.v9.tar.gz
RUN cd jpeg-9/ && ./configure --prefix=/usr/local/libjpeg --enable-shared --enable-static && make && make install && echo "/usr/local/libjpeg/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf jpeg-9/ && rm -rf jpegsrc.v9.tar.gz

# ImageMagick
COPY ./resources/linux/ImageMagick-7.0.6-7.tar.gz $SYDIR_DOWNLOAD/ImageMagick-7.0.6-7.tar.gz
RUN mkdir /usr/local/imagemagick
RUN tar -zxvf ImageMagick-7.0.6-7.tar.gz
RUN cd ImageMagick-7.0.6-7/ && ./configure --prefix=/usr/local/imagemagick --enable-shared --enable-lzw --without-perl --with-modules && make && make install
RUN rm -rf ImageMagick-7.0.6-7/ && rm -rf ImageMagick-7.0.6-7.tar.gz

# freetype
COPY ./resources/linux/freetype-2.6.5.tar.bz2 $SYDIR_DOWNLOAD/freetype-2.6.5.tar.bz2
RUN mkdir /usr/local/freetype
RUN tar -xjf freetype-2.6.5.tar.bz2
RUN cd freetype-2.6.5/ && ./configure --prefix=/usr/local/freetype --enable-shared --enable-static && make && make install && echo "/usr/local/freetype/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf freetype-2.6.5/ && rm -rf freetype-2.6.5.tar.bz2

# jemalloc
COPY ./resources/linux/jemalloc-4.5.0.tar.bz2 $SYDIR_DOWNLOAD/jemalloc-4.5.0.tar.bz2
RUN mkdir /usr/local/jemalloc
RUN tar -xjvf jemalloc-4.5.0.tar.bz2
RUN cd jemalloc-4.5.0/ && ./configure --prefix=/usr/local/jemalloc --with-jemalloc-prefix=je_ && make -j 4 && make install
RUN rm -rf jemalloc-4.5.0/ && rm -rf jemalloc-4.5.0.tar.bz2

# jdk
COPY ./resources/java/jdk-8u131-linux-x64.tar.gz $SYDIR_DOWNLOAD/jdk-8u131-linux-x64.tar.gz
RUN mkdir /usr/java
RUN tar -zxvf jdk-8u131-linux-x64.tar.gz
RUN mv jdk1.8.0_131/ /usr/java/jdk1.8.0
RUN rm -rf jdk-8u131-linux-x64.tar.gz