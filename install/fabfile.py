# -*- coding:utf-8 -*-
import sys
from initcentos7.swooleyaf.syinstall import *


def envHelp():
    print(' 命令格式:')
    print(' /usr/local/python3/bin/fab -f fabfile.py installEnv:envType="syMysql",envStep=1')
    print(' envType 环境类型,支持的类型如下:')
    print('   syMysql: swooleyaf的mysql环境')
    print('   syMongodb: swooleyaf的mongodb环境')
    print('   syFront: swooleyaf的前端环境')
    print('   syBackend: swooleyaf的后端环境')
    print('   syFrontBackend: swooleyaf的前后端混合环境')
    print(' envStep 执行步骤,大于0的整数')

def installEnv(envType, envStep, **params):
    envMap = {
        'syMysql': installSyMysql,
        'syMongodb': installSyMongodb,
        'syFront': installSyFront,
        'syBackend': installSyBackend,
        'syFrontBackend': installSyFrontAndBackend
    }
    func = envMap.get(envType, '')
    if not hasattr(func, '__call__'):
        print('环境类型不存在')
        sys.exit()

    step = int(envStep)
    if step < 1:
        print('执行步骤必须大于0')
        sys.exit()

    envParams = {}
    envParams['step'] = step
    func(envParams)
