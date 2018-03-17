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
        self._ports = [
            '21/tcp',
            '22/tcp',
            '80/tcp',
            '2379/tcp',
            '6379/tcp',
        ]
        self._steps = {
            1: SyTool.initSystemEnv,
            2: SyTool.initSystem,
            3: SyTool.openPorts,
            4: SyTool.installGit,
            5: SyTool.installNginx,
            6: SyTool.installPhp7,
            7: SyTool.installRedis,
            8: SyTool.installInotify,
            9: SyTool.installEtcd
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
