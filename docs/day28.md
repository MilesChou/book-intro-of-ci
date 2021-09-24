# 為 Legacy Code 接 CI Server

前幾天介紹了非常多種 CI server 的串接方法，想必大家看完都很想在自己負責的產品上接 CI server ，讓 CI server 幫忙執行測試等等。但真的花心思下去開做的時候，也許會發現前幾天的例子實在是太簡單了，實務會遇到的狀況通常很複雜。

今天就介紹，如果拿到某個產品的原始碼，該如何為它串接 CI Server （以下簡稱 **CI** ）。

## 首先必要要做的：加入版控系統

CI 有個重要的工作，是在每次提交程式時做 build ，然後記錄該提交是否成功。所以可想而知，一定需要一個版控系統記錄提交的歷程， CI 才有辦法說出哪次提交是成功可以用，哪次是失敗的別用。

以 Git 來說，原始碼加入版控系統的難度不高，只是市面上多種的托管服務可能會難以選擇。以下是一點選擇方向建議：

* 不在乎原始碼會被別人拿走的話，那就公開在 [GitHub][] 上吧
* 不想被別人拿走程式碼，但又不想花錢，不過開發團隊成員人數不多（五人以下），那 [Bitbucket][] 會是個好選擇
* 開發團隊成員人數較多的話，可以選擇 [GitLab][]

加好版控才能繼續下一步。

## 串接 CI / 自動化處理

這裡會建議有兩個方向可以繼續做，一種是直接串接 CI ，另一種是先寫自動化處理。

### 串接 CI

選好版控系統後，再來就可以串接適合的 CI 了。串接哪家 CI 的建議如下：

* GitHub 可以使用 [Travis CI][Day 24] ，或是 [Circle CI][Day 25] 也行
* Bitbucket 因為是不想公開，所以可以用 [Circle CI][Day 25] ，也可以用自家的 [Pipelines][Day 27]
* GitLab 當然用自家的 [GitLab CI][Day 26] 呀，不然要幹嘛？

> 以下使用 Circle CI 當範例

一開始串接 CI 可以先用最簡單的方法，讓它可以 pass ，如：

```yaml
machine:
  php:
    version: 7.0.7

test:
  override:
    - ls -al
    - exit 0
```

它雖然寫的很簡單，但實際上還是有驗證的效用。

首先專案先確定是執行在 PHP 7.0+ ，所以先把 `machine` 設定好。再來 `test` 的目的是先把 CI 會看到檔案先列出來，接下來要做其他操作就比較容易會有個依據。比方說 `ls` 看到有 `src` 與 `app` 目錄，那對應到 repository 應該也會看得到 `src` 與 `app` ，這是很單純的確認，也是一種驗證。另外它也驗證了 CI 串接與 `.yml` 格式都是正常的。

接著，如果有 `composer.json` 與 `composer.lock` 可以在第二次提交新增新的驗證：

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
    - ls -al
    - exit 0
```

雖然只是單純的安裝依賴套件，但它也有驗證的目的。比方說，它可以驗證 `composer.json` 與 `composer.lock` 格式正確，或是 `ls` 後會看到 `vendor` 等。

依此類推，可以一直寫。最後會寫到一個極限，因為某些東西還是要在本機執行才行。那這時就要考慮下一步的**自動化處理**了。

### 自動化處理

雖然現在深度學習非常熱門，但大多數的 CI 無法猜出要驗證的項目，因此撰寫一些**自動化處理**是絕對必要的。會說自動化處理這樣廣泛的說法，是因為 CI 其實不但需要自動化測試，也需要其他自動化的腳本。它們都可以幫開發人員處理很多任務。

當版控選好後，一開始也可以選擇自動化處理。那之後要串接 CI 就直接讓 CI 執行自己寫的自動化處理即可。跟先串接 CI 不同的地方在於，一個在 CI 上，用發現「缺什麼，加什麼」的方法在處理，那做到最後會發現，其實兩個方法是同樣的目的。

#### 自動化測試

有了自動化測試， CI 就會知道該怎麼幫你測程式了。

但在一包只有原始碼的程式上加測試，是件非常困難的事。還是有方法可以參考，比方說，不管單元測試再怎麼難，但產品如何操作是比較容易被理解的，因此第一步可以先加入 E2E Testing ，等測試結果穩定後，再進一步去修改程式，讓 Integration Testing 是容易被撰寫的。然後就這樣一步一步的把單元測試都補齊。

## 其他任務

一開始一定要確保產品是能運作的，所以建置可運行環境和寫自動化測試，相對來說是比較重要的。在這些建置都一個一個被加入 CI 的待辦清單裡，而且結果都很穩定時，就可以考慮加一些比較次要的工作，如 Inspection 或 Docker Build 等。

最後記得， CI 不是串接好就沒事了，開發人員要記得保持[好習慣][Day 5]，並持續為 build 流程改善，才能讓 CI 的價值最大化。

---

## 今日回顧

要串接 CI Server 其實並不難，如何讓 CI Server 發揮它應有的價值才是最重要的。

雖然 legacy code 串接 CI Server 通常沒辦法做很完整的檢查，但再小的檢查都是檢查，只要 CI Server 能幫助團隊發現任何小問題，都會是有價值的。

下一篇：[有了 CI Server，然後呢？][]

[Bitbucket]: https://bitbucket.org/
[GitHub]: https://github.com/
[GitLab]: https://gitlab.com/
[GitLab CI]: https://about.gitlab.com/gitlab-ci/

[Day 5]: /docs/day05.md
[Day 24]: /docs/day24.md
[Day 25]: /docs/day25.md
[Day 26]: /docs/day26.md
[Day 27]: /docs/day27.md
[有了 CI Server，然後呢？]: /docs/day29.md