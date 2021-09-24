# Build Script

[Day 6][] 曾提過， [Continuous Integration][] 這本書所稱的 Build 不是只有 Compilation ，應該還包含了 [Testing][Day 7] 、 [Inspection][Day 19] 、 [Deployment][Day 13] 。相信這些主題，大家一路看到今天應該大致知道它們在做什麼事了。

但不同階段通常要做的事都不大一樣，比方說 Deploy 本機接觸的部分可能就不會很完整；都上預發佈機器，也不大可能做底層的測試，等等。所以 Build 的行為是需要持續調整的，今天將會來聊聊 *Build Script* 裡面應該會有什麼內容。

我們從結果倒推回來想看看好了。如果說每一個階段都能產出某個東西給下一個階段使用的話，那 Build 的產出應該會是一個「可用的程式」。每一階段對可用程式的要求，都會有所不同，我們就能依這些需求來思考 Build Script 該做些什麼。

## Development

開發階段來說，產出可用的程式最主要目的是為了測試功能，因此能越快知道整體程式碼的狀態概觀越好。因此會建議開發是執行快速的測試與檢查，如單元測試和 Coding Style 檢查，甚至是比較快速的整合測試。

這個階段的 Build ，可能 10 秒內結束會比較理想。身為一個開發人員，我認為這個階段的 Build 如果太久就會不想執行，那很容易就會失去 CI 想要達成的目標了。

## Testing

測試階段當然重點是在測試。首先會執行開發人員的 Build 產生可執行檔，這樣能先確認開發人員提交程式碼沒唬爛。接著開始執行較慢的完整測試，包括整合測試、 DB 測試、 E2E 測試等等，另外也會執行 Inspection ，最後並產生測試報表與檢查報表，這就有如健康檢查報表一樣。如果有 API 文件需要產生的話，這個階段也建議可以一併做。

這些產出物，我們稱之為 *Artifacts* ，它們有個很重要的特色：只要有原始碼，我都能產生這些產出物。所以換言之， Artifacts 不適合進版控，我們只要留原始碼即可。但 Artifacts 通常包括了可執行檔，所以 Artifacts 會移交給 Production 執行部署

雖然時間應該不會規定，但通常會建議這個 Build 不要超過 10 分鐘。

## Production

這個階段就不能隨意做測試了。例如，測試要 arrange 資料庫是不可能的。那就沒辦法測了嗎？還是可以的，我們可以做 Smoke Testing 。它能先對待測目標做一些不影響內部資料的方法，比方說觀看登入頁面，這個方法就能測到頁面正常。

那這個階段也不產 Artifacts ，可能只會執行 deploy 和 smoke test 而已。

## 今日回顧

今天提了一點做法，但這些都是死的。最終團隊想怎麼跑，只要大方向正確，就不會有太多沒問題！
下一篇：[Pipeline][]

## 相關連結

[Continuous Integration]: https://www.amazon.com/Continuous-Integration-Improving-Software-Reducing/dp/0321336380

[Day 6]: /docs/day06.md
[Day 7]: /docs/day07.md
[Day 13]: /docs/day13.md
[Day 19]: /docs/day19.md
[Pipeline]: /docs/day21.md
