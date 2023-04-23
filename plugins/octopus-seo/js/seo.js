(function (root){
  var seo = function (component) {
    this.component = component;
  };
  seo.prototype.init = function (deeps) {
    var elements = document.getElementsByTagName('nb-layout-column');
    elements[0].addEventListener('click', (evt) => {
      if (!evt.target) {
        return ;
      }
      var target = evt.target;
      if (target.classList.contains('seo-variable')) {
        this.insertVariable(target)
      }
    });
  };
  seo.prototype.insertVariable = function (target) {
    var parent = target.dataset.parent;
    var controlName = target.dataset.control;
    var value = target.dataset.value;
    var controls = this.component.form.controls;
    if (controls[parent] && controls[parent].contains(controlName)) {
      var inputValue = controls[parent].controls[controlName].value;
      controls[parent].controls[controlName].setValue(inputValue + (inputValue ? ' ' + value : value));
    }
  };
  root.seo = seo;
})(window);
