# 命令
## 创建镜像
    docker build --build-arg MODULE_PORT=7100 -f aaa -t bbb ccc

- MODULE_PORT: 服务暴露的端口号,默认为7100
- aaa: dockerfile文件全名,包含路径和文件名
- bbb: 镜像别名
- ccc: 构建镜像的上下文根目录

# 说明
- base目录下为公共镜像文件
- 其他目录每一个都是一个单独的环境镜像文件