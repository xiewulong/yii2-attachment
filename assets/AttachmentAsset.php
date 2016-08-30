<?php
/*!
 * yii - asset - attachment
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-attachment
 * https://raw.githubusercontent.com/xiewulong/yii2-attachment/master/LICENSE
 * create: 2016/8/27
 * update: 2016/8/27
 * since: 0.0.1
 */

namespace yii\attachment\assets;

use Yii;
use yii\web\AssetBundle;

class AttachmentAsset extends AssetBundle {

	public $sourcePath = '@yii/attachment/dist';

	public $js = [
		'js/Attachment.js',
	];

	public $depends = [
		'yii\xui\JqueryAsset',
	];

}
