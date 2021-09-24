# 管理貨櫃的碼頭工人－－ Docker （ 3/3 ）

Docker 讓建置服務變得非常簡單，但相對馬上會面臨另一個困難－－如何管理？

今天將會使用 [Docker Compose][] 工具來幫忙管理容器。

## 安裝

安裝可以參考 Docker Compose 的 [GitHub Release][Docker Compose Release] 。目前看到的是 1.9.0 版，安裝指令如下：

```
$ curl -L https://github.com/docker/compose/releases/download/1.9.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
$ chmod +x /usr/local/bin/docker-compose
$ docker-compose version
docker-compose version 1.9.0, build 2585387
docker-py version: 1.10.6
CPython version: 2.7.12
OpenSSL version: OpenSSL 1.0.2j  26 Sep 2016
```

> Docker Compose 的設定檔有分 version 1 與 version 2 ，以下範例使用 version 1 。

# Hello Compose

[Day 15][] 最後有一個產生容器的指令如下：

```
$ docker run -d -p 8080:80 -v $PWD:/var/www/html php:7.0-apache
```

把它轉換成 Docker Compose 的方式，首先先建立 `docker-compose.yml` 檔案：

```yaml
web:
  image: php:7.0-apache
  ports:
    - 8080:80
  volumes:
    - .:/var/www/html
```

接著下啟動指令：

```
$ docker-compose up -d
Creating bookintroofci_web_1
```

> `-d` 代表會在背景執行

現在 PHP Apache 容器已經開好了，並且資料夾也同步， port 也開出去，可以開始開發了。

## Docker Link

PHP 開發大部分都需要連 MySQL ，那該怎麼設定？ Docker 原生的指令需要先建 MySQL ，建 PHP 的時候再設定 Link ：

```
$ docker run -d --name my-mysql -e MYSQL_ROOT_PASSWORD=password mysql:5.6
$ docker run -d --name web -p 8080:80 -v $PWD:/var/www/html --link my-mysql php:7.0-apache
```

接著就可以在 Apache 裡面使用 `my-mysql` 這個 hostname 連到 MySQL ：

```
$ docker exec -it web ping my-mysql
PING my-mysql (172.17.0.2): 56 data bytes
64 bytes from 172.17.0.2: icmp_seq=0 ttl=64 time=0.231 ms
64 bytes from 172.17.0.2: icmp_seq=1 ttl=64 time=0.156 ms
64 bytes from 172.17.0.2: icmp_seq=2 ttl=64 time=0.192 ms
```

但這麼多指令實在是很難懂，但換用 Docker Compose 是長這樣：

```yaml
web:
  image: php:7.0-apache
  ports:
    - 8080:80
  volumes:
    - .:/var/www/html
  links:
    - mysql
mysql:
  image: mysql:5.6
  environment:
    MYSQL_ROOT_PASSWORD: password
```

啟動一樣使用 `docker-compose up -d` 即可，是不是好懂許多了呀！

## 其他指令

Docker Compose 其他常用的指令如下：

### `docker-compose logs`

在 `up` 之後，可以用這個指令去查看容器的 Log ，這對 debug 或 monitor 是非常方便的。

### `docker-compose build`

每個服務（指的是上例的 `web` 與 `mysql` ）下面一定要有定義 `image` 或是 `build` ， `image` 表示要拉別人上傳好的， `build` 表示要自己來。這個指令可以直接執行全部有 build 元素的服務。

### `docker-compose pull`

同上，但是行為是 pull 所有有 image 元素的服務。

### `docker-compose run`

跟 `docker run` 類似，會產生全新的容器，而這個容器的設定會完全依照 `docker-compose.yml` 檔案裡定義的執行。

### `docker-compose exec`

跟 `docker exec` 類似，會在正在執行的容器上，執行新的指令。

## 今日回顧

Docker Compose 主要是方便處理容器的管理，但本質上依然是執行 Docker ，因此許多設定的概念跟 Docker 是一致的，如果使用上有問題，不妨參考 Docker 原生指令的說明，或許找得到解答。

下一篇：[流浪漢與碼頭工人的應用][]

## 相關連結

[Docker Compose]: https://docs.docker.com/compose/
[Docker Compose Release]: https://github.com/docker/compose/releases

[Day 15]: /docs/day15.md
[流浪漢與碼頭工人的應用]: /docs/day18.md
