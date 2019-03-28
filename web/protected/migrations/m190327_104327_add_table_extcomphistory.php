<?php

class m190327_104327_add_table_extcomphistory extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{storecorrection_ext}}', array(
          'id' => 'pk',
          'initiatoruserid' => 'int not null',
          'created_at' => 'datetime not null',
          'partnumber' => 'string not null',
          'qty' => 'int not null',
          'description' => 'text not null'
        ));

        $this->execute("comment on column {{storecorrection_ext}}.id is 'ID';");
        $this->execute("comment on column {{storecorrection_ext}}.initiatoruserid is 'Пользователь';");
        $this->execute("comment on column {{storecorrection_ext}}.created_at is 'Добавлено';");
        $this->execute("comment on column {{storecorrection_ext}}.partnumber is 'Партномер';");
        $this->execute("comment on column {{storecorrection_ext}}.qty is 'Количество';");
        $this->execute("comment on column {{storecorrection_ext}}.description is 'Описание';");

        $this->createIndex('storecorrection_ext_initiatoruserid', '{{storecorrection_ext}}', 'initiatoruserid', false);
        $this->createIndex('storecorrection_ext_partnumber', '{{storecorrection_ext}}', 'partnumber', false);
    }

    public function safeDown()
    {

        $this->dropIndex('storecorrection_ext_initiatoruserid','{{storecorrection_ext}}');
        $this->dropIndex('storecorrection_ext_partnumber','{{storecorrection_ext}}');
        $this->dropTable('{{storecorrection_ext}}');
    }
}