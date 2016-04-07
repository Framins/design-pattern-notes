# Classical / Modern Extend Pattern

class extends class / object extends object

Recommend Book: [Learning Javascript Design Patterns](http://www.books.com.tw/products/0010538538)

> The prototype pattern focuses on creating an object that can be used as a blueprint for other objects through prototypal inheritance. This pattern is inherently easy to work with in JavaScript because of the native support for prototypal inheritance in JS.

> Ref: [The Prototype Pattern](https://carldanley.com/js-prototype-pattern/)

## Default

```
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

**Disadvantage**

* no need all props from `this`
* pass parameters to child will cost too much

```
function Child(name) { this.name = name; }
```

* props which child owned refer to its parent not independent

## Borrowing Construct And Prototype Chain

```
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

**Disadvantage**

* parent constructor has been called twice

## Shared Prototype And Proxy Constructor (Holy Grail)

```
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

## Prototypal Inheritance

```
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

**ES5**

```
var parent = { name: "John" };
var child = Object.create(parent, { age: { value: 2 } });
console.log(child.hasOwnProperty("age")); // true
```

**ES6**

```
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

**Shallow Copy**

Copying object or array in shallow copy will only make a reference to parent, so if you change object value of the child side, the other side will change at the same time.

```
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

Deep copy will make copy for every properties include object and array.

```
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

```
// borrow method from array
function f() {
	return [].join.call(arguments, "+");
	// line above is an equivalent to return [].join.apply(arguments, ["+"]);
}
f(1,2,3,4,5);

// bind method to object
function bind(object, method) {
  return function () {
    return method.apply(object, [].slice.call(arguments));
  };
}
```

**Borrowing Method**

```
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

```
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

```
var p = new Parent();
class Parent {}
```

> Ref: [Classes - MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Classes#Hoisting)

**Lodash Related Functions**

* _.create // prototypal inheritance
* _.mixin // mix-in to lodash
* _.assign // shallow copy
* _.merge // deep copy
