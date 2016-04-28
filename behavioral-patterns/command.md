# Command pattern

### Story
從前從前，有一隻爬蟲他的使命就是爬出yahoo首頁的資料
```javascript
class Crawler {
    runOnPath (path) {
        console.log("抓" + path + "內的資料");
    }
}

const crawler = new Crawler();
crawler.runOnPath("http://tw.yahoo.com");
```
但是某天J...immy說，聽過robots.txt？他是禁止爬蟲進入某些頁面的一份文件。  
幫我加一下，應該很快吧。
```javascript
class Crawler {
    setRobots (txt) {
        console.log("分析robots.txt");
    }

    checkRobots (path) {
        console.log("檢查robots.txt規則");
    }

    runOnPath (path) {
        console.log("抓" + path + "內的資料");          
    }
}

const url = "http://tw.yahoo.com";
const crawler = new Crawler();
crawler.setRobots(url + "/robots.txt");
crawler.checkRobots(url);
crawler.runOnPath(url);
```
結果某天J..ack說，你抓取網頁速度太快了！我要隨機分佈！隨機一個數字，應該很快。  
後天J...ohn又說，你每抓一個網頁，請記錄一筆資訊！寫一筆資料，應該很快。  
大後天J...query又說....應該很快  
爬蟲表示：

### Problem
請求一直來 一直加程式 程式變得混亂
請求，處理請求，各自獨立？  

### Solution
```javascript
// robots.txt 處理
function CommandRobotstxt() {
    return function (path) {
        console.log("分析robots.txt  " + path + "/robots.txt");
        console.log("檢查robotstxt 是否符合規則");
    }
}

// 隨機分佈秒數
function CommandRandSeconds () {
    return function (path) {
        // 此處例子是舉同步處理的例子，並非真的等待
        console.log("隨機等待一個秒數");
    }
}

// Log
function CommandLogger () {
    return function (path) {
        console.log("記錄歷程" + path);
    }
}

// 爬取次數+1
function CommandNumCrawling () {
    return function (path) {
        console.log("爬取次數 + 1");
    }
}

class Crawler {
    constructor (path) {
        this.path = path;
        this.command = [];
    }

    addCommand (cmd) {
        this.command.push(cmd);
    }

    undoCommand () {
        this.command.pop();
    }

    runOnPath () {
        for (let i = 0; i < this.command.length; i++) {
            let cmd = this.command[i];
            cmd(this.path);
        }
    }
}

// 正常的爬取一個頁面
// 讓client更客製化的定義想要的command 不需要被 ***一 堆 的 if else*** 綁住
const url = "http://tw.yahoo.com";

console.log("正常爬行的爬蟲...");
const crawler = new Crawler(url);
crawler.addCommand(CommandRobotstxt());
crawler.addCommand(CommandRandSeconds());
crawler.addCommand(CommandLogger());
crawler.runOnPath();

// 滾回Command
console.log("\n有被退回的爬蟲...");
const crawlerUndo = new Crawler(url);
crawlerUndo.addCommand(CommandRobotstxt());
crawlerUndo.addCommand(CommandRandSeconds());
crawlerUndo.addCommand(CommandLogger());
crawlerUndo.addCommand(CommandNumCrawling());
if (true) {
    // 因為一些原因...所以要取消
    crawlerUndo.undoCommand();
}
crawlerUndo.runOnPath();
```
好der 爬蟲寫完了 看一下標準的Command Pattern定義  

### Definition
![命令模式](https://www.safaribooksonline.com/library/view/learning-javascript-design/9781449334840/httpatomoreillycomsourceoreillyimages1326904.png)

##### Receiver
接收命令的物件，主要就是執行命令的人，此例中就是path string, 因為透過各種command function 可以對path進行加工(ex: robotstxt裡面的修整刪選..等等)
##### Command  
命令物件的樣板，範例內未有，但是每個Command function回傳的皆可視為此物件之子類別
##### CocreteCommand  
實作命令，其實就是Command function 回傳的物件
##### Invoker  
存放命令佇列


### Wrap up  
Command Pattern 目的
1. 方便log
2. 允許Redo  
(書上說der, 私以為有點牽強，因為書上只用了一段console.log("Undo") 就呼嚨過去了RRRRR)
3. 命令物件，執行者兩者角色分離。比較好加功能。

> 敏捷開發原則：不要為程式碼加上猜測，或者是可能需要的功能。如果不清楚是否需要命令模式。就不要急著實作。只有在真正需要如取消/恢復操作，或者是有龐大命令佇列需求的時候再將程式重構為命令模式。
