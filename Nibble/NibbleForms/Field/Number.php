<?php

namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Useful;

class Number extends Text
{

    public function validate($val)
    {
        if (!empty($this->error)) {
            return false;
        }
        if (parent::validate($val))
            if (Useful::stripper($val) !== false) {
                if (!filter_var($val, FILTER_VALIDATE_FLOAT)) {
                    $this->error[] = 'must be numeric';
                }
            }

        return !empty($this->error) ? false : true;
    }

    public function returnField($form_name, $name, $value = '')
    {
        $this->field_type = 'number';

        return parent::returnField($form_name, $name, $value);
    }

}
