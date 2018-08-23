# -*- coding:utf-8 -*-
import sys
from initcentos7.swooleyaf.syinstall import *


def envHelp():
    print(' 命令格式:')
    print(' /usr/local/python3/bin/fab -f fabfile.py installEnv:envType="syMysql",envStep=1,envInit=1')
    print(' envType 环境类型,支持的类型如下:')
    print('   syMysql: swooleyaf的mysql环境')
    print('   syMongodb: swooleyaf的mongodb环境')
    print('   syFront: swooleyaf的前端环境')
    print('   syBackend: swooleyaf的后端环境')
    print('   syFrontBackend: swooleyaf的前后端混合环境')
    print(' envStep 执行步骤,大于0的整数')
    print(' envInit 初始化标识,0:不初始化环境 1:初始化环境')

def installEnv(envType, envStep, envInit, **params):
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
        envHelp()
        sys.exit()

    step = int(envStep)
    if step < 1:
        print('执行步骤必须大于0')
        sys.exit()

    initTag = int(envInit)
    initTags = [0, 1]
    if initTag not in initTags:
        print('初始化标识不合法')
        sys.exit()

    envParams = {}
    envParams['step'] = step
    envParams['init'] = initTag
    func(envParams)
