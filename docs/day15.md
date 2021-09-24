# 管理貨櫃的碼頭工人－－ Docker （ 1/3 ）

雖然[昨天的 Vagrant ][Day 15]使用起來真的非常方便，但因為本質是虛擬機，虛擬所要資源並不少，同時執行的數量就會有所限制。

因此，我們有另一個選擇－－ [Docker][] ，以使用方式來說很像虛擬化技術，但實際上是容器化技術。因不需要模擬整個 OS 層就能執行應用，這點為它加了許多優點，如較輕量、執行較快等。但相對的它也有一些缺點需要解決，其中問題最麻煩的在於，它只能在 Linux 環境下執行，因此一般都是另外安裝 VM 來做橋接執行。後來有出 Windows / OSX 的原生 Docker ，可是只支援 Windows 10 以上， Windows 7 / 8 的開發人員就只能乾瞪眼。 

總之，先暫時不討論環境無法執行的問題，來看看如何開發人員如何使用 Docker 來協助開發。 

## 安裝程式執行環境

安裝 Docker 可以參考[官網說明](https://www.docker.com/products/overview)。

接著身為 PHP 開發者，需要一個可以執行 PHP 的環境該怎麼辦？很簡單，只要下一行指令：

```
$ docker pull php
```

它的意思是下載 PHP 的映像檔回來。接著就能執行 PHP 互動交談介面：

```
$ docker run php
Interactive shell

php > $i = 10;
php > echo $i;
10
php > 
```

*Docker Run* 所產生出來的環境稱之為**容器**，這裡是一個 PHP 的容器正在執行，輸入 `exit` 即可離開，當離開後容器的執行階段就結束了。

有了 PHP 還想要 Node 怎麼辦？很簡單， PHP 換 Node 就行了：

```
$ docker run node
> var i = 10;
undefined
> console.log(i);
10
undefined
>  
```

我還想要 DB 還想要 Redis 還想要很多 server 呢？沒問題！只要是常見的， [Docker Hub][] 上一定都能找得到；只要找得到，就能照上面的方法下載回來執行。

## 如何自定 Docker 環境

Docker Hub 上有非常多選擇，可是通常需求都會比較特別。沒問題！我們一起來客製化吧！要客製化 Docker 環境有兩個主要的方法，一個是執行的時候安裝，另一個是使用 `Dockerfile` 。兩個方法都必須從即有的 Image 開始做起，一般會使用 `Dockerfile` ，但學會執行時期的安裝會好除錯，今天先講如何執行時期安裝。

比方說， PHP 開發需要 [Composer][] 。查了一下， Composer 的懶人安裝指令長這樣：

```
$ curl -sS https://getcomposer.org/installer | php
```

再來我們必須要執行一個 PHP 容器，但不一樣的是我們加一點參數：

```
$ docker run -it php bash
```

前面的 `-it` 白話地說，要下指令會加這個參數，詳情請看[說明文件][Docker Run]；後面的 `bash` 表示要啟動容器的時候，執行 bash 指令，意即開啟 bash shell 。這時就會看到另一個提示字元，別懷疑，現在已經進入 container 下了，可以下安裝指令看看。

```
root@9f39905a0466:/# curl -sS https://getcomposer.org/installer | php
All settings correct for using Composer
Downloading 1.2.4...

Composer successfully installed to: //composer.phar
Use it: php composer.phar
root@9f39905a0466:/# php composer.phar
Do not run Composer as root/super user! See https://getcomposer.org/root for details
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 1.2.4 2016-12-06 22:00:51
```

恭喜你成功啦！不過別開心的太早，離開再重新執行一次，會發現剛剛的 `composer.phar` 不見了。

```
$ docker run -it php bash
root@ee6540621f9c:/# php composer.phar
Could not open input file: composer.phar
root@ee6540621f9c:/#
```

這是 Docker 建立容器的特性，每次建立都會是全新的。來個美好的假設：假設我們的環境被打造成 Docker Image ，那是不是環境被開發人員或測試人員搞爛了，我們只要離開重新建立容器，又會是一條活龍了？是的沒錯！ Docker 真的能做到！但這確實也是個美好的假設，因為通常既有系統並不是那麼好轉換成 Docker 的。

話說回來，每次都要重新安裝，我們要的應該不是這樣鳥的客製化 Docker 呀！所以一般都是用 `Dockerfile` 來建置新的 Image 執行的。

## 其他參數

好吧，那我們來跑個網頁伺服器吧，我們來使用 `php:7.0-apache` 好了。開伺服器通常會希望在背景執行，我們可以加 `-d` 參數

```
$ docker run -d php:7.0-apache
a6621e1684b8730be9850d5b6e32ee1b6dc01f1ec9f5852ba91d92ef9705321d
```

回傳的亂碼是容器的身份證，未來想指定這個容器的話，都可以用這個身份證指向它。這時開瀏覽器打開 localhost 一看，怎麼是假的！這到底有沒有執行成功？想看有沒有執行成功的話，可以下 `docker ps` ：

```
$ docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS               NAMES
a6621e1684b8        php:7.0-apache      "apache2-foreground"   2 hours ago         Up 3 seconds        80/tcp              tiny_pasteur
```

咦有啊！一怒之下執行很多次：

```
$ docker run -d php:7.0-apache
$ docker run -d php:7.0-apache
$ docker run -d php:7.0-apache
$ docker ps 
  CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS               NAMES
  210de6c402ad        php:7.0-apache      "apache2-foreground"   2 hours ago         Up 1 seconds        80/tcp              nostalgic_panini
  82df9af1c2ad        php:7.0-apache      "apache2-foreground"   2 hours ago         Up 4 seconds        80/tcp              goofy_jones
  b0485230a28a        php:7.0-apache      "apache2-foreground"   2 hours ago         Up 5 seconds        80/tcp              happy_ride
  a6621e1684b8        php:7.0-apache      "apache2-foreground"   2 hours ago         Up About a minute   80/tcp              tiny_pasteur
```

發現容器都好好的在執行，這時應該發現原因了：它們都是 80 port 可是卻沒有衝突，代表它們是在一個互相隔離的環境下執行的。

那要如何看網頁呢？ Docker 有參數可以把 port 轉到本機，類似 Vagrant 的 `forwarded_port` 使用方法如下：

```
$ docker run -d -p 8080:80 php:7.0-apache
c6741b3f61a62a368c12f7eee84edd776ad74f52b4606b085dd061f79ae95832
```

這樣可以把本機的 8080 port 轉到容器的 80 port 。好啦，可以開網頁了！

咦？怎麼是 Forbidden ！沒道理啊！進去看看 Document Root 有沒有問題好了

```
$ docker exec -it c6741b3f61a62a368c12f7eee84edd776ad74f52b4606b085dd061f79ae95832 bash
```

這個指令可以在一個執行中的容器，再執行一個指令。因為我們想進去看看狀況，所以執行的指令就是 bash 了。

```
root@c6741b3f61a6:/var/www/html# ls -al
total 8
drwxr-xr-x 2 www-data www-data 4096 Oct 21 23:06 .
drwxr-xr-x 4 root     root     4096 Oct 21 23:06 ..
root@c6741b3f61a6:/var/www/html#
```

結果什麼都沒有！算了沒關係，我們建一個暫時的 `index.php` 試看看：

> 記得輸入完，最後要按 Ctrl + D

```
root@c6741b3f61a6:/var/www/html# cat > index.php
<?php
echo 'Hello Docker';

root@c6741b3f61a6:/var/www/html#
```

重整頁面後，就會看到 `Hello Docker` 了！

等等，不對！每次程式碼都要這樣輸入，會瘋掉吧！有沒有更好的方法？有的， Docker 有提供跟 Vagrant 的 `synced_folder` 很像的功能，可以做到檔案同步。不過要重新執行才行，先把原本的刪掉再重開：

```
$ docker rm --force c6741b3f61a62a368c12f7eee84edd776ad74f52b4606b085dd061f79ae95832
$ docker run -d -p 8080:80 -v $PWD:/var/www/html php:7.0-apache
```

`-v` 參數可以做檔案同步，上面的意思是把目前目錄（ `$PWD` 代表目前目錄）掛到容器的 `/var/www/html` 。再來在目前目錄可以用平常的編輯器新增檔案，檔案也都會同步到容器裡，這樣就會是一個簡單的 Docker 開發者環境了！ 

## 今日回顧

* Docker 每次啟動都會是全新的開始，非常適合需要常砍掉重練的場景，如測試。
* Docker 基本指令可以做出簡單的開發環境

明天將會介紹如何建置一個客製的環境。

下一篇：[管理貨櫃的碼頭工人－－ Docker （ 2/3 ）][]

## 相關連結

* [Docker —— 從入門到實踐](https://www.gitbook.com/book/philipzheng/docker_practice) | philipzheng
* [Docker 淺入淺出](https://docs.google.com/presentation/d/1V-UGtg2wp8wQR-ZiHsCKRQVJwTNCSgNn52hof5T3MIU/pub?start=false) | Miles

[Docker]: https://www.docker.com/
[Docker Hub]: https://hub.docker.com/
[Docker Run]: https://docs.docker.com/engine/reference/run/
[Composer]: https://getcomposer.org/

[Day 15]: /docs/day15.md
[管理貨櫃的碼頭工人－－ Docker （ 2/3 ）]: /docs/day16.md
