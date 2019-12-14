# 管理貨櫃的碼頭工人－－ Docker （ 2/3 ）

好啦！今天要來講如何使用 Dockerfile 建置一個客製化 Docker Image 了！

## Dockerfile 是什麼？

簡單來說，它是一個描述 Docker Image 如何建立的過程，描述的方法有點類似像安裝說明文件一樣。下面是一個 Dockerfile 範例：

```dockerfile
FROM php:7.0-apache
MAINTAINER MilesChou <jangconan@gmail.com>

# Install Extensions
RUN set -xe && \
        apt-get update && apt-get install --no-install-recommends --no-install-suggests -y \
            zlib1g-dev \
        && docker-php-ext-install \
            zip \
        && rm -r /var/lib/apt/lists/*

# Install Composer
RUN set -xe && \
        curl -sS https://getcomposer.org/installer | php && \
        mv composer.phar /usr/local/bin/composer
```

在撰寫 Dockerfile 的時候，關鍵字通常會用全大寫，如上面的例子是 `FROM` `MAINTAINER` `RUN` 三個。後面接的就是執行的內容，每個 Dockerfile 都一定會有一個 `FROM` ，它代表 Image 要從哪開始做起。 `MAINTAINER` 是標示 Dockerfile 維護者。 `RUN` 是執行 shell 指令，上面有兩個 `RUN` ，第一個是用 apt 安裝 zip extension ，第二個是安裝 PHP Composer 。

看完這個 Dockerfile 應該可以猜得出來，它描述了如何建置有 Composer 的 Docker Image 。實際建置的指令如下：

```
$ ls
Dockerfile
$ docker build -t mydocker . 

...

$ docker images
docker images
REPOSITORY                         TAG                 IMAGE ID            CREATED             SIZE
mydocker                           latest              63acc9eb7f3e        3 hours ago         406.1 MB
```

這樣就建好了一個有 Composer 的 Docker Image 了，來試 run 看看：

```
$ docker run -it --rm mydocker composer
Do not run Composer as root/super user! See https://getcomposer.org/root for details
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 1.2.4 2016-12-06 22:00:51

...
```

> 補充說明： `--rm` 指的是每次執行完指令，容器就刪除； Image 名稱後面接的是指令，所以也可以接 `composer` 或 `ls` 等等。

## 如何從頭寫一個 Dockerfile

寫 Dockerfile 並不難，做起來就很像是給一台空的機器，再安裝需要的軟體和設定，最後就會成為一個符合需求的 Docker Image 。

比方說我們想要一個可以執行 [Gulp][] 的 Image ，首先應該會想到的是，我們需要 Node 。因此第一步要上 [Docker Hub][] 找有沒有 Node ， Node 這麼熱門當然[官方][Docker Hub Node]有提供 Image 。因此我們決定好版本後，就可以開始寫第一行了：

```dockerfile
FROM node:6.9
```

寫好一行之後，這時要開始發揮 [CI 精神][Day 5]了！要先驗證嘛！因此先 build 再執行看看，有沒有 node 我們可以執行 `node -v` 試試：

```
$ docker build -t mygulp .
$ docker run -it --rm mygulp node -v
v6.9.2
```

有看到版本了！再來 Gulp 可以用 `package.json` 安裝，也可以用 `-g` 全域安裝，那我們要的是一個可以全域執行 Gulp 的 Image ，所以使用全域安裝好了：

```dockerfile
FROM node:6.9

RUN npm install -g gulp
```

這次應該就可以用 `gulp -v` 了！

```
$ docker run -it --rm mygulp gulp -v
[12:11:39] CLI version 3.9.1
```

接著我們就可以用這樣的方法來執行 Gulp 的任務了：

```
$ docker run -it --rm -v $PWD:/usr/src/app -w /usr/src/app mygulp gulp
```

## 今日回顧

今天學習了怎麼使用 Docker 建置屬於自己的環境，這樣就可以把家裡許多服務都容器化，但接著該如何管理它們呢？

這就是明天主題 [Docker Compose][] 的神奇妙用了！

下一篇：[管理貨櫃的碼頭工人－－ Docker （ 3/3 ）][]

## 相關連結

* [Docker Build](https://docs.google.com/presentation/d/1OrcP6FKFpLwmzPhmFH8-O9SHJEyu-_K69tPw2gqqsHs/pub?start=false) | Miles
* [Dockerfile reference](https://docs.docker.com/engine/reference/builder/) | Dockerfile 官方文件

[Gulp]: http://gulpjs.com/
[Docker Hub]: https://hub.docker.com/
[Docker Hub Node]: https://hub.docker.com/_/node/
[Docker Compose]: https://docs.docker.com/compose/

[Day 5]: /docs/day05.md
[管理貨櫃的碼頭工人－－ Docker （ 3/3 ）]: /docs/day17.md
