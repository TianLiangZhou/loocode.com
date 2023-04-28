import "alpinejs/dist/cdn.min"
import "whatwg-fetch"
import Toastify from "toastify-js"
(function (root) {
  root.Toastify = Toastify;

  root.defaultToast = function(text) {
    var id = 'btn-' + new Date().getSeconds();
    var children = '<div class="w-full flex items-center relative">'+
    '<div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 bg-primary rounded-lg">' +
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
      className: 'absolute right-4 top-4 flex items-center w-full max-w-xs p-4 mx-auto text-white bg-primary rounded-lg shadow z-50',
      text: children,
      escapeMarkup: false,
      duration: 3000,
    }).showToast();
    document.getElementById(id).addEventListener('click', () => {
      toast.hideToast();
    });
  };
  root.getTheme = function() {
    let theme = window.Cookie.getItem('_theme') || 'System';
    if (theme === 'System') {
      theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark' : 'Light';
    }
    return theme;
  };


  const Cookie = {
    getItem: function (sKey) {
      return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[-.+*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
    },
    setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
      if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) {
        return false;
      }
      let sExpires = "";
      if (vEnd) {
        switch (vEnd.constructor) {
          case Number:
            sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
            break;
          case String:
            sExpires = "; expires=" + vEnd;
            break;
          case Date:
            sExpires = "; expires=" + vEnd.toUTCString();
            break;
        }
      }
      document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
      return true;
    },
    removeItem: function (sKey, sPath, sDomain) {
      if (!sKey || !this.hasItem(sKey)) {
        return false;
      }
      document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "");
      return true;
    },
    hasItem: function (sKey) {
      return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[-.+*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
    },
    keys: /* optional method: you can safely remove it! */ function () {
      const aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
      for (let nIdx = 0; nIdx < aKeys.length; nIdx++) {
        aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]);
      }
      return aKeys;
    }
  };
  root.Cookie = Cookie;
  root.Components = {}, root.Components.popover = function ({
                                                              open: e = !1,
                                                              focus: t = !1
                                                            } = {}) {
    const n = ["[contentEditable=true]", "[tabindex]", "a[href]", "area[href]", "button:not([disabled])", "iframe", "input:not([disabled])", "select:not([disabled])", "textarea:not([disabled])"].map((e => `${e}:not([tabindex='-1'])`)).join(",");
    return {
      __type: "popover",
      open: e,
      init() {
        t && this.$watch("open", (e => {
          e && this.$nextTick((() => {
            !function (e) {
              const t = Array.from(e.querySelectorAll(n));
              !function e(n) {
                void 0 !== n && (n.focus({
                  preventScroll: !0
                }), document.activeElement !== n && e(t[t.indexOf(n) + 1]))
              }(t[0])
            }(this.$refs.panel)
          }))
        }));
        let e = n => {
          if (!document.body.contains(this.$el)) return void root.removeEventListener("focus", e, !0);
          let i = t ? this.$refs.panel : this.$el;
          if (this.open && n.target instanceof Element && !i.contains(n.target)) {
            let e = this.$el;
            for (; e.parentNode;)
              if (e = e.parentNode, e.__x instanceof this.constructor) {
                if ("popoverGroup" === e.__x.$data.__type) return;
                if ("popover" === e.__x.$data.__type) break
              }
            this.open = !1
          }
        };
        root.addEventListener("focus", e, !0)
      },
      onEscape() {
        this.open = !1, this.restoreEl && this.restoreEl.focus()
      },
      onClosePopoverGroup(e) {
        e.detail.contains(this.$el) && (this.open = !1)
      },
      toggle(e) {
        this.open = !this.open, this.open ? this.restoreEl = e.currentTarget : this.restoreEl && this.restoreEl.focus()
      }
    }
  }, root.Components.theme = function () {
    let theme = root.Components.popover.apply(this, arguments);
    theme.toggleTheme = function (e) {
      this.toggle();
      Alpine.store('theme').toggle(e.target.innerText);
    };
    return theme;
  }
  ;

  document.addEventListener('alpine:init', () => {
    let _theme = Cookie.getItem('_theme');
    Alpine.store('theme', {
      current: _theme || 'System',
      system: (_theme || 'System') === 'System' ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark' : 'Light') : _theme,
      toggle(theme) {
        this.current = theme;
        if (theme === 'System') {
          this.system = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark' : 'Light';
        } else {
          this.system = theme;
        }
        Cookie.setItem('_theme', theme, null, '/');
      }
    });
  });
})(window);

