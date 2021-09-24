# 假的！耦合業障重呀！（ 2/2 ）

[昨天][Day 10]提到了 Test Double 的其中兩個類型，分別是 *Dummy Object* 與 *Stub* 。在實務上，這兩個已經非常好用了，今天繼續把剩下三個類型說明，聽完應該就能應付九成的依賴問題了。

## Spy

有時候我們比較在乎的會是物件互動行為，比方說購物車下單的流程裡，我們就會非常在乎金流 API 是不是真的有被呼叫過，畢竟這才是真正轉換的結果。如果是這樣的需求，那我們可以使用 Spy 。這次用購物車物件與金流物件為例：

```php
<?php

namespace HelloWorld;

use Exception;

class Cart
{
    private $pay;

    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
    }

    public function order()
    {
        if (!$this->pay->checkout()) {
            throw new Exception('Checkout error');
        }
    }
}

namespace HelloWorld;

class Pay
{
    
    public function checkout()
    {
        // Do something

        return true;
    }
}
```

測試程式如下

```php
<?php

use Codeception\Util\Stub;

class CartTest extends \Codeception\Test\Unit
{
    public function testShouldCallCheckoutOneTimeWhenCartOrder()
    {
        // Arrange
        $payMock = Stub::make(\HelloWorld\Pay::class,
            [
                'checkout' => Stub::once(function() { return true;}),
            ]
        , $this);
    
        $target = new \HelloWorld\Cart($payMock);
    
        // Act
        $target->order();
    }
}
```

`Stub::once()` 表示預期要剛剛好被呼叫一次，如果沒有的話就會丟例外。當完成測試後，可以把原始碼 `$this->pay->checkout();` 註解試看看，應該會看到測試失敗的訊息，表示在執行下單的時候，並沒有呼叫金流結帳的 API 。

## Mock

Stub 可以測假資料， Spy 可以測互動，爭什麼！摻在一起做 Mock 啊！

是的，個人覺得 Mock 蠻像 Stub + Spy 的，除了他們個別的功能都能實作之外，它還能定義呼叫次數與回傳內容的對應，比方說上述購物車的例子，一個下單的流程裡，金流結帳的回傳，第一次是 true，第二次是 false ，這樣可讓購物車做其他處理。下面來看測試程式的例子：

```php
<?php

use Codeception\Util\Stub;

class CartTest extends \Codeception\Test\Unit
{
    public function testShouldThrowExceptionWhenCallOrderTwice()
    {
        // Arrange
        $this->expectException(Exception::class);
        $payMock = Stub::make(\HelloWorld\Pay::class,
            [
                'checkout' => Stub::consecutive(true, false),
            ]
            , $this);
        $target = new \HelloWorld\Cart($payMock);

        // Act
        $target->order();
        $target->order();
    }
}
```

上例是在執行 `Pay::checkout()` 第二次的時候回傳 false ， `Cart::order()` 發現是 false 就丟例外，這個測試主要只預期會有例外發生，因此是 pass 。 

## Fake

最後一個類型叫 Fake ，它是使用較低的成本實作依賴元件，它並不需要像前四個類型一樣還要定義預期的行為，因為它已經非常接近真實元件了。通常它都是服務層級的元件，如資料庫等。較低成本的實際做法有很多，如：

* 資料庫使用 SQLite ，因為初始化成本較小
* 服務使用虛擬化如 Vagrant / Docker ，除了成本小，大部分也都具備獨立的特性。
* 企業內部專門測試用的主機，通常也是 Fake 。（試想，正式環境使用的規格跟測試環境有一樣嗎？）

今天暫時不展示 Fake 的實際範例，只要知道通常是實作服務類即可。這類的實作可以參考後面幾天的文章：

* [Day 14 - Vagrant][Day 14]
* [Day 15 - Docker][Day 15]

## 耦合業障重

如果細心的朋友會發現，這兩天到目前為止，都尚未提到「耦合」的話題。但我想大家應該也注意到了，如果當測試的時候要考慮依賴問題，代表測試目標與被依賴的物件有耦合；有耦合就必須考慮互動方法與資料規格等問題，而這些都是 Test Double 能解決的。

---

跟大家分享一個親身的體驗：

記得我一開始知道有 Test Double 超開心的，因為測試的時候完全不需要管依賴物件到底初始化好了沒，只要假設它的回傳就好了，於是也沒想那麼多，測試一不順就用 Mock ，用了非常多。但某天測試環境整合的時候，發現業務需求有問題，於是調整了業務需求。這下好了， Mock 的假設全都是依賴業務需求所寫出來的，所以所有的 Mock 就必須修改。

程式的架構設計必然會有耦合，但會有上述問題代表測試目標與依賴物件耦合過多，這應該要在測試不順的階段發現並要解決。因此我們有寫測試的話，是可以提早發現耦合過多的問題，並提早解決的。

## 今日回顧

今天的範例程式在 [GitHub][Sample Code] 這裡。

* Spy 可以測試目標程式與依賴程式之間的互動
* Mock 是類似 Stub / Spy 的合體 
* Fake 通常用在服務層級

相信大家都可以了解 Test Double 原理與目的。可是別忘了，雖然它們跟真的物件行為很像，但那些都是**假的**，最後還是得回頭做真正的[整合測試][Day 9]才是最保險的。

Anyway ，寫整合測試或使用 Test Double 都可以提早發現耦合過多的問題，這對 CI 要求的「即早發現，即早治療」都是有幫助的。

下一篇：[測試範圍][]

## 相關連結

* [Test Double（1）：什麼是測試替身？][] | 搞笑談軟工

[Test Double（1）：什麼是測試替身？]: http://teddy-chen-tw.blogspot.tw/2014/09/test-double1.html
[Sample Code]: https://github.com/MilesChou/book-intro-of-ci/tree/ebea3dab7bd260fa601b94b533ca08bd0496a536

[Day 9]: /docs/day09.md
[Day 10]: /docs/day10.md
[Day 14]: /docs/day14.md
[Day 15]: /docs/day15.md
[測試範圍]: /docs/day12.md
