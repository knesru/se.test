<?php

class m191107_172754_requestid_not_unique extends CDbMigration
{
	public function up()
	{
	    $this->execute("DROP INDEX IF EXISTS extcomponents_requestid;");
        $this->createIndex('extcomponents_requestid', 'extcomponents', 'requestid', false);
	}

	public function down()
	{
		echo "m191107_172754_requestid_not_unique does not support migration down.\n";
		return false;
	}
}