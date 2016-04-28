# Memo pattern

### 用途
將物件狀態恢復到某動作前
```
class Player {
    constructor (username) {
        this.username = username;
        this.cash = 1000;
    }

    bet (betAmount) {
        this.cash -= betAmount;
    }

    getSnapShot () {
        const snapShot = new PlayerSnapshot();
        snapShot.username = this.username;
        snapShot.cash = this.cash;
        return snapShot;
    }

    undoSnapShot (snapShot) {
        this.username = snapShot.username;
        this.cash = snapShot.cash;
    }

    toString () {
        return this.username + '有額度' + this.cash;
    }
}

/**
 * 純粹的Data儲存物件
 */
class PlayerSnapshot {
    constructor () {
        this.username = '';
        this.cash = '';
    }
}

/**
 * 處理Data快照
 */
class PlayerCaretaker {
    constructor () {
        this.cache = {};
    }

    setSnapShot (snapshot) {
        this.cache[snapshot.username] = {
            username: snapshot.username,
            cash: snapshot.cash
        };

        // 此處可以儲存到cache db
    }

    getSnapShot (username) {
        return this.cache[username];
    }
}

// 對於client來講 不需要知道細節(回復的屬性)為何
// client僅需要知道何時需要回復，即著重在流程及商業邏輯
const eric7578 = new Player('eric7578');
console.log('開始時...');
console.log(eric7578.toString());

// 執行快照
const careTaker = new PlayerCaretaker();
careTaker.setSnapShot(eric7578.getSnapShot());

eric7578.bet(200);
console.log('下注後...');
console.log(eric7578.toString());

console.log('SERVER炸裂！！');
eric7578.undoSnapShot(careTaker.getSnapShot(eric7578.username));
console.log(eric7578.toString());
```

### 和命令模式的差異？
協同工作，可以在命令模式內產生undo的命令物件

```
// robots.txt 處理
function CommandRobotstxt() {
    return function (path) {
        console.log(path, '.........')
        if (path === 'http://you.shall.not.pass') {
            return false;
        } else {
            return path;
        }
    }
}

class CrawlerSnapShot {
    setSnapShot (snapshot) {
        // 一樣 可以放入cache db
        this.state = snapshot;
    }
    getSnapShot () {
        return this.state;
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

    getState () {
        return this.path;
    }

    undoState (state) {
        this.path = state;
    }

    runCommands () {
        for (let i = 0; i < this.command.length; i++) {
            this.path = this.command[i](this.path);
        }
    }

    runOnPath () {
        if (this.path) {
            console.log('抓取' + this.path);
        } else {
            console.log('不會抓取' + this.path);
        }
    }
}
console.log("退回但是狀態被回覆的爬蟲...");
const crawlerRedo = new Crawler("http://you.shall.not.pass");
crawlerRedo.addCommand(CommandRobotstxt());

const snapShot = new CrawlerSnapShot();
snapShot.setSnapShot(crawlerRedo.getState());
crawlerRedo.runCommands();

crawlerRedo.runOnPath();

// 回覆url
crawlerRedo.undoState(snapShot.getSnapShot());
crawlerRedo.runOnPath();
```

### 感想
1. 命令模式比較像是命令間的rollback
2. 備忘錄模式是針對物件整體狀態的rollback

### Definition
![備忘錄模式](http://images.cnitblog.com/blog/684470/201412/311438364504600.png)

##### Originator
要被快照，記錄狀態的物件 (Crawler)
##### Memento 備忘錄  
快照物件，可以被轉送的data (crawler.path)
##### Caretaker  
負責取得，緩存快照物件 (CrawlerSnapShot)
