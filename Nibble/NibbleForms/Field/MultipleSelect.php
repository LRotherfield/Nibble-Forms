<?php
namespace Nibble\NibbleForms\Field;

class MultipleSelect extends MultipleOptions
{

    public function __construct($label, $attributes = array())
    {
        parent::__construct($label, $attributes);
    }

    public function returnField($form_name, $name, $value = '')
    {
        $field = sprintf('<select name="%2$s[%1$s][]" id="%2$s_%1$s" multiple="multiple">', $name, $form_name);
        foreach ($this->options as $key => $val) {
            $attributes = $this->getAttributeString($val);
            $field .= sprintf('<option value="%s" %s>%s</option>', $key, (is_array($value) && in_array((string) $key, $value) ? 'selected="selected"' : '') . $attributes['string'], $attributes['val']);
        }
        $field .= '</select>';
        $class = !empty($this->error) ? 'error choice_label' : 'choice_label';

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s_%s" class="%s">%s</label>', $form_name, $name, $class, $this->label),
            'field' => $field,
            'html' => $this->html
        );
    }

}
