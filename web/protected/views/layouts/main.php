<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="ru"/>

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css"
          media="screen, projection"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css"
          media="print"/>
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css"
          media="screen, projection"/>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo Yii::app()->request->baseUrl; ?>/css/redmond/jquery-ui.min.css"/>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>

<div class="container" id="page">

    <div id="header" class="ui-widget-header">
        <div id="userMenu"><?php $this->widget('zii.widgets.CMenu', array(
                'items' => array(
                    array('label' => 'Журнал всех пользователей', 'url' => array('/actionhistory/log')),
                    array('label' => 'Настройки', 'itemOptions' => array('id' => 'open_settigs_menu'), 'visible' => $this->id == 'toassembly'),
                    array('label' => 'Главная', 'url' => array('/toassembly'), 'visible' => $this->id != 'toassembly'),
//				array('label'=>'Contact', 'url'=>array('/site/contact')),
                    array('label' => 'Войти', 'itemOptions' => array('id' => 'open_popup_login'), 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest),
                    array('label' => 'Выйти (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest),
                    array('label' => '[?]', 'url' => array('/help')),
                ),
            )); ?></div>
        <?php $this->widget('zii.widgets.CBreadcrumbs', array(
            'links' => $this->breadcrumbs, 'homeLink' => CHtml::encode(Yii::app()->name)
        )); ?><!-- breadcrumbs -->

    </div><!-- header -->
    <div id="mainmenu" style="display: none">
        <?php $this->widget('zii.widgets.CMenu', array(
            'items' => array(
                array('label' => 'На сборке', 'url' => array('/toAssembly'),),
                array(
                    'label' => 'Добавить',
                    'url' => array(
                        '/toAssembly/create',
                    ),
                    'visible' => ($this->id === 'toAssembly')
                ),
                array('label' => 'Пользователи', 'url' => array('/user'),),
            ),
        )); ?>
    </div><!-- mainmenu -->


    <?php echo $content; ?>

    <div id="footer">
        Copyright &copy; <?php echo date('Y'); ?> FBarinov for Yurion.<br/>
        All Rights Reserved.<br/>
        <?php echo Yii::powered(); ?>
    </div><!-- footer -->

</div><!-- page -->
</body>
</html>