<?php
/*!
 * yii2 - module - attachment
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-attachment
 * https://raw.githubusercontent.com/xiewulong/yii2-attachment/master/LICENSE
 * create: 2016/8/27
 * update: 2016/9/9
 * since: 0.0.1
 */

namespace yii\attachment;

use Yii;
use yii\imagine\Image;
use yii\helpers\FileHelper;
use OSS\OssClient;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

use yii\attachment\models\Attachment;

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

	public $styles = [];

	public $styleSeparator = '!';

	public $cloud;

	/**
	 * Put file to qiniu
	 *
	 * @since 0.0.1
	 * @param {string} $file
	 * @param {string} $path
	 * @return {boolean}
	 */
	private function putFileToQiniu($file, $path) {
		if(!isset($this->cloud['accessKey'])
			|| !isset($this->cloud['secretKey'])
			|| !isset($this->cloud['bucket'])) {
			return false;
		}

		$auth = new Auth($this->cloud['accessKey'], $this->cloud['secretKey']);
		$token  = $auth->uploadToken($this->cloud['bucket']);

		$uploadManager = new UploadManager();
		list(, $error) = $uploadManager->putFile($token, $path, $file);

		return $error === null;
	}

	/**
	 * Put file to oss
	 *
	 * @since 0.0.1
	 * @param {string} $file
	 * @param {string} $path
	 * @return {boolean}
	 */
	private function putFileToOss($file, $path) {
		if(!isset($this->cloud['accessKeyId'])
			|| !isset($this->cloud['accessKeySecret'])
			|| !isset($this->cloud['endpoint'])
			|| !isset($this->cloud['bucket'])) {
			return false;
		}

		if(!isset($this->cloud['isCName'])) {
			$this->cloud['isCName'] = false;
		}
		if(!isset($this->cloud['securityToken'])) {
			$this->cloud['securityToken'] = null;
		}

		try {
			$ossClient = new OssClient($this->cloud['accessKeyId'], $this->cloud['accessKeySecret'], $this->cloud['endpoint'], $this->cloud['isCName'], $this->cloud['securityToken']);
			$ossClient->uploadFile($this->cloud['bucket'], $path, $file);
			return true;
		} catch(OssException $e) {
			return false;
		}
	}

	/**
	 * Put file to cloud
	 *
	 * @since 0.0.1
	 * @param {string} $file
	 * @param {string} $path
	 * @return {boolean}
	 */
	public function putFileToCloud($file, $path) {
		if(!is_array($this->cloud) || !isset($this->cloud['type'])) {
			return false;
		}

		switch(strtolower($this->cloud['type'])) {
			case 'oss':
				return $this->putFileToOss($file, $path);
				break;
			case 'qiniu':
				return $this->putFileToQiniu($file, $path);
				break;
		}
	}

	/**
	 * Save file
	 *
	 * @since 0.0.1
	 * @param {string} $file
	 * @param {string} $path
	 * @return {boolean}
	 */
	public function saveFile($file, $path) {
		return $this->cloud ? $this->putFileToCloud($file, $path) : move_uploaded_file($file, $this->fullFilePath($path));
	}

	/**
	 * Return style path
	 *
	 * @since 0.0.1
	 * @param {string} $path
	 * @param {string} $style Image style
	 * @return {string}
	 */
	private function styleHandler($path, $style = null) {
		if($style) {
			if($this->cloud) {
				$path = $path . $this->styleSeparator . $style;
			} else if(isset($this->styles[$style])) {
				$fullFilePath = $this->fullFilePath($path);
				list($path, $extension) = explode('.', $path);
				$path = $path . $this->nameSeparator . $style . '.' . $extension;
				$fullThumbnailPath = $this->fullFilePath($path);
				if(!is_file($fullThumbnailPath)) {
					$styleOptions = $this->styles[$style];
					Image::thumbnail($fullFilePath, $styleOptions[0], $styleOptions[1], isset($styleOptions[2]) && $styleOptions[2] ? 'outbound' : 'inset')->save($fullThumbnailPath);
				}
			}
		}

		return $path;
	}

	/**
	 * Return full file url
	 *
	 * @since 0.0.1
	 * @param {string} $path
	 * @param {string} $style Image style
	 * @return {string}
	 */
	public function fullFileUrl($path, $style = null) {
		return ltrim(\Yii::$app->urlManager->createAbsoluteUrl(\Yii::getAlias(rtrim($this->baseFileUrl, '/') . '/' . ltrim($this->styleHandler($path, $style), '/'))), '/');
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
