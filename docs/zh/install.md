
``````
   __       _                          __                                             _    
  / _|     | |                        / _|                                           | |   
 | |_ _   _| |_ _   _ _ __ ___ ______| |_ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 |  _| | | | __| | | | '__/ _ \______|  _| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 | | | |_| | |_| |_| | | |  __/      | | | | | (_| | | | | | |  __/\ V  V / (_) | |  |   < 
 |_|  \__,_|\__|\__,_|_|  \___|      |_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
                                                                                                    
``````
安装环境
--

 - PHP >= 7.0.0
 - Laravel >= 5.5.0
 - Fileinfo PHP Extension
 
下载地址
-------
>github下载地址:https://github.com/v-sing/future-framework.git

安装
----
>Composer安装依赖库
````
composer require future/framework
````
>然后运行下面的命令来发布资源：
````
php artisan vendor:publish --provider="Future\Admin\AdminServiceProvider"
````
>在该命令会生成配置文件config/admin.php，可以在里面修改安装的地址、数据库连接、以及表名，建议都是用默认配置不修改。

>然后运行下面的命令完成安装：
````
php artisan admin:install
````
>启动服务后，在浏览器打开 http://localhost/admin/ ,使用用户名 admin 和密码 admin登陆.