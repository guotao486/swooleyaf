# python3安装(centos7)
    wget https://www.python.org/ftp/python/3.6.4/Python-3.6.4.tar.xz
    mkdir /usr/local/python3
    tar -Jxvf Python-3.6.4.tar.xz
    cd Python-3.6.4
    ./configure --prefix=/usr/local/python3
    make && make install
    /usr/local/python3/bin/pip3 install fabric3

# pycharm配置
    Setting->Project->Project Interpreter->Add Local
    设置Base interpreter并勾选上Inherit global site-packages

# 命令
    //所有系统环境均为CentOS7
    //安装服务器环境-前端
    /usr/local/python3/bin/fab -f fabfile.py installFront
    //安装服务器环境-后端
    /usr/local/python3/bin/fab -f fabfile.py installBackend
    //安装服务器环境-mysql
    /usr/local/python3/bin/fab -f fabfile.py installMysql
    //安装服务器环境-mongodb
    /usr/local/python3/bin/fab -f fabfile.py installMongodb