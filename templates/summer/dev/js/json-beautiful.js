import JSONFormatter from "json-formatter-js";

(function () {
  const inputContainer = document.getElementById('inputContainer');
  const outputContainer = document.getElementById('outputContainer');
  const inputJson = document.getElementById('inputJson');
  const output = document.getElementById('outputStruct');
  let isAutoConvert = true;
  let outputElement = null;
  let prevValue = "";
  let openLevel = 3;
  let outputDefaultDisplay = document.defaultView.getComputedStyle(outputContainer).display;


  const toConvert = function(value, open = 3) {
    if (prevValue === value) {
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
  // document.getElementById('toggleContainer').addEventListener('click', () => {
  //   if (display === 'none') {
  //     inputContainer.classList.remove('hidden');
  //     outputContainer.classList.add('hidden');
  //   }
  // });
  document.getElementById('expandJson').addEventListener('click', () => {
    if (prevValue && openLevel !== Infinity) {
      prevValue = "";
      toConvert(inputJson.value, Infinity);
    }
  });
})();
