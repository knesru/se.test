<?php


class CheckLoginAction extends CAction
{
    public function run()
    {
        $user = Yii::app()->user;
        return Controller::j(['logged' => !is_null($user->id)]);
    }
}