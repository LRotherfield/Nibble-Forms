<?php
namespace NibbleForms\Field;

class Select extends Options {

    protected $show_size;

    public function __construct($label, $options, $show_size = false, $required = true, $false_values = array()) 
    {
        parent::__construct($label, $options, $required, $false_values);
        $this->show_size = $show_size;
    }

    public function returnField($form_name, $name, $value = '') 
    {
        $field = sprintf('<select name="%3$s[%1$s]" id="%3$s_%1$s" %2$s>', $name, ($this->show_size ? "size='$this->show_size'" : ''), $form_name);
        foreach ($this->options as $key => $val) {
            $attributes = $this->getAttributeString($val);
            $field .= sprintf('<option value="%s" %s>%s</option>', $key, ((string) $key === (string) $value ? 'selected="selected"' : '') . $attributes['string'], $attributes['val']);
        }
        $field .= '</select>';
        $class = !empty($this->error) ? ' class="error"' : '';
        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s_%s"%s>%s</label>', $form_name, $name, $class, $this->label),
            'field' => $field,
            'html' => $this->html
        );
    }

}
