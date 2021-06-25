/*
 Copyright (c) 2007-2019, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or https://ckeditor.com/sales/license/ckfinder
 */

let config = {};
// Set your configuration options below.
// Examples:
// config.language = 'pl';
// config.skin = 'jquery-mobile';
config.language = 'zh_CN';
const hostDomain = window.parent.location.protocol + '//' + window.parent.location.host;
let currentDomain = null;
const selector = document.querySelectorAll("[type=\"text/javascript\"]")
selector.forEach(function (node, index, parent) {
    if (node.nodeName.toLowerCase() === 'script') {
        let l = document.createElement("a");
        l.href = node.src
        currentDomain = l.protocol + '//' + l.hostname
    }
});
const jwt = JSON.parse(window.localStorage.getItem('auth_app_token'));
config.connectorPath = '/backend/ckfinder/connector?token=' + jwt.value.replace(".", "--") + '&';
CKFinder.define(config);
