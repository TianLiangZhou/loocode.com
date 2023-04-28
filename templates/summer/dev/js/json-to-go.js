const jsonToGo = require("json-to-go/json-to-go");
(function () {

  const inputContainer = document.getElementById('inputContainer');
  const outputContainer = document.getElementById('outputContainer');
  const inputJson = document.getElementById('inputJson');
  const output = document.getElementById('outputStruct');
  let outputDefaultDisplay = document.defaultView.getComputedStyle(outputContainer).display;
  let isInlineType = false;
  let isOmitEmpty = false;
  const isAutoConvert = true;
  let outputContent = "";
  let prevValue = "";


  document.getElementById('inlineType').addEventListener('change', function() {
    isInlineType = this.checked;
    toConvert(inputJson.value);
  });
  document.getElementById('omitEmpty').addEventListener('change', function() {
    isOmitEmpty = this.checked;
    toConvert(inputJson.value);
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
  const toConvert = function(value) {
    prevValue = value;
    if (!value) {
      inputJson.classList.remove('!border-red-500');
      if (outputDefaultDisplay !== 'none') {
        output.innerHTML = '';
      }
      return ;
    }
    const struct = jsonToGo(value, '', !isInlineType, false, isOmitEmpty);
    if (struct.error) {
      inputJson.classList.add('!border-red-500');
      if (outputDefaultDisplay !== 'none') {
        output.innerHTML = '<p class="text-red-500 font-bold p-3">'+ struct.error +'</p>';
      }
    } else {
      outputContent = struct.go;
      const html = Prism.highlight(struct.go, Prism.languages.go, 'go');
      output.innerHTML = '<pre class="language-go !px-3 !py-2 !m-0 h-full !rounded-none scrollable"><code class="prism language-go" >'+html+'</code></pre>';
      if (outputDefaultDisplay === 'none') {
        inputContainer.classList.add('hidden');
      }
      outputContainer.classList.remove('hidden');
      inputJson.classList.remove('!border-red-500');
    }
  };
  inputJson.addEventListener('keyup', function() {
    if (prevValue != this.value) {
      if (isAutoConvert) {
        toConvert(this.value);
      }
    }
  });
  document.getElementById('copy').addEventListener('click', function () {
    if (!outputContent) {
      return ;
    }
    navigator.clipboard.writeText(outputContent).then(() => {
      defaultToast('已复制');
    });
  });
})();
