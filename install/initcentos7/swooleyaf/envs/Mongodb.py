# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.SyBase import SyBase
from initcentos7.swooleyaf.tools.SyTool import *


class Mongodb(SyBase):
    def __init__(self):
        super(Mongodb, self).__init__()
        self._profileEnv = [
            '',
            'ulimit -f unlimited',
            'ulimit -t unlimited',
            'ulimit -v unlimited',
            'ulimit -n 64000',
            'ulimit -m unlimited',
            'ulimit -u 64000',
        ]
        self._ports = [
            '21/tcp',
            '22/tcp',
            '80/tcp',
            '27017/tcp',
        ]
        self._steps = {
            1: SyTool.initSystem,
            2: SyTool.installMongodb
        }

    def install(self, params):
        step = params['step']
        func = self._steps.get(step, '')
        while hasattr(func, '__call__'):
            if step > 1:
                func()
            else:
                func(self._profileEnv, self._ports)

            step += 1
            func = self._steps.get(step, '')
