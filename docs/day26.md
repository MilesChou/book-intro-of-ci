# 功能強大的 －－ GitLab CI

[GitLab][] 本身功能非常豐富，加上它又是開源可以自行架設的，通常會是企業選擇版控的首選。後來又追加 [GitLab CI][] 與 [Pipeline][GitLab Pipeline] 功能，讓它的戰場跨越到 CI ，並成為一個企業內部管理開發流程的好選擇。

CI Server 跟版控通常會是不一樣的 server ， CI 會執行 Build ，並通知版控說，目前提交是否成功。那 GitLab CI 與 GitLab 的概念也一樣，它是另外獨立且可選的元件。打個比方， [Bitbucket][] 之於 [Circle CI][Day 25] ，就類似 GitLab 之於 GitLab CI 一樣。

以下將使用 GitLab.com 服務當作範例。

**注意**，如果是自己架設 GitLab 的話， 8.0 之後才支援 GitLab CI 。另外功能會參考目前最新版介紹，使用之前記得確認自己家的版本是否有支援。

## 設定專案

之前用的專案都是在 GitHub 上執行，要換到 GitLab 有很多種方法，那這邊示範比較簡單的 [Mirror Repository][GitLab Mirror Repository] 。如果想直接用 GitLab 上的專案直接開始的可以跳過這一小節。

首先先登入 GitLab 帳號，建立一個空的新專案。

![day26 step1-1][]

接著在右上角的齒輪，選擇 *Mirror Repository* 

![day26 step1-2][]

這裡要填的是可以 `git clone` 下來的地方，那因為專案是 public ，直接使用 https 會單純許多。

> 如果是 private 可參考 [Mirror Repository][GitLab Mirror Repository] 文件說明

![day26 step1-3][]

記得下面 *Trigger builds for mirror updates* 要打勾，因為我們是要靠 GitHub 的更新來觸發 GitLab CI 執行 Build 。

![day26 step1-4][]

再來進到專案抬頭的右下角，會有個 *Set up CI* 。

![day26 step1-5][]

點下去就會進入編輯界面，這裡就是要編輯 CI 的設定檔 `.gitlab-ci.yml` 了。

---

因為 Mirror Repository 每小時才會同步，這樣做可以立即觸發並看到結果，但同步可能就會有問題，建議是在 GitLab 另外開 branch 測試會比較好。

首先專案抬頭的中間偏左，有個 *Branches* 的連結。點下去後，右上角會有 New Branch 的按鈕，按下去後，在 branch name 打上想叫的名字，再按 Create Branch
 
![day26 step1-6][]

接著會被導到如下面的檔案列表頁面，點中間的 `+` 代表要新增檔案，接著打檔名 `.gitlab-ci.yml` 即可在不同 branch 練習建置 CI 了。

![day26 step1-7][]

## 版控串接

對其他家 SaaS CI 服務而言，要連結版控系統和 CI Server 才算串接成功，對 GitLab 而言，只要新增 `.gitlab-ci.yml` 檔，它就算串接完成了。

來看簡單的範例

```yaml
image: mileschou/php-testing-base:7.0

stages:
  - build
  - test
  - deploy

build_job:
  stage: build
  script:
    - composer install
  cache:
    untracked: true
  artifacts:
    paths:
      - vendor/

test_job:
  stage: test
  script:
    - php vendor/bin/codecept run
  dependencies:
    - build_job

deploy_job:
  stage: deploy
  script:
    - echo Deploy OK
  only:
    - release
  when: manual
```

建好之後，按下上面的 Pipelines 頁籤，就會看到有一個 Build 開始在跑了

![day26 step2-1][]

> 有興趣可以翻翻看[連結網頁](https://gitlab.com/MilesChou/book-intro-of-ci/pipelines)裡面有什麼。

GitLab 的功能比較複雜，先簡單說明。 GitLab 的 build 都是在 Docker 上執行的，因此一開始會需要定義 image 名稱，而它將會成為下面執行 build 的環境。

> 下面這個例子是我自己寫來測試用的，有興趣可以參考 [GitHub 原始碼](https://github.com/MilesChou/php-testing-base)。

```yaml
image: mileschou/php-testing-base:7.0
```

接著 GitLab CI 預定義有三個階段，也就是 `stages` ：

```yaml
stages:
  - build
  - test
  - deploy
```

一個 stages 可以定義一個或多個 job ，而 job 是定義在 yaml 最外層。比方說，上例的 `build_job` 階段對應的是 `build` stage 

```yaml
build:
  stage: build
  script:
    - composer install
  cache:
    untracked: true
  artifacts:
    paths:
      - vendor/
```

job 裡面的定義常用的如上面這個範例：

* `stage` 剛剛說明的，定義 job 要在哪個 stage 執行
* `script` 是 job 實際會執行的腳本，上例是安裝套件
* `cache` 會把套件快取起來，下次啟動同一個 job 的時候會再使用
* `artifacts` 會把裡面編譯或程式的產出物存放起來，提供介面給其他需要的人使用

再來 `test_job` 定義的稍有不同：

```yaml
test_job:
  stage: test
  script:
    - php vendor/bin/codecept run
  dependencies:
    - build_job
```

這邊 `dependencies` 設定意思是指，它這個測試要取用 `build_job` 的 `artifacts` 。別忘了，每個 job 都是獨立的 container ，互不相關，因此要靠這些設定來傳遞 Artifacts

`deploy_job` 的定義：

```yaml
deploy_job:
  stage: deploy
  script:
    - echo Deploy OK
  only:
    - release
  when: manual
```

* `only` 代表這個 job 只在 release branch 執行
* `when` 代表何時會執行，此例是指要手動觸發 job

### 使用 Docker

GitLab CI 上要使用 Docker 需要多做一些事，首先 Docker 是 Client / Server 架構，所以代表兩邊都需要準備一些東西才行。 Server 比較簡單，只要最上面定義全域服務即可：

```yaml
services:
  - docker:dind
```

它的做法會用 docker run 啟動 `docker:dind` 並把上面定義的 image 起的容器連結至 `docker:dind` 。所以另一個比較麻煩相信已經發現了，上面定義的 image 必須要有 Docker Client 與設定才能正常的連接。

因此，如果只是要 build image ，利用 `artifacts` 和參考[官方說明文件](https://docs.gitlab.com/ce/ci/docker/using_docker_build.html)應該很容易達成，以下是個簡單的範例：

> 注意 image 和 service 雖然有全域定義，但也能 job 各自定義

```yaml
deploy_job:
  stage: deploy
  image: docker:latest
  services:
    - docker:dind
  script:
    - docker build -t xxx:ooo .
  dependencies:
    - build_job
```

## 特異功能

GitLab CI 設計的蠻有彈性的，因此可以做的變化也不少。這裡提兩個小技巧：自定義 Stage 與 Job 自由對應，當 CI 流程需要很長的時候， GitLab 提供自定義 stage 的功能，舉例可以有五個 stage：

```yaml
stages:
  - build
  - test
  - release
  - docker
  - deploy
```

Stage 自定義功能其實還好，一般 CI 流程應該不會那麼多，但配合 Job 自由對應的彈性就會非常好用。如 CI 想要測 PHP 5.6 / 7.0 的範例：

```yaml
image: mileschou/php-testing-base:7.0

stages:
  - build
  - test
  - deploy

build:5.6:
  stage: build
  image: mileschou/php-testing-base:5.6
  script:
    - composer install
  cache:
    untracked: true
  artifacts:
    paths:
      - vendor/

build:7.0:
  stage: build
  image: mileschou/php-testing-base:7.0
  script:
    - composer install
  cache:
    untracked: true
  artifacts:
    paths:
      - vendor/

test:5.6:
  stage: test
  image: mileschou/php-testing-base:5.6
  script:
    - php vendor/bin/codecept run
  dependencies:
    - build:5.6

test:7.0:
  stage: test
  image: mileschou/php-testing-base:7.0
  script:
    - php vendor/bin/codecept run
  dependencies:
    - build:7.0

deploy_job:
  stage: deploy
  script:
    - echo Deploy OK
  dependencies:
    - build:7.0
  only:
    - release
  when: manual
```

> 上述設定執行結果： https://gitlab.com/MilesChou/book-intro-of-ci/pipelines/5546894

另外，因為是 GitLab 本家出的 CI ，因此它跟 GitLab 的版控與 issue tracker 功能整合都不錯。

---

## 今日回顧

撇除 GitLab.com 的速度真的有點慢之外，個人是認為 GitLab CI 對企業來說，會是一個不錯的選擇。

如果公司內部有在用 GitLab 而不能用外部服務的話，趕快看一下版本是不是 8.0 吧！幸運的話，馬上就可以開始 CI 囉！

下一篇：[多樣服務整合 －－ Pipelines][]

[Bitbucket]: https://bitbucket.org/
[GitLab]: https://gitlab.com/
[GitLab CI]: https://about.gitlab.com/gitlab-ci/
[GitLab Mirror Repository]: https://docs.gitlab.com/ee/workflow/repository_mirroring.html
[GitLab Pipeline]:https://about.gitlab.com/2016/05/22/gitlab-8-8-released/

[Day 25]: /docs/day25.md
[day26 step1-1]: /images/day26-gitlab-step-1-1.png
[day26 step1-2]: /images/day26-gitlab-step-1-2.png
[day26 step1-3]: /images/day26-gitlab-step-1-3.png
[day26 step1-4]: /images/day26-gitlab-step-1-4.png
[day26 step1-5]: /images/day26-gitlab-step-1-5.png
[day26 step1-6]: /images/day26-gitlab-step-1-6.png
[day26 step1-7]: /images/day26-gitlab-step-1-7.png
[day26 step2-1]: /images/day26-gitlab-step-2-1.png
[多樣服務整合 －－ Pipelines]: /docs/day27.md
