# -*- coding:utf-8 -*-
from syenv import *

# 配置之前先用命令rpm -qa | grep mariadb找出已经安装的数据库,然后用命令rpm -e --nodeps xxx删除已经安装的软件(xxx为前一步命令找到的软件名)
@roles('mysql')
def installMysql():
    installObj = SyEnvMysql()
    installObj.install()
    # 后续设置mysql登录帐号和密码以及授权需要登录服务器设置

@roles('mongodb')
def installMongodb():
    installObj = SyEnvMongodb()
    installObj.install()

@roles('front')
def installFront():
    installObj = SyEnvFront()
    installObj.install()

@roles('backend')
def installBackend():
    installObj = SyEnvBackend()
    installObj.install()

@roles('mixfb')
def installFrontAndBackend():
    installObj = SyEnvFrontAndBackend()
    installObj.install()