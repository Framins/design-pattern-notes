Proxy Pattern
=============

代理模式的 `代理` ，其實跟平常大家會提到的 `代理伺服器` `代理人` 等概念，是一模一樣的。

### Story

有一份工作叫佈署，佈署需要文件才能執行

```php
interface Worker
{
    // 佈署總是需要文件
    public function deploy($document);
}
```

今天公司只有一個工程師 Alex 會做佈署。

```php
class Alex implements Worker
{
    public function deploy($document)
    {
        echo "文件是這樣的，" . $document;
        echo "已執行完畢";
    }
}
```

但每次佈署文件丟過來，都是東缺西缺的，所以 Alex 決定找個代理人 Miles ，來幫他檢查文件。

```php
class Miles implements Worker
{
    private $worker;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    public function deploy($document)
    {
        $this->checkDocument($document);
        $this->worker->deploy($document);
    }

    public function checkDocument($document)
    {
        echo "文件已檢查";
        return true;
    }
}
```

再來大家要佈署程式，可以自己檢查過再給 Alex ，或是直接找 Miles 處理了。

```php
class Client
{
    public function main()
    {
        $document = "上去 git pull 就對了。";

        echo "直接請 Alex 佈署", PHP_EOL;
        echo "-------------------", PHP_EOL;

        $alex = new Alex();
        $alex->deploy($document);

        echo "-------------------", PHP_EOL;

        echo "找代理人 Miles 佈署", PHP_EOL;
        echo "-------------------", PHP_EOL;

        $miles = new Miles($alex);
        $miles->deploy($document);
    }
}
```

找代理處理事情，一樣可以執行的很好，而且文件也是檢查完成才佈署

### Definition

Proxy Pattern 的定義：

> Provide a surrogate or placeholder for another object to control access to it.

為物件提供代理，以便在代理物件裡控制存取原物件。

### UML

通用類別圖如下：

![](http://plantuml.com/plantuml/png/oymhIIrAIqnELGWkJSfAJIvHgEPI009jXUh4fEAIeiJaabg5eDJ2qjJY4WrDhbgkv9p4ucAWI68EL0DK9A0elwAI2sQ8JOskBeeY50c8MYHf1Hkee8ALWZQIE000)

各角色定義如下：

- Subject: 抽象主題角色，一個普通業務類型的定義
- RealSubject: 具體主題角色，具體實作業務的角色
- Proxy: 代理主題角色，它不負責實作業務，只負責應用具體主題角

代理模式有兩種模式：

#### 普通代理

上述情境即是最基本的 **普通代理** ，任何人都能當 Alex 代理，只要能給 Alex 他要的文件即可。

#### 強制代理

現實有種情況是， PM 不想透過代理人，想直接找 Alex 佈署，但 Alex 說：「我很忙，麻煩找我的代理人處理」。這種情境稱之為 **強制代理** ，具體主題角色只承認它指定的代理。實作如下

```php
class Alex implements Worker
{
    private $proxy;

    public function getProxy()
    {
        $this->proxy = new Miles($this);
        return $this->proxy;
    }

    public function deploy($document)
    {
        if ($this->isProxy()) {
            echo "文件是這樣的，", $document, PHP_EOL;
            echo "已執行完畢", PHP_EOL;
        } else {
            echo "麻煩找我的代理人處理", PHP_EOL;
        }
    }

    public function isProxy()
    {
        return $this->proxy !== null;
    }
}
```

以後要佈署前都要問 Alex 要找哪個代理人才行了

```php
class Client
{
    public function main()
    {
        $document = "上去 git pull 就對了。";

        echo "直接請 Alex 佈署", PHP_EOL;
        echo "-------------------", PHP_EOL;

        $alex = new Alex();
        $alex->deploy($document);

        echo "-------------------", PHP_EOL;

        echo "問 Alex 的代理是誰", PHP_EOL;
        echo "-------------------", PHP_EOL;

        $proxy = $alex->getProxy();
        $proxy->deploy($document);
    }
}
```

### Extensions

代理模式在擴展功能是很方便的，主要有兩種方法。其中之一在故事的例子已經出現：在實作代理物件的同時，加入額外的行為 (檢查文件)

另一種稱之為 **動態代理** ，動態代理的特色是，代理物件的功能是動態產生的。

> 因為是動態的，所以需要利用很多耗效能的反射機制，使用上需多加考量

首先，先寫一個動態代理物件：

```php
class Delegator
{
    private $target;

    public function __construct($target)
    {
        $this->target = new $target();
    }

    public function __call($name, $args)
    {
        $r = new ReflectionClass($this->target);
        if ($method = $r->getMethod($name)) {
            if ($method->isPublic() && !$method->isAbstract()) {
                return $method->invokeArgs($this->target, $args);
            }
        }
    }
}
```

這個物件沒有實作任何東西，看起來不知道能幹嘛。換來看 Client 怎麼使用：

```php
class Client
{
    public function main()
    {
        $document = "上去 git pull 就對了。";

        echo "產生動態代理物件", PHP_EOL;

        $proxy = new Delegator('Ifalo\DP\Proxy\Alex');

        echo "執行佈署", PHP_EOL;

        echo "-------------------", PHP_EOL;

        $proxy->deploy($document);
    }
}
```

Delegator 沒實作，場景沒建構 Alex 物件，可是很神奇的程式卻能執行成功？這就是動態代理！

今天如果想在佈署前檢查文件的話，只要改一下動態代理：

```php
class Delegator
{
    private $target;

    public function __construct($target)
    {
        $this->target = new $target();
    }

    public function __call($name, $args)
    {
        if ($name == 'deploy') {
            echo "文件已檢查", PHP_EOL;
        }

        $r = new ReflectionClass($this->target);
        if ($method = $r->getMethod($name)) {
            if ($method->isPublic() && !$method->isAbstract()) {
                return $method->invokeArgs($this->target, $args);
            }
        }
    }
}
```

上述程式是硬刻在 Delegator 上。實作上會 extract class 並都可以事後在動態代理上調整追加，最終會變成主要物件和其他物件，而整合是由 Client 整合。

以一個購物車的物件為例，可能會有以下物件：

- 客戶購買商品主要商業邏輯的物件
- 代理物件只做呼叫或轉發訊息
- 其他功能如： Log 、會員權限等，與主要商業邏輯無關的物件

利用動態代理的特性，可以一開始專注在主要商業邏輯的實作上，其他功能可以在主功能完成後，再用動態代理的模式無痛追加