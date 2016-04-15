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
