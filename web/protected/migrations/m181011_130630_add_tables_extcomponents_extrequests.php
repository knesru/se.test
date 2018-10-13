<?php

class m181011_130630_add_tables_extcomponents_extrequests extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('extcomponents', array(
            'id' => 'pk',
            'partnumberid' => 'int not null',
            'partnumber' => 'string not null',
            'amount' => 'int not null',
            'userid' => 'int not null',
            'purpose' => 'text default null',
            'created_at' => 'datetime',
            'delivered' => 'int default null',
            'assembly_to' => 'datetime default null',
            'install_to' => 'datetime default null',
            'deficite' => 'text default null',
            'description' => 'text default null',
            'install_from' => 'datetime default null',
            'priority' => 'int default null',
            'requestid'=> 'int default null'
        ));

        $this->execute("comment on column extcomponents.purpose is 'Назначение';");
        $this->execute("comment on column extcomponents.created_at is 'Добавлено';");
        $this->execute("comment on column extcomponents.amount is 'Кол-во';");
        $this->execute("comment on column extcomponents.delivered is 'Сдано';");
        $this->execute("comment on column extcomponents.assembly_to is 'Скомплектовать до';");
        $this->execute("comment on column extcomponents.install_to is 'Монтаж до';");
        $this->execute("comment on column extcomponents.install_from is 'Монтаж с';");

        $this->addForeignKey('FK_extcomponents_components_partnumberid', 'extcomponents', 'partnumberid', 'tcomponent', 'partnumberid', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_extcomponents_users_userid', 'extcomponents', 'userid', 'sf_guard_user', 'id', 'CASCADE', 'CASCADE');

        // Add unique index for one to one rel
        $this->createIndex('extcomponents_partnumberid', 'extcomponents', 'partnumberid', false);
        $this->createIndex('extcomponents_partnumber', 'extcomponents', 'partnumber', false);
        $this->createIndex('extcomponents_requestid', 'extcomponents', 'requestid', true);
	}

	public function safeDown()
	{
		$this->dropForeignKey('FK_extcomponents_users_userid','extcomponents');
		$this->dropForeignKey('FK_extcomponents_components_partnumberid','extcomponents');
		$this->dropIndex('extcomponents_partnumberid','extcomponents');
		$this->dropIndex('extcomponents_partnumber','extcomponents');
		$this->dropIndex('extcomponents_requestid','extcomponents');
		$this->dropTable('extcomponents');
	}
}