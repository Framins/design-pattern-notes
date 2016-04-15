<?php

class Product
{
    public $_name;
    public $_color;
    public $_engine;

    public function show()
    {
        print_r([
            'name'   => $this->_name,
            'color'  => $this->_color,
            'engine' => $this->_engine
        ]);
    }
}

abstract class carBuilder
{
    protected $_product;

    public function __construct()
    {
        $this->_product = new Product();
    }

    public abstract function setColor();
    public abstract function setName();
    public abstract function setEngine();
    public abstract function isCheckAir();

    public function getResult()
    {
        return $this->_product->show();
    }
}

class BMWBuilder extends carBuilder
{
    protected $_name = 'BMW';
    protected $_color = 'black';
    protected $_engine = '2500P';
    protected $_isCheck = false;

    public function setName()
    {
        $this->_product->_name = $this->_name;
    }

    public function setColor()
    {
        $this->_product->_color = $this->_color;
    }

    public function setEngine()
    {
        $this->_product->_engine = $this->_engine;
    }

    public function isCheckAir()
    {
        $this->_isCheck = true;
        return $this->_isCheck;
    }
}

class VolksBuilder extends carBuilder
{
    protected $_name = 'Volkswagen';
    protected $_color = 'white';
    protected $_engine = '1500P';
    protected $_isCheck = false;

    public function setName()
    {
        $this->_product->_name = $this->_name;
    }

    public function setColor()
    {
        $this->_product->_color = $this->_color;
    }

    public function setEngine()
    {
        $this->_product->_engine = $this->_engine;
    }

    public function isCheckAir()
    {
        $this->_isCheck = true;
        return $this->_isCheck;
    }
}

class director
{
    public function build(carBuilder $builder)
    {
        $builder->setName();
        $builder->setColor();
        $builder->setEngine();
        if ($builder->isCheckAir()) {
            return $builder->getResult();
        }
        return null;
    }
}

$director = new director();
$director->build(new BMWBuilder);
$director->build(new VolksBuilder);
