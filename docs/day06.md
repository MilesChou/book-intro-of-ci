# CI 起步走

在我第一次要做 CI 時，是毫無方向不知道該先做什麼好。那今天要講的是，這五天常常提起最實際要做的，但也還沒深入討論的細節－－**驗證**。

事前先說明一下，之所以會用驗證這個名詞，主要是不想與其他專業用語重複，如「測試」，怕讓還沒完全了解的朋友們會有先入為主的想法。

> 之後會參考 [Continuous Integration][] 這本書所採用的關鍵字做解說。

## 什麼是 Build ？

前五天說的*驗證*，其實指是 CI 這本書所提的 *Build* ，而裡面是這樣定義 Build 的：

> A build is much more than a compile. A build may consist of the compilation, testing, inspection, and deployment—among other things.  
> — Continuous Integration

Build 不只是 compile ，它包含 [Compilation](#compilation) 、 [Testing](#testing) 、 [Inspection](#inspection) 、 [Deployment](#deployment) ，等等。

### Compilation

程式語言也是一種「語言」，它也有屬於自己的文法和描述。而 *Compilation* 最原始的定義，是把原始碼翻譯成機器碼，如 C 的原始碼可以翻譯成機器碼，即可執行。 

因為通常 compile 最後的結果都是可執行的，後來定義就比較廣：只要從原始碼變成任何形式的可執行檔案都可以叫 compilation ，如 [CoffeeScript][] compile 成可執行的 JavaScript 檔。

那這個階段要驗證什麼呢？寫程式就像在寫文章一樣，而 compiler 的工作是做翻譯官。當它發現文章的文法不對，就會說看不懂無法翻譯；或是語意有問題怕電腦誤會，而出現 warning 提醒等等。

通過 compiler 這關，至少能確定電腦會執行。如果無法通過，代表被 compiler 退稿，必須要把文法和語義修改正確，直到它能翻譯為止，因為後面的項目大部分都依賴 compilation 的結果。

#### Example

以 [Day 2][] 的 Hello World 為例，因為 PHP 是動態語言，所以只要直接執行沒有出現語法錯誤，即表示 compile 是成功的：

```php
<?php

echo 'Hello World'

?>
```

比方說上面程式碼少了一個分號，因此 PHP compiler 就會靠邀說文章看不懂，因為少分號。

> Compilation 細節不多做說明，因為只要程式語言的文法與描述正確， compiler 幾乎都看得懂。

### Testing

Compiler 看得懂文章，並不代表翻譯過後的內容跟規格是一致的。必須真的用電腦去執行，並跟規格做確認，這就是 *Testing* 。

實際上 testing 要做的事很簡單：就是執行程式，並驗證程式在特定狀況下的執行結果是如同預期的。因此 testing 要驗證的是：程式所實作出來的功能，有解決需求嗎？

#### Example 

一樣拿 Hello World 為例。顧名思義，程式會輸出 `Hello World` 所以叫 Hello World 。

```php
<?php

echo 'null';

?>
```

上面這段程式碼語法是正確可執行的，可是執行結果並不是 `Hello World` ，這跟規格定義的不同，因此雖然 Compilation 通過，但 Testing 是不通過的。

#### Non-Functional Testing

另外，一般 Testing 大部分指的是 *Functional Testing* ，也就是測功能。另一種叫 *Non-Functional Testing* ，它是測功能以外的項目，如安全、效能、系統負載等等。

### Inspection

Compiler 看得懂文章且譯文電腦執行也符合規格，但這並不代表人類就會看得懂原文。對原文做分析檢查，就稱為 *Inspection* 。

而 inspection 跟 testing 不同點在於，一個是檢查原始碼，一個是測試執行結果。換句話說， inspection 不一定要經過 compiler 的步驟，而 testing 是必須要的。

這個檢查可以幫助開發人員了解原始碼的狀況，並可做出改善決策，如基本的 coding style 檢查；某個 Method 行數過多不好維護；某個 Class 被用了很多次，要是它沒有自動化測試可能會很危險，等等。 

通常這個項目就不一定會是必須要通過的，通常會因為產品性質，而讓原始碼必須不符合一些通用的規則。但 inspection 提供的資訊還是有利於改善原始碼，雖然不是必要通過，但還是建議要做。

### Deployment

通過完驗證，最後就是要把程式放上實際要執行的環境上跑了。這部分會視流程，而決定交付對象為何。比方說，有必要的手動測試，那這裡交付對象將會是手動測試人員。

在做佈署時，比較保險的做法是考慮如何 rollback 。[莫非定律][]：「凡是可能出錯的事必定會出錯」，即使測試與檢查的項目再完整，都還是有可能出錯。

> [Day 5][] 的習慣有養成的話，要找到前一個正確版本就會非常簡單。

## 結論

[昨天][Day 5]提到的是一些好習慣，了解概念就會知道這些本來都該做，只是 CI 提倡要常常做。而上面幾個項目相信大家一直都有在做，但不一定很重視。今天是要提醒大家，這幾個項目都是 CI 的一部分，如果持續關注這些項目的執行結果，並持續改善修正，也算是開始 CI 了！

個人是建議：總之，就開始做吧！無論是要從 [Compilation](#compilation) 開始，或是柿子挑軟的吃，先從 [Inspection](#inspection) 只要有原始碼就能做的開始，都可以。

[先要對，才會有，再求好][Day 4]

只要確定手上在做的事，有驗證到任何一個小環節，這樣就對了！持續執行下去，接著再持續改善，這樣就會越來越好了！

## 今日回顧

* Build 包含了 Compilation 、 Testing 、 Inspection 、 Deployment 等等，每個階段都有它驗證的目標。
* 不管哪個階段都可以馬上開始做。持續做，持續改善，就能確保驗證的目標有一定品質。

因為 Testing 跟業務功能與價值直接相關，也比較好理解。所以我們明天會先從如何寫 Testing 程式開始：

下一篇： [Hello Testing][Day 7]

## 相關連結

* [Continuous Integration][]
* [莫非定律][]

[Day 2]: /docs/day02.md
[Day 4]: /docs/day04.md
[Day 5]: /docs/day05.md
[Day 7]: /docs/day07.md
[CoffeeScript]: http://coffeescript.org/
[Continuous Integration]: https://www.amazon.com/Continuous-Integration-Improving-Software-Reducing/dp/0321336380
[莫非定律]: https://zh.wikipedia.org/wiki/%E6%91%A9%E8%8F%B2%E5%AE%9A%E7%90%86
