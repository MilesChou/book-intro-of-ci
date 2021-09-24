# CI 工具大亂鬥

三十天也快結束了，大家對 CI 已經有更深的了解了。今天開始會講 CI 工具，有了概念後再開始實作，相信會做得更有樣子。

CI server 主要的目的是隨時待命，只要程式碼有新的提交，它就會開始忙著測東測西，最後會跟你說本次提交是成功還是失敗。歷史記錄是必要的，報表和 Artifacts 通常也都會幫你收集起來備存。 

## CI server 實作

目前常見的 CI server 實作如下：

* [GitLab CI](https://about.gitlab.com/gitlab-ci/) | GitLab built-in Service
* [Pipelines](https://bitbucket.org/product/features/pipelines) | Bitbucket built-in Service
* [Jenkins CI](http://jenkins-ci.org/) | 可參考隔壁棚的相關主題： [30 天入門 Ansible 及 Jenkins-CI][]
* [Concourse CI](https://concourse.ci/) | 可參考隔壁棚的主題： [不一樣的 CI/CD 工具：Concourse 初探][]
* [Circle CI](https://circleci.com/)
* [Travis CI](https://travis-ci.org/)
* [Drone.io](https://drone.io/)
* [TeamCity](https://www.jetbrains.com/teamcity/) | JetBrains 家出的 CI
* [Codeship](https://codeship.com/)
* [Bamboo](https://www.atlassian.com/software/bamboo) | Atlassian 家出的 CI
* [Scrutinizer](https://scrutinizer-ci.com/) | 需要付錢，功能很多，有語言限制
* [PHPCI](https://www.phptesting.org/)
* [Strider CD](http://strider-cd.github.io/)

未來幾天會講的實作與比較如下：

|   比較   |  [Dapper][Day 23]  |  [Travis CI][Day 24]  |  [Circle CI][Day 25]  |  [GitLab CI][Day 26]  |  [Pipelines][Day 27]  |
| -------- | -------- | ----------- | ----------- | ----------- | ----------- |
| 服務類型 | Local | SaaS | SaaS | SaaS / On-premise | SaaS |
| 價格 | Free | Free / [Enterprise](https://travis-ci.com/plans) |  Free / [Pricing](https://circleci.com/pricing/) | Free / On-premise | Free / [Pricing](https://bitbucket.org/product/pricing/upcoming) |
| 支援私有專案 | N/A | Yes (Enterprise) | Yes | Yes | Yes |
| 支援的版控系統 | N/A | GitHub | GitHub / Bitbucket | GitLab | Bitbucket |
| 測試執行環境 | Build Immediately  |  Ubuntu 12.04 / 14.04 | Ubuntu 12.04 / 14.04 | Docker / On-premise | Docker |
| 設定檔格式 | Dockerfile + script | `.travis.yml` | `circle.yml` | `.gitlab-ci.yml` | `bitbucket-pipelines.yml` |
| 支援 Docker | N/A | 支援 | 支援 | 使用 DinD | 無 |
| 特殊技能 | Local 執行 | 多組環境定義與測試 | Artifacts 代管 | 彈性的 Pipeline 定義 | 支援 Mercurial |

選擇講這幾家，主要當然是因為比較熟。另一個原因是，它們都有一些免費的方案可以選擇，而且可以快速跟常見的版控串接，並立即看到結果。

---

## 今日回顧

今天稍微休息一下，明天開始將會介紹如何使用這些工具。
下一篇：[自己來的好選擇 －－ Dapper][]

[30 天入門 Ansible 及 Jenkins-CI]: http://ithelp.ithome.com.tw/users/20103346/ironman/1021
[不一樣的 CI/CD 工具：Concourse 初探]: http://ithelp.ithome.com.tw/users/20065771/ironman/1020

[Day 23]: /docs/day23.md
[Day 24]: /docs/day24.md
[Day 25]: /docs/day25.md
[Day 26]: /docs/day26.md
[Day 27]: /docs/day27.md
[自己來的好選擇 －－ Dapper]: /docs/day23.md
