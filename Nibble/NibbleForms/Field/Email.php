<?php

namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Useful;

class Email extends Text
{

    private $confirm = false;

    public function validate($val)
    {
        if (!empty($this->error)) {
            return false;
        }
        if (parent::validate($val)) {
            if (Useful::stripper($val) !== false) {
                if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $this->error[] = 'must be a valid email address';
                }
            }
        }
        if ($this->confirm) {
            $request = strtoupper($this->form->getMethod()) == 'POST' ? $_POST : $_GET;
            if ($val != $request[$this->form->getName()][$this->confirm]) {
                $this->error[] = 'The email addresses provided do not match';
            }
        }

        return !empty($this->error) ? false : true;
    }

    public function addConfirmation($field_name, array $attributes = array())
    {
        $this->form->addField($field_name, 'email', $attributes + $this->attributes);
        $this->confirm = Useful::slugify($field_name, '_');;
    }

    public function returnField($form_name, $name, $value = '')
    {
        $this->field_type = 'email';

        return parent::returnField($form_name, $name, $value);
    }

}
