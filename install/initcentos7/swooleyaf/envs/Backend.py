# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.SyBase import SyBase
from initcentos7.swooleyaf.tools.SyTool import *


class Backend(SyBase):
    def __init__(self):
        super(Backend, self).__init__()
        self._profileEnv = [
            '',
            'export LUAJIT_LIB=/usr/local/luajit/lib',
            'export LUAJIT_INC=/usr/local/luajit/include/luajit-2.0',
            "export CPPFLAGS='-I/usr/local/libjpeg/include -I/usr/local/freetype/include'",
            "export LDFLAGS='-L/usr/local/libjpeg/lib -L/usr/local/freetype/lib'",
            'export LD_LIBRARY_PATH=\$LD_LIBRARY_PATH:/usr/local/lib',
            'export ETCDCTL_API=3',
            'export PATH=\$PATH:/usr/local/git/bin:/usr/local/bin',
        ]

    def install(self):
        SyTool.initSystemEnv(self._profileEnv)
        SyTool.initSystem()
        run('firewall-cmd --zone=public --add-port=21/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=22/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=80/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=2379/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=6379/tcp --permanent')
        run('firewall-cmd --reload')
        SyTool.installNginx()
        SyTool.installPhp7()
        SyTool.installRedis()
        SyTool.installInotify()
        SyTool.installEtcd()