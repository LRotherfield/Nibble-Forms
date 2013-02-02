<?php

namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Useful;

class Password extends Text
{

    private $confirm = false;
    private $min_length = false;
    private $alphanumeric = false;

    public function __construct($label, $attributes = array())
    {
        parent::__construct($label, $attributes);
        if (isset($attributes['alphanumeric'])) {
            $this->alphanumeric = $attributes['alphanumeric'];
        }
        if (isset($attributes['min_length'])) {
            $this->min_length = $attributes['min_length'];
        }
    }

    public function validate($val)
    {
        if (!empty($this->error)) {
            return false;
        }
        if (parent::validate($val)) {
            if (Useful::stripper($val) !== false) {
                if ($this->min_length && strlen($val) < $this->min_length) {
                    $this->error[] = sprintf('must be more than %s characters', $this->min_length);
                }
                if ($this->alphanumeric && (!preg_match("/[A-Za-z]+/", $val) || !preg_match("/[0-9]+/", $val))) {
                    $this->error[] = 'must have at least one alphabetic character and one numeric character';
                }
            }
        }
        if ($this->confirm) {
            $request = strtoupper($this->form->getMethod()) == 'POST' ? $_POST : $_GET;
            if ($val != $request[$this->form->getName()][$this->confirm]) {
                $this->error[] = 'The passwords provided do not match password';
            }
        }

        return !empty($this->error) ? false : true;
    }

    public function returnField($form_name, $name, $value = '')
    {
        $this->field_type = 'password';

        return parent::returnField($form_name, $name, $value);
    }

    public function addConfirmation($field_name, $attributes = array())
    {
        $this->form->addField($field_name, 'password', $attributes + $this->attributes);
        $this->confirm = $field_name;
    }

}
