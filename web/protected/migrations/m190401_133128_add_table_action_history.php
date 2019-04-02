<?php

class m190401_133128_add_table_action_history extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{actionhistory}}', array(
            'id' => 'pk',
            'initiatoruserid' => 'int not null',
            'created_at' => 'datetime not null',
            'partnumber' => 'string',
            'ext_id' => 'string',
            'requestid' => 'string',
            'action' => 'string',
            'description' => 'text not null',
            'severity'=>'string not null'
        ));

        $this->execute("comment on column {{actionhistory}}.id is 'ID';");
        $this->execute("comment on column {{actionhistory}}.initiatoruserid is 'Пользователь';");
        $this->execute("comment on column {{actionhistory}}.created_at is 'Добавлено';");
        $this->execute("comment on column {{actionhistory}}.partnumber is 'Партномер';");
        $this->execute("comment on column {{actionhistory}}.ext_id is 'Строка';");
        $this->execute("comment on column {{actionhistory}}.requestid is 'Заявка';");
        $this->execute("comment on column {{actionhistory}}.action is 'Действие';");
        $this->execute("comment on column {{actionhistory}}.description is 'Описание';");
        $this->execute("comment on column {{actionhistory}}.severity is 'Строгость';");

        $this->createIndex('actionhistory_initiatoruserid', '{{actionhistory}}', 'initiatoruserid', false);
        $this->createIndex('actionhistory_partnumber', '{{actionhistory}}', 'partnumber', false);
        $this->createIndex('actionhistory_severity', '{{actionhistory}}', 'severity', false);
    }

    public function safeDown()
    {

        $this->dropIndex('actionhistory_initiatoruserid','{{actionhistory}}');
        $this->dropIndex('actionhistory_partnumber','{{actionhistory}}');
        $this->dropIndex('actionhistory_severity','{{actionhistory}}');
        $this->dropTable('{{actionhistory}}');
    }
}