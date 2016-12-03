# Agile 與 CI 之間的火花

繼 [Day 1][] 與 [Day 2][] 了解了什麼是 DevOps 與 CI 後，接著我們來看看 Agile 相關開發方法，與 DevOps 和 CI 之間的關係為何？

> 以下沒有特別分類或選擇，單純簡介一下我比較了解的 Agile 相關方法來跟 DevOps 與 CI 做個對照。

## Code Review

開發者把程式碼完成後，照 CI 精神，要把功能測試過才能提交。雖然「人非聖賢，孰能無過」，但由另一位開發者也來檢查程式碼與測試功能，絕對能降低出錯的機率。

只要有「開發者完成程式後，由另一位開發者審查」這樣的流程，通常稱 **Code Review** 。

### Pair Programming

![](https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Pair_programming_1.jpg/1920px-Pair_programming_1.jpg)

> source: [wiki][Pair Programming]

如果說 CI 的目的是為了要即早發現 bug 即早修正的話，那 [Pair Programming][] 會是一個避免把 bug 寫下去的開發方法。一個專心寫程式，另一個專心檢查，問題被發現的機率會非常高，通常在進版控系統前就會被發現了，所以 bug 完全沒有擴散的機會。 Pair programming 還有其他好處，如它能讓內部成員學習與分享開發方法和知識，並提高專案透明度與內部完整性，對於維護產品的成本可以大大降低。這是一個非常符合 DevOps 與 CI 精神的開發方法，確實執行後，將會帶來非常大的效益。

可惜的是，台灣大部分管理階層的人不買單，通常原因是人力資源不足。確實初期投入的人力成本會比較高，最主要是一開始同步資訊會比較花時間。但執行越多次，效率就會越來越快，是一個回本非常高的投資。

> 就像第一次同步 [Dropbox](https://www.dropbox.com/) 的檔案也會很久。當完成後，同步就會非常快速了。

### Pull Request

[Pull Request][] 顧名思義，它指的是「要求程式碼管理者拉取特定的程式碼」。發出要求後，會有一個屬於此要求的獨立空間，可以讓所有開發者，甚至是非開發者，一起審查程式碼並討論。等到管理員滿意時，管理員會表示接受並把程式碼拉取並合併。

目前大多的程式碼管理系統，如 [GitHub][] 、 [GitLab][] 、 [Bitbucket][] ，都有實作此功能。討論空間的做法也五花八門，但方向都是儘可能讓開發者好審查或追查等。

> 註： GitLab 叫 *Merge Request* ，但實際上是實作一樣的概念。

跟 [Pair Programming](#Pair Programming) 比較起來， Pair Programming 的回饋是即時的， Pull Request 的回饋速度會比較慢，雖然開發者時間被佔用的少，但資訊同步的成效也比較差。

Pull Request 目的也跟 DevOps 和 CI 有關，主要是讓修改程式的資訊能被透明化；並且在這個階段，可以解決程式錯誤。

## TDD

[TDD][] 是開發方法，它會先依需求來撰寫失敗測試，再實作程式去讓測試通過，接著重構，如此不斷循環。因為必須不斷測試讓程式通過後，才能繼續下一步；此外，會有測試程式本身與產出報告可以分享資訊給團隊成員，因此 TDD 是一個具備 DevOps 與 CI 精神的開發方法。

## BDD

[BDD][] 也是開發方法，它會先定義出一份可執行的需求文件，且這份文件是開發人員、測試人員甚至是驗收人員都容易理解的。這份文件跟團隊之間的關係，會有以下特性：

* 文件容易理解，所以非開發人員可以修改文件
* 文件可以拿來執行，所以非開發人員容易得知目前程式的狀況為何
* 文件通常會執行驗收測試，所以非開發人員可以依此決定是否要釋出
* 承上三點，非開發人員也能參與 [Pull Request](#Pull Request) 階段的討論

此文件提高了團隊透明度，也授權團隊成員對文件的修改權限與責任，也具備固定的驗證流程。所以 BDD 是一個同時具備 DevOps 與 CI 精神的開發方法。

## Scrum

[Scrum][] 是一個 Agile 開發方法框架，它有提供許多實際的方法來達到 Agile 所提倡的「擁抱改變」。 DevOps 與 CI 的精神，都是要隨時得知目前狀況（指測量與驗證），並依目前狀況做合理的改善（指決策與除錯），最後再繼續迎接下一個任務。 DevOps 與 CI 也是提倡擁抱改變的。
 
Scrum 有許多回饋機制，不僅提高透明度，也讓團隊更容易因應改變。相信喜歡 Scrum 的朋友們，一定也會喜歡 DevOps 與 CI 。

## Kanban

[Kanban][] 是一個精實開發的方法。它透過「可視化」「 WIP 限制」「拖拉系統」三個主要的步驟來讓團隊達到精實的境界。

* 「可視化」能提高團隊透明度，與 DevOps 精神相同。
* 「 WIP 限制」能讓團隊成員能專心處理任務，而不會被多工的狀態影響品質，這很像 CI 「改完程式就驗證」的時候，驗證的範圍只會專注在修改的程式上，而不會被其他程式影響。
* 「拖拉系統」授權團隊選擇任務的權限與責任，也跟 DevOps 精神相同。

簡而言之， Kanban 提倡的精實，也與 DevOps 與 CI 非常契合。

## 今日回顧

今天之所以會提到 Agile 相關的開發方法，主要是因為它們所提倡的觀念非常相似。喜歡 Agile 的朋友們，不妨可以多了解一下 DevOps 的概念。另外也有其他相關主題可以參考，如 [Extreme Programming](https://en.wikipedia.org/wiki/Extreme_programming) 等。

這三天都在聊 [DevOps][Day 1] 、 [CI][Day 2] 、 Agile 的基本概念，如同灌籃高手的赤木所說：「基礎是非常重要的」。後面談的實務做法，不單只是因為「這麼做比較好」，更是因為「這樣做可以減少某些不必要的錯誤，所以才這麼做」，而為何能減少錯誤？原因都在前幾天的基本概念裡了！

下一篇：**先求有，再求好？**

## 相關連結

* [Code Review][] | 維基百科
* [Pair Programming][] | 維基百科
* [Pull Request][] | GitHub
* [TDD][] | 維基百科
* [BDD][] | 維基百科
* [Scrum][] | 維基百科
* [Kanban][] | 維基百科
* [你們 code review 了嗎？](http://kf013099.blogspot.tw/2014/08/code-review.html) | 阿官新創日誌

[Day 1]: /docs/day01.md
[Day 2]: /docs/day02.md
[Code Review]: https://en.wikipedia.org/wiki/Code_review
[Pair Programming]: https://en.wikipedia.org/wiki/Pair_programming
[Pull Request]: https://help.github.com/articles/about-pull-requests/
[TDD]: https://en.wikipedia.org/wiki/Test-driven_development
[BDD]: https://en.wikipedia.org/wiki/Behavior-driven_development
[Scrum]: https://en.wikipedia.org/wiki/Scrum_(software_development)
[Kanban]: https://en.wikipedia.org/wiki/Kanban_(development)
[GitHub]: https://github.com/
[GitLab]: https://gitlab.com/
[Bitbucket]: https://bitbucket.org/
