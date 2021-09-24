# 不公開專案的好選擇 －－ Circle CI

跟 [Travis CI][Day 24] 最大不同的是， [Circle CI][] 除了內建 [GitHub][] 整合外，也能整合 [Bitbucket][] 。另外 build 的能見度，會看版控的能見度來決定。換句話說，只要是私有的 repository ，其他路人就無法看到 build 的結果，這會是一般企業所想要的功能。

> 能見度的說明文件我目前找不到，但測出來的結果是這樣沒錯。

當然，免費是有限制的。看[方案說明][Circle CI Pricing]是說，免費限制是一個月只能有 1500 build minutes 。意思應該是，如果平均一個 build 花了 3 分鐘，那一個月只能執行約 500 個 build 。因為隔一個月就會重新計算，所以如果在達到上限的時候，先使用本機 Build 像 [Dapper][Day 23] 先擋一下，勉強也是可行。

> 目前我還尚未達到上限，上述都只是推測

## 版控串接

首先當然是登入 Circle CI 了，進[登入頁面][Circle CI Login]後，能選擇要用 GitHub 或 Bitbucket 登入。登入驗證完後，左邊選 *Add Project* ，右邊就會出現可以連結的 repo 有哪些。如果沒有的話，也可以按右上角的 *Reload Organizations* 重新整理一下。

找到 Project 就可以把 *Build Project* 按下去！

![day25 step1][]

接著它就開始 build 了

![day25 step2][]

欸等等，我們好像少做一件事？好像還沒定義測試的方法對吧？

是的，雖然它可以偵測檔案並猜測該執行什麼動作，以目前的例子是 `composer install` 和 `npm install` 。但該如何測試還是得明確定義：

![day25 step3][]

> [網頁連結](https://circleci.com/gh/MilesChou/book-intro-of-ci/1)

## 定義 Build 的方法

Circle CI 使用 `circle.yml` 來定義 Build 的方法，以下是一個簡單的範例：

```yaml
machine:
  php:
    version: 7.0.7

dependencies:
  cache_directories:
    - vendor
  override:
    - composer install

test:
  override:
    - php vendor/bin/codecept run

general:
  branches:
    only:
      - release
```

簡單解釋一下每個部分的功能，一開始 `machine` 應該很好懂，這裡使用 PHP 7.0.7 。

```yaml
machine:
  php:
    version: 7.0.7
```

> Circle CI 提供 Ubuntu 12.04 / 14.04 的版本，可以使用的程式語言可以參考： https://circleci.com/docs/build-image-trusty/

接著 `dependencies` 與 `test` 都是 lifecycle 的一部分。而 `dependencies` 定義了依賴，對 Composer 來說，依賴的處理當然是 `composer install` 所做的事。另外依賴可以 cache 的，所以也在這裡設定。

```yaml
dependencies:
  cache_directories:
    - vendor
  override:
    - composer install
    
test:
  override:
    - php vendor/bin/codecept run
```

> `override` 是來取代 lifecycle 預定義的方法，其他 lifecycle 可以參考： https://circleci.com/docs/manually/

最後是定義限制 `release` branch 執行。

```yaml
general:
  branches:
    only:
      - release
```

加入 `circle.yml` 並 push ，即可讓測試通過

![day25 step4][]

> [網頁連結](https://circleci.com/gh/MilesChou/book-intro-of-ci/2)

成功或失敗的歷程，都會好好地幫你記錄起來在[這個網頁](https://circleci.com/gh/MilesChou/book-intro-of-ci)。

## Docker 的用法

[官方說明](https://circleci.com/docs/docker/) 。簡單來說，只要 machine 加入這些定義即可：

```yaml
machine:
  services:
    - docker
```

需注意的是，目前使用上有發現，它在執行某些 Docker 指令會有問題，如：

```
$ docker run --rm <some_image>
$ docker exec -it <some_container> ping 8.8.8.8
```

但其他常用的，像 run 、 build 或 push 等，都是沒問題的  

## 特異功能

Circle CI 也有我認為蠻好用的「特異功能」

### 多環境 

Circle CI 目前用起來比較方便的地方在於，它可以多個環境疊加，如：

```yaml
machine:
  php:
    version: 7.0.7
  node:
    version: v6.1.0
```

這樣就可以同時執行 npm 和 composer 了：

```yaml
dependencies:
  cache_directories:
    - "node_modules"
    - "vendor"
  override:
    - npm install
    - composer install
```

### Artifacts 託管

另外就是它可以託管儲放 Artifacts ，預設路徑是用環境變數 `$CIRCLE_ARTIFACTS` 來代替。實際使用方法如下：

```yaml
test:
  override:
    - php vendor/bin/codecept run --skip acceptance --coverage
  post:
    - mv tests/_output $CIRCLE_ARTIFACTS/output
```

最後 build 的結果就會出現 Artifacts 的頁籤，然後就可以進去看到 code coverage ，某種程度也算蠻方便的。

![day25 artifacts][]

> [網頁連結](https://circleci.com/gh/MilesChou/book-intro-of-ci/4)，但 Artifacts 只有參與專案的人才看得到，有興趣可以參考 `circle.yml` 自己玩看看。

### 背景執行程式

有時候必須要在測試前執行某些服務，才能開始跑測試，如 [Selenium][] / [PhantomJS][] ，或是 Web Server 等， Circle CI 也能做得到。

下面的例子是執行測試前，先啟動 PHP Built-in server ：


```yaml
test:
  pre:
    - php -S 0.0.0.0:8080:
        background: true
  override:
    - php vendor/bin/codecept run --skip acceptance --coverage
  post:
    - mv tests/_output $CIRCLE_ARTIFACTS/output
```

需注意的是，我測試的結果，執行服務啟動好像要放在 `test` 的區塊才會正常。

> 相關文件可以參考： https://circleci.com/docs/background-process/

---

## 今日回顧

[Bitbucket] + [Circle CI] 因為都支援私有專案，而且串接服務也非常容易。使用它們可以立即建立企業用的小型 CI 專案，即使是 Legacy Code ，也是能從 build 開始，一步一步搭建起簡單的 CI 哦！

下一篇：[功能強大的 －－ GitLab CI][]

## 相關連結

* [Bitbucket][]
* [GitHub][]
* [Selenium][]
* [PhantomJS][]

[Bitbucket]: https://bitbucket.org/
[Circle CI]: https://circleci.com/
[Circle CI Enterprise]: https://circleci.com/enterprise/
[Circle CI Login]: https://circleci.com/vcs-authorize/
[Circle CI Pricing]: https://circleci.com/pricing/
[GitHub]: https://github.com/
[Selenium]: http://www.seleniumhq.org/
[PhantomJS]: http://phantomjs.org/

[Day 23]: /docs/day23.md
[Day 24]: /docs/day24.md
[day25 step1]: /images/day25-circle-step-1.png
[day25 step2]: /images/day25-circle-step-2.png
[day25 step3]: /images/day25-circle-step-3.png
[day25 step4]: /images/day25-circle-step-4.png
[day25 artifacts]: /images/day25-circle-artifacts.png
[功能強大的 －－ GitLab CI]: /docs/day26.md
