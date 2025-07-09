import JSONFormatter from "json-formatter-js";
import Toastify from "toastify-js";

(function (root) {
  root.Toastify = Toastify;
  root.defaultToast = function (text) {
    var id = 'btn-' + new Date().getSeconds();
    var children = '<div class="w-full flex items-center relative">' + '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 !bg-primary rounded-lg">' + '<svg class="fill-white h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g data-name="Layer 2"><g data-name="checkmark-circle"><rect width="24" height="24" opacity="0"/><path d="M9.71 11.29a1 1 0 0 0-1.42 1.42l3 3A1 1 0 0 0 12 16a1 1 0 0 0 .72-.34l7-8a1 1 0 0 0-1.5-1.32L12 13.54z"/><path d="M21 11a1 1 0 0 0-1 1 8 8 0 0 1-8 8A8 8 0 0 1 6.33 6.36 7.93 7.93 0 0 1 12 4a8.79 8.79 0 0 1 1.9.22 1 1 0 1 0 .47-1.94A10.54 10.54 0 0 0 12 2a10 10 0 0 0-7 17.09A9.93 9.93 0 0 0 12 22a10 10 0 0 0 10-10 1 1 0 0 0-1-1z"/></g></g></svg>' + '<span class="sr-only">Copy icon</span>' + '</div>' + '<div class="ml-3 text-sm font-normal">' + text + '</div>' + '<button type="button" id="' + id + '" class="ml-auto -mx0.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8" data-dismiss-target="#toast-default" aria-label="Close">' + '<span class="sr-only">Close</span>' + '<svg aria-hidden="true" class="fill-white w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>' + '</button>' + '</div>';
    const toast = Toastify({
      className: 'absolute right-4 top-4 flex items-center w-full max-w-xs p-4 mx-auto text-white !bg-primary rounded-lg shadow z-50',
      text: children,
      escapeMarkup: false,
      duration: 3000,
    }).showToast();
    document.getElementById(id).addEventListener('click', () => {
      toast.hideToast();
    });
  };
  const inputContainer = document.getElementById('inputContainer');
  const outputContainer = document.getElementById('outputContainer');
  /**
   *
   * @type {HTMLInputElement}
   */
  const inputJson = document.getElementById('inputJson');
  const output = document.getElementById('outputStruct');
  let isAutoConvert = true;
  let outputElement = null;
  let prevValue = "";
  let openLevel = 1;
  let outputDefaultDisplay = document.defaultView.getComputedStyle(outputContainer).display;


  const toConvert = function(value, open = 1) {
    if (prevValue === value && open === openLevel) {
      return ;
    }
    prevValue = value;
    openLevel = open;
    if (outputElement) {
      output.removeChild(outputElement);
      outputElement = null;
    }
    if (!value) {
      inputJson.classList.remove('!border-red-500');
      output.classList.remove('!text-red-500');
      return ;
    }
    try {
      const obj = JSON.parse(value);
      outputElement = new JSONFormatter(obj, open, {
        hoverPreviewEnabled: false,
        theme: getTheme() === 'Dark' ? 'dark': '',
      }).render();
      const display = document.defaultView.getComputedStyle(outputContainer).display
      if (display === 'none') {
        inputContainer.classList.add('hidden');
        outputContainer.classList.remove('hidden');
      }
      outputElement.classList.add('p-3');
      output.appendChild(outputElement);
      output.classList.remove('!text-red-500');
      output.classList.add('overflow-y-scroll');
      inputJson.classList.remove('!border-red-500');
      inputJson.value = JSON.stringify(obj, null, 4);
    } catch (e) {
      outputElement = document.createElement('div');
      outputElement.classList.add('p-3');
      outputElement.innerText = e;
      output.appendChild(outputElement);
      output.classList.add('!text-red-500');
      inputJson.classList.add('!border-red-500');
    }
  };
  inputJson.addEventListener('keyup', function() {
    if (isAutoConvert) {
      toConvert(this.value);
    }
  });
  document.getElementById('containerFull').addEventListener('click', () => {
    const display = document.defaultView.getComputedStyle(inputContainer).display
    if (display !== 'none') {
      inputContainer.classList.add('hidden');
    } else {

      inputContainer.classList.remove('hidden');
      if (outputDefaultDisplay === 'none') {
        outputContainer.classList.add('hidden');
      }
    }
  });
  document.getElementById('expandJson').addEventListener('click', () => {
    if (openLevel !== Infinity) {
      toConvert(inputJson.value, Infinity);
    } else {
      toConvert(inputJson.value, 1);
    }
  });
  document.getElementById('clearInput').addEventListener('click', () => {
    inputJson.value = ""
    toConvert("")
  });

  document.getElementById('copy').addEventListener('click', function () {
    if (!inputJson.value) {
      return;
    }
    navigator.clipboard.writeText(JSON.stringify(JSON.parse(inputJson.value), null, 4)).then(() => {
      root.defaultToast('已复制');
    });
  });
  document.getElementById('exampleJson').addEventListener('click', function () {
    inputJson.value = `{
  "url": "https://loocode.com/tools/json-beautifier",  
  "name": "JSON美化工具",
  "description": "一个在线的JSON美化工具，支持格式化、压缩、转换等功能。",
  "age": 18,
  "isStudent": false,
  "courses": [
    { "courseName": "数学", "score": 1 },
    { "courseName": "英语", "score": -1 }
  ],
  "address": {
    "street": "拱墅区",
    "city": "杭州市",
    "zip": "33000中"
  }
}`;
    toConvert(inputJson.value);
  });
  var langLinkMap = {
    "ts":     "/tool/json-to-typescript",
    "kt":     "/tool/json-to-kotlin",
    "php":    "/tool/json-to-php",
    "java":   "/tool/json-to-java",
    "golang": "/tool/json-to-golang-struct",
    "csharp": "/tool/json-to-csharp",
  };
  for (let element of document.getElementsByClassName('toCode')) {
    element.addEventListener('click', (evt) => {
      if (!inputJson.value) {
        return ;
      }
      root.localStorage.setItem("_bf_json_value", inputJson.value);
      var lang = element.getAttribute("data");
      root.open(langLinkMap[lang], "_blank")
    });
  }
})(window);
