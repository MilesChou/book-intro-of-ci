# 假的！耦合業障重呀！（ 1/2 ）

細心一點的朋友們，或許會發現昨天有個細節沒討論到：「依賴的元件如果因為某些原因而無法初始化的話，該怎麼辦？」，這其實是一個常發生的問題，只是常常用不同的形式呈現。比方說：

* 「欸，你的物件什麼時候才會寫好啊？等你寫好我才能測試」
* 「你的後端 API 寫好了嗎？沒寫好我前端沒辦法測」
* 「資料庫要等 DBA 開好，我才有辦法繼續測試」
* 「你那邊 API 好了嗎？我 API 寫好了，等你寫好要開始測試串 API 了」 

上述問題都是因為依賴的元件還沒好，所以無法確認自己的程式是否有問題。

### 好，我等

問題遇到了總是要解決，一種最直接的解決方法，同時也是大家最不樂見卻又很常使用的：「等待」。等依賴的元件完成再來整合自己的程式。這個方法有幾個缺點：

* [Day 1][] CALMS 裡， Lean 提到要消除浪費，而 Lean 也提到浪費有七種，其中一種就是「等待」，這是管理人員都不想看到的。
* 需要等待依賴完成，才能確認程式是否正確，這代表不但做不到 [Day 5][] 提到的「頻繁驗證」，而且相對的還會有壞處：「問題範圍過大，難以聚焦」。這是開發人員不想遇到的。

### 寫文件給大家看如何？

為了不要浪費在等待、為了能提前做驗證，又出現了另一種常見的做法：「規格書」等，通常會使用傳統檔案格式，如 Office 文件或 Wiki 等，來規定兩個元件間的互動方式。文件裡預定義了互動方式，讓雙方都能對互動行為做適當的測試。比方說文件有一個可以接受 POST 與 FormData 並回傳 JSON 資料的 API。對依賴方而言，可以假設 API 回傳的資料為正確的 JSON 資料並實作在程式裡，就能執行後續的測試了；對被依賴方而言，只要實作出符合規格書的程式即可。

這方法看似合理，不過也有潛在的問題：

* 規格書內容大部分的人都不想看，因為內容通常很複雜，或格式多樣化難理解，一般都是遇到不得不查詢的問題才會看
* 因為複雜且大部分的人不想看，所以此文件維護更新的頻率非常低
* 真正發生問題時，都會查不到可用資料，只好直接查程式，最後就會惡性循環，文件一年半載沒更新都很常見
* 要測程式的時候，為了實作回傳的假資料，要一直不斷修改程式，最後還要再改回原樣，煩死了

### 寫在程式好了

寫文件有維護問題的話，那不如把假資料寫到測試程式裡好了。

比方說以[昨天][Day 9]的例子， Number 物件預定要實作了 `mux()` 的方法，但還沒實作出來，但另一個要實作平方的物件 Square 要開始開發測試了。那測試可以這樣寫：

```php
<?php

class SquareTest extends \Codeception\Test\Unit
{
    public function testShouldGet100WhenParamsIs10()
    {
        // Arrange
        $number = new NumberFake(10);
        $target = new \HelloWorld\Square();
        $excepted = 100;

        // Act
        $actual = $target->square($number);

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}

class NumberFake extends \HelloWorld\Number
{
    public function mux()
    {
        return 100;
    }
}
```

Square 程式：

```php
<?php

namespace HelloWorld;

class Square
{
    public function square(Number $number)
    {
        return $number->mux($number->get());
    }
}
```

上面可以看得出，程式會丟 `10` 進 mux() 方法，而 `NumberFake` 會實作預期的假設值，參考乘法的規格書， 10 * 10 要回傳 `100` ，這樣就讓後續的測試可以繼續執行。

> 其他如 API 串接的狀況大同小異，只要還沒實作，但使用類似的方法回傳符合規格書上的值，都算相同的情況

可是這樣也有問題，實作這些東西可讀性不好，又會有命名衝突問題。不知道有沒有工具能解決這個問題？

有的，只要是要實作這些測試用的假程式假資料等，都通稱叫 **Test Double**！

## Test Double

看完上述問題與解決過程，相信大家了解 Test Double 的目的是要解決測試遇到的依賴問題。 Test Double 的中文叫「測試替身」，正如其名，它是測試才會用的替身。如果把測試案例當成是在演戲的話，正牌的物件就是「演員」，假的物件則是「替身」。它們跟演員和替身的關係一樣，替身有某些部分跟演員很像，但看戲的人或是使用物件的 Client 應該是無感的。

Test Double 有五種類型，這兩天會說明這幾種類型的實作與應用範例。

## Dummy Object

有時，測試案例執行測試（ Act ）的時候，並不會執行到依賴。但初始化目標物件會需要依賴物件，這時可以使用 Dummy Object 。

舉個例子： Number 實作了 `save()` 與 `load()` 方法可以把數值存入資料庫或讀取，而資料庫連線是建構的時候傳入。程式實作如下：

```php
<?php

namespace HelloWorld;

use PDO;

class Number
{
    private $number;
    private $pdo;

    public function __construct($number, PDO $pdo)
    {
        $this->number = $number;
        $this->pdo = $pdo;
    }

    public function add($addend)
    {
        return $this->number + $addend;
    }

    public function sub($subtrahend)
    {
        return $this->number - $subtrahend;
    }

    public function get()
    {
        return $this->number;
    }
    
    public function save()
    {
        // Use PDO        
    }
    
    public function load()
    {
        // Use PDO
    }
}
```

從程式可以看出，當我在測 `add()` `sub()` `get()` 時，跟 PDO 完全無關。這時可以用 Dummy Object ，測試程式範例如下：

```php
<?php

class NumberTest extends \Codeception\Test\Unit
{
    public function testShouldGet1WhenConstructArgIs1()
    {
        // Arrange
        $pdoMock = \Codeception\Util\Stub::make('PDO');
        $target = new \HelloWorld\Number(1, $pdoMock);
        $excepted = 1;

        // Act
        $actual = $target->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
```

> 因為原程式的建構子修改，所以其他測試也需要修改，這邊就不佔篇幅了。

這裡可以發現，雖然建構子被限制要 PDO 物件才能傳入，但程式還是能執行，因為它實際的運作原理正是使用繼承。

## Stub

有時會需要假物件在被執行某個方法時，固定回傳某個值，有點像 HardCode 的概念。 Stub 正是可以簡單實作出這樣的功能。

回頭看最上面的 Square 的 Example ，它正是需要回傳固定值的案例，我們來把它改成 Stub 。首先，要被替身的程式要先定義 mux() 方法：

```php
<?php

namespace HelloWorld;

class Number
{
    // ...

    public function mux()
    {
        // Not implement;
    }

    // ...
}
```

再來就是我們 Stub 上場了

```php
<?php

class SquareTest extends \Codeception\Test\Unit
{
    public function testShouldGet100WhenParamsIs10()
    {
        // Arrange
        $numberMock = \Codeception\Util\Stub::make(\HelloWorld\Number::class, ['mux' => 100]);
        $target = new \HelloWorld\Square();
        $excepted = 100;

        // Act
        $actual = $target->square($numberMock);

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
```

> 我們也可以很無聊試看看在 Assert 加入 `$this->assertEquals(100, $numberMock->mux();` 測看看是不是真的拿到 100 。

## 今日回顧

* 如果依賴問題使用文件規範雙方行為，常常會很難維護
* 如果有辦法使用今天的方法解決，建議盡可能使用，因為它們也是「可執行的文件」的一部分，可以節省許多重複的測試工作
* 如果只是一個能看的假物件，稱之為 Dummy Object
* 如果要 HardCode 假的回傳，稱之為 Stub

今天講了五種類型的兩種，範例程式可以在 [GitHub][Sample Code] 找到，明天會再講其他三種 Test Double 。

下一篇：[假的！耦合業障重呀！（ 2/2 ）][]

## 相關連結

* [Test Double（1）：什麼是測試替身？][] | 搞笑談軟工
* [只有一位開發人員的專案也需要了解如何消除七種浪費][] | 搞笑談軟工


[Test Double（1）：什麼是測試替身？]: http://teddy-chen-tw.blogspot.tw/2014/09/test-double1.html
[只有一位開發人員的專案也需要了解如何消除七種浪費]: http://teddy-chen-tw.blogspot.tw/2012/10/blog-post_30.html

[Day 1]: /docs/day01.md
[Day 5]: /docs/day05.md
[Day 9]: /docs/day09.md
[Sample Code]: https://github.com/MilesChou/book-intro-of-ci/tree/82245f693cea79619f708ed035115aeb54330807
[假的！耦合業障重呀！（ 2/2 ）]: /docs/day11.md
