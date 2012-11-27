<?php
namespace NibbleForms\Field;

class Password extends Text
{

    private $confirm = false;
    private $min_length = false;
    private $alphanumeric = false;

    public function __construct($label, $attributes = array(), $content = '/.*/')
    {
        parent::__construct($label, $attributes, $content);
        if (isset($attributes['alphanumeric']))
            $this->alphanumeric = $attributes['alphanumeric'];
        if (isset($attributes['min_length']))
            $this->min_length = $attributes['min_length'];
    }

    public function validate($val)
    {
        if (!empty($this->error))
            return false;
        if (parent::validate($val)) {
            if (\NibbleForms\Useful::stripper($val) !== false) {
                if ($this->min_length && strlen($val) < $this->min_length)
                    $this->error[] = sprintf('must be more than %s characters', $this->min_length);
                if ($this->alphanumeric && (!preg_match("/[A-Za-z]+/", $val) || !preg_match("/[0-9]+/", $val)))
                    $this->error[] = 'must have at least one alphabetic character and one numeric character';
            }
        }
        if ($this->confirm) {
            if ($val != $_POST[$this->confirm]) {
                $form = \NibbleForms\NibbleForm::getInstance();
                $form->{$this->confirm}->error[] = 'must match password';
            }
        }
        return !empty($this->error) ? false : true;
    }

    public function returnField($name, $value = '')
    {
        $this->field_type = 'password';
        return parent::returnField($name, $value);
    }

    public function addConfirmation($label, $open_field = false, $close_field = false, $open_html = false, $close_html = false)
    {
        $form = \NibbleForms\NibbleForm::getInstance();
        if ($form->checkField('confirm_password')) {
            $i = 2;
            while ($form->checkField('confirm_password_' . $i))
                $i++;
            $form->{'confirm_password_' . $i} = new Password($label, $this->attributes, $this->content);
            $form->{'confirm_password_' . $i}->customHtml($open_field, $close_field, $open_html, $close_html);
            $this->confirm = 'confirm_password_' . $i;
        } else {
            $form->confirm_password = new Password($label, $this->attributes, $this->content);
            $form->confirm_password->customHtml($open_field, $close_field, $open_html, $close_html);
            $this->confirm = 'confirm_password';
        }
    }

}