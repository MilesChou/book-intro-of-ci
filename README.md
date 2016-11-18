# CI 從入門到入坑

DevOps 說：開發、測試與維運應該互相合作，解決問題並完成任務。 CI 是開發與測試密切合作，並產出讓維運可快速佈署的高品質軟體。相信對開發者來說，沒有什麼能比「寫完程式立即看到成果」來的興奮的事了。讓我們來體驗 30 天的興奮，並一起入坑 CI 吧！

* [GitBook](https://www.gitbook.com/book/mileschou/intro-of-ci/details)
* [GitHub](https://github.com/MilesChou/book-intro-of-ci)

> 此為第 8 屆 iT 邦幫忙鐵人賽 [DevOps 組][DevOps Team]參選作品之一。

## 前言

在開始寫作之前，我想先聊一下，為何我會選 CI 當作是主題吧！

至今跟許多人聊 CI ，有些人以為它是特效藥。好一點的還知道要請測試人員寫腳本，讓電腦代替人工自動化處理測試步驟，再手動觸發 CI server 處理；慘一點的，搞不好覺得請 MIS 架 CI server 串專案後， bug 就會煙消雲散。

**假的！**

CI 是一個觀念或文化，大部分的人講 CI 都是在說 CI server ，雖然有相關，但是它們有點不大一樣。一定是先有 CI ，才會有 CI server 。但上述這些人很有可能都忘了 CI 最初的原意了。

[Waterfall][] 裡，開發階段和測試階段被明顯區分開來。也許這做法有某些優點，但在現今講究快速迭代開發的時代裡，它的缺點特別明顯。對於在意這些缺點的 Waterfall 團隊而言， CI 是特效藥沒錯，而且是個非常苦，讓人嚥不下口的良藥。如同簡介所說， CI 講的正是「開發與測試密切合作」。習慣 Waterfall 的朋友，通常比較難理解這些好處。只有在藥效出現作用時，才會知道它真的能解決專案的某些問題。

選 CI 當主題是因為，我想在藥外面包一層糖衣，雖然說穿了還是在吃藥，但至少讓這些朋友們比較願意服用。服用完後，軟體品質不一定能馬上提高，但相信至少不會再爛下去了，接著維運佈署高品質軟體上線自然會更安心，也更有信心！

最後，祝大家都能 **快快樂樂開發，平平安安上線。** 

## 目錄

Loading ...

## 誌謝

* 推坑的 [DevOps Taiwan](https://www.facebook.com/groups/DevOpsTaiwan/) 好友們

[DevOps Team]: http://ithelp.ithome.com.tw/ironman/signup/list?group=devops
[Waterfall]: https://en.wikipedia.org/wiki/Waterfall_model
