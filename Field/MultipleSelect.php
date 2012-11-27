<?php
namespace NibbleForms\Field;

class MultipleSelect extends MultipleOptions
{

    protected $show_size = false;

    public function __construct($label, $options, $attributes = array())
    {
        parent::__construct($label, $options, $attributes);
        if (isset($attributes['show_size']))
            $this->show_size = $attributes['show_size'];
    }

    public function returnField($name, $value = '')
    {
        $field = sprintf('<select name="%1$s[]" id="%1$s" %2$s multiple="multiple">', $name, ($this->show_size ? "size='$this->show_size'" : ''));
        foreach ($this->options as $key => $val) {
            $attributes = $this->getAttributeString($val);
            $field .= sprintf('<option value="%s" %s>%s</option>', $key, (is_array($value) && in_array((string) $key, $value) ? 'selected="selected"' : '') . $attributes['string'], $attributes['val']);
        }
        $field .= '</select>';
        $class = !empty($this->error) ? ' class="error"' : '';
        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
            'field' => $field,
            'html' => $this->html
        );
    }

}