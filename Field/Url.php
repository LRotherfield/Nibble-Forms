<?php
namespace NibbleForms\Field;

class Url extends Text
{

    public function validate($val)
    {
        if (!empty($this->error)) {
            return false;
        }
        if (parent::validate($val)) {
            if (\NibbleForms\Useful::stripper($val) !== false) {
                if (!filter_var($val, FILTER_VALIDATE_URL)) {
                    $this->error[] = 'must be a valid URL';
                }
            }
        }

        return !empty($this->error) ? false : true;
    }

    public function returnField($form_name, $name, $value = '')
    {
        $this->field_type = 'url';

        return parent::returnField($form_name, $name, $value);
    }

}
