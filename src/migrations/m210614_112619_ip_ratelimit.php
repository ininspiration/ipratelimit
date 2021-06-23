<?php

use yii\db\Migration;

/**
 * Class m210614_112619_ip_ratelimit
 */
class m210614_112619_ip_ratelimit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("ip_ratelimit", [
            "id" => $this->primaryKey(),
            "client_ip" => $this->string(50)->notNull()->defaultValue(""),
            "allowance" => $this->integer()->notNull(),
            "created_at" => $this->integer()->notNull(),
            "allowance_updated_at" => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210614_112619_ip_ratelimit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210614_112619_ip_ratelimit cannot be reverted.\n";

        return false;
    }
    */
}
