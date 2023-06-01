import Toastify from "toastify-js"
(function (root) {
  root.Toastify = Toastify;
  root.defaultToast = function(text) {
    var id = 'btn-' + new Date().getSeconds();
    var children = '<div class="w-full flex items-center relative">'+
        '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 !bg-primary rounded-lg">' +
        '<svg class="fill-white h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g data-name="Layer 2"><g data-name="checkmark-circle"><rect width="24" height="24" opacity="0"/><path d="M9.71 11.29a1 1 0 0 0-1.42 1.42l3 3A1 1 0 0 0 12 16a1 1 0 0 0 .72-.34l7-8a1 1 0 0 0-1.5-1.32L12 13.54z"/><path d="M21 11a1 1 0 0 0-1 1 8 8 0 0 1-8 8A8 8 0 0 1 6.33 6.36 7.93 7.93 0 0 1 12 4a8.79 8.79 0 0 1 1.9.22 1 1 0 1 0 .47-1.94A10.54 10.54 0 0 0 12 2a10 10 0 0 0-7 17.09A9.93 9.93 0 0 0 12 22a10 10 0 0 0 10-10 1 1 0 0 0-1-1z"/></g></g></svg>'+
        '<span class="sr-only">Copy icon</span>' +
        '</div>'+
        '<div class="ml-3 text-sm font-normal">'+text+'</div>'+
        '<button type="button" id="'+id+'" class="ml-auto -mx0.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8" data-dismiss-target="#toast-default" aria-label="Close">'+
        '<span class="sr-only">Close</span>'+
        '<svg aria-hidden="true" class="fill-white w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>'+
        '</button>'+
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
  var output = document.getElementById('outputString');
  var outputContainer = document.getElementById('outputContainer');
  var inputContainer = document.getElementById('inputContainer');
  document.getElementById('inputFile').addEventListener('change', function (event) {
    var file = event.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.readAsDataURL(file)
      reader.onload=function (evt) {
        output.value = reader.result;
        outputContainer.classList.remove('hidden');
        inputContainer.classList.add('hidden');
      };
      reader.onerror=function (evt) {
        root.defaultToast("读取文件:[" + file.name + "]出错!");
      }
    }
  });
  document.getElementById('btnCopy').addEventListener('click', function(event) {
    navigator.clipboard.writeText(output.value).then(() => {
      root.defaultToast('已复制');
    });
  });
  document.getElementById('btnRefresh').addEventListener('click', function(event) {
    outputContainer.classList.add('hidden');
    inputContainer.classList.remove('hidden');
  });
  var imageContainer = document.querySelector('#imageContainer');
  document.getElementById('btnView').addEventListener('click', function (event) {
    var image = imageContainer.querySelector('img');
    image.src = output.value;
    image.onload = function () {
      imageContainer.style.left = ((outputContainer.offsetWidth/ 2) - (image.width / 2) - 32) + 'px';
      imageContainer.classList.remove('hidden');
    };
  });
  document.body.addEventListener('click', function() {
    if (!imageContainer.classList.contains('hidden')) {
      imageContainer.classList.add('hidden');
    }
  });
})(window)
