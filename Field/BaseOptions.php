<?php
namespace NibbleForms\Field;

abstract class BaseOptions extends FormField 
{

    protected $label, $options;
    protected $required = true;
    public $error = array();

    public function __construct($label, $options = array(), $attributes = array()) 
    {
        $this->label = $label;
        $this->options = $options;
        if (isset($attributes['required'])){
            $this->required = $attributes['required'];
        }
    }

    public function getAttributeString($val) 
    {
        $attribute_string = '';
        if (is_array($val)) {
            $attributes = $val;
            $val = $val[0];
            unset($attributes[0]);
            foreach ($attributes as $attribute => $arg) {
                $attribute_string .= $arg ? ' ' . ($arg === true ? $attribute : "$attribute=\"$arg\"") : '';
            }
        }
        return array('val' => $val, 'string' => $attribute_string);
    }

}