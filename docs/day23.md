# 自己來的好選擇 －－ Dapper

[Dapper][] 是 [Rancher Labs, Inc.][Rancher Labs] 的一個開源工具，它的簡介也非常的簡單好了解：

> Docker build wrapper

同時也是這個工具的核心概念：使用 [Docker][] 來包裝 build 的過程。

昨天雖然有提到非常多個 SaaS 服務，但其實 Dapper 並不是 Service ，它是本機的一個指令，執行 build 的時候還是得靠手動觸發。但因為它在執行 build 過程中，會在 Docker 建好的容器中執行，如同全新建好的環境一般，這跟大多數的 CI server 做法很像，所以才會把這個歸類在 CI 工具。

## 源起

為什麼我會使用 Dapper ？其實是無意間挖 Rancher 的原始碼發現的。當時遇到一個有點難解的問題： Docker 可以簡單地建立上線環境並執行，但開發環境通常會比上線環境裝更多東西，可是我又想用 Docker 去建立開發環境，不就要兩個 `Dockerfile` 嗎？如果有兩個，那我又該如何配置檔案結構？看到 Dapper 的功能，發現它能解決我的問題，所以才會研究它。後來發現它非常好用，因此現在才會選擇要講這套工具。

那什麼時候才會需要 Dapper ？依我的經驗，通常需要 compilation 階段且會產生 Artifacts 的專案，會最有感覺。我的專案是 Web ，需要做 js compile / sass compile ，使用 Dapper 後，團隊間的 Node 環境問題幾乎就只剩 Docker 而已了，而 Dapper 本身使用 Go 撰寫，不會有其他依賴問題。除了團隊資訊互通簡單外， Dapper 要修改環境版本也非常容易，因為是 Dockerfile ，開發人員可以輕鬆更換 Node 版本或套件的版本等。

相對的，什麼時候不適合用 Dapper ？目前 Dapper 執行 build 是使用單一容器，因此如果喜歡 Microservice 的朋友，可能會不喜歡 Dapper 必須要這樣做，因為所有 build 所要的服務，都必須建置在這個容器裡，這一點都不微服務。相對的，如果喜歡使用單一 Vagrant 服務全包打天下的朋友，或許會很喜歡。

好的，不管怎麼說， Dapper 能使用容器處理 build ，並產生 Artifacts ，只要有這個需求都能玩看看。    

## 安裝

Dapper 只依賴 Docker ，因此先把 Docker 裝好，接著在執行它提供的安裝指令：

```
$ curl -sL https://releases.rancher.com/dapper/latest/dapper-`uname -s`-`uname -m` > /usr/local/bin/dapper
$ chmod +x /usr/local/bin/dapper
$ dapper --version
dapper version v0.3.3
```

## 實作

以下舉我實際使用 Dapper 實作 [Gulp][] build 的情境。首先可以看一下 Dapper help ：

```
$ dapper --help
NAME:
   dapper - Docker build wrapper

	Dockerfile variables

	DAPPER_SOURCE          The destination directory in the container to bind/copy the source
	DAPPER_CP              The location in the host to find the source
	DAPPER_OUTPUT          The files you want copied to the host in CP mode
	DAPPER_DOCKER_SOCKET   Whether the Docker socket should be bound in
	DAPPER_RUN_ARGS        Args to add to the docker run command when building
	DAPPER_ENV             Env vars that should be copied into the build

USAGE:
   dapper [global options] command [command options] [arguments...]

VERSION:
   v0.3.3

COMMANDS:
   help, h	Shows a list of commands or help for one command
   
GLOBAL OPTIONS:
   --file, -f 'Dockerfile.dapper'	Dockerfile to build from
   --socket, -k				Bind in the Docker socket
   --mode, -m 'auto'			Execution mode for Dapper bind/cp/auto [$DAPPER_MODE]
   --no-out, -O				Do not copy the output back (in --mode cp)
   --build				Perform Dapperfile build
   --directory, -C '.'			The directory in which to run, --file is relative to this
   --shell, -s				Launch a shell
   --debug, -d				Print debugging
   --quiet, -q				Make Docker build quieter
   --help, -h				show help
   --generate-bash-completion		
   --version, -v			print the version
```

它的說明分成兩個區塊，一個是 *Dockerfile variables* ，也就是在 Dockerfile 裡定的變數；另一個則是 Dapper 指令的參數。

Dapper 使用 `Dockerfile.dapper` 做為預設 build 的 Dockerfile ，先建立起來備用。那我們要用的 Gulp 需要 Node 的環境，因此可以先開始決定這個檔案要 `FROM node` 了，以下使用 Node 6.9 版：

```dockerfile
FROM node:6.9
```

接著就可以下 dapper 指令了：

```
$ dapper
Sending build context to Docker daemon 22.15 MB
Step 1 : FROM node:6.9
---> 178934e73268
Successfully built 178934e73268
Sending build context to Docker daemon 22.15 MB
Step 1 : FROM book-intro-of-ci:release
---> 178934e73268
Step 2 : COPY . /source/
---> d5c37d08981e
Removing intermediate container d3b6ca0ecb77
Successfully built d5c37d08981e
> var i = 10
undefined
> console.log(i)
10
undefined
>
(To exit, press ^C again or type .exit)
>
```

Dapper 預設會先依 `Dockerfile.dapper` 建立第一個 Image ， tag 會是 `<project_name>:<branch_name>` ，上例即 `book-intro-of-ci:release` 。同時建一個暫時的 Dockerfile ，開頭會直接 FROM 第一個 image ，然後把所有檔案複製進第二個 Image 。而這樣做最主要的用途是要做 Cache ，後面慢慢看下去就會知道了。  

開頭的 *Sending build context to Docker daemon* 其實會跑個約兩三秒，我們順手來優化一下。 Dapper 本質還是在跑 Docker Build ，所以把 `.dockerignore` 該忽略的檔案加一下就好，如：

```
# Vagrant files
.vagrant

# Git files
.git

# Docker files
.dockerignore
docker-compose.yml
Dockerfile
Dockerfile.dapper

# Node files
node_modules

# Composer files
vendor
```

執行看看，理論上就會變得很小很神速了：

```
$ dapper
Sending build context to Docker daemon 1.119 MB
```

再來 Dapper 有個參數是 `--shell` 可以開啟 bash ，我們來試試

```
$ dapper --shell
root@96fafa6c58e4:/# ls -l source/docs/
total 180
-rw-r--r-- 1 root root 9781 Dec 19 01:16 day01.md
-rw-r--r-- 1 root root 7784 Dec 19 01:16 day02.md
-rw-r--r-- 1 root root 6978 Dec 19 01:16 day03.md
-rw-r--r-- 1 root root 6455 Dec 19 01:16 day04.md
-rw-r--r-- 1 root root 8701 Dec 19 01:16 day05.md

...
```

可以發現這目錄好像有點熟悉，這就是第二個 Image 的內容。因此，照剛剛執行的過程加上結果來看， `Dockerfile.dapper` 會是第一個 Image 的結果，只要安裝 Gulp 相關的東西就好。原始碼在第二個 Image 
丟進去 build 即可。有了這些想法，那我們繼續來試看看安裝全域的 Gulp ，並把 CMD 換成 gulp ：

```dockerfile
FROM node:6.9

RUN npm install -g gulp

CMD ["gulp"]
```

Dapper 指令下下去：

```
$ dapper

Building ...

[16:30:48] Local gulp not found in /
[16:30:48] Try running: npm install gulp
FATA[0003] exit status 1
$
```

Gulp 可以用了，只是它要求要安裝 gulp 在該目錄。因為 `node_modules` 被忽略了，所以改成用 COPY 進去再安裝會比較好。需要注意的是，原始碼預設會放在 `/source` 所以記得要改一下 WORKDIR ：

```dockerfile
FROM node:6.9

RUN npm install -g gulp

WORKDIR /source
COPY package.json .
RUN npm install

CMD ["gulp"]
```

這次 dapper 的訊息不一樣了：

```
$ dapper

Building ...

[16:42:15] No gulpfile found
```

給它 `gulpfile.js` 吧

```javascript
var gulp = require('gulp');

gulp.task('build', function () {

});

gulp.task('test', ['build'], function () {

});

gulp.task('default', ['test'], function () {

});
```

Dapper 這次好像有點樣子了

```
$ dapper
[16:46:09] Using gulpfile /source/gulpfile.js
[16:46:09] Starting 'build'...
[16:46:09] Finished 'build' after 253 μs
[16:46:09] Starting 'test'...
[16:46:09] Finished 'test' after 36 μs
[16:46:09] Starting 'default'...
[16:46:09] Finished 'default' after 34 μs
```

可能有時候不想 run 全部，只想 run test 該怎麼辦？這時當然是回頭看看 dapper 的功能囉，它的指令格式是這樣的：

```
USAGE:
   dapper [global options] command [command options] [arguments...]
```

其實試了一下會發現，上面的 command 是跟 Docker Run 接在 image 後面的 command 是一樣的，因此我們可以利用 ENTRYPOINT 來達成目的

```dockerfile
FROM node:6.9

RUN npm install -g gulp

WORKDIR /source
COPY package.json .
RUN npm install

ENTRYPOINT ["gulp"]
CMD ["default"]
```

Run 的結果如下

```
$ dapper build
[16:51:58] Using gulpfile /source/gulpfile.js
[16:51:58] Starting 'build'...
[16:51:58] Finished 'build' after 255 μs
```

## 處理 Artifacts

容器都是執行完就銷毀的，那 build 產生的 Artifacts 該怎麼辦？身為一個 Build Tool ， Dapper 當然也有辦法解決，只是這時就要參考它的 Dockerfile 可以設定的變數了。這個需求的解決方法是加 `DAPPER_OUTPUT` 變數，實際的做法如下：

```dockerfile
FROM node:6.9

RUN npm install -g gulp

WORKDIR /source
COPY package.json .
RUN npm install

ENV DAPPER_OUTPUT ./dist

ENTRYPOINT ["gulp"]
CMD ["default"]
```

只要設定好 `DAPPER_OUTPUT` ，在 dapper 執行完後，會把容器裡專案目錄下的 dist ，複製到外面的專案目錄下，實際其實就類似是執行 docker cp ：

```
$ docker cp <container>:/source/dist ./
```

它的複製方法有 cp 和 bind ，這些就留給大家自行研究了。

---

## 今日回顧

今天的[原始碼參考](https://github.com/MilesChou/book-intro-of-ci/tree/50f4d8d734322f0c1c470f9c433daabb5341726b)

我個人覺得 Dapper 最有趣的地方在於：自由建置環境、自建全新容器、與自動執行爽快這三點。

在一開始開發寫 Dapper 和自動化腳本都會需要多花點時間，但套一句葉大的話，不用 Dapper 前，這些其實還是都做一做比較好。在完成之後，就不大需要擔心環境的變動，或與其他開發者同步環境的問題了。

## 相關連結

* [Gulp][]

[Dapper]: https://github.com/rancher/dapper
[Docker]: https://www.docker.com/
[Rancher Labs]: http://rancher.com/
[Gulp]: http://gulpjs.com/
