export default class Queue {
  constructor() {
    this.numberQueue = 0;
    this.countQueues = 0;
    this.arrayQueues = [];
  }

  addJob(func) {
    this.arrayQueues.push(func);
  }

  start() {
    this.countQueues = _.size(this.arrayQueues);
    if (this.countQueues && this.numberQueue < this.countQueues) {
      this.arrayQueues[this.numberQueue](() => {
        this.numberQueue += 1;
        this.start();
      });
    }
  }
}
