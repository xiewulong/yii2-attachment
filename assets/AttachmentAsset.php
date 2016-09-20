<?php
/*!
 * yii - asset - attachment
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-attachment
 * https://raw.githubusercontent.com/xiewulong/yii2-attachment/master/LICENSE
 * create: 2016/8/27
 * update: 2016/9/20
 * since: 0.0.1
 */

namespace yii\attachment\assets;

use Yii;
use yii\components\AssetBundle;

class AttachmentAsset extends AssetBundle {

	public $sourcePath = '@yii/attachment/dist';

	public $depends = [
		'yii\xui\JqueryAsset',
	];

	public function init() {
		parent::init();

		$this->js[] = 'js/Attachment' . $this->minimal . '.js';
	}

}
