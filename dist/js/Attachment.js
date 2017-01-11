/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;
/******/
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/*!***********************************************************!*\
  !*** ./vendor/xiewulong/yii2-attachment/js/Attachment.js ***!
  \***********************************************************/
/***/ function(module, exports) {

	'use strict';
	
	var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();
	
	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
	
	/*!
	 * Attachment
	 * xiewulong <xiewulong@vip.qq.com>
	 * create: 2016/8/27
	 * version: 0.0.1
	 */
	
	(function ($, window, document, undefined) {
		var Attachment = function () {
			function Attachment(input, fn) {
				var prop = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	
				_classCallCheck(this, Attachment);
	
				this.input = input;
				this._name = this.input.name;
				this.$input = $(input);
				this.$parent = this.$input.parent();
				this.action = prop.action || this.$input.attr('data-attachment-upload-action');
				this.multiple = prop.multiple || this.$input.attr('data-attachment-multiple');
				this.csrf = {
					param: prop.csrf_param || this.$input.attr('data-csrf-param') || '_csrf',
					token: prop.csrf_token || this.$input.attr('data-csrf-token')
				};
				this.pre = prop.pre || 'Attachment';
				this.before = prop.before || function () {};
				this.fn = fn;
	
				this.input.value && this.action && this.fn && this.upload();
			}
	
			_createClass(Attachment, [{
				key: 'upload',
				value: function upload() {
					var _this = this;
	
					this.before && this.before.call(this.input);
	
					this.setName();
					this.createElements();
	
					window[this.name] = function (d) {
						_this.callback(d);
					};
	
					this.$form.submit();
				}
			}, {
				key: 'createElements',
				value: function createElements() {
					this.$iframe = $('<iframe style="display:none;" name="' + this.name + '"></iframe>').appendTo('body');
					this.$form = $('<form style="display:none;" action="' + this.action + '" target="' + this.name + '" method="post" enctype="multipart/form-data"></form>').appendTo('body');
					this.multiple && this.$form.append('<input type="hidden" name="multiple" value="' + this.multiple + '" />');
					this.csrf.token && this.$form.append('<input type="hidden" name="' + this.csrf.param + '" value="' + this.csrf.token + '" />');
					this.$form.append('<input type="hidden" name="name" value="' + this.name + '" />');
					this.$form.append(this.input);
				}
			}, {
				key: 'setName',
				value: function setName() {
					var random = (+new Date()).toString() + Math.floor(Math.random() * 999 + 100).toString();
	
					this.name = this.pre + random;
					this.input.name = this.name + (this.multiple ? '[]' : '');
				}
			}, {
				key: 'callback',
				value: function callback(d) {
					this.input.name = this._name;
					this.input.value = '';
					this.$parent.append(this.input);
					this.$form.remove();
					this.$iframe.remove();
					this.fn.call(this.input, d);
				}
			}]);
	
			return Attachment;
		}();
	
		$(document).on('change', '[data-attachment-upload]', function () {
			new Attachment(this, function (d) {
				$(this).trigger('uploaded.attachment.file', d);
			}, {
				'before': function before() {
					$(this).trigger('upload.attachment.file');
				}
			});
		});
	
		$.fn.attachment = function (fn, prop) {
			return this.on('change', function () {
				new Attachment(this, fn, prop);
			});
		};
	})(jQuery, window, document);

/***/ }
/******/ ]);
//# sourceMappingURL=Attachment.js.map