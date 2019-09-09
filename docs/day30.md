# 三十天總結

總算到了尾聲，先來回顧一下我們討論到的主題吧！

### 基礎理論，大家都該了解

我們聊到什麼是 [DevOps][Day 1] ，知道了 [CI 精神][Day 2]與養成[好習慣][Day 5]。

### 程式整合，是開發人員的基本功

程式整合其實就是在測試元件是不是有正常執行，所以我們也聊到了如何做[單元測試][Day 8]與[整合測試][Day 9]。除了程式執行要正常外，原始碼也需要做[檢查][Day 19]。

### 環境整合，是開發人員與維運人員的整合

執行測試必須要有環境，如果開發能預先[考慮環境問題][Day 13]，並在本機預先[練習上線][Day 18]，會讓部署更順利。

### 自動化整合

將程式碼的修改當作是生產線的起點，那中間的過程都能[自動化][Day 21]並一棒交一棒，最後再交付可用的軟體，目前也有許多可以參考使用的 [SaaS 服務][Day 22] 。

### 整合至 Legacy Code

CI 怎麼開始？做下去就對了！ [Legacy Code][Day 28] 也許雖然很難寫自動化測試，但還是有辦法一步一步建置出 CI Server 來的。

## 完成之後

當 CI Server 建置好，自動化測試都撰寫好之後，相信 CI Server 應該能開始幫忙抓鬼了。有了 CI Server 之後，就可以：

* 專心開發，不會因為把舊功能改壞又忘了測，而讓壞掉的程式上線。
* 放心重構，把原始碼一些比較難理解的部分都重寫；會不會把功能改壞，這點問 CI Server 就會知道了。
* 安心部署，開發人員有 CI 精神，每次提交都會做好測試；忘了測，也有 CI Server 在背後盯著。

記得， CI 只是 DevOps 的開始，後面還可以做 CD 、 Measurement 等等。

那為什麼要從 CI 開始呢？先想一下，還記得[先要對，才會有，再求好][Day 4]嗎？

* 「先要對」指的正是 **Continuous Integration** ，為的是要產出正確的產品
* 「才會有」指的是 **Continuous Delivery** ，讓產品的功能可以持續更新
* 「再求好」是 **Measurement** ，或是也可以硬說成是 **Continuous Measurement** ，這正是在做持續改善，讓產品更好。
* 求好改善的過程會做一些改變，這些改變要「先要對」。

這個循環剛好是 DevOps Toolchain 幾個重要階段的目的：

![DevOps Toolchain](https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Devops-toolchain.svg/512px-Devops-toolchain.svg.png)

> source: [wiki](https://en.wikipedia.org/wiki/DevOps_toolchain)

CI 是完成 DevOps 其他階段重要的基礎，如果沒有 CI ，後面將會問題重重。而 CI 的本質就是要**不斷測試**，要讓測試不斷地執行就需要**自動化**。因此，別懷疑了，開始推團隊寫自動化測試吧！

---

## 最後的回顧

沒想到真的達成發了三十天的文章的成就了！

CI 剛學習不久，已經盡力把知道的都寫出來了，希望真的能幫助到大家。如果對文章有任何建議都可以回應，或是直接 [GitHub][] 發 PR ，感謝大家支持！

[GitHub]: https://github.com/MilesChou/book-intro-of-ci

[Day 1]: /docs/day01.md
[Day 2]: /docs/day02.md
[Day 4]: /docs/day04.md
[Day 5]: /docs/day05.md
[Day 8]: /docs/day08.md
[Day 9]: /docs/day09.md
[Day 13]: /docs/day13.md
[Day 18]: /docs/day18.md
[Day 19]: /docs/day19.md
[Day 21]: /docs/day21.md
[Day 22]: /docs/day22.md
[Day 28]: /docs/day28.md
