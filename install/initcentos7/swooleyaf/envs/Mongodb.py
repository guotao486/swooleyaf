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

    def install(self):
        SyTool.initSystemEnv(self._profileEnv)
        SyTool.initSystem()
        run('firewall-cmd --zone=public --add-port=21/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=22/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=80/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=27017/tcp --permanent')
        run('firewall-cmd --reload')
        run('echo "never" > /sys/kernel/mm/transparent_hugepage/enabled && echo "never" > /sys/kernel/mm/transparent_hugepage/defrag')

        mongoLocal = ''.join([syDicts['path.package.local'], '/resources/mongodb/mongodb-linux-x86_64-rhel70-3.2.17.tgz'])
        mongoRemote = ''.join([syDicts['path.package.remote'], '/mongodb-linux-x86_64-rhel70-3.2.17.tgz'])
        put(mongoLocal, mongoRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf mongodb-linux-x86_64-rhel70-3.2.17.tgz')
            run('mv mongodb-linux-x86_64-rhel70-3.2.17/ /usr/local/mongodb')
            run('mkdir /usr/local/mongodb/data && mkdir /usr/local/mongodb/data/db && mkdir /usr/local/mongodb/data/logs')
            run('rm -rf mongodb-linux-x86_64-rhel70-3.2.17.tgz')

        mongoConfigLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mongodb/mongodb.conf'])
        mongoConfigRemote = '/usr/local/mongodb/mongodb.conf'
        put(mongoConfigLocal, mongoConfigRemote)

        # crontab任务对应的txt文件结束必须按回车键另起一行
        mongoCronLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mongodb/crontab.txt'])
        mongoCronRemote = ''.join([syDicts['path.package.remote'], '/crontab.txt'])
        put(mongoCronLocal, mongoCronRemote)
        run('crontab %s' % mongoCronRemote)
        run('rm -rf %s' % mongoCronRemote)