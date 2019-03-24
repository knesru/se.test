<?php

class m190320_100712_add_column_status extends CDbMigration
{
	public function up()
	{
        $this->addColumn('extcomponents', 'status', 'int default 0');
        $this->createIndex('extcomponents_status', 'extcomponents', 'status', false);
	}

	public function down()
	{
        $this->dropIndex('extcomponents_status', 'extcomponents');
        $this->dropColumn('extcomponents','status');
	}
}