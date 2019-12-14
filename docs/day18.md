# 流浪漢與碼頭工人的應用

我們先來回顧前幾天聊的：

流浪漢－－ *Vagrant* 的文章回顧：

* [到處流浪的伺服器][Day 14]

碼頭工人－－ *Docker* 的文章回顧：

* [管理貨櫃的碼頭工人－－ Docker （ 1/3 ）][Day 15]
* [管理貨櫃的碼頭工人－－ Docker （ 2/3 ）][Day 16]
* [管理貨櫃的碼頭工人－－ Docker （ 3/3 ）][Day 17]

這兩個技術都能達成下列目的：

* *應用隔離*
* *儲存再發佈*
* *環境即程式碼*
* *具備可攜性*

那該如何應用這些技術在 CI 上呢？回想 [Day 2][] 提到寫 Hello World 程式要做的六個步驟，試著把上面的結果套進這六個步驟：

1. *準備環境:* 當環境具備可攜性後，代表環境可以帶著跑，可以簡單地從無到有建置環境
2. *寫程式:* 準備好的環境與外界隔離，修改程式時不會影響環境，要透過部署指令才會進入環境
3. *部署程式:* 完成的程式透過部署指令進環境
4. *測試程式:* 在環境裡使用測試指令執行測試
5. *驗證結果:* 除了人工測試外，測試指令都會有驗證的規格與方法
6. *除錯:* 得到結果後，就能除錯了

而**準備環境**、**部署程式**、**測試程式**這三個步驟，正是這兩個技術發揮強項的地方。以下就請各方代表說一下，他們如何為開發者做到這三件事：

## 流浪漢代表

*Vagrant* 使用 Hypervisor 雖然資源佔用的比 Docker 多，但它使用上的概念較容易理解。說穿了嘛， Vagrant 的本質其實就是 VM ，至於使用哪種 VM 則是 [Provider][Vagrant Provider] 決定，大多都是使用 [VirtualBox][] 。

1.  準備環境

    Vagrant 使用 `Vagrantfile` 來配置環境，換句話說，只要環境裝好 Vagrant 和 Provider ，加上 `Vagrantfile` 即可建置環境。它由 Ruby 所撰寫，所以可以使用程式語法處理某些配置任務。  

2.  部署程式

    Hypervisor 模擬出來的是一個獨立環境，修改程式並不會影響這個獨立環境。而 Vagrant 提供了 `synced_folder` 參數，讓外界資料夾與獨立環境的某個資料夾做同步，只要進去執行部署指令即可把程式佈到環境。

3.  測試程式

    Vagrant 環境建置不難，且每次建置的結果會是一致的。因此非常適合使用在測試上，只要環境被搞壞或是測試結束，都能安心把虛擬機砍掉重練，完全不用害怕。要在其他電腦上重現結果，也非常地方便。需要查詢系統 Log 記錄，因 Vagrant 就是一台 Linux ， SSH 進去把它當 Linux 用即可。

### 使用建議

Vagrant 環境的基礎是使用 Hypervisor 建立出來的 VM ，資源較獨立，但就是很吃資源。除非是特殊場合不能直接使用 Docker 外（如 Windows 7 ），其他都會建議使用 Vagrant 做開發，將會方便許多。另外， Vagrant 概念較好懂，也比較沒有資源共用的問題，學習容易。這也可以列入考慮的因素。

## 碼頭工人代表

*Docker* 是容器化技術，資源佔用較小，同樣的實體機可以開出更多容器。雖然很像 Vagrant ，用起來也很像，即使 Docker 與 Vagrant 有指令或概念上的對照表，但記得，這兩個是不一樣的。

1.  準備環境

    Docker 使用 `Dockerfile` 定義容器環境；使用 Docker Build 建置 Image ；最後使用 Docker Run 建立容器並執行。這樣就是一個完整的環境準備過程。其中 Image 還可以進一步放在 Docker Hub 供其他團隊成員或更多人下載，可提升環境一致性，同時也省下了 Docker Build 的時間。  

2.  部署程式

    容器建議要有的特色之一－－不可變的，因此通常部署程式的過程會被定義在 `Dockerfile` 裡，在準備環境的過程中就會做部署，而不是容器開好再另外部署。另一種方法是使用 Volume Mapping ，是一個類似資料夾同步的方法，可以把外部程式碼同步進容器內。但這會比較像是 Workaround 的做法，還是建議定義在 `Dockerfile` 內較好。

3.  測試程式

    Docker Run 可以啟動容器，即可對容器執行測試。容器資源消耗少，甚至還可以做平行化測試。 Docker 的 Log 建議用 stdout 輸出，可以直接用 `docker logs` 查詢，非常方便。

### 使用建議

Docker 建立在 Linux 64-bit 上，即使 Windows / Mac 有出 Native Docker Engine ，但許多舊版都不支援。因此環境需求建議還是限制在會用 Linux 上會比較好。另外容器的概念跟 VM 不大一樣，學習上並不是那麼容易，這也需要考慮。

## 隱藏角色，流浪的碼頭工人

如果想用 Docker 會有 Linux 的限制，那我們能不能用 Vagrant 裝 Docker 就好了？當然可以，這個方法唯一的缺點是，因為多了一層 layer ，所以 port 轉接與資料夾同步，都需要做兩層，另外速度也會慢了一點點，不過這些問題在開發階段都好處理，應該只要注意一下即可。

## 今日回顧

Vagrant 與 Docker 各有它們的好用之處，但主要目的都是為了快速建置一個可拋式環境，讓開發人員與維運人員可以安心做測試與部署的練習。只要練習的夠多次，上線的風險自然就比較小。同時這也是[開發如何考慮維運][Day 13]的實際做法唷！

下一篇：[Inspection][]

## 相關連結

* [Vagrant Tutorial（1）雲端研發人員，你也需要虛擬機！](http://www.codedata.com.tw/social-coding/vagrant-tutorial-1-developer-and-vm) | William Yeh @ CodeData
* [Docker —— 從入門到實踐](https://www.gitbook.com/book/philipzheng/docker_practice) | philipzheng

[Vagrant Provider]: https://www.vagrantup.com/docs/providers/
[VirtualBox]: https://www.virtualbox.org/

[Day 2]: /docs/day02.md
[Day 13]: /docs/day13.md
[Day 14]: /docs/day14.md
[Day 15]: /docs/day15.md
[Day 16]: /docs/day16.md
[Day 17]: /docs/day17.md
[Inspection]: /docs/day19.md
