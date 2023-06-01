(()=>{var P=Object.create;var O=Object.defineProperty;var E=Object.getOwnPropertyDescriptor;var S=Object.getOwnPropertyNames;var A=Object.getPrototypeOf,N=Object.prototype.hasOwnProperty;var x=(i,r)=>()=>(r||i((r={exports:{}}).exports,r),r.exports);var F=(i,r,f,d)=>{if(r&&typeof r=="object"||typeof r=="function")for(let c of S(r))!N.call(i,c)&&c!==f&&O(i,c,{get:()=>r[c],enumerable:!(d=E(r,c))||d.enumerable});return i};var T=(i,r,f)=>(f=i!=null?P(A(i)):{},F(r||!i||!i.__esModule?O(f,"default",{value:i,enumerable:!0}):f,i));var C=x((b,k)=>{(function(i,r){typeof b=="object"&&typeof k<"u"?k.exports=r():typeof define=="function"&&define.amd?define(r):(i=i||self).JSONFormatter=r()})(b,function(){"use strict";function i(e){return e===null?"null":typeof e}function r(e){return!!e&&typeof e=="object"}function f(e){if(e===void 0)return"";if(e===null||typeof e=="object"&&!e.constructor)return"Object";var t=/function ([^(]*)/.exec(e.constructor.toString());return t&&t.length>1?t[1]:""}function d(e,t,n){return e==="null"||e==="undefined"?e:(e!=="string"&&e!=="stringifiable"||(n='"'+n.replace(/"/g,'\\"')+'"'),e==="function"?t.toString().replace(/[\r\n]/g,"").replace(/\{.*\}/,"")+"{\u2026}":n)}function c(e){var t="";return r(e)?(t=f(e),Array.isArray(e)&&(t+="["+e.length+"]")):t=d(i(e),e,e),t}function o(e){return"json-formatter-"+e}function s(e,t,n){var a=document.createElement(e);return t&&a.classList.add(o(t)),n!==void 0&&(n instanceof Node?a.appendChild(n):a.appendChild(document.createTextNode(String(n)))),a}(function(e){if(e&&typeof window<"u"){var t=document.createElement("style");t.setAttribute("media","screen"),t.innerHTML=e,document.head.appendChild(t)}})(`.json-formatter-row {
  font-family: monospace;
}
.json-formatter-row,
.json-formatter-row a,
.json-formatter-row a:hover {
  color: black;
  text-decoration: none;
}
.json-formatter-row .json-formatter-row {
  margin-left: 1rem;
}
.json-formatter-row .json-formatter-children.json-formatter-empty {
  opacity: 0.5;
  margin-left: 1rem;
}
.json-formatter-row .json-formatter-children.json-formatter-empty:after {
  display: none;
}
.json-formatter-row .json-formatter-children.json-formatter-empty.json-formatter-object:after {
  content: "No properties";
}
.json-formatter-row .json-formatter-children.json-formatter-empty.json-formatter-array:after {
  content: "[]";
}
.json-formatter-row .json-formatter-string,
.json-formatter-row .json-formatter-stringifiable {
  color: green;
  white-space: pre;
  word-wrap: break-word;
}
.json-formatter-row .json-formatter-number {
  color: blue;
}
.json-formatter-row .json-formatter-boolean {
  color: red;
}
.json-formatter-row .json-formatter-null {
  color: #855A00;
}
.json-formatter-row .json-formatter-undefined {
  color: #ca0b69;
}
.json-formatter-row .json-formatter-function {
  color: #FF20ED;
}
.json-formatter-row .json-formatter-date {
  background-color: rgba(0, 0, 0, 0.05);
}
.json-formatter-row .json-formatter-url {
  text-decoration: underline;
  color: blue;
  cursor: pointer;
}
.json-formatter-row .json-formatter-bracket {
  color: blue;
}
.json-formatter-row .json-formatter-key {
  color: #00008B;
  padding-right: 0.2rem;
}
.json-formatter-row .json-formatter-toggler-link {
  cursor: pointer;
}
.json-formatter-row .json-formatter-toggler {
  line-height: 1.2rem;
  font-size: 0.7rem;
  vertical-align: middle;
  opacity: 0.6;
  cursor: pointer;
  padding-right: 0.2rem;
}
.json-formatter-row .json-formatter-toggler:after {
  display: inline-block;
  transition: transform 100ms ease-in;
  content: "\u25BA";
}
.json-formatter-row > a > .json-formatter-preview-text {
  opacity: 0;
  transition: opacity 0.15s ease-in;
  font-style: italic;
}
.json-formatter-row:hover > a > .json-formatter-preview-text {
  opacity: 0.6;
}
.json-formatter-row.json-formatter-open > .json-formatter-toggler-link .json-formatter-toggler:after {
  transform: rotate(90deg);
}
.json-formatter-row.json-formatter-open > .json-formatter-children:after {
  display: inline-block;
}
.json-formatter-row.json-formatter-open > a > .json-formatter-preview-text {
  display: none;
}
.json-formatter-row.json-formatter-open.json-formatter-empty:after {
  display: block;
}
.json-formatter-dark.json-formatter-row {
  font-family: monospace;
}
.json-formatter-dark.json-formatter-row,
.json-formatter-dark.json-formatter-row a,
.json-formatter-dark.json-formatter-row a:hover {
  color: white;
  text-decoration: none;
}
.json-formatter-dark.json-formatter-row .json-formatter-row {
  margin-left: 1rem;
}
.json-formatter-dark.json-formatter-row .json-formatter-children.json-formatter-empty {
  opacity: 0.5;
  margin-left: 1rem;
}
.json-formatter-dark.json-formatter-row .json-formatter-children.json-formatter-empty:after {
  display: none;
}
.json-formatter-dark.json-formatter-row .json-formatter-children.json-formatter-empty.json-formatter-object:after {
  content: "No properties";
}
.json-formatter-dark.json-formatter-row .json-formatter-children.json-formatter-empty.json-formatter-array:after {
  content: "[]";
}
.json-formatter-dark.json-formatter-row .json-formatter-string,
.json-formatter-dark.json-formatter-row .json-formatter-stringifiable {
  color: #31F031;
  white-space: pre;
  word-wrap: break-word;
}
.json-formatter-dark.json-formatter-row .json-formatter-number {
  color: #66C2FF;
}
.json-formatter-dark.json-formatter-row .json-formatter-boolean {
  color: #EC4242;
}
.json-formatter-dark.json-formatter-row .json-formatter-null {
  color: #EEC97D;
}
.json-formatter-dark.json-formatter-row .json-formatter-undefined {
  color: #ef8fbe;
}
.json-formatter-dark.json-formatter-row .json-formatter-function {
  color: #FD48CB;
}
.json-formatter-dark.json-formatter-row .json-formatter-date {
  background-color: rgba(255, 255, 255, 0.05);
}
.json-formatter-dark.json-formatter-row .json-formatter-url {
  text-decoration: underline;
  color: #027BFF;
  cursor: pointer;
}
.json-formatter-dark.json-formatter-row .json-formatter-bracket {
  color: #9494FF;
}
.json-formatter-dark.json-formatter-row .json-formatter-key {
  color: #23A0DB;
  padding-right: 0.2rem;
}
.json-formatter-dark.json-formatter-row .json-formatter-toggler-link {
  cursor: pointer;
}
.json-formatter-dark.json-formatter-row .json-formatter-toggler {
  line-height: 1.2rem;
  font-size: 0.7rem;
  vertical-align: middle;
  opacity: 0.6;
  cursor: pointer;
  padding-right: 0.2rem;
}
.json-formatter-dark.json-formatter-row .json-formatter-toggler:after {
  display: inline-block;
  transition: transform 100ms ease-in;
  content: "\u25BA";
}
.json-formatter-dark.json-formatter-row > a > .json-formatter-preview-text {
  opacity: 0;
  transition: opacity 0.15s ease-in;
  font-style: italic;
}
.json-formatter-dark.json-formatter-row:hover > a > .json-formatter-preview-text {
  opacity: 0.6;
}
.json-formatter-dark.json-formatter-row.json-formatter-open > .json-formatter-toggler-link .json-formatter-toggler:after {
  transform: rotate(90deg);
}
.json-formatter-dark.json-formatter-row.json-formatter-open > .json-formatter-children:after {
  display: inline-block;
}
.json-formatter-dark.json-formatter-row.json-formatter-open > a > .json-formatter-preview-text {
  display: none;
}
.json-formatter-dark.json-formatter-row.json-formatter-open.json-formatter-empty:after {
  display: block;
}
`);var y=/(^\d{1,4}[\.|\\/|-]\d{1,2}[\.|\\/|-]\d{1,4})(\s*(?:0?[1-9]:[0-5]|1(?=[012])\d:[0-5])\d\s*[ap]m)?$/,w=/\d{2}:\d{2}:\d{2} GMT-\d{4}/,g=/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/,p=window.requestAnimationFrame||function(e){return e(),0},h={hoverPreviewEnabled:!1,hoverPreviewArrayCount:100,hoverPreviewFieldCount:5,animateOpen:!0,animateClose:!0,theme:null,useToJSON:!0,sortPropertiesBy:null};return function(){function e(t,n,a,l){n===void 0&&(n=1),a===void 0&&(a=h),this.json=t,this.open=n,this.config=a,this.key=l,this._isOpen=null,this.config.hoverPreviewEnabled===void 0&&(this.config.hoverPreviewEnabled=h.hoverPreviewEnabled),this.config.hoverPreviewArrayCount===void 0&&(this.config.hoverPreviewArrayCount=h.hoverPreviewArrayCount),this.config.hoverPreviewFieldCount===void 0&&(this.config.hoverPreviewFieldCount=h.hoverPreviewFieldCount),this.config.useToJSON===void 0&&(this.config.useToJSON=h.useToJSON),this.key===""&&(this.key='""')}return Object.defineProperty(e.prototype,"isOpen",{get:function(){return this._isOpen!==null?this._isOpen:this.open>0},set:function(t){this._isOpen=t},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isDate",{get:function(){return this.json instanceof Date||this.type==="string"&&(y.test(this.json)||g.test(this.json)||w.test(this.json))},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isUrl",{get:function(){return this.type==="string"&&this.json.indexOf("http")===0},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isArray",{get:function(){return Array.isArray(this.json)},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isObject",{get:function(){return r(this.json)},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isEmptyObject",{get:function(){return!this.keys.length&&!this.isArray},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"isEmpty",{get:function(){return this.isEmptyObject||this.keys&&!this.keys.length&&this.isArray},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"useToJSON",{get:function(){return this.config.useToJSON&&this.type==="stringifiable"},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"hasKey",{get:function(){return this.key!==void 0},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"constructorName",{get:function(){return f(this.json)},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"type",{get:function(){return this.config.useToJSON&&this.json&&this.json.toJSON?"stringifiable":i(this.json)},enumerable:!0,configurable:!0}),Object.defineProperty(e.prototype,"keys",{get:function(){if(this.isObject){var t=Object.keys(this.json);return!this.isArray&&this.config.sortPropertiesBy?t.sort(this.config.sortPropertiesBy):t}return[]},enumerable:!0,configurable:!0}),e.prototype.toggleOpen=function(){this.isOpen=!this.isOpen,this.element&&(this.isOpen?this.appendChildren(this.config.animateOpen):this.removeChildren(this.config.animateClose),this.element.classList.toggle(o("open")))},e.prototype.openAtDepth=function(t){t===void 0&&(t=1),t<0||(this.open=t,this.isOpen=t!==0,this.element&&(this.removeChildren(!1),t===0?this.element.classList.remove(o("open")):(this.appendChildren(this.config.animateOpen),this.element.classList.add(o("open")))))},e.prototype.getInlinepreview=function(){var t=this;if(this.isArray)return this.json.length>this.config.hoverPreviewArrayCount?"Array["+this.json.length+"]":"["+this.json.map(c).join(", ")+"]";var n=this.keys,a=n.slice(0,this.config.hoverPreviewFieldCount).map(function(m){return m+":"+c(t.json[m])}),l=n.length>=this.config.hoverPreviewFieldCount?"\u2026":"";return"{"+a.join(", ")+l+"}"},e.prototype.render=function(){this.element=s("div","row");var t=this.isObject?s("a","toggler-link"):s("span");if(this.isObject&&!this.useToJSON&&t.appendChild(s("span","toggler")),this.hasKey&&t.appendChild(s("span","key",this.key+":")),this.isObject&&!this.useToJSON){var n=s("span","value"),a=s("span"),l=s("span","constructor-name",this.constructorName);if(a.appendChild(l),this.isArray){var m=s("span");m.appendChild(s("span","bracket","[")),m.appendChild(s("span","number",this.json.length)),m.appendChild(s("span","bracket","]")),a.appendChild(m)}n.appendChild(a),t.appendChild(n)}else{(n=this.isUrl?s("a"):s("span")).classList.add(o(this.type)),this.isDate&&n.classList.add(o("date")),this.isUrl&&(n.classList.add(o("url")),n.setAttribute("href",this.json));var j=d(this.type,this.json,this.useToJSON?this.json.toJSON():this.json);n.appendChild(document.createTextNode(j)),t.appendChild(n)}if(this.isObject&&this.config.hoverPreviewEnabled){var u=s("span","preview-text");u.appendChild(document.createTextNode(this.getInlinepreview())),t.appendChild(u)}var v=s("div","children");return this.isObject&&v.classList.add(o("object")),this.isArray&&v.classList.add(o("array")),this.isEmpty&&v.classList.add(o("empty")),this.config&&this.config.theme&&this.element.classList.add(o(this.config.theme)),this.isOpen&&this.element.classList.add(o("open")),this.element.appendChild(t),this.element.appendChild(v),this.isObject&&this.isOpen&&this.appendChildren(),this.isObject&&!this.useToJSON&&t.addEventListener("click",this.toggleOpen.bind(this)),this.element},e.prototype.appendChildren=function(t){var n=this;t===void 0&&(t=!1);var a=this.element.querySelector("div."+o("children"));if(a&&!this.isEmpty)if(t){var l=0,m=function(){var j=n.keys[l],u=new e(n.json[j],n.open-1,n.config,j);a.appendChild(u.render()),(l+=1)<n.keys.length&&(l>10?m():p(m))};p(m)}else this.keys.forEach(function(j){var u=new e(n.json[j],n.open-1,n.config,j);a.appendChild(u.render())})},e.prototype.removeChildren=function(t){t===void 0&&(t=!1);var n=this.element.querySelector("div."+o("children"));if(t){var a=0,l=function(){n&&n.children.length&&(n.removeChild(n.children[0]),(a+=1)>10?l():p(l))};p(l)}else n&&(n.innerHTML="")},e}()})});var L=T(C());(function(){let i=document.getElementById("inputContainer"),r=document.getElementById("outputContainer"),f=document.getElementById("inputJson"),d=document.getElementById("outputStruct"),c=!0,o=null,s="",y=3,w=document.defaultView.getComputedStyle(r).display,g=function(p,h=3){if(s!==p){if(s=p,y=h,o&&(d.removeChild(o),o=null),!p){f.classList.remove("!border-red-500"),d.classList.remove("!text-red-500");return}try{let e=JSON.parse(p);o=new L.default(e,h,{hoverPreviewEnabled:!1,theme:getTheme()==="Dark"?"dark":""}).render(),document.defaultView.getComputedStyle(r).display==="none"&&(i.classList.add("hidden"),r.classList.remove("hidden")),o.classList.add("p-3"),d.appendChild(o),d.classList.remove("!text-red-500"),d.classList.add("overflow-y-scroll"),f.classList.remove("!border-red-500")}catch(e){o=document.createElement("div"),o.classList.add("p-3"),o.innerText=e,d.appendChild(o),d.classList.add("!text-red-500"),f.classList.add("!border-red-500")}}};f.addEventListener("keyup",function(){c&&g(this.value)}),document.getElementById("containerFull").addEventListener("click",()=>{document.defaultView.getComputedStyle(i).display!=="none"?i.classList.add("hidden"):(i.classList.remove("hidden"),w==="none"&&r.classList.add("hidden"))}),document.getElementById("expandJson").addEventListener("click",()=>{s&&y!==1/0&&(s="",g(f.value,1/0))})})();})();
