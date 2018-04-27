# -*- coding:utf-8 -*-
from fabric.api import env

env.hosts = [
]

# 建议账号用root,避免权限问题
env.passwords = {
    'root@192.168.1.1:22': 'password1',
    'root@192.168.1.2:22': 'password2',
    'root@192.168.1.3:22': 'password3',
    'root@192.168.1.4:22': 'password4',
    'root@192.168.1.5:22': 'password5'
}

env.roledefs = {
    'mysql': ['root@192.168.1.1:22',],
    'mongodb': ['root@192.168.1.2:22',],
    'front': ['root@192.168.1.3:22',],
    'backend': ['root@192.168.1.4:22',],
    'mixfb': ['root@192.168.1.5:22',]
}

syDicts = {
    'path.package.local': '/home/jw/download',
    'path.package.remote': '/home/download',
    'git.user.name': 'jiangwei',
    'git.user.email': 'jiangwei07061625@163.com'
}