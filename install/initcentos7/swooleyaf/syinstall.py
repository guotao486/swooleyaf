# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.Mysql import *
from initcentos7.swooleyaf.envs.Mongodb import *
from initcentos7.swooleyaf.envs.Front import *
from initcentos7.swooleyaf.envs.Backend import *
from initcentos7.swooleyaf.envs.FrontBackend import *

# 配置之前先用命令rpm -qa | grep mariadb找出已经安装的数据库,然后用命令rpm -e --nodeps xxx删除已经安装的软件(xxx为前一步命令找到的软件名)
@roles('mysql')
def installSyMysql():
    obj = Mysql()
    obj.install()
    # 后续设置mysql登录帐号和密码以及授权需要登录服务器设置

@roles('mongodb')
def installSyMongodb():
    obj = Mongodb()
    obj.install()

@roles('front')
def installSyFront():
    obj = Front()
    obj.install()

@roles('backend')
def installSyBackend():
    obj = Backend()
    obj.install()

@roles('mixfb')
def installSyFrontAndBackend():
    obj = FrontBackend()
    obj.install()