# Factory Method Pattern

簡單來說，工廠方法就是 new 一個物件的替代品，將物件的生產包裝成一個方法

## Story

在一個購物系統中，原本只有個會員類別(Member class)

```php
class Member
{

}
```

某一天～行銷老大說～我發現某些人消費金額特別多，我們來給這些會員另一種身份等級－－叫他黃金會員好了，然後他們有特殊的優惠折扣，這樣他們就會買更多東西。

於是ＲＤ想想 ... ，我只要加個 type 區別身份，再加個 getDiscount 給折扣就可以啦～～～～

```php
class GoldMember
{
    public function getType()
    {
        return "gold";
    }

    public function getDiscount()
    {
        return 0.8;
    }
}
```

然後在需要建立物件時只要多個判斷就好拉～～～

```php
if ($typeIsGold) {
    new GoldMember();
} else {
    new Member();
}
```

## Problem

* `new Member()` 的程式不只一個地方
*  日後公司又要增加一種會員等級

## Solution

```php
interface IUser
{
    public function getType();
    public function getDiscount();
}

class Member implements IUser
{
    public function getType()
    {
        return "Normal";
    }

    public function getDiscount()
    {
        return 1;
    }
}

class GoldMember implements IUser
{
    public function getType()
    {
        return "Gold";
    }

    public function getDiscount()
    {
        return 0.8;
    }
}

abstract class AbstractUserFactory
{
    /**
     * @param String $type 會員類型
     * @return IUser 會員的實體
     */
    abstract public function createUser($type);
}

class UserFactory extends AbstractUserFactory
{
    public function createUser($type)
    {   
        $user = null;
        switch ($type) {
            case 'normal':
                $user = new Member();
                break;
            case 'gold':
                $user = new GoldMember();
                break;
            default:
                throw new Exception('Unknown Member Type');
        }
        return $user;
    }
}
```

Client 怎麼使用

```php
class Client
{
    public function main()
    {
        $userFactory = new UserFactory();
        $member = $userFactory->createUser('normal');
        echo PHP_EOL, "Call me ", $member->getType(), ' user!!', PHP_EOL;

        $userFactory = new UserFactory();
        $goldMember = $userFactory->createUser('gold');
        echo PHP_EOL, "Call me ", $goldMember->getType(), ' user!!', PHP_EOL;
    }
}
```

## Intent

> Define an interface for creating an object, but let subclasses decide which class to instantiate.
  Factory Method lets a class defer instantiation to subclasses.
>
> 定義一個用於創建物件的介面，讓子類別決定要實例化哪個類別。工廠方法讓一個類別的實例化工作遞延到其子類別。

## UML

![uml](http://i.imgur.com/zrdVaa8.png)

工廠模式的主要四個角色：

* *Product：抽象產品類別, 定義產品通性 。*
* *ConcreteProduct：具體實作 Product 。*
* *Creator：抽象化工廠, 宣告產生 Product 的抽象方法。*
* *ConcreteCreator：實作工廠, 回傳實體 ConcreteProduct 物件。*

### Context

* 建立者(Creator)無法預期將產生何種物件，並希望其子類別來決定生產何種物件時。
* 當類別將權力下放給一個或多個子類別，你又希望將「交付給哪些子類別」的知識集中在一處時。
* 需要靈活、可拓展的框架時。例如常應用於驅動程式設置，使用JDBC連接資料庫，切換資料庫只需修改驅動名稱。

## Extend

* 多態性的喪失和模式的退化：一般來說，工廠對象應當有一個抽象的父類型，如果工廠等級結構中只有一個具體工廠類的話，抽象工廠就可以省略，
  也將發生了退化。當只有一個具體工廠，在具體工廠中可以創建所有的產品對象，並且工廠方法設計為靜態方法時，工廠方法模式就退化成Simple Factory模式。
* 多個工廠方法：在抽象工廠角色中可以定義多個工廠方法，從而使具體工廠角色實現這些不同的工廠方法，這些方法可以包含不同的業務邏輯，
  以滿足對不同的產品對象的需求。
* 延遲初始化(Lazy initialization)、產品對象的重復使用：工廠對象將已經創建過的產品保存到一個集合（如數組、List等）中，
  然後根據客戶對產品的請求，對集合進行查詢。也就是說在Map容器中，如果有符合要求的產品對象，就直接取出返回；
  如果沒有，那麼就創建一個新的，然後放到集合中，再返回給客戶端。EX.限制產品類別最大實例化數量，可透過Map物件數量控管，例如資料庫最大連線數設定。

## Sample

日誌記錄器（多個工廠)  

![diary](http://i.imgur.com/tUFZtZ4.jpg)

Lazy initialization  

![lazy](http://i.imgur.com/oYK8Zlb.png)

## Advantages

* 良好封裝性、清晰代碼架構：工廠方法創建客戶需要的具體產品物件，客戶只需關心所需產品對應的工廠，無須關心創建細節，
  甚至無須知道具體產品類別的類名，降低模組間耦合。
* 屏蔽產品類別：產品類別實作如何變化，呼叫者都不必擔心，只需關心產品介面/抽象類別。
* 優秀的拓展性、角色多態性：工廠模式在系統中增加新產品時，只需要修改具體工廠類別和添加一個具體產品即可。
  符合”開閉原則“(「開」:對於組件功能的擴展是開放的；「閉」:對於原有代碼的修改是封閉的)。

## Disadvantages

* 在添加新產品時，需要编寫新的具體產品類，而且有時還要提供與之對應的具體工廠類，系统中class數量增加，在一定程度上增加系统複雜度，效能開销。
* 考慮到系统的可拓展性，需要引入抽象層，在客户端代碼中均使用抽象層進行定義，增加了系统的抽象性和理解難度，
  且在實現時可能需要用到DOM、反射等技術，增加系统實現難度。

## More link

* [Bank Sample](https://sites.google.com/site/stevenattw/design-patterns/factory-method)
* [Maze Game](http://www.eli.sdsu.edu/courses/spring98/cs635/notes/creational/creational.html)
