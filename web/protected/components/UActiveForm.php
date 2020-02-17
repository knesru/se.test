<?php


class UActiveForm extends CActiveForm
{
    public function rowField($input,$model,$attribute,$css_row_class='')
    {
        $out = CHtml::openTag('div',array('class'=>'row '.$css_row_class));
        $out.= $this->labelEx($model, $attribute);
        $out.= $input;
        $out.= $this->error($model, $attribute);
        $out.= CHtml::closeTag('div');
        return $out;
    }

    public function rowTextField($model,$attribute,$html_options,$css_row_class='',$print=true)
    {
        $input = $this->textField($model, $attribute,$html_options);
        $out = $this->rowField($input,$model,$attribute,$css_row_class);
        if($print){
            print $out;
        }
        return $out;
    }

    public function rowDateField($model,$attribute,$html_options,$css_row_class='',$print=true)
    {
        $input = $this->dateField($model, $attribute,$html_options);
        $out = $this->rowField($input,$model,$attribute,$css_row_class);
        if($print){
            print $out;
        }
        return $out;
    }

    public function rowHiddenField($model,$attribute,$html_options,$css_row_class='',$print=true)
    {
        $out = $this->hiddenField($model, $attribute,$html_options);
        if($print){
            print $out;
        }
        return $out;
    }

    public function rowCheckBoxField($model,$attribute,$html_options,$css_row_class='',$print=true)
    {
        $input = $this->checkBox($model, $attribute,$html_options);
        $out = $this->rowField($input,$model,$attribute,$css_row_class);
        if($print){
            print $out;
        }
        return $out;
    }

    public function rowTextArea($model,$attribute,$html_options,$css_row_class='',$print=true)
    {
        $input = $this->textArea($model, $attribute,$html_options);
        $out = $this->rowField($input,$model,$attribute,$css_row_class);
        if($print){
            print $out;
        }
        return $out;
    }
}
