<?php

/* Require Nibble Forms 2 */
require_once dirname(__FILE__) . '/NibbleForm.class.php';

/* Get Nibble Forms 2 instance called mega_form */
$form = \NibbleForms\NibbleForm::getInstance('mega_form');

/* Text field with custom class and max length attribute */
$form->addField('first_name', 'text', array(
    'class' => 'testy classes',
    'max_length' => 20
));

/* Email field, not required and custom label text */
$email = $form->addfield('email', 'email', array(
    'required' => false,
    'label' => 'Please confirm your email'
        ));
/* Email confirmation field which must match the value for email */
$email->addConfirmation('confirm_email', array(
    'label' => 'Please confirm your email'
));

/* Radio button field with two options, first option has an additional attribute */
$form->addField('example_select', 'radio', array(
    'choices' => array(
        "one" => array('data-example' => 'data-attribute-value', 'Choice One'),
        "two" => "Choice Two"),
    'false_values' => array("two")
));

/* If the form is valid, do something */
if ($form->validate()) {
    echo "Form has validated";
}