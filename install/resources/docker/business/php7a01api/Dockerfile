FROM system-php7
MAINTAINER jiangwei "jiangwei07061625@163.com"
LABEL image.title="a01项目api模块" image.version="1.0.0" image.desc="基于PHP7系统环境构建" image.tag="business-php7a01api"
ENV SYDIR_DOWNLOAD /home/download
WORKDIR $SYDIR_DOWNLOAD

RUN mkdir -p /home/phpspace/swooleyaf_libs && mkdir -p /home/phpspace/swooleyaf
COPY ./resources/docker/resources/php7a01/sya01.tar.gz $SYDIR_DOWNLOAD/sya01.tar.gz
RUN tar -zxvf sya01.tar.gz
RUN mv swooleyaf/* /home/phpspace/swooleyaf
RUN rm -rf sya01.tar.gz
COPY ./resources/docker/resources/php7a01/sylibs.tar.gz $SYDIR_DOWNLOAD/sylibs.tar.gz
RUN tar -zxvf sylibs.tar.gz
RUN mv swooleyaf_libs/* /home/phpspace/swooleyaf_libs
RUN rm -rf sylibs.tar.gz
COPY ./resources/docker/resources/php7a01/yaconf-cli.tar.gz $SYDIR_DOWNLOAD/yaconf-cli.tar.gz
RUN tar -zxvf yaconf-cli.tar.gz
RUN mv yaconf-cli/* /home/configs/yaconf-cli
RUN rm -rf yaconf-cli.tar.gz

ARG MODULE_PORT=7100
EXPOSE ${MODULE_PORT}

ENTRYPOINT ["/usr/local/php7/bin/php", "/home/phpspace/swooleyaf/helper_service.php"]