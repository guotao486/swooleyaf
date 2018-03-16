# -*- coding:utf-8 -*-
from initcentos7.swooleyaf.envs.SyBase import SyBase
from initcentos7.swooleyaf.tools.SyTool import *


class Mysql(SyBase):
    def __init__(self):
        super(Mysql, self).__init__()
        self._profileEnv = [
            '',
        ]

    def install(self):
        SyTool.initSystemEnv(self._profileEnv)
        SyTool.initSystem()
        run('firewall-cmd --zone=public --add-port=21/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=22/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=80/tcp --permanent')
        run('firewall-cmd --zone=public --add-port=3306/tcp --permanent')
        run('firewall-cmd --reload')
        run('rm -rf /etc/my.cnf')
        run('yum -y install make cmake libaio libaio-devel bison-devel ncurses-devel perl-Data-Dumpe')
        run('groupadd mysql && useradd -g mysql mysql -s /sbin/nologin')
        run('mkdir /usr/local/mysql/data && mkdir /home/logs/mysql && touch /home/logs/mysql/error.log && chown -R mysql /home/logs/mysql && chgrp -R mysql /home/logs/mysql')

        mysqlLocal = ''.join([syDicts['path.package.local'], '/resources/mysql/mysql-5.6.37.tar.gz'])
        mysqlRemote = ''.join([syDicts['path.package.remote'], '/mysql-5.6.37.tar.gz'])
        put(mysqlLocal, mysqlRemote)
        with cd(syDicts['path.package.remote']):
            run('tar -zxvf mysql-5.6.37.tar.gz')
            run('cd mysql-5.6.37/ && cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql -DMYSQL_DATADIR=/usr/local/mysql/data -DSYSCONFDIR=/etc/my.cnf -DWITH_MYISAM_STORAGE_ENGINE=1 -DWITH_INNOBASE_STORAGE_ENGINE=1 -DMYSQL_UNIX_ADDR=/usr/local/mysql/mysql.sock -DMYSQL_TCP_PORT=3306 -DENABLED_LOCAL_INFILE=1 -DWITH_PARTITION_STORAGE_ENGINE=1 -DEXTRA_CHARSETS=all && make && make install')
            run('chown -R mysql:mysql /usr/local/mysql')

        mysqlConfigLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mysql/my.cnf'])
        mysqlConfigRemote = '/etc/my.cnf'
        put(mysqlConfigLocal, mysqlConfigRemote)
        with cd('/usr/local/mysql'):
            run('./scripts/mysql_install_db --user=mysql --basedir=/usr/local/mysql --datadir=/usr/local/mysql/data')

        mysqlServiceLocal = ''.join([syDicts['path.package.local'], '/configs/swooleyaf/mysql/mysql.service'])
        mysqlServiceRemote = '/lib/systemd/system/mysql.service'
        put(mysqlServiceLocal, mysqlServiceRemote)
        run('chmod 754 %s' % mysqlServiceRemote)
        run('systemctl enable mysql')

        with cd(syDicts['path.package.remote']):
            run('rm -rf mysql-5.6.37/ && rm -rf mysql-5.6.37.tar.gz')