![](http://git.augmentum.com.cn/jay.huang/eagle/uploads/2a98c84869f884a785472b2c9fdaeac0/image.png)
![](http://git.augmentum.com.cn/jay.huang/eagle/wikis/uploads/57dc8b4ac67cd93a8265c60d77e48c85/demo.png)

## 本地开发
- 1.安装docker，docker-compose
- 2.进入项目根目录
    - ./start.sh init 初始化，拉取容器镜像
    - ./start.sh up 启动容器
    - ./start.sh stop 停止容器
    - ./start.sh build 将当前目录的backend和frontend代码映射到容器中，然后进入容器
        - backend:
           - composer (composer.json), -->vender
           - ~~cd backend~~
           - ~~composer install~~
        - frontend:
           - npm (package.json), -->node_modules
           - cd frontend
           - cnpm install
           - npm run build<env>  -->dist
           - npm run buildLocal  --> .env.local
        - h5:
           - npm (package.json), -->node_modules
           - cd h5
           - cnpm install
           - npm run build<env>
- 3.本地域名:
    - backend: api.dev.com 
    - frontend: frontend.dev.com
    - h5: h5.dev.com

## Jenkins配置
```
cd /home/workspace/flycloud/flycloud/
sudo chmod -R 777 .git/
git pull origin $branch
git checkout $branch
cd /home/workspace/flycloud/flycloud/backend/
sudo composer install
cd /home/workspace/flycloud/flycloud/frontend/
sudo cnpm install
sudo npm run $frontendEnv
```


# 项目说明
```
软件架构
采用前后端分离的软件模式，包含三个应用，PC 端、微信端以及后端 API 应用：

PC 端页面采用的基于 vue 的 ivew-admin 进行开发，其基于 ivew 开发的，很多通用组件可以从里面引入
微信端页面采用的基于 vue 的 vux 进行开发
后端 API 应用基于 Yii2 框架进行开发
系统环境：

操作系统：Ubuntu 16.04
服务器：Nginx 1.15
数据库：MongoDB 4.0.8
编程语言：PHP 7.2
项目说明
目录结构说明
- backend：主要提供 api 供 admin 后台和 h5 页面调用
- frontend：admin 后台，供内部管理人员使用
- h5：微信手机端页面，供终端用户浏览和使用

backend 项目
请求与响应
请求

所有的请求参数设置为 json 格式，本项目默认接受 json 格式

响应

所有的响应的返回内容，请按如下格式

[
    'code' => 200,          // 返回状态码
    'data' => [             // 返回具体内容
        ...
    ],
    'msg' => 'success'      // 返回内容说明
]

开发流程
建立 issue

建立 branch
在从远程拉取代码后，在本地从 develop 分支 checkout 出一份新的分支，分支名 标签-issue id-eagle-模块，如：feat-1-eagle-backend等

发送 pull request
在本地开发完成功后，运行

git add：将代码添加到暂存区
git commit：格式为 feat(eagle): update config file(backend) #1
git push：将代码推送到远程
在远程，确认自己提交的代码没有问题后，就可以发 pull request 了