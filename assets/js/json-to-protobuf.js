import Toastify from "toastify-js";
const googleAnyImport = "google/protobuf/any.proto";
const googleTimestampImport = "google/protobuf/timestamp.proto";

const googleAny = "google.protobuf.Any";
const googleTimestamp = "google.protobuf.Timestamp";

class Result {
  success;
  error;
  constructor(success, error) {
    this.success = success;
    this.error   = error;
  }
}

class ProtoPrimitiveType {
  name;
  complex;
  merge;
  constructor(name, complex, merge,) {
    this.name = name;
    this.complex = complex;
    this.merge = merge;
  }
}

const boolProtoPrimitiveType = new ProtoPrimitiveType("bool", false, false);
const stringProtoPrimitiveType = new ProtoPrimitiveType("string", false, false);
const int64ProtoPrimitiveType = new ProtoPrimitiveType("int64", false, true);
const complexProtoType = new ProtoPrimitiveType(googleAny, true, false);
const timestampProtoType = new ProtoPrimitiveType(googleTimestamp, false, false);

export class Options {
  inline=true;
  googleProtobufTimestamp = true;
  mergeSimilarObjects = false;
  constructor(inline, googleProtobufTimestamp, mergeSimilarObjects,) {
    this.inline = inline;
    this.googleProtobufTimestamp = googleProtobufTimestamp;
    this.mergeSimilarObjects = mergeSimilarObjects;
  }
}

class Collector {
  imports;
  messages;
  messageNameSuffixCounter;
  constructor() {
    this.imports = new Set();
    this.messages = [];
    this.messageNameSuffixCounter = {};
  }

  addImport(importPath) {
    this.imports.add(importPath);
  }

  generateUniqueName(source) {
    if (this.messageNameSuffixCounter.hasOwnProperty(source)) {
      const suffix = this.messageNameSuffixCounter[source];

      this.messageNameSuffixCounter[source] = suffix + 1;

      return `${source}${suffix}`
    }

    this.messageNameSuffixCounter[source] = 1;

    return source;
  }

  addMessage(lines) {
    this.messages.push(lines);
  }

  getImports() {
    return this.imports;
  }

  getMessages() {
    return this.messages;
  }
}

class Analyzer {
  mergeSimilarObjectMap = {};
  options = {};
  constructor(options) {
    this.options = options;
  }

  analyze(json) {
    if (this.directType(json)) {
      return this.analyzeObject({"first": json});
    }
    if (Array.isArray(json)) {
      return this.analyzeArray(json)
    }
    return this.analyzeObject(json);
  }

  directType(value) {
    switch (typeof value) {
      case "string":
      case "number":
      case "boolean":
        return true;
      case "object":
        return value === null;
    }
    return false;
  }

  analyzeArray(array) {
    const inlineShift = this.addShift();
    const collector = new Collector();
    const lines = [];

    const typeName = this.analyzeArrayProperty("nested", array, collector, inlineShift)

    lines.push(`    ${typeName} items = 1;`);

    return render(collector.getImports(), collector.getMessages(), lines, this.options);
  }

  analyzeObject(json) {
    const inlineShift = this.addShift();
    const collector = new Collector();
    const lines = [];
    let index = 1;

    for (const [key, value] of Object.entries(json)) {
      const typeName = this.analyzeProperty(key, value, collector, inlineShift)

      lines.push(`    ${typeName} ${key} = ${index};`);

      index += 1;
    }

    return render(collector.getImports(), collector.getMessages(), lines, this.options);
  }

  analyzeArrayProperty(key, value, collector, inlineShift) {
    // [] -> any
    const length = value.length;
    if (length === 0) {
      collector.addImport(googleAnyImport);

      return `repeated ${googleAny}`;
    }

    // [[...], ...] -> any
    const first = value[0];
    if (Array.isArray(first)) {
      collector.addImport(googleAnyImport);

      return `repeated ${googleAny}`;
    }

    if (length > 1) {
      const primitive = this.samePrimitiveType(value);

      if (primitive.complex === false) {
        return `repeated ${primitive.name}`;
      }
    }

    return `repeated ${this.analyzeObjectProperty(key, first, collector, inlineShift)}`;
  }

  analyzeProperty(key, value, collector, inlineShift) {
    if (Array.isArray(value)) {
      return this.analyzeArrayProperty(key, value, collector, inlineShift);
    }

    return this.analyzeObjectProperty(key, value, collector, inlineShift);
  }

  analyzeObjectProperty(key, value, collector, inlineShift) {
    const typeName = this.analyzeType(value, collector);

    if (typeName === "object") {
      if (this.options.mergeSimilarObjects) {
        const [mergeSimilarObjectKey, canMerge] = this.mergeSimilarObjectKey(value);

        if (canMerge) {
          if (this.mergeSimilarObjectMap.hasOwnProperty(mergeSimilarObjectKey)) {
            return this.mergeSimilarObjectMap[mergeSimilarObjectKey];
          }

          const messageName = collector.generateUniqueName(toMessageName(key));

          this.mergeSimilarObjectMap[mergeSimilarObjectKey] = messageName;

          this.addNested(collector, messageName, value, inlineShift);

          return messageName;
        }
      }

      const messageName = collector.generateUniqueName(toMessageName(key));

      this.addNested(collector, messageName, value, inlineShift);

      return messageName;
    }

    return typeName;
  }

  mergeSimilarObjectKey(source) {
    const lines = [];

    for (const [key, value] of Object.entries(source)) {
      const [typeName, canMerge] = this.mergeSimilarObjectType(value);

      if (canMerge) {
        lines.push([key, typeName])
      } else {
        return ["", false];
      }
    }

    return [JSON.stringify(lines), true]
  }

  mergeSimilarObjectType(value) {
    if (Array.isArray(value)) {
      return ["", false];
    }

    switch (typeof value) {
      case "string":
        if (this.options.googleProtobufTimestamp && /\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(\+\d\d:\d\d|Z)/.test(value)) {
          return [googleTimestamp, true];
        } else {
          return ["string", true];
        }
      case "number":
        return [numberType(value), true];
      case "boolean":
        return ["bool", true];
    }

    return ["", false];
  }

  analyzeType(value, collector) {
    switch (typeof value) {
      case "string":
        if (this.options.googleProtobufTimestamp && /\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(\+\d\d:\d\d|Z)/.test(value)) {
          collector.addImport(googleTimestampImport);

          return googleTimestamp;
        } else {
          return "string";
        }
      case "number":
        return numberType(value);
      case "boolean":
        return "bool";
      case "object":
        if (value === null) {
          collector.addImport(googleAnyImport);

          return googleAny;
        }

        return "object";
    }

    collector.addImport(googleAnyImport);

    return googleAny;
  }

  toPrimitiveType(value) {
    switch (typeof value) {
      case "string":
        if (this.options.googleProtobufTimestamp && /\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(\+\d\d:\d\d|Z)/.test(value)) {
          return timestampProtoType;
        } else {
          return stringProtoPrimitiveType;
        }
      case "number":
        return new ProtoPrimitiveType(numberType(value), false, true);
      case "boolean":
        return boolProtoPrimitiveType;
    }

    return complexProtoType;
  }

  samePrimitiveType(array) {
    let current = this.toPrimitiveType(array[0]);
    if (current.complex) {
      return current;
    }

    for (let i = 1; i < array.length; i++) {
      const next = this.toPrimitiveType(array[i]);

      if (next.complex) {
        return next;
      }

      current = mergePrimitiveType(current, next);
      if (current.complex) {
        return current;
      }
    }

    return current;
  }

  addNested(collector, messageName, source, inlineShift) {
    const lines = [];

    lines.push(`${inlineShift}message ${messageName} {`);

    let index = 1;

    for (const [key, value] of Object.entries(source)) {
      const typeName = this.analyzeProperty(key, value, collector, inlineShift)

      lines.push(`${inlineShift}    ${typeName} ${key} = ${index};`);

      index += 1;
    }

    lines.push(`${inlineShift}}`);

    collector.addMessage(lines);
  }

  addShift() {
    if (this.options.inline) {
      return `    `;
    }
    return "";
  }
}

export function convert(source, options) {
  if (source === "") {
    return new Result("", "");
  }

  // hack that forces floats to stay as floats
  const text = source.replace(/\.0/g, ".1");

  try {
    const json = JSON.parse(text);

    const analyzer = new Analyzer(options);

    return new Result(analyzer.analyze(json), "");
  } catch (e) {
    return new Result("", e.message);
  }
}

function toMessageName(source) {
  return source.charAt(0).toUpperCase() + source.substr(1).toLowerCase();
}

function render(imports, messages, lines, options) {
  const result = [];

  result.push(`syntax = "proto3";`);

  if (imports.size > 0) {
    result.push("");

    for (const importName of imports) {
      result.push(`import "${importName}";`);
    }
  }

  result.push("");
  if (options.inline) {
    result.push("message SomeMessage {");
    if (messages.length > 0) {
      result.push("");
      for (const message of messages) {
        result.push(...message);
        result.push("");
      }
    }
    result.push(...lines);
    result.push("}");
  } else {
    for (const message of messages) {
      result.push(...message);
      result.push("");
    }

    result.push("message SomeMessage {");
    result.push(...lines);
    result.push("}");
  }

  return result.join("\n");
}

function mergePrimitiveType(a, b) {
  if (a.name === b.name) {
    return a;
  }

  if (a.merge && b.merge) {
    if (a.name === "double") {
      return a;
    }

    if (b.name === "double") {
      return b;
    }

    if (a.name === "int64") {
      return a;
    }

    if (b.name === "int64") {
      return b;
    }

    if (a.name === "uint64") {
      if (b.name === "uint32") {
        return a;
      }
    } else if (b.name === "uint64") {
      if (a.name === "uint32") {
        return b;
      }
    }

    return int64ProtoPrimitiveType;
  }

  return complexProtoType;
}

function numberType(value) {
  if (value % 1 === 0) {
    if (value < 0) {
      if (value < -2147483648) {
        return "int64";
      }
      return "int32";
    }
    if (value > 4294967295) {
      return "uint64";
    }
    return "uint32";
  }
  return "double";
}

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
    if (display !== 'none') {
      inputContainer.classList.add('hidden');
    } else {
      inputContainer.classList.remove('hidden');
      if (outputDefaultDisplay === 'none') {
        outputContainer.classList.add('hidden');
      }
    }
  });
  const toConvert = function (value) {
    prevValue = value;
    if (!value) {
      inputJson.classList.remove('!border-red-500');
      if (outputDefaultDisplay !== 'none') {
        output.innerHTML = '';
      }
      return;
    }
    const options = new Options(true, true, false);
    const result = convert(value, options);
    if (!result.success) {
      inputJson.classList.add('!border-red-500');
      if (outputDefaultDisplay !== 'none') {
        output.innerHTML = '<p class="text-red-500 font-bold p-3">' + result.error + '</p>';
      }
      return;
    }
    outputContent = result.success;
    const html = Prism.highlight(outputContent, Prism.languages[window.ConvertOptions.lang], window.ConvertOptions.lang);
    output.innerHTML = '<pre class="language-' + window.ConvertOptions.lang + ' !px-3 !py-2 !m-0 h-full !rounded-none scrollable"><code class="prism language-' + window.ConvertOptions.lang + '" >' + html + '</code></pre>';
    if (outputDefaultDisplay === 'none') {
      inputContainer.classList.add('hidden');
    }
    outputContainer.classList.remove('hidden');
    inputJson.classList.remove('!border-red-500');
  };
  inputJson.addEventListener('keyup', function () {
    if (prevValue !== this.value) {
      if (isAutoConvert) {
        toConvert(this.value);
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
})(window);
