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

# 贝叶斯分类
    https://www.jianshu.com/p/f6a3f3200689

# 配置
## swooleyaf环境安装
    详情参见文件: initcentos7/swooleyaf/helper_swooleyaf.md

## sywaf防火墙
    详情参见文件: resources/nginx/lualib/helper_sywaf.md

# 命令
## swooleyaf环境安装
    //所有系统环境均为CentOS7
    //安装服务器环境
    /usr/local/python3/bin/fab -f fabfile.py installEnv:envType="syMysql",envStep=1
        envType 环境类型,支持的类型如下:
            syMysql: swooleyaf的mysql环境
            syMongodb: swooleyaf的mongodb环境
            syFront: swooleyaf的前端环境
            syBackend: swooleyaf的后端环境
            syFrontBackend: swooleyaf的前后端混合环境
        envStep 执行步骤,大于0的整数