# -*- coding:utf-8 -*-
from abc import ABCMeta,abstractmethod


class SyBase(object):
    __metaclass__ = ABCMeta

    _profileEnv = []

    def __init__(self):
        self._profileEnv = []

    @abstractmethod
    def install(self):
        pass
