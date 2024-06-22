import {quicktype, InputData, jsonInputForTargetLanguage,} from "quicktype-core";
import {convert as protobufConvert, Options} from "./json-to-protobuf";
import Toastify from "toastify-js"

(function (root) {
  root.Toastify = Toastify;
  root.defaultToast = function (text) {
    var id = 'btn-' + new Date().getSeconds();
    var children = '<div class="w-full flex items-center relative">' +
        '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 !bg-primary rounded-lg">' +
        '<svg class="fill-white h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g data-name="Layer 2"><g data-name="checkmark-circle"><rect width="24" height="24" opacity="0"/><path d="M9.71 11.29a1 1 0 0 0-1.42 1.42l3 3A1 1 0 0 0 12 16a1 1 0 0 0 .72-.34l7-8a1 1 0 0 0-1.5-1.32L12 13.54z"/><path d="M21 11a1 1 0 0 0-1 1 8 8 0 0 1-8 8A8 8 0 0 1 6.33 6.36 7.93 7.93 0 0 1 12 4a8.79 8.79 0 0 1 1.9.22 1 1 0 1 0 .47-1.94A10.54 10.54 0 0 0 12 2a10 10 0 0 0-7 17.09A9.93 9.93 0 0 0 12 22a10 10 0 0 0 10-10 1 1 0 0 0-1-1z"/></g></g></svg>' +
        '<span class="sr-only">Copy icon</span>' +
        '</div>' +
        '<div class="ml-3 text-sm font-normal">' + text + '</div>' +
        '<button type="button" id="' + id + '" class="ml-auto -mx0.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8" data-dismiss-target="#toast-default" aria-label="Close">' +
        '<span class="sr-only">Close</span>' +
        '<svg aria-hidden="true" class="fill-white w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>' +
        '</button>' +
        '</div>';
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
  let outputDefaultDisplay = document.defaultView.getComputedStyle(outputContainer).display;
  let isInlineType = false;
  let isOmitEmpty = false;
  const isAutoConvert = true;
  let outputContent = "";
  let prevValue = "";


  document.getElementById('inlineType').addEventListener('change', function () {
    isInlineType = this.checked;
    toConvert(inputJson.value);
  });
  document.getElementById('omitEmpty').addEventListener('change', function () {
    isOmitEmpty = this.checked;
    toConvert(inputJson.value);
  });

  document.getElementById('containerFull').addEventListener('click', () => {
    const display = document.defaultView.getComputedStyle(inputContainer).display
    let maxWidth = ""
    if (root.innerWidth >= 1024) {
      maxWidth = "max-w-[calc((100vw-7rem)/2)]"
    } else if (root.innerWidth >= 768) {
      maxWidth = "max-w-[calc(100vw-3.5rem)]"
    } else {
      maxWidth = "max-w-[calc(100vw-2rem)]"
    }
    if (display !== 'none') {
      inputContainer.classList.add('hidden');
      if (outputContent) {
        let htmlPreElement = output.querySelector('pre');
        htmlPreElement.classList.remove(maxWidth)
      }
    } else {
      inputContainer.classList.remove('hidden');
      if (outputDefaultDisplay === 'none') {
        outputContainer.classList.add('hidden');
      }
      if (outputContent) {
        let htmlPreElement = output.querySelector('pre');
        htmlPreElement.classList.add(maxWidth)
      }
    }
  });

  const toConvertDisplay = (content) => {
    outputContent = content
    let maxWidth = ""
    if (root.innerWidth >= 1024) {
      maxWidth = "max-w-[calc((100vw-7rem)/2)]"
    } else if (root.innerWidth >= 768) {
      maxWidth = "max-w-[calc(100vw-3.5rem)]"
    } else {
      maxWidth = "max-w-[calc(100vw-2rem)]"
    }
    const html = Prism.highlight(content, Prism.languages[window.ConvertOptions.lang], window.ConvertOptions.lang);
    output.innerHTML = '<pre class="h-[calc(100vh-13rem)] ' + maxWidth + ' language-' + window.ConvertOptions.lang + ' !px-3 !py-2 !m-0 !rounded-none scrollable"><code class="prism language-' + window.ConvertOptions.lang + '" >' + html + '</code></pre>';
    if (outputDefaultDisplay === 'none') {
      inputContainer.classList.add('hidden');
    }
    outputContainer.classList.remove('hidden');
    inputJson.classList.remove('!border-red-500');
  };

  const toConvert = function (value) {
    prevValue = value;
    if (!value) {
      inputJson.classList.remove('!border-red-500');
      if (outputDefaultDisplay !== 'none') {
        output.innerHTML = '';
      }
      return;
    }
    if (window.ConvertOptions.lang === 'protobuf') {
      console.log(value)
      const result = protobufConvert(value, new Options(true, true, false))
      if (!result.success) {
        inputJson.classList.add('!border-red-500');
        if (outputDefaultDisplay !== 'none') {
          output.innerHTML = '<p class="text-red-500 font-bold p-3">' + result.error + '</p>';
        }
        return ;
      }
      toConvertDisplay(result.success)
    } else {
      let jsonInput = jsonInputForTargetLanguage(window.ConvertOptions.lang);
      try {
        jsonInput.addSourceSync({
          name: 'ExampleGenerate',
          samples: [value]
        });
      } catch (error) {
        inputJson.classList.add('!border-red-500');
        if (outputDefaultDisplay !== 'none') {
          output.innerHTML = '<p class="text-red-500 font-bold p-3">' + error + '</p>';
        }
        return;
      }
      const inputData = new InputData();
      inputData.addInput(jsonInput);
      quicktype({
        inputData,
        rendererOptions: window.ConvertOptions.rendererOptions || {},
        lang: window.ConvertOptions.lang,
      }).then((res) => {
        toConvertDisplay(res.lines.join("\n"));
      });
    }
  };

  inputJson.addEventListener('keyup', function () {
    if (prevValue !== this.value) {
      if (isAutoConvert) {
        toConvert(this.value);
      }
    }
  });
  inputJson.addEventListener('dblclick', function () {
    if (!outputContainer.classList.contains("hidden")) {
      return;
    }
    if (this.value) {
      if (isAutoConvert) {
        toConvert(this.value)
      }
    }
  });
  document.getElementById('copy').addEventListener('click', function () {
    if (!outputContent) {
      return;
    }
    navigator.clipboard.writeText(outputContent).then(() => {
      root.defaultToast('已复制');
    });
  });
  root.addEventListener('resize', (evt) => {
    let htmlPreElement = output.querySelector('pre');
    if (evt.target.innerWidth < 1024) {
      if (!inputContainer.classList.contains('hidden')) {
        if (!outputContainer.classList.contains("hidden")) {
          inputContainer.classList.add('hidden')
        }
        htmlPreElement.classList.remove("max-w-[calc((100vw-7rem)/2)]")
      }
      if (evt.target.innerWidth < 768) {
        htmlPreElement.classList.remove("max-w-[calc(100vw-3.5rem)]")
        htmlPreElement.classList.add("max-w-[calc(100vw-2rem)]")
      } else {
        htmlPreElement.classList.add("max-w-[calc(100vw-3.5rem)]")
      }
    } else {
      if (inputContainer.classList.contains('hidden')) {
        inputContainer.classList.remove('hidden')
        htmlPreElement.classList.remove("max-w-[calc(100vw-3.5rem)]", "max-w-[calc(100vw-2rem)]")
        htmlPreElement.classList.add("max-w-[calc((100vw-7rem)/2)]")
      }
    }
  });
  let _bfJson = root.localStorage.getItem("_bf_json_value");
  if (_bfJson) {
    inputJson.value = JSON.stringify(JSON.parse(_bfJson), null, 4);
    toConvert(inputJson.value);
  }
})(window);
