# 什麼是 DevOps ？

> 前言在 [GitHub](https://github.com/MilesChou/book-intro-of-ci) ，文章會同步更新。

在開始聊 CI 前，先來談談 DevOps 。

DevOps 簡而言之，就是 Development + Operations ，也就是開發與維運。但大部分的文章都會說是「開發」「測試」「維運」三者的結合。如同下面這張圖想表示的意義一樣，當三者有了交集，即是 DevOps

![](https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Devops.svg/512px-Devops.svg.png)

> source: [wiki][Wiki DevOps]

DevOps 也像是 Agile 的延伸，從它的歷史與目的可以觀察的到。 [王立恒的文章][iThome DevOPs]是這麼說的：

> *Patrick Debois* 了解到，開發團隊與維運團隊不僅中間像隔了座山，運作方面還處處衝突。

因為先前已有 Agile 開發的經驗了，所以這時有人覺得不對勁，怎麼會在維運這關卡著呢？於是有人開始思考，會不會有更好的解決方法，於是：

> *John Allspaw* 跟 *Paul Hammond* 認為打造新一代軟體的方法應該是讓開發團隊及維運團隊兩個都變得透明，並將兩者互相整合在一起。

想法跟大部分的 Agile 方法類似：**提高透明度，並整合兩者**。可想而知，團隊間的資訊越透明，整合度越高，發生衝突的機率會越小，而 DevOps 最初的目標，正是要解決 Dev & Ops 之間的衝突！雖然文章內並沒有提到測試角度的說法，不過約略可以猜想的到：測試是必要流程，有專業人員，並且它也是個需要跟開發與維運緊密合作的重要角色，因此測試也常出現在 DevOps 的討論範圍，如同 wiki 上的示意圖。

### DevOps 想要達成的目標為何？

從 *Patrick Debois* 發現的問題與參考葉大[一句話囊括 DevOps 的目標][]一文，可以了解，最大的目標即為**速度**。「天下武功，唯快不破」，從發現需求到產品上線的時間越短，能得到的回饋與市場也就越大；但快還不夠，還要好，也就是要有**品質**！如果只有快，而沒有品質，只是更快把 bug 上線，並破壞企業名聲而已。如何兼顧速度與品質，即為 DevOps 的主要目標。

## DevOps 到底在做什麼？

為何會出現 DevOps ，相信已經有個感覺了。那它究竟在做些什麼事呢？

有文章會提到用 **CALMS** 的角度來說明 DevOps 的要領，這是下列五個英文單字的縮寫：

* *Culture*
* *Automation*
* *Lean*
* *Measurement*
* *Sharing*

這是了解 DevOps 概念的好方向之一。

## Culture

**文化**排 DevOps 要領裡的第一個，當之無愧。 DevOps 並不是一種技能，不是叫開發去學如何管機器，也不是叫維運去寫程式。 DevOps 的 Dev + Ops 是指「開發多去想維運面，維運多去想開發面」。舉例來說，開發寫程式時，把設定檔設計的非常適合維運人員管理；或是開發想導入一些套件管理工具，維運可以支持並配合上線。

不同團隊或職能的人，都有權力為專案做某些改善，並為這些修改負責。這樣跨團隊或跨技能的緊密合作文化，即是 DevOps ！

## Automation

還記得前面有提到， Dev 和 Ops 合作上有障礙，才會有人提出應當要有 DevOps 的觀念。這時**自動化**將會是個很重要的要領，它是需求的潤滑劑，需求丟給開發人員開發完後，有了自動化測試，很快就會滑到準備上線；有了自動化佈暑，一不小心就滑到上線了。

另外，它也是團隊合作的潤滑劑。

比方說，原本的障礙：開發人員要上線時，丟了一包程式，然後維運人員丟上去沒多久，客戶就打來靠邀壞了。維運人員查了半天才發現原來是新程式有用到第三方 library 沒包進去，接著又繼續搞半天，只為了把 library 安裝至正式上線的機器上，想當然而，維運人員靠邀了。不過開發人員這時出來講話了：程式裡有文件啊！

大多數的狀況下，管理層面的人會出面提議兩邊坐下來討論，接著就會出現 SOP 。比方說，開發上線前要測試，測試完要寫測試報告，然後填寫上線文件與表單送交維運，等待維運上線。這一類的 SOP 通常每次的內容都差不多，久了就會有人開始不想寫。然後，換開發人員靠邀了。

其實，每次上線的流程大同小異，有自動化能取代 SOP 的話，這一類的靠邀就能避免掉很多了。

## Lean

> Lean [參考書籍](http://www.books.com.tw/products/0010669225)，也有人會解釋 L 為 *Learn* ，但我個人認為 Lean 跟 DevOps 的目標吻合度更高，所以我選擇說明 Lean 。

**精實**就得提一下，精實軟體開發的七大原則：

1.  消除浪費 (eliminate waste)

    對於 DevOps 想完成的目標來說， Dev 與 Ops 間的衝突，就是一個最大的浪費。只要具備 DevOps 的觀念，自然會一起想各種方法來解決這些衝突，如自動化、持續整合、持續佈署等。

2.  增強學習 (amplify learning)

    在 Dev 與 Ops 分開不同團隊或不同職能時，代表這是兩個專業。 DevOps 為了要做到互助合作，解決不必要的衝突，勢必得學習其他專業，來了解衝突是如何發生，並考慮更多角度來想出更好的方法。

3.  盡量延遲決策 (decide as late as possible)

    精實原意指的是流程的改變，盡量等待資訊完整後，再下決策。
    
    我個人認為在 DevOps 上，一個軟體開發生命週期裡，很多資訊在 Dev 階段是無法確定的，常常需要到了 Ops 階段、或上線階段才能確定，如資料庫連線資訊、業務管理者密碼等。但因為開發階段時期修改的成本是最小的，因此這些資訊都是需要使用一些手法保留給 Ops / 業務人員修改，如環境變數或後台管理介面等。

4.  盡快交付 (deliver as faster as possible)

    如同 Agile ，盡快交付的目的正是為了盡快得到回饋，並盡快修正。如何盡快交付？在努力消除浪費與完成自動化的同時，交付的時間自然會越來越快。

5.  授權團隊 (empower the team)

    Dev 與 Ops 雖然是分開的團隊。**在互助的前提下**，給對方團隊一些特權，也許會讓交付的速度急速成長。比方說， Ops 決定導入 [Docker][] ，授權開發團隊可自己定義 Dockerfile 並以 Docker Image 交付。因此授權加上 Docker 開發方便的特性，讓開發團隊交付速度大幅提升。

6.  崁入完整性 (build integrity in)

    完整性有分「內部完整性」與「外部完整性」。內部完整性意指內部人員在工作內部整合的程度很高，如開發完交付測試是用「滑」過去的。當團隊在接受需求到產出的過程是非常流暢完整的，則是外部完整性的特徵。
    
    同時具備內外部完整性的團隊，不僅外表看起來精實，內部人員也因為工作內容整合，而會互相學習成長。

7.  著眼整體 (see the whole)

    會取 DevOps 這個名詞，我想應該是要提醒 Dev 與 Ops ：開發與維運是「互助合作，相輔相成」，而不是「針鋒相對，一事無成」。從更多角度思考產品，會讓產品的品質更好，如開發階段就思考如何上線，因此設定會盡可能採用環境變數；維運思考如何方便開發，因此會有開發專用的 [Vagrant Box][] 或 Docker Image 。

## Measurement

DevOps 也具備著 Agile 的精神，所以不怕改變。但改變總是要有憑有據，有了*測量*，可以提示團隊如何做更正確的改善。通常測量的目標會是維運時期的數據，但其實從需求到上線整個流程的過程，都有可以記錄的地方，比方說某個需求在開發過程中所出現的 bug 率特別高、或是 bug 被重開的次數等，都有助於團隊思考改善的方向。

## Sharing

DevOps 是一種文化，而*分享*是一個創造 DevOps 文化的最好方法。分享的內容可以有很多，如文章、經驗、工具等，甚至上述的**測量**出來的數據也能分享給團隊所有人，讓開發人員與測試人員，甚至是業務人員都能根據數據做出最好的決策。因此，分享也是增加團隊透明度的好方法。

---

DevOps 的要領環環相扣，尤其關鍵都在「人」身上，也就是文化，很難一蹴可幾。必須要先從自身做起，再慢慢影響他人。

這 30 天要討論的 CI 是開發團隊與測試團隊一起 DevOps 的最佳實踐。接下來我們就一起來看看 CI 要如何達成這不可能的任務吧！

> 先把自己做好，再把測試團隊拉下水！

## 今日回顧

* DevOps 是開發、測試、維運三者的結合。
* DevOps 的要領 CALMS = Culture 、 Automation 、 Lean 、 Measurement 、 Sharing
* DevOps 是一種文化，所以並不是一個人的事，而是大家的事。

下一篇：還記得第一次寫程式嗎？

## 相關連結

* [DevOps][Wiki DevOps] - Wiki
* [為什麼會出現DevOps？][iThome DevOPs] - iThome
* [什麼是 DevOps？](https://aws.amazon.com/tw/devops/what-is-devops/) - AWS
* [什麼是 DevOps？](http://blog.chengweichen.com/2015/08/devops-taiwan-meetup-devops-ithome.html) - Chen Cheng-Wei
* [一句話囊括 DevOps 的目標][] - William Yeh

[Wiki DevOps]: https://zh.wikipedia.org/zh-tw/DevOps
[iThome DevOPs]: http://www.ithome.com.tw/news/96861
[Docker]: https://www.docker.com/
[Vagrant Box]: https://www.vagrantup.com/docs/boxes.html
[一句話囊括 DevOps 的目標]: http://school.soft-arch.net/blog/79569/devops-goals-in-a-nutshell
