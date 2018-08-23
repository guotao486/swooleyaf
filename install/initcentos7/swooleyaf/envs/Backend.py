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
            "export CPPFLAGS='-I/usr/local/libjpeg/include -I/usr/local/freetype/include -I/usr/local/include -I/usr/local/zlib/include -I/usr/local/pcre/include'",
            "export LDFLAGS='-L/usr/local/libjpeg/lib -L/usr/local/freetype/lib -L/usr/local/lib -L/usr/local/lib64 -L/usr/local/zlib/lib -L/usr/local/pcre/lib'",
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
            1: SyTool.initSystem,
            2: SyTool.installGit,
            3: SyTool.installPcre,
            4: SyTool.installZlib,
            5: SyTool.installOpenssl,
            6: SyTool.installNghttp2,
            7: SyTool.installJpeg,
            8: SyTool.installImageMagick,
            9: SyTool.installFreetype,
            10: SyTool.installNginx,
            11: SyTool.installPhp7,
            12: SyTool.installRedis,
            13: SyTool.installInotify,
            14: SyTool.installEtcd
        }

    def install(self, params):
        step = params['step']
        func = self._steps.get(step, '')
        while hasattr(func, '__call__'):
            if step > 1:
                func()
            else:
                func(self._profileEnv, self._ports, params['init'])

            step += 1
            func = self._steps.get(step, '')
