<?php

namespace Nibble\NibbleForms\Field;

abstract class MultipleOptions extends BaseOptions
{

    protected $minimum_selected = false;

    public function __construct($label, $attributes = array())
    {
        parent::__construct($label, $attributes);
        if (isset($attributes['minimum_selected'])) {
            $this->minimum_selected = $attributes['minimum_selected'];
        }
    }

    public function validate($val)
    {
        if (is_array($val)) {
            if ($this->minimum_selected && count($val) < $this->minimum_selected) {
                $this->error[] = sprintf('at least %s options must be selected', $this->minimum_selected);
            }
            foreach ($val as $answer) {
                if (in_array($answer, $this->false_values)) {
                    $this->error[] = "$answer is not a valid choice";
                }
            }
        } elseif ($this->required) {
            $this->error[] = 'is required';
        }

        return !empty($this->error) ? false : true;
    }

}
