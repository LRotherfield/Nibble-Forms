<?php
namespace NibbleForms\Field;

class Hidden extends Text
{

    public function __construct($attributes = array(), $label = false, $content = '/.*/')
    {
        parent::__construct($label, $attributes, $content);
    }

    public function returnField($name, $value = '')
    {
        $this->field_type = 'hidden';
        return parent::returnField($name, $value);
    }

}