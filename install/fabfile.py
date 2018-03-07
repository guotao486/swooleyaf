# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.syinstall import *

def installEnv(tag):
    tagMap = {
        'syMysql': installSyMysql,
        'syMongodb': installSyMongodb,
        'syFront': installSyFront,
        'syBackend': installSyBackend,
        'syFrontBackend': installSyFrontAndBackend
    }

    func = tagMap.get(tag, '')
    if hasattr(func, '__call__'):
        func()
    else:
        print('环境类型不存在')