# Hello Testing

> 今天開始會有比較多實際範例，但以 PHP 為主，其他語言可能需要切換一下。

如果依層級分類的話，相對最底層的 Testing 就稱之為 **Unit Testing** ，中文稱之為「單元測試」。

> 為了跟其他英文名詞有所區別，以下將會用中文來稱呼「測試」與「單元測試」。

雖然沒有明確定義，但單元測試指的單元，通常是架構上粒度最小的 *Function* 或是 *Class* 等。

以下來看看我們如何寫測試程式，來測試這些單元。

## Function

首先來看下面這段程式碼：

```php
<?php
// add.php

function add($x, $y) 
{
    $sum = $x + $y;

    return $sum;
}
```

一開始寫程式，通常我們測試可能會像這樣：

```php
<?php
// addTest.php

include 'add.php';

$num = add(1, 2);

var_dump($num); // will see 3
```

接著我們會開瀏覽器，並確認 `$num` 是不是正常的。這樣就稱之為一個 *Test Case* ，也稱為「測試案例」。 

再來個複雜的：

```php
<?php
// sum.php

function sum(array $numbers = []) 
{
    $sum = 0;

    foreach ($numbers as $number) {
        $sum = $sum + $number;
    }

    return $sum;
}
```

測試程式如下：

```php
<?php
// sumTest.php

include 'sum.php';

$arr = [1, 2, 3];

$sum = sum($arr);

var_dump($sum); // will see 6
```

只要開瀏覽器看到 6 代表程式測試結果是正確的。

### 什麼是 3A 原則呢？

從這個兩個簡單的 function 與測試案例，我們會發現有幾個共同點。

1.  一定會有明顯執行測試的目標，如 `addTest.php` 是 `add(1, 2);` ； `sumTest.php` 是 `sum($arr);` 
2.  一定會有驗證的方法，此兩例都是用 `var_dump()` 取得數值並做人工驗證
3.  可能會有安排初始化輸入值的過程。值得一提的是，雖然上面兩例都有輸入值，但如果直接執行 `sum()` 是會回傳 0 的。因此還需要另外測試這段程式碼：
    ```php
    <?php
    
    include 'sum.php';
    
    $sum = sum();
    
    var_dump($sum); // will see 0
    ```
    而這個測試案例就沒有輸入值

那什麼是 3A 原則呢？ 3A 的全名為： `Arrange` `Act` `Assert` 這三個步驟是寫單元測試的 pattern ，剛好都是 A 開頭，故稱之為「 3A 原則」。上面的三個共同點剛好對應到 3A 原則，分別為：

* **Arrange:** 初始化的過程
* **Act:** 執行測試的目標，並取得實際結果
* **Assert:** 驗證結果

因為一個測試案例應該至少會有後面兩個明顯的階段，所以我習慣上會把這幾個區塊用註解分開，並且會想辦法讓測試目標和結果變比較清楚點，如 `addTest.php` 會改寫成這樣：

```php
<?php
// addTest.php

include 'add.php';

// Arrange
$x = 1;
$y = 2;
$excepted = 3;

// Act
$actual = add($x, $y);

// Assert
if ($actual === $excepted) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}
```

這裡 Assert 也懶得人工確認了，改成直接講正確或錯誤就好。

## Class

有了 3A 原則，要測 class 其實應該就有方向了。舉個例子，有一個 class 如下：

```php
<?php
// number.php

class Number
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
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
}
```

雖然測試程式的執行順序是 `Arrange` `Act` `Assert` ，但首先我們可以先思考要測什麼。從這個例子， `get()` 應該是蠻明顯想測的目標， new 物件給數字 `get()` 就會拿回來，可以測是不是真的有拿回來。所以先這樣寫：

```php
<?php
// numberTest.php

include 'number.php';

// Act
$target = new Number($number);
$actual = $target->get();
```

再來驗證的方法也很明確，跟傳入的 `$number` 一樣即可：

```php
<?php
// numberTest.php

include 'number.php';

// Act
$target = new Number($number);
$actual = $target->get();

// Assert
if ($actual === $number) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}
```

最後會發現只差 `$number` 定義好就可以跑了， `$number` 因為在寫 Act 時，就會很清楚該丟什麼進去，此例是實數。最後再整理一下程式碼：  


```php
<?php
// numberTest.php

include 'number.php';

// Arrange
$number = 10;
$excepted = $number;

// Act
$target = new Number($number);
$actual = $target->get();

// Assert
if ($actual === $excepted) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}
```

其他測試也依續補上：


```php
<?php
// numberTest.php

include 'number.php';

// Arrange
$number = 10;
$excepted = $number;

$addend = 10;
$exceptedAdd = 20;

$subtrahend = 5;
$exceptedSub = 5;

// Act
$target = new Number($number);
$actual = $target->get();

// Assert
if ($actual === $excepted) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}

// Act
$target = new Number($number);
$actual = $target->add($addend);

// Assert
if ($actual === $exceptedAdd) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}

// Act
$target = new Number($number);
$actual = $target->sub($subtrahend);

// Assert
if ($actual === $exceptedSub) {
    echo 'test add OK';
} else {
    echo 'test add Fail';
}
```

以上是土法煉鋼的測試寫法。大家可以思考一下，其實跟平常手動測試功能的想法是一樣的：

1. 一開始一定會想測一個功能，比方說會員列表 (Act)
2. 再來一定會思考會員列表應該會長什麼樣，應該會是表格一列一列的樣子 (Assert)
3. 打開發現沒有東西，知道是因為資料庫還沒有資料，所以就去新增資料 (Arrange)
4. 最後再打開一次，確認有資料，於是測試就通過了

## 今日回顧

* 3A 原則是測試的 pattern
* 有了 3A 原則，思考如何寫測試程式會比較清楚

測試程式知道該如何寫了，可是照上面的說法做的話，不就到處都有檔案嗎？到底該如何管理呢？因此，明天該是我們發揮美德的時候了！

下一篇：[讓我們繼續懶下去][]

## 相關連結

* [動手寫 Unit Test][] | In 91 30天快速上手TDD

[動手寫 Unit Test]: https://dotblogs.com.tw/hatelove/2012/11/07/learning-tdd-in-30-days-day3-how-to-write-a-unit-test-code

[讓我們繼續懶下去]: /docs/day08.md
