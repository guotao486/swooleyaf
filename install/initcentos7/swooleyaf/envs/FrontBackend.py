# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.SyBase import SyBase
from initcentos7.swooleyaf.tools.SyTool import *


class FrontBackend(SyBase):
    def __init__(self):
        super(FrontBackend, self).__init__()
        self._profileEnv = [
            '',
            'export LUAJIT_LIB=/usr/local/luajit/lib',
            'export LUAJIT_INC=/usr/local/luajit/include/luajit-2.0',
            "export CPPFLAGS='-I/usr/local/libjpeg/include -I/usr/local/freetype/include'",
            "export LDFLAGS='-L/usr/local/libjpeg/lib -L/usr/local/freetype/lib'",
            'export LD_LIBRARY_PATH=\$LD_LIBRARY_PATH:/usr/local/lib',
            'export ETCDCTL_API=3',
            'export JAVA_HOME=/usr/java/jdk1.8.0',
            'export CLASSPATH=.:\$JAVA_HOME/jre/lib/rt.jar:\$JAVA_HOME/lib/dt.jar:\$JAVA_HOME/lib/tools.jar',
            'export PATH=\$PATH:/usr/local/git/bin:/usr/local/bin:\$JAVA_HOME/bin:\$JAVA_HOME/jre/bin',
        ]

    def install(self):
        SyTool.initSystemEnv(self._profileEnv)
        SyTool.initSystem()
        SyTool.installGit()
        run('firewall-cmd --zone=public --add-port=21/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=22/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=80/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=2379/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=6379/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=8983/tcp --permanent')
        run('firewall-cmd --reload')
        SyTool.installNginx()
        SyTool.installPhp7()
        SyTool.installJava()
        SyTool.installRedis()
        SyTool.installInotify()
        SyTool.installEtcd()