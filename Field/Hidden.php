<?php
namespace NibbleForms\Field;

class Hidden extends Text 
{

    public function __construct($attributes = array(), $label = false, $content = '/.*/') 
    {
        parent::__construct($label, $attributes, $content);
    }

    public function returnField($form_name, $name, $value = '') 
    {
        $this->field_type = 'hidden';
        return parent::returnField($form_name, $name, $value);
    }

}