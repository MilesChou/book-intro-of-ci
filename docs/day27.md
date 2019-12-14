# 多樣服務整合 －－ Pipelines

[Pipelines][] 是 [Bitbucket][] 提供的線上 CI 服務。至於為何說是「多樣的服務整合」，因為 Bitbucket 出自於 [Atlassian][] 家，他們還有出許多常見的企業解決方案如 [JIRA][] 、 [HipChat][] 等。除此之外，它還支援了 [Mercurial][] ，當團隊如果使用 Mercurial 的話，也是個不錯的選擇。

既然是企業解決方案，代表蠻多強大的功能都需要錢，所以我試的功能就不是很多了。

## 設定專案

眼尖的朋友，應該有注意到[昨天的 GitLab][Day 26] 在講 Mirror Repository 時，上面是 *Pull from* ，而下面是 *Push to* 。沒錯，今天就用這個設定同步到 Bitbucket 吧。

![day27 step1-1][]

那一樣可以開一個 branch 做測試，這邊就不截圖了。

## 版控串接

跟 GitLab CI 直接內建不大一樣， Pipelines 是需要手動啟用的，在專案主頁右邊的選單裡可以找得到：

![day27 step2-1][]
  
再來就用力給它啟用下去吧！接著它會跟你說要定義 `bitbucket-pipelines.yml` ，這是 Pipelines 的設定檔。這邊先直接使用 PHP 的樣版：

![day27 step2-2][]

接著就會進入編輯界面，那記得右上角的 branch 名稱要記得改測試 branch 。以下是範例

```yaml
image: mileschou/php-testing-base:7.0

pipelines:
  default: 
    - step:
        script:
          - composer install
          - php vendor/bin/codecept run
  
  branches:
    release: 
      - step:
          script:
            - composer install
            - php vendor/bin/codecept run
            - echo Release!!
```

執行結果如下：

![day27 step2-3][]

> 測試結果可以參考[這裡](https://bitbucket.org/MilesChou/book-intro-of-ci/addon/pipelines/home#!/results/%7Baa1388af-a0d6-432c-a78a-feccaeb5dd7b%7D)

首先，一開始的 `image` 跟 GitLab CI 一樣，是定義測試環境的 Docker Image 。因為也是要測 PHP ，所以使用同一個 image ：

```yaml
image: mileschou/php-testing-base:7.0
```

而下面 `pipelines` 開始才是定義 build 的步驟。 `default` 下面的是當其他指定的狀況都不符合的時候才會跑，下面 `step` 和 `script` 的模式是固定的：

```yaml
default: 
  - step:
      script:
        - composer install
        - php vendor/bin/codecept run
```

`script` 後面就是實際 build 的步驟，這裡就安裝套件與執行測試而已。

另一個 `branches` 裡面則是指定特定的 branch pattern 執行特定的步驟。以本例來說，是指定 `release` branch ：

```yaml
branches:
  release: 
    - step:
        script:
          - composer install
          - php vendor/bin/codecept run
          - echo Release!!
```

除了指定單一 branch 之外，也可以用簡單的萬用字元如 `release-*` 可以匹配 `release-1.0.0` 等。另外也可以指定特定 tag 的執行步驟。

那可惜的是，它雖然使用 Docker Image 做為環境，可是卻不能執行 Docker ，應用範圍可能就會比其他家 CI 來的小一點。

其他功能可以參考[官方文件](https://confluence.atlassian.com/bitbucket/bitbucket-pipelines-792496469.html) 

---

## 本日回顧

原本想說 Bitbucket 內帶的 CI 應該有很多厲害的功能，想不到這麼快就結束了。 Pipelines 是今年才釋出的，功能很單純。如果要做簡單的測試與部署是足夠的，但要像 GitLab CI 那樣複雜就比較困難。

不過還是可以繼續觀望，因為 Pipelines 設計上還有很多沒利用的空間，很有可能未來會再釋出更多功能，我們就繼續期待吧！

下一篇：[為 Legacy Code 接 CI Server][]

[Atlassian]: https://www.atlassian.com/
[Bitbucket]: https://bitbucket.org/
[HipChat]: https://www.hipchat.com/
[Mercurial]: https://www.mercurial-scm.org/
[JIRA]: https://www.atlassian.com/software/jira
[Pipelines]: https://bitbucket.org/product/features/pipelines

[Day 26]: /docs/day26.md
[day27 step1-1]: /images/day27-pipelines-step-1-1.png
[day27 step2-1]: /images/day27-pipelines-step-2-1.png
[day27 step2-2]: /images/day27-pipelines-step-2-2.png
[day27 step2-3]: /images/day27-pipelines-step-2-3.png
[為 Legacy Code 接 CI Server]: /docs/day28.md
