<?php

namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Useful;
use Nibble\NibbleForms\Field;

class Text extends Field
{

    protected $label,
            $content = '/.*/',
            $attribute_string = '',
            $class = '',
            $required = true;
    public $error = array(),
            $field_type = 'text';

    public function __construct($label, $attributes = array())
    {
        $this->label = $label;
        if (isset($attributes['required'])) {
            $this->required = $attributes['required'];
        } else {
            $attributes['required'] = true;
        }
        if (isset($attributes['content'])) {
            $this->content = $attributes['content'];
        }
        $this->attributes = $attributes;
    }

    public function attributeString()
    {
        if (!empty($this->error)) {
            $this->class = 'error';
        }
        $this->attribute_string = '';
        foreach ($this->attributes as $attribute => $val) {
            if ($attribute == 'class') {
                $this->class.= ' ' . $val;
            } else {
                $this->attribute_string .= $val ? ' ' . ($val === true ? $attribute : "$attribute=\"$val\"") : '';
            }
        }
    }

    public function returnField($form_name, $name, $value = '')
    {
        $this->attributeString();

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label === false ? false : sprintf('<label for="%s_%s" class="%s">%s</label>', $form_name, $name, $this->class, $this->label),
            'field' => sprintf('<input type="%1$s" name="%6$s[%2$s]" id="%6$s_%2$s" value="%3$s" %4$s class="%5$s" />', $this->field_type, $name, $value, $this->attribute_string, $this->class, $form_name),
            'html' => $this->html
        );
    }

    public function validate($val)
    {
        if ($this->required) {
            if (Useful::stripper($val) === false) {
                $this->error[] = 'is required';
            }
        }
        if (Useful::stripper($val) !== false) {
            if (!preg_match($this->content, $val)) {
                $this->error[] = 'is not valid';
            }
        }

        return !empty($this->error) ? false : true;
    }

}
