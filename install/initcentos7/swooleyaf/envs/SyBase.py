# -*- coding:utf-8 -*-
from abc import ABCMeta,abstractmethod


class SyBase(object):
    __metaclass__ = ABCMeta

    _profileEnv = []
    _ports = []
    _steps = {}

    def __init__(self):
        self._profileEnv = []
        self._ports = []
        self._steps = {}

    @abstractmethod
    def install(self, params):
        pass
