<?php
use yii\components\Migration;

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
			'id' => $this->primaryKey()->comment(\Yii::t($this->messageCategory, 'id')),
			'client_id' => $this->string(68)->notNull()->unique()->comment(\Yii::t($this->messageCategory, 'client id')),
			'name' => $this->string()->notNull()->comment(\Yii::t($this->messageCategory, 'name')),
			'basename' => $this->string()->notNull()->comment(\Yii::t($this->messageCategory, 'basename')),
			'extension' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'extension')),
			'path' => $this->text()->notNull()->comment(\Yii::t($this->messageCategory, 'path')),
			'type' => $this->string(68)->notNull()->defaultValue('Image')->comment(\Yii::t($this->messageCategory, 'type')),
			'size' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'size')),
			'status' => $this->boolean()->notNull()->defaultValue(1)->comment(\Yii::t($this->messageCategory, 'status')),
			'pv' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'page view')),
			'uv' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'unique visitor')),
			'operator_id' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'operator id')),
			'creator_id' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'creator id')),
			'created_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'created time')),
			'updated_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'updated time')),
		], $tableOptions);
		$this->createIndex('status', '{{%attachment}}', 'status');
		$this->addCommentOnTable('{{%attachment}}', \Yii::t($this->messageCategory, 'attachment'));
	}

	public function safeDown() {
		$this->dropTable('{{%attachment}}');
	}

}
