<?php
/*!
 * yii2 - module - attachment
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-attachment
 * https://raw.githubusercontent.com/xiewulong/yii2-attachment/master/LICENSE
 * create: 2016/8/27
 * update: 2016/8/30
 * since: 0.0.1
 */

namespace yii\attachment;

use Yii;

class Module extends \yii\components\Module {

	public $defaultRoute = 'load';

	public $messageCategory = 'attachment';

	public $read;

	public $write = ['@'];

	public $nameSeparator = '_';

	public $pre;

	public $typeNameEnable = true;

	public $timeNameEnable = true;

	public $timePathEnable = true;

	public $statisticsEnable = false;

	public $lockTypes = [];

	public $unsupportTypes = [];

	public $min = 0;

	public $max = 0;

	public $baseFilePath = '@webroot/assets';

	public $baseFileUrl = '@web/assets';

	/**
	 * Return full file url
	 *
	 * @since 0.0.1
	 * @param {string} $path
	 * @return {string}
	 */
	public function fullFileUrl($path) {
		return ltrim(\Yii::$app->urlManager->createAbsoluteUrl(\Yii::getAlias(rtrim($this->baseFileUrl, '/') . '/' . ltrim($path, '/'))), '/');
	}

	/**
	 * Return full file path
	 *
	 * @since 0.0.1
	 * @param {string} $path
	 * @return {string}
	 */
	public function fullFilePath($path) {
		return \Yii::getAlias(rtrim($this->baseFilePath, '/') . '/' . ltrim($path, '/'));
	}

	/**
	 * Generate file path
	 *
	 * @since 0.0.1
	 * @param {string} $name
	 * @return {string}
	 */
	public function generatePath($name) {
		$path = $this->timePathEnable ? date('Y/m/d/') : null;

		$_path = \Yii::getAlias(rtrim($this->baseFilePath, '/') . '/' . $path);
		if(!is_dir($_path)) {
			mkdir($_path, 0777, true);
		}

		return $path . $name;
	}

	/**
	 * Generate a file name
	 *
	 * @since 0.0.1
	 * @param {string} $client_id
	 * @param {string} $type
	 * @return {string}
	 */
	public function generateName($client_id, $type) {
		$nameArray = [];
		if($this->pre) {
			$nameArray[] = rtrim($this->pre, $this->nameSeparator);
		}
		if($this->typeNameEnable && $type) {
			$nameArray[] = strtolower($type);
		}
		if($this->timeNameEnable) {
			$nameArray[] = date(($this->timePathEnable ? null : 'Ymd'). 'His');
		}
		$nameArray[] = $client_id;

		return implode($this->nameSeparator, $nameArray);
	}

	/**
	 * Generate a client id
	 *
	 * @since 0.0.1
	 * @return {string}
	 */
	public function generateClientId() {
		return md5(uniqid(mt_rand(), true));
	}

}
