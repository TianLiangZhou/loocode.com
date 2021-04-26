require("@github/markdown-toolbar-element")
const App = require("./main").App

function Posts() {
  App.call(this)
  let attribute = {
    id: 0
  };
  this.getAttribute = function (name) {
    return attribute[name];
  }
  this.setAttribute = function (name, value) {
    attribute[name] = value;
  }
}

Posts.prototype = Object.create(App.prototype)
Posts.prototype.view = function (id) {
  global.fetch(this.getRequestUrl('stats'), {
    method: 'POST',
  }).then((response) => {
  });
}
Posts.prototype.isPostViews = function () {
  const match = this.options.path.match(/\/(post)\/(\d+)/i);
  if (match !== null && match.length > 2 && match[2] !== undefined) {
    return parseInt(match[2], 10)
  }
  return null
}
Posts.prototype.bootstrap = function () {
  let id = this.isPostViews();
  if (id !== null) {
    this.setAttribute('id', id);
    this.view(id)
  }
}
Posts.prototype.event = function () {
  const _this = this;
  const like = document.getElementById('btn-like');
  if (like) {
    like.addEventListener('click', function () {
      if (!_this.login()) {
        return ;
      }
      global.fetch(
        _this.getRequestUrl('like'),
        {
          method: 'POST',
        }
      ).then((response) => {
        if (response.status === 200) {
          return response.json();
        }
      }).then((body) => {
        if (body.code === 200) {
        }
      });
    })
  }
  const comment = document.getElementById('commentButton')
  comment.addEventListener('click', function () {
    if (!_this.login()) {
      return ;
    }
    const content = document.getElementById('commentTextarea').value;
    if (content === '') {
      alert("评论内容不能为空哦!");
      return false;
    }
    this.setAttribute('disabled', true)
    this.classList.remove("bg-red-500");
    this.classList.add("bg-gray-500");
    global.fetch(
      _this.getRequestUrl('comment'),
      {
        method: "POST",
        body: JSON.stringify({content: content}), // data can be `string` or {object}!
        headers: new Headers({
          'Content-Type': 'application/json'
        }),
      }
    ).then((response) => {
      this.removeAttribute('disabled')
      this.classList.add("bg-red-500");
      this.classList.remove("bg-gray-500");
      if (response.status === 200) {
        return response.json();
      }
    }).then(body => {
      if (body.code === 200) {
        this.value = "";
        window.location = window.location.href + "#reply1";
      } else {
        _this.login(body.code)
      }
    });
    return false;
  });
}
Posts.prototype.getRequestUrl = function (name) {
  return this.options.url[name] + '/' + this.getAttribute('id')
}
const app = new Posts();
app.init();
