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

class UploadController extends Controller {

	public $defaultAction = 'index';

	private $errmsg;

	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['index', 'ueditor'],
						'allow' => true,
						'roles' => $this->module->write,
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'index' => ['post'],
				],
			],
		];
	}

	public function beforeAction($action) {
		return $action->id == 'ueditor' || parent::beforeAction($action);
	}

	public function actionUeditor($action) {
		\Yii::$app->response->format = 'json';

		$config = Json::decode(preg_replace('/(\/\*[\s\S]+?\*\/)|(\s+)/', '', file_get_contents(\Yii::getAlias('@yii/xui/dist/plugins/ueditor/php/config.json'))));
		$config['imageAllowFiles'] = ['.gif', '.jpg', '.png'];

		if($action == 'config') {
			return $config;
		}

		// $this->module->lockTypes = ['Image'];
		// $this->module->min = 0;
		// $this->module->max = $config['imageMaxSize'];
		$name = 'upfile';
		$response = ['state' => \Yii::t($this->module->messageCategory, 'File upload failed') . ', ' . \Yii::t($this->module->messageCategory, 'Please try again')];

		if(isset($_FILES[$name])) {
			$file = $_FILES[$name];
			if($data = $this->upload($file)) {
				$response['state'] = 'SUCCESS';
				$response['title'] = $file['name'];
				$response['type'] = $file['type'];
				$response['size'] = $file['size'];
				$response['url'] = '/' . str_replace('/upload/ueditor', null, \Yii::$app->controller->route) . '?id=' . $data->id;
			} else if($this->errmsg) {
				$response['state'] = \Yii::t($this->module->messageCategory, $this->errmsg);
			}
		}


		return $response;
	}

	public function actionIndex() {
		$name = \Yii::$app->request->post('name', null);
		$multiple = \Yii::$app->request->post('multiple', false);
		$response = ['error' => true, 'message' => \Yii::t($this->module->messageCategory, 'File upload failed') . ', ' . \Yii::t($this->module->messageCategory, 'Please try again')];

		if($name && $_FILES && isset($_FILES[$name])) {
			$files = $_FILES[$name];
			if($multiple) {
				$error = false;
				$data = [];
				foreach($files['tmp_name'] as $index => $file) {
					if($_data = $this->upload([
						'name' => $files['name'][$index],
						'type' => $files['type'][$index],
						'tmp_name' => $file,
						'error' => $files['error'][$index],
						'size' => $files['size'][$index],
					])) {
						$data[] = $_data;
					} else {
						$error = true;
					}
				}
			} else {
				$data = $this->upload($files);
				$error = !$data;
			}
			if($error) {
				if($this->errmsg) {
					$response['message'] = \Yii::t($this->module->messageCategory, $this->errmsg);
				}
			} else {
				$response['error'] = false;
				$response['message'] = \Yii::t($this->module->messageCategory, 'File upload successful');
			}
			if($data) {
				$response['data'] = $data;
			}
		}

		return '<script type="text/javascript">parent.' . $name . '(' . Json::encode($response) . ');</script>';
	}

	private function upload($file) {
		$error = false;
		$type = Attachment::getTypeByMimeType($file['type']);
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$extensions = FileHelper::getExtensionsByMimeType($file['type']);
		if(!$extension
			|| !in_array($extension, $extensions)
			|| $type === null
			|| !(!$this->module->lockTypes || in_array($type, $this->module->lockTypes))
			|| !(!$this->module->unsupportTypes || !in_array($type, $this->module->unsupportTypes))) {
			$this->errmsg = 'Please upload the right file type';
			$error = true;
		} else if($this->module->min && $file['size'] < $this->module->min) {
			$this->errmsg = 'File size too small';
			$error = true;
		} else if($this->module->max && $file['size'] > $this->module->max) {
			$this->errmsg = 'File size too large';
			$error = true;
		}
		if(!$error) {
			$item = new Attachment;
			$item->scenario = 'upload';
			$item->client_id = $this->module->generateClientId();
			$item->type = $type;
			$item->size = $file['size'];
			$item->extension = $extension;
			$item->name = $this->module->generateName($item->client_id, $item->type);
			$item->path = $this->module->generatePath($item->fullName);
			$item->name = $item->basename = basename($file['name'], '.' . $item->extension);
			if($this->module->saveFile($file['tmp_name'], $item->path)) {
				if($item->commonHandler()) {
					return [
						'id' => $item->client_id,
						'name' => $item->fullName,
					];
				}
				$this->errmsg = $item->firstErrorInFirstErrors;
			} else {
				$this->errmsg = 'File save failed';
			}
		}

		return [];
	}

}
