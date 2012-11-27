<?php
namespace NibbleForms\Field;

abstract class Options extends BaseOptions
{

    protected $false_values = false;

    public function __construct($label, $options = array(), $attributes = array())
    {
        parent::__construct($label, $options, $attributes);
        if (isset($attributes['false_values']))
            $this->false_values = $attributes['false_values'];
    }

    public function validate($val)
    {
        if ($this->required)
            if (\NibbleForms\Useful::stripper($val) === false)
                $this->error[] = 'is required';
        if (in_array($val, $this->false_values))
            $this->error[] = 'is not a valid selection';
        return !empty($this->error) ? false : true;
    }

}