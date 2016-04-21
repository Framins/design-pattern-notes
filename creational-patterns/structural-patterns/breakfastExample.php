<?php

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