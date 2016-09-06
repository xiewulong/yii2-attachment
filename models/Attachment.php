<?php
namespace yii\attachment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\components\ActiveRecord;

/**
 * Attachment model
 *
 * @since 0.0.1
 * @property {integer} $id
 * @property {string} $client_id
 * @property {string} $name
 * @property {string} $basename
 * @property {string} $extension
 * @property {string} $path
 * @property {string} $type
 * @property {integer} $size
 * @property {integer} $status
 * @property {integer} $pv
 * @property {integer} $uv
 * @property {integer} $operator_id
 * @property {integer} $creator_id
 * @property {integer} $created_at
 * @property {integer} $updated_at
 */
class Attachment extends ActiveRecord {

	const TYPE_EXCEL = 'Excel';
	const TYPE_IMAGE = 'Image';
	const TYPE_WORD = 'Word';

	const STATUS_DELETED = 0;
	const STATUS_ACTIVE = 1;

	public $messageCategory = 'attachment';

	protected $statisticsEnable = true;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%attachment}}';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::className(),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[[
				'client_id',
				'name',
				'basename',
				'extension',
				'path',
				'type',
			], 'trim'],

			[[
				'client_id',
				'name',
				'basename',
				'extension',
				'path',
				'type',
				'size',
			], 'required'],

			['type', 'default', 'value' => static::TYPE_IMAGE],
			['type', 'in', 'range' => [
				static::TYPE_EXCEL,
				static::TYPE_IMAGE,
				static::TYPE_WORD,
			]],

			['status', 'default', 'value' => static::STATUS_ACTIVE],
			['status', 'in', 'range' => [
				static::STATUS_ACTIVE,
				static::STATUS_DELETED,
			]],

			// Query data needed
			[['client_id'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		$scenarios = parent::scenarios();

		$scenarios['upload'] = [
			'client_id',
			'name',
			'basename',
			'extension',
			'path',
			'type',
			'size',
			'status',
			'operator_id',
			'creator_id',
		];

		$scenarios['rename'] = [
			'name',
			'operator_id',
		];

		$scenarios['delete'] = [
			'status',
			'operator_id',
		];

		return $scenarios;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => \Yii::t($this->messageCategory, 'Attachment id'),
			'client_id' => \Yii::t($this->messageCategory, 'Client id'),
			'name' => \Yii::t($this->messageCategory, 'Name'),
			'basename' => \Yii::t($this->messageCategory, 'Basename'),
			'extension' => \Yii::t($this->messageCategory, 'Extension'),
			'path' => \Yii::t($this->messageCategory, 'Path'),
			'type' => \Yii::t($this->messageCategory, 'Type'),
			'size' => \Yii::t($this->messageCategory, 'Size'),
			'status' => \Yii::t($this->messageCategory, 'Status'),
			'pv' => \Yii::t($this->messageCategory, 'Page view'),
			'uv' => \Yii::t($this->messageCategory, 'Unique Visitor'),
			'operator_id' => \Yii::t($this->messageCategory, 'Operator id'),
			'creator_id' => \Yii::t($this->messageCategory, 'Creator id'),
			'created_at' => \Yii::t($this->messageCategory, 'Created time'),
			'updated_at' => \Yii::t($this->messageCategory, 'Updated time'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeHints() {
		return [
			'id' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'choose'),
				'attribute' => \Yii::t($this->messageCategory, 'Attachment id'),
			]),
			'client_id' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Generate'),
				'attribute' => \Yii::t($this->messageCategory, 'Client id'),
			]),
			'name' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Name'),
			]),
			'basename' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Basename'),
			]),
			'extension' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Extension'),
			]),
			'path' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Name'),
			]),
			'type' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Type'),
			]),
			'size' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Enter'),
				'attribute' => \Yii::t($this->messageCategory, 'Size'),
			]),
			'status' => \Yii::t($this->messageCategory, 'Please {action} {attribute}', [
				'action' => \Yii::t($this->messageCategory, 'Choose'),
				'attribute' => \Yii::t($this->messageCategory, 'Status'),
			]),
		];
	}

	/**
	 * Return type items in every scenario
	 *
	 * @since 0.0.1
	 * @return {array}
	 */
	public function typeItems() {
		return [
			[
				static::TYPE_EXCEL => \Yii::t($this->messageCategory, static::TYPE_EXCEL),
				static::TYPE_IMAGE => \Yii::t($this->messageCategory, static::TYPE_IMAGE),
				static::TYPE_WORD => \Yii::t($this->messageCategory, static::TYPE_WORD),
			],
		];
	}

	/**
	 * Return status items in every scenario
	 *
	 * @since 0.0.1
	 * @return {array}
	 */
	public function statusItems() {
		return [
			[
				static::STATUS_DELETED => \Yii::t($this->messageCategory, 'Deleted'),
				static::STATUS_ACTIVE => \Yii::t($this->messageCategory, 'Active'),
			],
		];
	}

	/**
	 * Running a common Handler
	 *
	 * @since 0.0.1
	 * @return {boolean}
	 */
	public function commonHandler() {
		if(!$this->validate()) {
			return false;
		}

		$this->operator_id = \Yii::$app->user->isGuest ? 0 : \Yii::$app->user->identity->id;
		if($this->scenario == 'upload') {
			$this->creator_id = $this->operator_id;
		}

		return $this->save(false);
	}

	/**
	 * Running attachment full name
	 *
	 * @since 0.0.1
	 * @return {string}
	 */
	public function getFullName() {
		return $this->name . '.' . $this->extension;
	}

	/**
	 * Running attachment full basename
	 *
	 * @since 0.0.1
	 * @return {string}
	 */
	public function getFullBasename() {
		return $this->basename . '.' . $this->extension;
	}

	/**
	 * Return attachment type
	 *
	 * @since 0.0.1
	 * @param {string} $mimetype
	 * @return {string}
	 */
	public static function getTypeByMimeType($mimeType) {
		switch(mb_strtolower($mimeType, 'utf-8')) {
			case 'application/vnd.ms-excel':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				$type = static::TYPE_EXCEL;
				break;
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$type = static::TYPE_IMAGE;
				break;
			case 'application/msword':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
				$type = static::TYPE_WORD;
				break;
			default:
				$type = null;
				break;
		}

		return $type;
	}

}
