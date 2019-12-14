# 爭什麼！摻在一起做整合測試啊！

廣義的說，當測試有包含多個單元時，就算是整合測試了。即使單元測試完整，各單元功能也正常，但組合在一起時，通常還是會發生許多莫名的問題。這時先不要想哪個單元沒寫好，而是先摻在一起做整合測試比較好

整合測試的目的是為了確保各單元間**互動正常**，各單元正常不表示組合起來會如我們所預期的。這道理在很多地方都適用，最常見的，一位知名的前端高手與一位知名後端高手合作，通常一開始也會不如預期，會需要後續整合。

## 物件的整合測試

很多情況都可以稱之為整合測試，首先我們先做物件的整合測試。昨天已經有寫好 Number 物件了，今天來寫一個程式來用它看看：

```php
<?php // src/Sum.php

namespace HelloWorld;

class Sum
{
    public function sum(array $numbers)
    {
        $sum = new Number(0);

        foreach ($numbers as $number) {
            $sum = new Number($sum->add($number->get()));
        }

        return $sum;
    }
}
```

新增測試的指令：

```
$ php vendor/bin/codecept generate:test functional Sum
Test was created in /Users/miles/GitHub/MilesChou/book-intro-of-ci/tests/functional/SumTest.php
```

測試程式的範例：

```php
<?php // tests/functional/SumTest.php

class SumTest extends \Codeception\Test\Unit
{
    public function testShouldGet6WhenParamsOneTwoThreeNumberObject()
    {
        // Arrange
        $target = new \HelloWorld\Sum();
        $numbers = [
            new \HelloWorld\Number(1),
            new \HelloWorld\Number(2),
            new \HelloWorld\Number(3),
        ];
        $excepted = 6;

        // Act
        $actual = $target->sum($numbers)->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
```

執行結果

![Class Run][]

範例程式連結： [GitHub](https://github.com/MilesChou/book-intro-of-ci/tree/47e7a0c51aea664e714f5c4b7c368f22e7644b8e)

## 今日回顧

單元測試完整，不代表整合會正常，仍需要整合測試來確保。有一個很好的反例，跟大家分享：

* 門 service 提供上鎖服務，測試只要能卡栓能正常上鎖，即測試通過。
* 門 service 提供開門服務，測試只要門能正常打開，即測試通過。

結果端口測試想說先 call 上鎖再 call 開門，應該門會打不開的。結果卻打開了。為什麼呢？請參考下圖：

![](https://media.giphy.com/media/l0MYSpvx4pnsoMNz2/giphy.gif)

> source https://giphy.com/

因此，整合測試是需要測的。下次如果 API 串接遇到整合問題在爭論誰對誰錯時，相信大家就可以很有自信地跳出來說：**爭什麼！摻在一起做整合測試啊！**

下一篇：[假的！耦合業障重呀！（ 1/2 ）][]

## 相關連結

* [Integration Testing][] | 維基百科

[Integration Testing]: https://en.wikipedia.org/wiki/Integration_testing
[Class Run]: /images/day09-codeception-class-run.png
[假的！耦合業障重呀！（ 1/2 ）]: /docs/day10.md
