# Classical / Modern Extend Pattern

Classical 在此並不是指傳統或者是經典，而是書中作者用來代表與 Class 相關的意思。而 Modern 則沒其他用意，就是指現在常用的意思。

Recommend Book: [Learning Javascript Design Patterns](http://www.books.com.tw/products/0010538538)

推薦書籍：[Javascript 設計模式](http://www.books.com.tw/products/0010538538)

> The prototype pattern focuses on creating an object that can be used as a blueprint for other objects through prototypal inheritance. This pattern is inherently easy to work with in JavaScript because of the native support for prototypal inheritance in JS.

> Prototype Pattern 旨在於利用原型繼承的概念將物件藍圖化並藉此創建新物件，而原生的 Javascript 便帶有原型繼承，因此在操作及說明上相對簡單許多。

> Ref: [The Prototype Pattern](https://carldanley.com/js-prototype-pattern/)


## Default
預設用法

```javascript
function inherit(Child, Parent) {
	Child.prototype = new Parent();
}

function Parent(name) { this.name = name || 'Adam'; }
Parent.prototype.say = function() { return this.name; }
function Child(name) {}
inherit(Child, Parent);
var kid = new Child();
kid.say();
```

Child extends both `this` and prototype props from parent.

子物件同時繼承了來自父物件的 `this` 以及 prototype 中的所有屬性及方法。

**Disadvantage**

* no need all props from `this`
* pass parameters to child will cost too much

```javascript
function Child(name) { this.name = name; }
```

* props which child owned refer to its parent not independent

**缺點**

* 會繼承到 `this` 中許多不必要的屬性。
* 傳參數給子物件時相對較麻煩，需重新定義子物件。
* 子物件所擁有的屬性是參考其父物件，並不是獨立複製出來的。

## Borrowing Construct And Prototype Chain

借用建構式及原型鏈

```javascript
function Parent(name) { this.name = name || 'Adam'; }
Parent.prototype.say = function() { return this.name; }

function Child(a, b, c, d) {
	Parent.apply(this, arguments);
}
Child.prototype = new Parent();

var kid = new Child("James");
console.log(kid.name);
console.log(kid.say());
delete kid.name;
console.log(kid.say());
```

Child will get independent props from parent and also refer to parent prototype.

預設用法的加強版，子物件將會取得來自父物件的屬性且是獨立非參考，另外也會參考其父物件的原型。

**Disadvantage**

* parent constructor has been called twice

```javascript
Parent.apply(this, arguments); // <--

Child.prototype = new Parent(); // <--
```

**缺點**

* 父物件被建構兩次。

## Shared Prototype And Proxy Constructor (Holy Grail)

共享原型及代理建構式（聖杯模式）

```javascript
var inherit = (function() {
    var Proxy = function() {};
    return function(Child, Parent) {
        Proxy.prototype = Parent.prototype;
        Child.prototype = new Proxy();
        Child.uber = Parent.prototype;
        Child.prototype.constructor = Child;
    }
}());

function Parent() {}
function Child() {}
inherit(Child, Parent);
var kid = new Child();
kid.constructor.name
```

Creating a temporary constructor which prototype referred by child share prototype with parent.

建立一個暫時的代理建構式與父物件共享父原型，其代理原型則由子物件參考。

## Prototypal Inheritance

原型繼承

```javascript
function object(o) {
    function F() {}
    F.prototype = o;
    return new F();
}

var parent = { name: "John" };
var child = object(parent);
console.log(child.name); // "John"
```

Just like holy grail pattern, creating an empty object and its prototype refer to parent, and return a new instance which created by this empty object.

如同聖杯模式，建立一個空物件其原型參考自父物件，並回傳一個以其為藍圖產生的實體。

**ES5**

```javascript
var parent = { name: "John" };
var child = Object.create(parent, { age: { value: 2 } });
console.log(child.hasOwnProperty("age")); // true
```

**ES6**

```javascript
class Parent {
  constructor() {
    this.name = "John";
  }
}
class Child extends Parent {
  constructor(age) {
    super();
    this.age = { value: age };
  }
}
var child = new Child();
console.log(child.name, child.age("18")); // "John 18"
console.log(child.hasOwnProperty("name")); // true
console.log(child.hasOwnProperty("age")); // true
```

## Implement Extend With Duplicating Properties

利用複製屬性實現繼承

**Shallow Copy**

Copying object or array in shallow copy will only make a reference to parent, so if you change object value of the child side, the other side will change at the same time.

**淺複製**

淺複製對於物件（包含陣列）的複製僅利用參考的方式，因此如果在 child 處變更物件的值，相對地 parent 處也會受到影響。

```javascript
function extend(parent, child) {
  var i;
  child = child || {};
  for (i in parent) {
    if (parent.hasOwnProperty(i)) {
      child[i] = parent[i];
    }
  }
  return child;
}
var parent = { name: "John", job: ["Backend", "Frontend"] };
var child = extend(parent);
child.job.push("PO");
console.log(parent.job, child.job);
```

**Deep Copy**

Deep copy will make copy for every properties include object and array by recursion.

**深複製**

深複製對物件（包含陣列）則會使用遞迴的方式將其屬性一一完整複製。

```javascript
function extendDeep(parent, child) {
  var i,
  toStr = Object.prototype.toString,
  astr = "[object Array]";
  
  child = child || {};
  
  for (i in parent) {
    if (parent.hasOwnProperty(i)) {
      if (typeof parent[i] === "object") {
        child[i] = (toStr.call(parent[i]) === astr) ? [] : {};
        extendDeep(parent[i], child[i]);
      } else {
        child[i] = parent[i];
      }
    }
  }
  return child;
}
var parent = { name: "John", job: ["Backend", "Frontend"] };
var child = extendDeep(parent);
child.job.push("PO");
console.log(parent.job, child.job);
```

## Method Borrowing And Binding

方法借用及綁定

```javascript
// borrow method from array
// 從陣列借用 join 方法
function f() {
	return [].join.call(arguments, "+");
	// line above is an equivalent to return [].join.apply(arguments, ["+"]);
}
f(1,2,3,4,5);

// bind method to object
// 綁定方法至物件
function bind(object, method) {
  return function () {
    return method.apply(object, [].slice.call(arguments));
  };
}
```

**Borrowing Method**

借用方法

```javascript
// ES5
var parent = { name: "John", say: function (greet) { return greet + ", " + this.name; } };
var child = { name: "Jacob" };
var childSay = bind(child, parent.say);
console.log(childSay("Yo")); // "Yo, Jacob"

// ES6
class Parent {
  constructor() {
    this.name = "John";
  }
  say(greet) {
    return greet + ", " + this.name;
  }
}
class Child {
  constructor(age) {
    this.name = "Jacob";
  }
}
let p = new Parent();
let c = new Child();
c.say = p.say.bind(c);
console.log(c.say("Yo"));
```

**Binding Method**

綁定方法

```javascript
// ES5 Class
React.createClass({
  onClick: function(event) {},
  render: function() {
    return <button onClick={this.onClick} />;
  }
});

// ES6 Class
class Counter extends React.Component {
  constructor() {
    super();
    this.tick = this.tick.bind(this);
  }
  tick() {}
}

// ES6 Arrow Function & ES7 Class Properties
class Counter extends React.Component {
  tick = () => {}
}

// ES7 Bind Operator
class Counter extends React.Component {
  onClick(event) {}
  render() {
    return <button onClick={::this.onClick} />;
  }
}

// ES7 Decorator
class Counter extends React.Component {
  @autobind
  tick() {}
}
```

> Ref:
>
> * [New in React v0.4: Autobind by Default](https://facebook.github.io/react/blog/2013/07/02/react-v0-4-autobind-by-default.html)
> * [React v0.13.0 Beta 1](https://facebook.github.io/react/blog/2015/01/27/react-v0.13.0-beta-1.html#autobinding)
> * [Classical and Functional React Get Married and Bind the Knot in a Decorated Wedding](https://medium.com/@gilbox/classical-and-functional-react-get-married-and-have-a-baby-7acf5d0cf00e#.mp1s26szb)
> * [autobind decorator](https://github.com/andreypopp/autobind-decorator)

**Classes Are Not Hoisted**

類別並不會提升

```javascript
var p = new Parent();
class Parent {}
```

> Ref: [Classes - MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Classes#Hoisting)

**Lodash Related Functions**

* _.create // prototypal inheritance
* _.mixin // mix-in to lodash
* _.assign // shallow copy
* _.merge // deep copy