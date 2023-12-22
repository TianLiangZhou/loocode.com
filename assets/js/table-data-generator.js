import { Parser } from "node-sql-parser"
import format from "date-fns/format";
(function () {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  const phonePrefix = ['130','131','132','134','135','136','137','138','139', '151','152','153','155','182','185','193', '192'];
  let randomString = (length) => {
    let result = '';
    for (let i = 0; i < length; i++) {
      const randomIndex = Math.floor(Math.random() * characters.length);
      result += characters.charAt(randomIndex);
    }
    return result;
  }
  let randomNumber = (max) => {
    return Math.floor(Math.random() * max);
  }
  let randomNumberRange = (min, max) => {
    return Math.floor(Math.random() * (max - min) + min);
  }
  let counter = 0;
  let generatorInteger = (column) => {
    if (column.autoIncrement) {
      return 'null';
    }
    if (column.forceDefault) {
      return column.defaultValue || 0;
    }
    if (column.customValue !== '') {
      let strings = column.customValue.split('||');
      return parseInt(strings.length < 2 ? strings[0] : strings[randomNumber(strings.length)], 10);
    }
    if (column.name.indexOf('time') > 0) {
      return Math.floor(((new Date().getTime()) - randomNumber(86400)) / 1000);
    }
    if (column.name.indexOf('tiny') >= 0) {
      return Math.floor(randomNumber(256));
    }
    if (column.name.indexOf('small') >= 0) {
      return Math.floor(randomNumber(65536));
    }
    counter++;
    return counter;
  };

  let generatorString = (column) => {
    if (column.forceDefault) {
      return column.defaultValue || 0;
    }
    if (column.customValue !== '') {
      let strings = column.customValue.split('||');
      return strings.length < 2 ? strings[0] : strings[randomNumber(strings.length)];
    }
    if (column.name.indexOf('avatar') >= 0) {
      return location.protocol + '//' + location.hostname + '/bundles/octopuspress/images/avatar.png';
    }
    if (column.name.indexOf('email') >= 0) {
      return randomString(8) + '@' + ['gmail.com', '163.com', '126.com', 'qq.com', 'hotmail.com'][randomNumber(5)];
    }
    if (column.name.indexOf('mobile') >= 0 || column.name.indexOf('phone') >= 0) {
      return phonePrefix[randomNumber(phonePrefix.length)]+(''+randomNumberRange(1111, 9999))+(''+randomNumberRange(1111,9999));
    }
    if (column.name.indexOf('password') >= 0 || column.name.indexOf('pwd') >= 0) {
      return 'e10adc3949ba59abbe56e057f20f883e';
    }
    if (column.type === 'char') {
      return randomString(column.length);
    }
    return randomString(randomNumber(column.length));
  }

  let generatorDatetime = (column) => {
    if (column.forceDefault) {
      return column.defaultValue || '1970-01-01 00:00:00';
    }
    if (column.customValue !== '') {
      let strings = column.customValue.split('||');
      return strings.length < 2 ? strings[0]: strings[randomNumber(strings.length)];
    }
    let date = new Date();
    date.setTime(new Date().getTime() - randomNumber(86400 * 15 * 1000));
    if (column.name === 'date') {
      return format(date, 'yyyy-MM-dd');
    }
    return format(date, 'yyyy-MM-dd HH:mm:ss');
  }

  let generatorJson = (column) => {
    if (column.customValue !== '') {
      let strings = column.customValue.split('||');
      return strings.length < 2 ? strings[0] : strings[randomNumber(strings.length)];
    }
    return "{}";
  }


  window['parseSql'] = function () {
    this.extra.isParsed = false;
    let sqlInput = document.getElementById('sql');
    let value = sqlInput.value;
    try {
      const parser = new Parser()
      let ast = parser.astify(value);
      if (Array.isArray(ast)) {
        ast = ast[0];
      }
      console.log(ast);
      if (ast.type !== 'create') {
        sqlInput.classList.add('!border-red-500');
        return false;
      }
      if (ast.create_definitions.length < 1) {
        sqlInput.classList.add('!border-red-500');
        return false;
      }
      let columns = ast.create_definitions.filter(item => item.resource === 'column');
      if (columns.length < 1) {
        sqlInput.classList.add('!border-red-500');
        return false;
      }
      sqlInput.classList.remove('!border-red-500');
      let columnArray = [];
      columns.forEach(item => {
        let column = {
          name: item.column.column,
          type: item.definition.dataType.toLowerCase(),
          length: item.definition.length,
          defaultValue: item.default_val ? item.default_val.value.value : undefined,
          autoIncrement: !!item.auto_increment,
          generator: null,
          generatorName: '',
          customValue: '',
          forceDefault: false,
        };
        let type = column.type;
        if (type.indexOf('int') >= 0 || type.indexOf('number') >= 0 || type.indexOf('long') >= 0 || type.indexOf('double') >= 0 || type.indexOf('float') >= 0 || type.indexOf('dec') >= 0) {
          column.generator = generatorInteger;
          column.generatorName = 'generatorInteger';
        } else if (type.indexOf('json') >= 0) {
          column.generator = generatorJson;
          column.generatorName = 'generatorJson';
        } else if (type.indexOf('char') >= 0 || type.indexOf('text') >= 0) {
          column.generator = generatorString;
          column.generatorName = 'generatorString';
        } else if (type.indexOf('date') >= 0) {
          column.generator = generatorDatetime;
          column.generatorName = 'generatorDatetime';
        } else {
          column.generator = generatorString;
          column.generatorName = 'generatorString';
        }
        columnArray.push(column);
      });
      this.extra.table = ast.table[0].table;
      this.extra.isParsed = true;
      this.extra.columns = columnArray;
    } catch (e) {
      console.log(e);
      sqlInput.classList.add('!border-red-500');
    }
  };
  window['convertSqlGenerator'] = function () {
    // Example parser
    if (!this.extra.isParsed) {
      alert('请点击解析按钮，解析DDL!!!!');
      return false;
    }
    let count = parseInt(document.getElementById('count').value || '100', 10);
    let sqls = [];
    let columns = this.extra.columns;
    let columnUnion = columns.map(item => '`' + item.name + '`').join(',');

    for (let i = 0; i < count; i++) {
      let values = [];
      columns.forEach(item => {
        let value = item.generator(item);
        if (item.generatorName === 'generatorString' || item.generatorName === 'generatorDatetime' || item.generatorName === 'generatorJson') {
          value = "'" + value + "'";
        }
        values.push(value);
      });
      let sql = 'INSERT INTO `' + this.extra.table + '`(' + columnUnion + ') VALUE (' + values.join(',') + ');';
      sqls.push(sql);
    }
    this.data = sqls.join("\n");
    // Creating a Blob for having a csv file format
    // and passing the data with type
    const blob = new Blob([this.data], { type: 'text/plain' });
    // Creating an object for downloading url
    const url = window.URL.createObjectURL(blob)
    // Creating an anchor(a) tag of HTML
    const a = document.createElement('a')
    // Passing the blob downloading url
    a.setAttribute('href', url)
    // Setting the anchor tag attribute for downloading
    // and passing the download file name
    a.setAttribute('download', this.extra.table+'.sql');
    // Performing a download with click
    a.click()
    setTimeout(() => {
      window.URL.revokeObjectURL(url);
    },0);
  };
})()
