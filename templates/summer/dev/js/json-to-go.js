const jsonToGo = require("json-to-go/json-to-go");
(function () {
  var inputContainer = document.getElementById('inputContainer');
  var outputContainer= document.getElementById('outputContainer');
  var inputJson = document.getElementById('inputJson');
  var isInlineType = isOmitEmpty = false;
  var isAutoConvert = true;
  var outputContent = "";
  document.getElementById('inlineType').addEventListener('change', function() {
    isInlineType = this.checked;
    toConvert(inputJson.value);
  });
  document.getElementById('omitEmpty').addEventListener('change', function() {
    isOmitEmpty = this.checked;
    toConvert(inputJson.value);
  });
  document.getElementById('autoConvert').addEventListener('change', function() {
    isAutoConvert = this.checked;
    if (isAutoConvert) {
      toConvert(inputJson.value);
    }
  });



  outputContainer.addEventListener('dblclick', function() {
    outputContainer.classList.add('hidden');
    inputContainer.classList.remove('hidden');
  });
  inputContainer.addEventListener('dblclick', function() {
    toConvert(inputJson.value);
  });
  const toConvert = function(value) {
      if (!value) {
        inputJson.classList.remove('!border-red-500');
        return ;
      }
      var output = document.getElementById('outputStruct');
      var struct = jsonToGo(value, '', !isInlineType, false, isOmitEmpty);
      if (struct.error) {
        inputJson.classList.add('!border-red-500');
      } else {
        outputContent = struct.go;
        const html = Prism.highlight(struct.go, Prism.languages.go, 'go');
        output.innerHTML = '<pre class="language-go !px-3 !py-2 !m-0 h-full"><code class="prism language-go" >'+html+'</code></pre>';
        inputContainer.classList.add('hidden');
        outputContainer.classList.remove('hidden');
        inputJson.classList.remove('!border-red-500');
      }
  };
  inputJson.addEventListener('keyup', function() {
    if (isAutoConvert) {
      toConvert(this.value);
    }
  });
  document.getElementById('convert').addEventListener('click', function () {
    toConvert(inputJson.value);
  });
  document.getElementById('copy').addEventListener('click', function () {
    var _this = this;
    navigator.clipboard.writeText(outputContent).then(() => {
      _this.innerText="已复制";
      setTimeout(() => {
        _this.innerText="复制";
      }, 2000);
    });
  });
})();
