FROM base-common
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="nginx基础环境" image.version="1.0.0" image.desc="基于全局基础环境构建" image.tag="base-nginx"
ENV SYDIR_DOWNLOAD=/home/download
ENV LUAJIT_LIB=/usr/local/luajit/lib
ENV LUAJIT_INC=/usr/local/luajit/include/luajit-2.0
ENV CPPFLAGS="-I/usr/local/include -I/usr/local/zlib/include -I/usr/local/pcre/include"
ENV LDFLAGS="-L/usr/local/lib -L/usr/local/lib64 -L/usr/local/zlib/lib -L/usr/local/pcre/lib"
ENV PATH=$PATH:/usr/local/git/bin:/usr/local/bin
RUN echo "" >> /etc/profile && echo "export LUAJIT_LIB=/usr/local/luajit/lib" >> /etc/profile && echo "export LUAJIT_INC=/usr/local/luajit/include/luajit-2.0" >> /etc/profile && echo "export CPPFLAGS='-I/usr/local/include -I/usr/local/zlib/include -I/usr/local/pcre/include'" >> /etc/profile && echo "export LDFLAGS='-L/usr/local/lib -L/usr/local/lib64 -L/usr/local/zlib/lib -L/usr/local/pcre/lib'" >> /etc/profile && echo "export PATH=\$PATH:/usr/local/git/bin:/usr/local/bin" >> /etc/profile
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
RUN rm -rf pcre-8.42/

# zlib
COPY ./resources/linux/zlib-1.2.11.tar.gz $SYDIR_DOWNLOAD/zlib-1.2.11.tar.gz
RUN mkdir /usr/local/zlib
RUN tar -zxvf zlib-1.2.11.tar.gz
RUN cd zlib-1.2.11/ && ./configure --prefix=/usr/local/zlib && make && make install && echo "/usr/local/zlib/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf zlib-1.2.11/

# openssl
COPY ./resources/nginx/openssl-1.0.2p.tar.gz $SYDIR_DOWNLOAD/openssl-1.0.2p.tar.gz
RUN mkdir /usr/local/openssl
RUN tar -zxvf openssl-1.0.2p.tar.gz
RUN cd openssl-1.0.2p/ && ./config --prefix=/usr/local/openssl shared zlib && make && make install && echo "/usr/local/openssl/lib" >> /etc/ld.so.conf && ldconfig
RUN rm -rf openssl-1.0.2p/
RUN mv /usr/bin/openssl /usr/bin/openssl.old && mv /usr/include/openssl /usr/include/openssl.old && ln -s /usr/local/openssl/bin/openssl /usr/bin/openssl && ln -s /usr/local/openssl/include/openssl /usr/include/openssl

# nghtt2
COPY ./resources/linux/nghttp2-1.26.0.tar.bz2 $SYDIR_DOWNLOAD/nghttp2-1.26.0.tar.bz2
RUN tar -xf nghttp2-1.26.0.tar.bz2
RUN mv nghttp2-1.26.0/ /usr/local/nghttp2
RUN cd /usr/local/nghttp2 && ./configure && make && make install
RUN rm -rf nghttp2-1.26.0.tar.bz2
