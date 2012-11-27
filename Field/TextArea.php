<?php
namespace NibbleForms\Field;

class TextArea extends Text
{

    public function __construct($label, $attributes, $content = '/.*/')
    {
        parent::__construct($label, $attributes, $content);
        if (!isset($attributes['rows']))
            $attributes['rows'] = 6;
        if (!isset($attributes['cols']))
            $attributes['cols'] = 60;
    }

    public function returnField($name, $value = '')
    {
        $this->attributeString();
        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s" class="%s">%s</label>', $name, $this->class, $this->label),
            'field' => sprintf('<textarea name="%1$s" id="%1$s" class="%2$s" %4$s>%3$s</textarea>', $name, $this->class, $value, $this->attribute_string),
            'html' => $this->html
        );
    }

}
