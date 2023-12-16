import { Cron } from "croner";
import format from "date-fns/format";

document.addEventListener('alpine:init', () => {
  Alpine.data('crontab', () => ({
    value: '* 1 * * *',
    format: true,
    nextMultiple: [],
    second: false,
    active: -1,
    randoms: [
        '* * * * *',
        '*/2 * * * *',
        '1-59/2 * * * *',
        '*/3 * * * *',
        '*/4 * * * *',
        '*/5 * * * *',
        '*/6 * * * *',
        '*/10 * * * *',
        '*/15 * * * *',
        '*/20 * * * *',
        '*/30 * * * *',
        '30 * * * *',
        '0 * * * *',
        '0 */2 * * *',
        '0 */3 * * *',
        '0 */4 * * *',
        '0 */6 * * *',
        '0 */8 * * *',
        '0 */12 * * *',
        '0 9-17 * * *',
        '0 0 * * *',
        '0 1 * * *',
        '0 2 * * *',
        '0 8 * * *',
        '0 9 * * *',
        '0 0 * * 1',
        '0 0 * * 2',
        '0 0 * * 3',
        '0 0 * * 4',
        '0 0 * * 5',
        '0 0 * * 1-5',
        '0 0 * * 6,0',
        '0 0 * * 0',
        '0 0 1 * *',
        '0 0 1 */2 *',
        '0 0 1 */3 *',
        '0 0 1 */6 *',
        '0 0 1 1 *',
    ],
    name: '',
    examples: [
      '每分钟', '每偶数分钟', '每奇数分钟', '每3分钟', '每4分钟', '每5分钟', '每6分钟', '每10分钟', '每15分钟', '每20分钟', '每30分钟', '每小时的30分钟', '每小时', '每2小时', '每3小时', '每4小时', '每6小时', '每8小时', '每12小时', '9点到17点内每小时', '每天零点', '每天凌晨1点', '每天凌晨2点', '每天早上8点', '每天上午9点', '每个星期一', '每个星期二', '每个星期三', '每个星期四', '每个星期五', '每个工作日', '每个周六周日', '仅限周末', '每月', '每隔一个月', '每个季度', '每半年', '每年'
    ],
    init() {
      this._showDate(Cron(this.value));
    },
    _showDate(job) {
      this.nextMultiple = job.nextRuns(10).map(item => format(item, 'yyyy-MM-dd HH:mm:ss'));
      this.second = job.getPattern().split(' ').length >= 6;
      let number = this.randoms.findIndex(item => item === this.value);
      if (number >= 0) {
        this.name = this.examples[number];
      }
    },
    blur() {
      try {
        this._showDate(Cron(this.value));
        this.format = true;
      } catch (e) {
        this.format = false;
        this.nextMultiple = [];
        this.second = false;
      }
      this.active = -1;
    },
    focus(e) {
      console.log(this.format);
      /** @type {HTMLInputElement} */
      if (!this.format) {
        return ;
      }
      let input = this.$refs.crontab;
      let position = input.selectionEnd;
      let lens = this._valueSplitForLength();
      for (let i = 0; i < lens.length; i++) {
        if (position < lens[i]) {
          this.activated(lens.length === 5 ? i+1 : i, lens);
          break;
        }
      }
    },
    _valueSplitForLength() {
      let strings = this.value.split(' ');
      let pre = 0;
      return strings.map((val, index) => {
        pre = (val.length + 1) + pre;
        return pre;
      });
    },
    activated(cell, lens) {
      if (!this.format) {
        return ;
      }
      this.active = cell;
      if (lens === undefined) {
        lens = this._valueSplitForLength();
      } else {
        return ;
      }
      /** @type {HTMLInputElement} */
      let input = this.$refs.crontab;
      input.focus();
      if (lens.length === 5) {
        cell -= 1;
      }
      console.log(cell,lens, cell === 0 ? 0 :
          lens[cell - 1],
          lens[cell] - 1);
      input.setSelectionRange(
          cell === 0 ? 0 :
                 lens[cell - 1],
          lens[cell] - 1,
      );
    },
    random() {
      let index = Math.floor(Math.random() * this.randoms.length);
      if (this.randoms[index]) {
        this.value = this.randoms[index];
        this.blur();
      }
    },
    selectExample(index){
      if (this.randoms[index]) {
        this.value = this.randoms[index];
        this.blur();
      }
    }
  }));
});
