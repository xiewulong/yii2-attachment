<?php
/*!
 * yii - widget - attachment
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-attachment
 * https://raw.githubusercontent.com/xiewulong/yii2-attachment/master/LICENSE
 * create: 2016/8/27
 * update: 2016/8/30
 * since: 0.0.1
 */

namespace yii\attachment\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use yii\attachment\assets\AttachmentAsset;

class Attachment extends Widget {

	public $model;

	public $attribute;

	public $value;

	public $options = [];

	public $hiddenOptions = [];

	public $fileOptions = [];

	public $uploadAction;

	public $loadAction;

	public $multiple = false;

	private $_name;

	private $_value;

	public function init() {
		parent::init();

		$this->setNameAndValue();
		AttachmentAsset::register($this->view);
	}

	public function run() {
		return Html::tag('div', $this->hiddenInput . $this->fileInput, $this->options);
	}

	protected function getFileInput() {
		if($this->multiple) {
			$this->_name .= '[]';
			$this->fileOptions['multiple'] = $this->fileOptions['data-attachment-multiple'] = 'multiple';
		}
		return Html::input('file', null, null, ArrayHelper::merge($this->fileOptions, [
			'data-attachment-upload' => $this->_name,
			'data-attachment-upload-action' => $this->uploadAction,
			'data-attachment-load-action' => $this->loadAction,
			'data-csrf-param' => \Yii::$app->request->csrfParam,
			'data-csrf-token' => \Yii::$app->request->csrfToken,
		]));
	}

	protected function getHiddenInput() {
		return $this->_value ? Html::input('hidden', $this->_name, $this->_value, $this->hiddenOptions) : null;
	}

	private function setNameAndValue() {
		if($this->model) {
			$this->_name = Html::getInputName($this->model, $this->attribute);
			$this->_value = Html::getAttributeValue($this->model, $this->attribute);
			if(!array_key_exists('id', $this->options)) {
				$this->options['id'] = Html::getInputId($this->model, $this->attribute);
			}
		} else {
			$this->_name = $this->attribute;
			$this->_value = $this->value;
		}
	}

}
