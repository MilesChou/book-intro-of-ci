# 開源專案的好選擇 －－ Travis CI 

Travis CI 有分 .org 的[免費版][travis-ci.org]跟 .com 的[企業版][travis-ci.com] 。

> 以下會拿過去寫的測試程式來做實驗

## 版控串接

Travis CI 有一點個人覺得可惜的是，它只能接 [GitHub][] ，但沒關係，它還是有特異功能後面會提到。

首先當然是要登入進 Travis CI ，它登入被限制只能使用 GitHub 帳號，所以記得要申請並決定好要使用哪個 GitHub 帳號。

再來進來後左邊 Repository 的旁邊會看到一個 `+` ，這是新增要連動的 Project ，按下去吧！

![day24 step1][]

接著，先讓 Travis CI 知道 GitHub 裡有什麼專案，按右上角的 *Sync account* 即可更新。接著再把下面想連動的 repository 開關打開即可。

![day24 step2][]

到此， repository 與 CI 的連動就做好了。下一步就是要建立 `.travis.yml` 檔：

```yaml
language: php

php:
  - 7.0

before_script:
  - composer install

script:
  - php vendor/bin/codecept run

cache:
  directories:
    - vendor

branches:
  only:
    - release
```

分別說明上面各區塊的意思，語言是 PHP 7.0 這點應該很好懂。

```yaml
language: php

php:
  - 7.0
```

> 其他語言參考： https://docs.travis-ci.com/user/getting-started/

接著 `before_script` 與 `script` 是 lifecycle 一部分：

```yaml
before_script:
  - composer install

script:
  - php vendor/bin/codecept run
```

> Lifecycle 參考文件： https://docs.travis-ci.com/user/customizing-the-build/

`cache` 可以定義 build 的過程會產生某些檔案是可以在下一次 build 利用的。 Composer 應該就會是 `vendor` ， npm 則是 `node_modules` ，等等。

```yaml
cache:
  directories:
    - vendor
```

> Cache 參考文件： https://docs.travis-ci.com/user/caching

最後定義它只測 release branch ，因為不一定每個 branch 都會想跑測試 

```yaml
branches:
  only:
    - release
```

> Branch 參考文件： https://docs.travis-ci.com/user/customizing-the-build/#Building-Specific-Branches

準備好就可以 commit push 了！然後就可以在 Travis CI 網站上的 dashboard 看測試跑呀跑的，除了準備環境和預定義會做的事以外，還有剛剛設定的 lifecycle 都會執行到：

![day24 step3][]

最後的結果一定會是 pass / fail ，這些結果都會被記錄下來，上例的結果可以來[這個網頁](https://travis-ci.org/MilesChou/book-intro-of-ci/builds/186528953)查看。

## 特異功能

個人覺得它是開源專案的好選擇，最主要是因為這個特異功能：它可以把同個測試方法，放到多個測試環境下個別測試，並提供個別結果參考。

比方說 PHP 框架都會說它支援 PHP 5.3 以上，但如果真的要測的話，就必須個別切換與測試。但對 Travis 而言設定就非常簡單，如上例我想要測 PHP 5.5 / 5.6 / 7.0 / HHVM 是否都正常，可以在語言設定：

```yaml
language: php

php:
  - 5.5
  - 5.6
  - 7.0
  - hhvm
```

接著它就會起四個環境，並平行執行（下圖的 PHP: 7 與 PHP: hhvm 是平行執行的）：

![day24 extra][]

這個 build 的結果是 PHP 5.5 下無法正常執行，[這個網頁](https://travis-ci.org/MilesChou/book-intro-of-ci/builds/186529946)可以參考錯誤訊息。後面就可以依資訊考慮要修正，或是放棄 PHP 5.5 了，非常好用！

## Docker

有了 Docker 等於有了滿滿的大平台可以用。如果要使用 Docker ，官方也有提供[使用的方法](https://docs.travis-ci.com/user/docker/) 。

---

## 今日回顧

除了上面基本的測試功能外， Travis CI 還有提供測試完之後的後續處理，如[部署](https://docs.travis-ci.com/user/deployment/)、 [Artifacts 處理](https://docs.travis-ci.com/user/uploading-artifacts/)、[通知](https://docs.travis-ci.com/user/notifications)，等。

最後， Travis CI 的功能非常簡單，但也因為簡單，要把它安插進 [Pipeline][Day 21] 裡是非常容易的。別看了，一起來 CI 吧！

下一篇：[不公開專案的好選擇 －－ Circle CI][]

[travis-ci.org]: https://travis-ci.org/
[travis-ci.com]: https://travis-ci.com/
[GitHub]: https://github.com/

[Day 21]: /docs/day21.md
[day24 step1]: /images/day24-travis-step-1.png
[day24 step2]: /images/day24-travis-step-2.png
[day24 step3]: /images/day24-travis-step-3.png
[day24 extra]: /images/day24-travis-extra.png
[不公開專案的好選擇 －－ Circle CI]: /docs/day25.md
