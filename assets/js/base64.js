import {encode, decode} from 'js-base64';
(function (root){


  var input = document.getElementById('inputString');
  var output = document.getElementById('outputString');

  var btnEncode = document.getElementById('btnEncode');
  var btnDecode = document.getElementById('btnDecode');

  btnEncode.addEventListener('click', (event) => {
    if (input.value) {
      output.value = encode(input.value);
    }
  });
  btnDecode.addEventListener('click', (event) => {
    if (input.value) {
      output.value = decode(input.value);
    }
  });
})(window)
