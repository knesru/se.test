<?php

class m191209_135054_add_tasks_table extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{tasks}}', array(
            'id' => 'pk',
            'name' => 'string not null',
            'customer' => 'string',
            'userid' => 'int',
            'user_name' => 'string',
            'managerid' => 'int',
            'manager_name' => 'string',
            'contract' => 'string',
            'created_at' => 'date',
            'delivery_date' => 'date',
            'store_delivery_date' => 'date',
            'inspection_type' => 'string',
            'warranty' => 'int',
            'notes'=>'string',
            'store_acceptance_date'=>'date',
            'official_delivery_date'=>'date',
            'statusid'=>'int',
            'updated_at'=>'date',
        ));

        $this->execute("comment on column {{tasks}}.id is 'ID';");
        $this->execute("comment on column {{tasks}}.name is 'Номер';");
        $this->execute("comment on column {{tasks}}.customer is 'Заказчик';");
        $this->execute("comment on column {{tasks}}.userid is 'ID руководителя';");
        $this->execute("comment on column {{tasks}}.user_name is 'Руководитель';");
        $this->execute("comment on column {{tasks}}.managerid is 'ID ведущего';");
        $this->execute("comment on column {{tasks}}.manager_name is 'Ведущий';");
        $this->execute("comment on column {{tasks}}.contract is 'Контракт, договор, счет';");
//        $this->execute("comment on column {{tasks}}.products is 'Наименоване товара';");
        $this->execute("comment on column {{tasks}}.created_at is 'Дата внесения заказа';");
        $this->execute("comment on column {{tasks}}.delivery_date is 'Срок поставки';");
        $this->execute("comment on column {{tasks}}.store_delivery_date is 'Срок сдачи на склад';");
        $this->execute("comment on column {{tasks}}.inspection_type is 'Тип приемки';");
        $this->execute("comment on column {{tasks}}.warranty is 'Гарантия';");
        $this->execute("comment on column {{tasks}}.notes is 'Примечания';");
        $this->execute("comment on column {{tasks}}.store_acceptance_date is 'Дата поступления на склад';");
        $this->execute("comment on column {{tasks}}.official_delivery_date is 'Дата отгрузки по документам';");
        $this->execute("comment on column {{tasks}}.statusid is 'ID Статуса';");
//        $this->execute("comment on column {{tasks}}.status is 'Статус';");
        $this->execute("comment on column {{tasks}}.updated_at is 'Дата последнего обновления';");

        $this->createTable('{{products}}', array(
            'id' => 'pk',
            'name' => 'string not null',
            'amount' => 'int',
            'units' => 'string',
            'acceptorid' => 'int',
            'taskid'=>'int not null',
            'created_at'=>'date'
        ));

        $this->execute("comment on column {{products}}.id is 'ID';");
        $this->execute("comment on column {{products}}.name is 'Изделие';");
        $this->execute("comment on column {{products}}.amount is 'Количество';");
        $this->execute("comment on column {{products}}.units is 'Единицы измерения';");
        $this->execute("comment on column {{products}}.acceptorid is 'Получатель';");
        $this->execute("comment on column {{products}}.taskid is 'ID задачи';");

        $this->addForeignKey('FK_tasks_sf_guard_user_userid', '{{tasks}}', 'userid', 'sf_guard_user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK_products_tasks_taskid', '{{products}}', 'taskid', '{{tasks}}', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('tasks_name', '{{tasks}}', 'name', true);
        $this->createIndex('tasks_customer', '{{tasks}}', 'customer', false);
    }

    public function safeDown()
    {

        $this->dropIndex('tasks_customer','{{tasks}}');
        $this->dropIndex('tasks_name','{{tasks}}');
        $this->dropForeignKey('FK_tasks_sf_guard_user_userid','{{tasks}}');
        $this->dropForeignKey('FK_products_tasks_taskid','{{products}}');
        $this->dropTable('{{products}}');
        $this->dropTable('{{tasks}}');
    }
}