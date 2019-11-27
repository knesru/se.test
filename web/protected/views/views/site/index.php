<?php
/**
 * @var $dataProvider CActiveDataProvider
 * @var $model Component
 */

$this->pageTitle=Yii::app()->name . ' - На сборке';
$this->breadcrumbs=array(
	'На сборке',
);

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->getComps(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name'=>'partnumber',
		),
		array(
			'name'=>'updated',
		),
		array(
			'name'=>'value',
            'header'=>'value',
            'filter'=>false,
            'value'=>function($data,$row){
                /** @var Component $data */
                if(!empty($data->componentproperty->p_2)){
                    $val = floatval($data->componentproperty->p_2);
                    if($val===0){
                        return $val;
                    }
                    $SI = 0;
                    $SI_prefix = array(
                            -6=>'a',
                            -5=>'f',
                            -4=>'p',
                            -3=>'n',
                            -2=>'µ',
                            -1=>'m',
                            0=>'',
                            1=>'k',
                            2=>'M',
                            3=>'G',
                            4=>'T',
                            5=>'P',
                    );
                    while ($val>=1000){
                        $val/=1000;
                        $SI++;
                    }
                    while ($val<1){
                        $val*=1000;
                        $SI--;
                    }
                    return $val.$SI_prefix[$SI];
                }
                return 0;
            }
		),
	),
));
