# Decorator pattern
> Design Pattern: Decorator Pattern in PHP

Story
-------------
從前從前... 有一間早餐店

![早餐店UML](http://i.imgur.com/G3acLZf.png)

假設一間早餐店的菜單上面有四樣餐點，菜單是抽象類別，早餐店提供的餐點都繼承它，方法是取得成本。

早餐店老闆想要改變菜單，在主食加上不同的配料。

ex. 漢堡夾蛋、漢堡夾肉、吐司加火腿、吐司加起司..

Problem
-------------
如果都繼承菜單抽象類別，會導致類別爆炸。

![類別爆炸UML](http://i.imgur.com/9K7desp.png)

***

Intent
-------------
> 想要對一個物件加不同的功能。

Introduction
-------------

> - 裝飾者模式可以對一個物件更有彈性的加上功能。
> - 動態地給一個物件附加一些額外的職責，同時保持相同的介面。
> - 就擴充功能來說，裝飾者提供了繼承機制的彈性方案。

UML
-------------
![裝飾者模式UML](http://i.imgur.com/agdSmRa.png)

> - component: 核心角色，抽象元件，是介面或是抽象類別，定義我們最核心的物件。
> - concreteComponet: 被裝飾者角色，具體元件，實作抽象元件。
> - decorator: 裝飾者角色，實作元件介面或是抽象方法。
> - concreteDecorator: 具體裝飾者角色，實作裝飾者。

早餐店UML
-------------
![早餐店UML](http://i.imgur.com/pGZu6Sl.png)

How to work
-------------
![包飯糰](http://i.imgur.com/tiNUQBE.png)

> 1. 拿一個吐司的主食物件
> 2. 以起司物件裝飾
> 3. 以火腿物件裝飾
> 4. 呼叫cost()方法，並依賴委派(delegate)將配料的價格加上去

Example
-------------

```php
/**
 * 早餐店菜單的抽象類別(component)
 */
abstract class BreakfastComponent
{
    protected $name;

    // 子類別實作，取得描述
    public abstract function getName();

    // 子類別實作，取得成本
    public abstract function getCost();
}
```

```php
/**
 * 主食吐司具體實作(concreteComponent)
 */
class Toast extends BreakfastComponent
{
    public function __construct()
    {
        $this->name = 'toast';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCost()
    {
        return 10;
    }
}
```

```php
/**
 * 主食漢堡具體實作(concreteComponent)
 */
class Hamburger extends BreakfastComponent
{
    public function __construct()
    {
        $this->name = 'Hamburger';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCost()
    {
        return 20;
    }
}
```

```php
/**
 * 配料的抽象類別(decorator)
 * 聯繫元件介面的連結
 */
abstract class CondimentDecorator extends BreakfastComponent
{
    public function __construct(BreakfastComponent $breakfast)
    {
        $this->name = $breakfast;
    }
}
```

```php
/**
 * 配料Cheese的具體實作(concreteComponent)
 */
class Cheese extends CondimentDecorator
{
    public function getName()
    {
        return $this->name->getName() . ' add Cheese';
    }

    public function getCost()
    {
        return 10 + $this->name->getCost();
    }
}
```

```php
/**
 * 配料Ham的具體實作(concreteComponent)
 */
class Ham extends CondimentDecorator
{
    public function getName()
    {
        return $this->name->getName() . ' add Ham';
    }

    public function getCost()
    {
        return 15 + $this->name->getCost();
    }
}
```

```php
/**
 * Client
 */
// 點吐司
$toast = new Toast();
var_dump('餐點:' . $toast->getName());
var_dump('價格:' . $toast->getCost() . '元');

// 點吐司加起司
$toastAddCheese = new Cheese($toast);
var_dump('餐點:' . $toastAddCheese->getName());
var_dump('價格:' . $toastAddCheese->getCost() . '元');

// 點吐司加火腿
$toastAddHam = new Ham($toast);
var_dump('餐點:' . $toastAddHam->getName());
var_dump('價格:' . $toastAddHam->getCost() . '元');

// 點吐司加起司加火腿
$toastAddCheese = new Cheese($toast);
$toastAddCheeseAddHam = new Ham($toastAddCheese);
var_dump('餐點:' . $toastAddCheeseAddHam->getName());
var_dump('價格:' . $toastAddCheeseAddHam->getCost() . '元');
```

Advantages and Disadvantages
-------------
> - Advantages:
> 彈性動態擴充功能，不影響原本的程式。
> - Disadvantages:
> 裝飾者會導致程式中出現很多小類別，導致程式變複雜。

When
-------------
> 想在既有的物件上添加一些功能，而不希望影響其他物件。
> 藉由Decorator模式，你可以先建立核心功能，之後在裝飾這些核心，為客戶加上特殊功能。

Discussion
-------------
> 公司產品適用地方？

Reference
-------------
[設計模式（九）裝飾者模式（Decorator Pattern)](http://www.zendei.com/article/6281.html)

[PIN Blog 裝飾者模式筆記](https://dotblogs.com.tw/pin0513/archive/2010/01/04/12779.aspx)

<書籍>深入淺出：設計模式

<書籍>PHP設計模式

<書籍>設計模式之禪
