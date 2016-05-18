# Template Method Pattern

# Intent
> Separate the construction of a complex object from its representation so that the same construction process can create different representations.

> 只先定義好演算法的輪廓，某些步驟則留給子類別去填補，以便在不改變演算法整體架構的情況下讓子類別去實作某些步驟。

# UML
![](https://goo.gl/BGSa5k)

# Story
今天要寫一個沖泡系統，泡茶 & 泡咖啡

- 泡`茶`步驟
```
step1. 把水煮沸
step2. 浸泡茶葉
step3. 把茶倒進杯子
step4. 加檸檬or糖
```
- 泡`咖啡`步驟
```
step1. 把水煮沸
step2. 沖泡咖啡粉
step3. 把咖啡倒進杯子
step4. 加牛奶or糖
```
我們可以發現，上述的步驟有不少是雷同的

Example 1
-------------
```php
abstract class brew
{
    /**
     * 沖泡系統
     */
    final public function prepareRecipe()
    {
        $this->boilWater();
        $this->doBrew();
        $this->pourInCup();
        $this->addCondiments();
    }

    protected function boilWater()
    {
        var_dump('煮沸熱水');
    }

    protected function pourInCup()
    {
        var_dump('倒入杯子');
    }

    /**
     * 沖泡/浸泡 功能
     */
    protected abstract function doBrew();

    /**
     * 沖泡/浸泡 功能
     */
    protected abstract function addCondiments();
}

class tea extends brew
{
    protected function doBrew()
    {
        var_dump('浸泡茶葉');
    }

    protected function addCondiments()
    {
        var_dump('加入檸檬');
    }
}

class coffee extends brew
{
    protected function doBrew()
    {
        var_dump('沖泡咖啡粉');
    }

    protected function addCondiments()
    {
        var_dump('加入糖及牛奶');
    }
}
```
執行一下
```
var_dump('顧客點了一杯沖泡茶加檸檬');
$tea = new tea();
var_dump('開始沖泡茶');
$tea->prepareRecipe();
var_dump('完成檸檬綠茶沖泡');

var_dump('==========================');

var_dump('顧客點了一杯咖啡加牛奶及糖');
$coffee = new coffee();
var_dump('開始沖泡咖啡');
$coffee->prepareRecipe();
var_dump('完成拿鐵咖啡沖泡');
```


延伸：當顧客希望不要自動加入檸檬 糖 牛奶，讓其變成選項，可使用hook

Example2
-------------
```php
abstract class brewWithHook
{
    protected $customerWantsCondiments = true;

    /**
     * 沖泡系統
     */
    final public function prepareRecipe()
    {
        $this->boilWater();
        $this->brew();
        $this->pourInCup();
        if ($this->customerWantsCondiments) {
            $this->addCondiments();
        }
    }

    protected function boilWater()
    {
        var_dump('煮沸熱水');
    }

    protected function pourInCup()
    {
        var_dump('倒入杯子');
    }

    /**
     * 沖泡/浸泡 功能
     */
    protected abstract function brew();

    /**
     * 沖泡/浸泡 功能
     */
    protected abstract function addCondiments();
}

class tea extends brewWithHook
{
    protected function brew()
    {
        var_dump('浸泡茶葉');
    }

    protected function addCondiments()
    {
        var_dump('加入檸檬');
    }

    public function setUserNotAddCondiments()
    {
        $this->customerWantsCondiments = false;
    }
}

class coffee extends brewWithHook
{
    protected function brew()
    {
        var_dump('沖泡咖啡粉');
    }

    protected function addCondiments()
    {
        var_dump('加入糖及牛奶');
    }

    public function setUserNotAddCondiments()
    {
        $this->customerWantsCondiments = false;
    }
}
```
執行一下
```
var_dump('顧客點了一杯不加檸檬的沖泡茶');
$tea = new tea();
$tea->setUserNotAddCondiments();
var_dump('開始沖泡茶');
$tea->prepareRecipe();
var_dump('完成茶葉沖泡');

var_dump('==========================');

var_dump('顧客點了一杯黑咖啡');
// 客戶不加牛奶糖
$coffee = new coffee();
$coffee->setUserNotAddCondiments();
var_dump('開始沖泡咖啡');
$coffee->prepareRecipe();
var_dump('完成黑咖啡沖泡');
```

## 適用情境
* 一次性實現一個算法的不變的部分，並將可變的行為留給子類來實現。
* 各子類中公共的行為應被提取出來並集中到一個公共父類中以避免代碼重複。
* 控制子類的擴展。

## 優點
* 模板方法模式在定義了一組算法，將具體的實現交由子類負責。
* 模板方法模式是一種代碼復用的基本技術。
* 模板方法模式導致一種反向的控制結構，通過一個父類調用其子類的操作，通過對子類的擴展增加新的行為，符合“開閉原則”。

## 缺點
* 每一個不同的實現都需要一個子類來實現，導致類的個數增加，是的系統更加龐大。
* 為了提供差異行為，如果讓子類別任意覆寫父類別的方法，則：
    * 父類別與子類別以及不同的子類別之間可能會存在重複程式碼。
    * 繼承架構所形成的程式碼將會不容易被理解。

## 內部使用部分
* 運彩各球類會員設定值限縮功能

## Reference
* [Template 樣板模式](https://goo.gl/IPwTrh)

