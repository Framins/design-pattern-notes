# Builder Pattern


# Intent

> Separate the construction of a complex object from its representation so thatthe same construction process can create different representations.Parse a complex representation, create one of several targets.

> 分散一個複雜結構的物件，使相同的構建過程可以建立不同的樣式。解析複雜的表示，創建幾個目標之一。

> Ref: [Builder Design Pattern](https://sourcemaking.com/design_patterns/builder)


# UML
![common](http://i.imgur.com/Hr6IgKy.png)
# Example

![representation image](http://i.imgur.com/MTbIZou.png)

```
<?php
class Product
{
    public $_name;
    public $_account;
}
abstract class foodBuilder
{
    protected $_product;
    protected $_food;
    protected $_account;
    public function __construct()
    {
        $this->_product = new Product();
    }
    public abstract function setMeal();
    public abstract function setAccount();
    public abstract function getResult();
}
class hamburgerBuilder extends  foodBuilder
{
    protected $_food = 'hamburger';
    protected $_account = '50';
    public function setMeal()
    {
        $this->_product->_name = $this->_food;
    }
    public function setAccount()
    {
        $this->_product->_account = $this->_account;
    }
    public function getResult()
    {
        return $this->_product;
    }
}
class cokeBuilder extends foodBuilder
{
    protected $_food = 'coke';
    protected $_account = '20';
    public function setMeal()
    {
        $this->_product->_name = $this->_food;
    }
    public function setAccount()
    {
        $this->_product->_account = $this->_account;
    }
    public function getResult()
    {
        return $this->_product;
    }
}
class CashierDirector
{
    public function build(foodBuilder $builder)
    {
        $builder->setMeal();
        $builder->setAccount();
        return $builder->getResult();
    }
}
$director = new CashierDirector();
print_r($director->build(new hamburgerBuilder));
print_r($director->build(new cokeBuilder));

```


#適用場景

* Product需具有複雜的內部結構。
* 需要生成的Product對象的屬性相互依賴，builder pattern可以強迫生成順序。
* 在對象創建過程中會使用到系統中的一些其它對象，這些對像在產品對象的創建過程中不易得到。

#使用效果

* builder pattern使得產品的內部表象可以獨立的變化。builder pattern可以使客戶端不必知道產品內部組成的細節。
* 每一個Builder都相對獨立，而與其它的Builder無關。
* 模式所建造的最終產品更易於控制。


#優點

builder pattern可以很好的將一個對象的實現與相關的“業務”邏輯分離開來，從而可以在不改變事件邏輯的前提下，使增加(或改變)實現變得非常容易。

#缺點

builder接口的修改會導致所有執行類的修改。


> Ref:
> * [Builder Design Pattern](https://sourcemaking.com/design_patterns/builder)
> * [設計模式詳解](http://yansu.org/2014/04/19/design-patterns-of-php.html)
> * [愛T-blog-php 建造者生成器模式](http://blog.itiwin.cn/php-builder-pattern.html)

