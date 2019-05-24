<?php
/* @var $this ToAssemblyController */
/* @var $model Extcomponents */
/** @var string $logData */

$this->breadcrumbs=array(
	'История действий на '.date('Y.m.d H:i:s')
);

$data = $logData['data'];
print '<table class="lighttable">';
print '<tr><th>#</th><th>Дата</th><th>Пользователь</th><th>Действие</th></tr>';
foreach ($data as $i=>$datum){
    /*
     * 'userid' => $item->initiatoruserid,
                'description' => $item->description,
                'severity' => $item->severity,
                'created_at' => $item->created_at
     * */
    printf('<tr><td>%d</td><td>%s</td><td>%s</td><td>%s</td></tr>',
        $i,
        $datum['created_at'],
        $datum['user'],
        $datum['description']
        );
}
print '</table>';