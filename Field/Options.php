<?php
namespace NibbleForms\Field;

abstract class Options extends BaseOptions 
{

    protected $false_values = array();

    public function __construct($label, $attributes = array()) 
    {
        parent::__construct($label, $attributes);
    }

    public function validate($val) 
    {
        parent::validate($val);
        if ($this->required){
            if (\NibbleForms\Useful::stripper($val) === false){
                $this->error[] = 'is required';
            }
        }
        return !empty($this->error) ? false : true;
    }

}