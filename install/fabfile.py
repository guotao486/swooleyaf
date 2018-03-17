# -*- coding:utf-8 -*-
import sys
import getopt
from initcentos7.swooleyaf.syinstall import *


def envHelp():
    print(' 命令格式:')
    print('   /usr/local/python3/bin/fab -f fabfile.py --sytag=syMysql --systep=1')
    print(' --sytag 环境类型,支持的类型如下:')
    print('   syMysql: swooleyaf的mysql环境')
    print('   syMongodb: swooleyaf的mongodb环境')
    print('   syFront: swooleyaf的前端环境')
    print('   syBackend: swooleyaf的后端环境')
    print('   syFrontBackend: swooleyaf的前后端混合环境')
    print(' --systep 开始执行步骤,大于0的整数')

if __name__ == '__main__':
    tagMap = {
        'syMysql': installSyMysql,
        'syMongodb': installSyMongodb,
        'syFront': installSyFront,
        'syBackend': installSyBackend,
        'syFrontBackend': installSyFrontAndBackend
    }

    func = ''
    params = {}
    options, args = getopt.getopt(sys.argv[1:], "", ['syhelp', 'sytag=', 'systep='])
    for key,val in options:
        if key == '--syhelp':
            envHelp()
            sys.exit()
        elif key == '--sytag':
            func = tagMap.get(val, '')
            if not hasattr(func, '__call__'):
                print('环境类型不存在')
                sys.exit()
        elif key == '--systep':
            if not isinstance(val, int):
                print('执行步骤必须为整数')
                sys.exit()
            elif val <= 0:
                print('执行步骤必须大于0')
                sys.exit()
            params['step'] = val
    func(params)