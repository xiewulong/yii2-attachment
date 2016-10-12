<?php
namespace yii\attachment\controllers;

use Yii;
use yii\components\Controller;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

use yii\attachment\models\Attachment;

class LoadController extends Controller {

	public $defaultAction = 'index';

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['index'],
						'allow' => true,
						'roles' => $this->module->read,
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'index' => ['get'],
				],
			],
		];
	}

	public function actionIndex($id, $style = null) {
		$item = Attachment::findOne([
			'client_id' => $id,
			'status' => Attachment::STATUS_ACTIVE,
		]);
		if(!$item
			|| !(!$this->module->lockTypes || in_array($item->type, $this->module->lockTypes))
			|| !(!$this->module->unsupportTypes || !in_array($item->type, $this->module->unsupportTypes))) {
			throw new NotFoundHttpException(\Yii::t($this->module->messageCategory, 'no matched data'));
		}

		return $this->redirect($this->module->fullFileUrl($item->accessed($this->module->statisticsEnable)->path, $style && $item->type == 'Image' ? $style : null));
	}

}
