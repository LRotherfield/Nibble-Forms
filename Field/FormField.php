<?php
namespace NibbleForms\Field;

abstract class FormField
{

    public $custom_error = array();
    public $html = array(
        'open_field' => false,
        'close_field' => false,        
        'open_html' => false,
        'close_html' => false
    );

    /**
     * Return the current field, i.e label and input
     */
    abstract public function returnField($name, $value = '');

    /**
     * Validate the current field
     */
    abstract public function validate($val);

    /**
     * Apply custom error message from user to field
     */
    public function errorMessage($message)
    {
        $this->custom_error[] = $message;
    }

    /**
     * Apply custom html to open and close of field
     */
    public function customHtml($open_field = false, $close_field = false, $open_html = false, $close_html = false)
    {
        $this->html = array('open_field' => $open_field, 'close_field' => $close_field, 'open_html' => $open_html, 'close_html' => $close_html);
    }

}
