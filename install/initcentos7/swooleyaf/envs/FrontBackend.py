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
        self._ports = [
            '21/tcp',
            '22/tcp',
            '80/tcp',
            '2379/tcp',
            '6379/tcp',
            '8983/tcp',
        ]
        self._steps = {
            1: SyTool.initSystemEnv,
            2: SyTool.initSystem,
            3: SyTool.openPorts,
            4: SyTool.installGit,
            5: SyTool.installNginx,
            6: SyTool.installPhp7,
            7: SyTool.installJava,
            8: SyTool.installRedis,
            9: SyTool.installInotify,
            10: SyTool.installEtcd
        }

    def install(self, params):
        step = params['step']
        func = self._steps.get(step, '')
        while hasattr(func, '__call__'):
            if step == 1:
                func(self._profileEnv)
            elif step == 3:
                func(self._ports)
            else:
                func()

            step += 1
            func = self._steps.get(step, '')
