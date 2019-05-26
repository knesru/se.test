<?php
/**
 * Created by PhpStorm.
 * User: Unomi
 * Date: 26.05.2019
 * Time: 15:00
 */

class ARModel extends CActiveRecord
{
    private $_old_attributes;
    public function SaveState()
    {
        $this->_old_attributes = $this->attributes;
    }
    public function getOldVal($attr)
    {
        if(isset($this->_old_attributes[$attr])) {
            return $this->_old_attributes[$attr];
        }
        return null;
    }
    public function setAttributes($values, $safeOnly=true)
    {
        $this->SaveState();
        return parent::setAttributes($values, $safeOnly);
    }
}