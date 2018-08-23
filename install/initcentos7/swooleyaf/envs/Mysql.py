# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.SyBase import SyBase
from initcentos7.swooleyaf.tools.SyTool import *


class Mysql(SyBase):
    def __init__(self):
        super(Mysql, self).__init__()
        self._profileEnv = [
            '',
        ]
        self._ports = [
            '21/tcp',
            '22/tcp',
            '80/tcp',
            '3306/tcp',
        ]
        self._steps = {
            1: SyTool.initSystem,
            2: SyTool.installMysql
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
