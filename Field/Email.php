<?php
namespace NibbleForms\Field;

class Email extends Text 
{

    private $confirm = false;

    public function validate($val) 
    {
        if (!empty($this->error)){
            return false;
        }
        if (parent::validate($val)){
            if (\NibbleForms\Useful::stripper($val) !== false) {
                if (!filter_var($val, FILTER_VALIDATE_EMAIL)){
                    $this->error[] = 'must be a valid email address';
                }
            }
        }
        if ($this->confirm) {
            if ($val != $_POST[$this->confirm]) {
                $form = \NibbleForms\NibbleForm::getInstance();
                $form->{$this->confirm}->error[] = 'must match email';
            }
        }
        return !empty($this->error) ? false : true;
    }

    public function addConfirmation($label, $open_field = false, $close_field = false, $open_html = false, $close_html = false) 
    {
        $form = \NibbleForms\NibbleForm::getInstance();
        if ($form->checkField('confirm_email')) {
            $i = 2;
            while ($form->checkField('confirm_email_' . $i)){
                $i++;
            }
            $form->{'confirm_email_' . $i} = new Email($label, $this->attributes, $this->content);
            $form->{'confirm_email_' . $i}->customHtml($open_field, $close_field, $open_html, $close_html);
            $this->confirm = 'confirm_email_' . $i;
        } else {
            $form->confirm_email = new Email($label, $this->attributes, $this->content);
            $form->confirm_email->customHtml($open_field, $close_field, $open_html, $close_html);
            $this->confirm = 'confirm_email';
        }
    }

    public function returnField($name, $value = '') 
    {
        $this->field_type = 'email';
        return parent::returnField($name, $value);
    }

}