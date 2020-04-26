<?php

class m191030_090653_pnid_not_null_reqid_character_var extends CDbMigration
{
	public function up()
	{
        $this->execute("alter table extcomponents alter column partnumberid drop not null;");
        $this->execute("alter table extcomponents alter column requestid type character varying;");
	}

	public function down()
	{
		return false;
	}
}