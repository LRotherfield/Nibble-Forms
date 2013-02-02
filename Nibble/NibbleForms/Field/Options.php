<?php

namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Useful;

abstract class Options extends BaseOptions
{

    public function validate($val)
    {
        if ($this->required) {
            if (Useful::stripper($val) === false) {
                $this->error[] = 'is required';
            }
        }
        if (in_array($val, $this->false_values)) {
            $this->error[] = "$val is not a valid choice";
        }

        return !empty($this->error) ? false : true;
    }

}
