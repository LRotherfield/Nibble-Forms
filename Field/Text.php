<?php
namespace NibbleForms\Field;

class Text extends FormField 
{

    protected $label, $content;
    protected $attribute_string, $class = '';
    protected $required = true;
    public $error = array();
    public $field_type = 'text';

    public function __construct($label, $attributes = array(), $content = '/.*/') 
    {
        $this->label = $label;
        if (isset($attributes['required'])){
            $this->required = $attributes['required'];
        }
        else{
            $attributes['required'] = true;
        }
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function attributeString() 
    {
        if (!empty($this->error)){
            $this->class = 'error';
        }
        $this->attribute_string = '';
        foreach ($this->attributes as $attribute => $val) {
            if ($attribute == 'class'){
                $this->class.= ' ' . $val;
            }
            else{
                $this->attribute_string .= $val ? ' ' . ($val === true ? $attribute : "$attribute=\"$val\"") : '';
            }
        }
    }

    public function returnField($name, $value = '') 
    {
        $this->attributeString();
        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label === false ? false : sprintf('<label for="%s" class="%s">%s</label>', $name, $this->class, $this->label),
            'field' => sprintf('<input type="%1$s" name="%2$s" id="%2$s" value="%3$s" %4$s class="%5$s" />', $this->field_type, $name, $value, $this->attribute_string, $this->class),
            'html' => $this->html
        );
    }

    public function validate($val) 
    {
        if ($this->required){
            if (Useful::stripper($val) === false){
                $this->error[] = 'is required';
            }
        }
        if (Useful::stripper($val) !== false){
            if (!preg_match($this->content, $val)){
                $this->error[] = 'is not valid';
            }
        }
        return !empty($this->error) ? false : true;
    }

}