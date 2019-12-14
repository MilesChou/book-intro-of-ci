# 讓我們繼續懶下去

有句話是這麼說的：「懶惰是工程師的美德」。因為懶，所以才會寫出各式各樣強大的工具。是的，今天的標題就是我們的目標。

> 今天開始會介紹一些工具，會以 PHP 為主。

## 測試框架

[昨天][Day 7]有寫出一些陽春的測試程式。雖然夠用，但如果要做進階的測試方法或是分析測試過程，顯然要實作出更多功能才能符合需求。但不用擔心，開源的世界裡有非常多前輩，實作出專為測試使用的框架，讓我們寫測試可以輕鬆很多。

> 開源專案用起來開心的話，不妨去 GitHub 按個 Star 支持一下吧！

PHP 的測試框架裡，我最常使用的是 [Codeception][] ，它是以 [PHPUnit][] 為基礎打造出來的。會常用是因為它支援非常多框架與外掛，可以依個人喜好去調整。

## 建置環境

使用 PHP 要裝套件的話，首推當然是 [Composer][] 。安裝方法的懶人包如下：

```
$ curl -sS https://getcomposer.org/installer | php
$ chmod +x composer.phar
```

這兩個指令會產生可執行的 `composer.phar` ，然後可以直接執行。想要用全域的方式執行需要再下這個指令：（以下會以全域的方式當範例） 

```
$ sudo mv composer.phar /usr/local/bin/composer
```

安裝好後，建個空目錄，進去下指令就能安裝 Codeception 了：

```
$ mkdir -p /path/to/project
$ cd /path/to/project
$ composer require --dev codeception/codeception
```

這個指令會產生 `composer.json` 、 `composer.lock` 兩個檔案與 `vendor` 目錄。目前的結構如下：

```yaml
ProjectRoot:
  - vendor/
  - composer.json
  - composer.lock
```

其中 `composer.json` 是定義這個專案（也就是這個目錄）所需要安裝的套件為何，可以使用 `composer require <vendor>/<package>` 的指令或手動修改 JSON 新增。 `--dev` 參數則是標記此套件是開發階段才會使用。不可能上線了才在測試，一定是開發的時候測，所以 Codeception 會加 `--dev` 參數。

當一切就绪時，可以下這個指令確認是否有安裝成功：

```
$ php vendor/bin/codecept --version
Codeception version 2.2.7
```

有看到版號的話，恭喜你，環境建好了！

## 初始化目錄結構

確定指令可以操作後，接著下這個指令，就會幫你把一開始的目錄都初始化好：

```
$ php vendor/bin/codecept bootstrap
Initializing Codeception in /Users/miles/GitHub/MilesChou/book-intro-of-ci 

File codeception.yml created       <- global configuration
tests/unit created                 <- unit tests
tests/unit.suite.yml written       <- unit tests suite configuration
tests/functional created           <- functional tests
tests/functional.suite.yml written <- functional tests suite configuration
tests/acceptance created           <- acceptance tests
tests/acceptance.suite.yml written <- acceptance tests suite configuration
tests/_output was added to .gitignore
 --- 
tests/_bootstrap.php written <- global bootstrap file
Building initial Tester classes
Building Actor classes for suites: acceptance, functional, unit
 -> AcceptanceTesterActions.php generated successfully. 0 methods added
\AcceptanceTester includes modules: PhpBrowser, \Helper\Acceptance
AcceptanceTester.php created.
 -> FunctionalTesterActions.php generated successfully. 0 methods added
\FunctionalTester includes modules: \Helper\Functional
FunctionalTester.php created.
 -> UnitTesterActions.php generated successfully. 0 methods added
\UnitTester includes modules: Asserts, \Helper\Unit
UnitTester.php created.

Bootstrap is done. Check out /Users/miles/GitHub/MilesChou/book-intro-of-ci/tests directory
```

它做了什麼上面都有寫，這部分就不贅述了。不過有兩個地方我個人習慣上會調整：

1. `.gitignore` 它會新增一行，那行我會刪除
2. 複製檔案 `cp ./tests/_output/.gitignore ./tests/_support/_generated/`

初始化完後，就可以開始執行了

```
$ php vendor/bin/codecept run
```

第一次跑的結果如下：

![First Run][]

當然，我們還沒開始寫測試呢！新增測試的方法如下：

```
$ php vendor/bin/codecept generate:test unit Number
Test was created in /Users/miles/GitHub/MilesChou/book-intro-of-ci/tests/unit/NumberTest.php
```

它會提示說，有新增一個測試檔在 `tests/unit/NumberTest.php` 這裡。裡面預設有一個叫 `testMe()` 的方法，我們在裡面加一個一定會過的測試看看：

```php
<?php

class NumberTest extends \Codeception\Test\Unit
{
    public function testMe()
    {
        $actual = true;
        $this->assertTrue($actual);
    }
}
```

`$this->assertTrue($actual)` 講白話一點指的是：假設 `$actual` 這個變數的內容是 true ，若是 false 的話，假設就錯了。以這個例子，這個假設是永遠正確的。我們來看執行結果：

![Second Run][]

如果看到跟上面一樣結果的話，恭喜你，第一個測試寫好了！

## 正式寫測試程式

首先要設定 `Namespace` ， PHP 的 namespace 可以使用 `composer.json` 設定，設定檔範例如下：

```json
{
    "require-dev": {
        "codeception/codeception": "^2.2"
    },
    "autoload": {
        "psr-4": {
            "HelloWorld\\": "src"
        }
    }
}
```

再來先把昨天的 Number 類別加到專案裡，程式如下： (注意要加 `namespace`)

```php
<?php // src/Number.php

namespace HelloWorld;

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

目錄結構：（注意 `Number.php` 檔案位置）

```yaml
ProjectRoot:
  - src/
    - Number.php
  - tests/
  - vendor/
  - codeception.yml
  - composer.json
  - composer.lock
```

需要注意的是，上面三個都是互有關聯，關聯如下：

* composer.json 檔裡，使用 PSR4 ，設定為 `"HelloWorld\\": "src"` ，代表 `HelloWorld` 的 Namespace 會進來 `src` 找
* `Number.php` 自然就是放在 src 下面了

設定好之後，下 `composer dump-autoload` 會重新產生 autoload 規則檔。只要程式一開始有載入 `vendor/autoload.php` ，之後就可以在任何地方 `new \HelloWorld\Number()` 了。

Codeception 在開始執行前，會先載入 `_bootstrap.php` 做初始化，如載入 `autoload.php` 就是一個可以做的事，它的樣板檔也是這麼說的：

```php
<?php // tests/_bootstrap.php
// This is global bootstrap for autoloading

require __DIR__ . '/../vendor/autoload.php';
```

再來回到剛剛的測什麼都能過的測試檔 `NumberTest.php` ，我們來加第一個真正的測試：

```php
<?php // tests/unit/NumberTest.php

class NumberTest extends \Codeception\Test\Unit
{
    public function testShouldGet1WhenConstructArgIs1()
    {
        // Arrange
        $target = new \HelloWorld\Number(1);
        $excepted = 1;

        // Act
        $actual = $target->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
```

這邊解釋一下：

* Function 名稱習慣上會寫的很口語，如同上面所看到的，因為測試結果看到的都是 function 名稱居多。
* 測試套件通常會提供許多 assert 供選擇，上例是最常用的 `assertEquals()` ，判斷兩個值是否相等。

執行一下，如果看到下面這張圖，代表你第一個測試寫成功了。

![Final Run][]

Codeception 可以整合的功能很多，不過今天就先把單元測試寫好就好。未來測試會一直加上去，但對開發者而言，只要下執行指令，它就會把所有測試都全部跑過，並產生報表說測試有沒有通過，非常地方便。

今天的範例程式可以到[這裡][Sample Code]下載哦！未來還會持續更新，記得可以用 [Git][] 還原版本。

## 今日回顧

* Codeception 環境安裝
* 基本單元測試
* 一鍵執行所有測試

下一篇：[爭什麼！摻在一起做整合測試啊！][]

## 相關連結

[Codeception]: http://codeception.com/
[PHPUnit]: https://phpunit.de/
[Composer]: https://getcomposer.org/
[Git]: https://git-scm.com/
[Sample Code]: https://github.com/MilesChou/book-intro-of-ci/tree/eaab947de9dc1b210d31c0bc64560fbd1060c7a2

[Day 7]: /docs/day07.md
[First Run]: /images/day08-codeception-first-run.png
[Second Run]: /images/day08-codeception-second-run.png
[Final Run]: /images/day08-codeception-final-run.png
[爭什麼！摻在一起做整合測試啊！]: /docs/day09.md