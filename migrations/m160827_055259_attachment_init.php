<?php
use yii\components\Migration;

use yii\attachment\models\Attachment;

class m160827_055259_attachment_init extends Migration {

	public $messageCategory ='attachment';

	public function init() {
		$this->messagesPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'messages';

		parent::init();
	}

	public function safeUp() {
		$tableOptions = null;
		if($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%attachment}}', [
			'id' => $this->primaryKey()->comment(\Yii::t($this->messageCategory, 'Id')),
			'client_id' => $this->string(68)->notNull()->unique()->comment(\Yii::t($this->messageCategory, 'Client id')),
			'name' => $this->string()->notNull()->comment(\Yii::t($this->messageCategory, 'Name')),
			'basename' => $this->string()->notNull()->comment(\Yii::t($this->messageCategory, 'Basename')),
			'extension' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'Extension')),
			'path' => $this->text()->notNull()->comment(\Yii::t($this->messageCategory, 'Path')),
			'type' => $this->string(68)->notNull()->defaultValue(Attachment::TYPE_IMAGE)->comment(\Yii::t($this->messageCategory, 'Type')),
			'size' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'Size')),
			'status' => $this->smallInteger()->notNull()->defaultValue(Attachment::STATUS_ACTIVE)->comment(\Yii::t($this->messageCategory, 'Status')),
			'pv' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'Page view')),
			'uv' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'Unique visitor')),
			'operator_id' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'Operator id')),
			'creator_id' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'Creator id')),
			'created_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'Created time')),
			'updated_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'Updated time')),
		], $tableOptions);
		$this->createIndex('status', '{{%attachment}}', 'status');
		$this->addCommentOnTable('{{%attachment}}', \Yii::t($this->messageCategory, 'Attachment'));
	}

	public function safeDown() {
		$this->dropTable('{{%attachment}}');
	}

}
