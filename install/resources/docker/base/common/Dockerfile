FROM centos:7
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="公共基础环境" image.version="1.0.0" image.desc="全局基础环境,其他所有镜像都是基于该镜像集成" image.tag="base-common"
RUN yum -y install gdb vim zip nss gcc gcc-c++ net-tools wget htop lsof unzip bzip2 curl-devel libcurl-devel zlib-devel epel-release perl-ExtUtils-MakeMaker expat-devel gettext-devel openssl-devel iproute.x86_64 autoconf automake make cmake libtool libtool-ltdl libtool-ltdl-devel libpng.x86_64 freetype.x86_64 libjpeg-turbo.x86_64 libjpeg-turbo-devel.x86_64 libjpeg-turbo-utils.x86_64 libpng-devel.x86_64 freetype-devel.x86_64 libjpeg-turbo-devel
RUN wget -O /etc/yum.repos.d/CentOS-Base.repo http://mirrors.aliyun.com/repo/Centos-7.repo
RUN yum -y update
RUN yum clean all
RUN systemctl enable crond
RUN mkdir /home/configs && mkdir /home/logs && mkdir /usr/local/mysql && mkdir /home/download