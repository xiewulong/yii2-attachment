/*!
 * Attachment
 * xiewulong <xiewulong@vip.qq.com>
 * create: 2016/8/27
 * version: 0.0.1
 */

(function($, window, document, undefined) {
	var Attachment	= function(input, fn, prop) {
			prop = prop || {};
			this.input = input;
			this._name = this.input.name;
			this.$input = $(input);
			this.$parent = this.$input.parent();
			this.action = prop.action || this.$input.attr('data-attachment-upload-action');
			this.multiple = prop.multiple || this.$input.attr('data-attachment-multiple');
			this.csrf = {
				param: prop.csrf_param || this.$input.attr('data-csrf-param') || '_csrf',
				token: prop.csrf_token || this.$input.attr('data-csrf-token'),
			};
			this.pre = prop.pre || 'Attachment';
			this.before = prop.before || function() {};
			this.fn = fn;

			this.input.value && this.action && this.fn && this.upload();
		};

	Attachment.prototype = {
		upload: function() {
			var _this = this;
			this.before && this.before.call(this.input);
			this.setName();
			this.createElements();
			window[_this.name] = function(d) {_this.callback(d);};
			this.$form.submit();
		},
		createElements: function() {
			this.$iframe = $('<iframe style="display:none;" name="' + this.name + '"></iframe>').appendTo('body');
			this.$form = $('<form style="display:none;" action="' + this.action + '" target="' + this.name + '" method="post" enctype="multipart/form-data"></form>').appendTo('body');
			this.multiple && this.$form.append('<input type="hidden" name="multiple" value="' + this.multiple + '" />');
			this.csrf.token && this.$form.append('<input type="hidden" name="' + this.csrf.param + '" value="' + this.csrf.token + '" />');
			this.$form.append('<input type="hidden" name="name" value="' + this.name + '" />');
			this.$form.append(this.input);
		},
		setName: function() {
			var random = (+ new Date()).toString() + Math.floor(Math.random() * 999 + 100).toString();
			this.name = this.pre + random;
			this.input.name = this.name + (this.multiple ? '[]' : '');
		},
		callback: function(d) {
			this.input.name = this._name;
			this.input.value = '';
			this.$parent.append(this.input);
			this.$form.remove();
			this.$iframe.remove();
			this.fn.call(this.input, d);
		}
	}

	$(document).on('change', '[data-attachment-upload]', function() {
		new Attachment(this, function(d) {
			$(this).trigger('uploaded.attachment.file', d);
		}, {
			'before': function() {
				$(this).trigger('upload.attachment.file');
			}
		});
	});

	$.fn.attachment = function(fn, prop) {
		return this.on('change', function() {
			new fileupload(this, fn, prop);
		});
	};

	window.Attachment = Attachment;
})(jQuery, window, document);
