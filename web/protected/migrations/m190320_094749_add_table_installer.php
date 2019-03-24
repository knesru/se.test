<?php

class m190320_094749_add_table_installer extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{installer}}', array(
            'id' => 'pk',
            'name' => 'string not null',
            'phone' => 'string not null',
            'created_at' => 'datetime',
        ));

        $this->addColumn('extcomponents', 'installerid', 'int default null');

        $this->execute("comment on column {{installer}}.name is 'Полное имя';");
        $this->execute("comment on column {{installer}}.created_at is 'Добавлен';");
        $this->execute("comment on column {{installer}}.phone is 'Телефон';");

        $this->createIndex('installer_name', '{{installer}}', 'name', false);
        $this->createIndex('installer_phone', '{{installer}}', 'phone', false);
    }

    public function safeDown()
    {

        $this->dropIndex('installer_name','extcomponents');
        $this->dropIndex('installer_phone','extcomponents');
        $this->dropColumn('extcomponents','installerid');
        $this->dropTable('extcomponents');
    }
}