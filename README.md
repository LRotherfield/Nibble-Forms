## Nibble Forms 2, PHP form class

Nibble Forms 2 is a PHP form class that allows developers to quickly create 
HTML5 forms and validate submitted results.  Nibble Forms 2 is an evolution
of the original [Nibble Forms][1] class, it follows some of the key principles
of Nibble Forms;

* Simple to instantiate: Nibble Forms 2 can be insantiated without any 
arguments, this means getting a form object to add fields to can be as simple as
`$form = \NibbleForms\NibbleForm::getInstance();`

* Simple form field calls: Each form field in Nibble Forms 2 can be instantiated
with just a name and type (or name, type and choices if its a choice field like a select).  
This means just one line of code sets up all the HTML5 markup and all the PHP 
validation methods. `$form->addField('example_text_field', 'text');`

* Flexible: Nibble forms 2 still allows developers to choose the default form
markup, add extra field attributes, change field validation, markup forms with
custom HTML, turn on HTML5 validation and much more.

* Validation out of the box:  Each form field in Nibble Forms 2 has standard
validation built in.  E.g. Email fields accept only valid emails etc

In addition, it is evident that there are some flaws with the originial Nibble
Forms when using it in a large application.  These flaws where the starting 
drive for making Nibble Forms 2:

* Attributes array: The original Nibble Forms had many arguments per form field and
the arguments where not always in the same order.  This made developing with
Nibble Forms a slower process because each set of arguments has to be remembered
or looked up for each form field.  Nibble forms 2 only has 3 arguments, field_name,
field_type and field_arguments.  The field arguments is always an array of arguments which
means no order has to be remembered.  All fields have standard arguments that can
be defined in the array (like `"required" => true`) and some (like option fields)
have additional arguments that can be defined too (like the array of choices).

* Render individual form elements:  In the original Nibble Forms there was a 
method for each field to add extra HTML markup around it.  When trying to 
customise the layout of the form anything more than just adding a div around a 
field, this method made the layout very messy and often broken.  Nibble Forms 2 
allows the developer to render individual form rows (label, field and errors)
or even individual elements of a row so they can mark up the form with any
structure needed. `$form->renderRow('example_text_field')`

* PHP namespaces:  To make the code more legible, Nibble Forms 2 has each field
in its own file in the NibbleForms\Field namespace.  There is an autoloader
so that each field can be easily loaded and extended, and, new fields can be 
created by developers messing with the core code file.

* Add field method:  This function was needed for two reasons;
    - The original Nibble Forms used magic getters and setters to make form fields which
falls down when a field name is used that is also used as a class variable.
    - Because of the namespaces, creating a form would require writing the namespace
for each field each time one was added, the addField method makes adding a field
a very simple call.

## Simple usage

Each form used in one server request requires its own instance of Nibble Forms,
the class must be included in order to get an instance:

``` php
/* Require the Nibble Forms class */
require_once dirname(__FILE__) . '/NibbleForm.class.php';
/* Get an instance of the form called "form_one" */
$form = \NibbleForms\NibbleForm::getInstance('form_one');
```

Form fields can then be added to a form instance using the addField method:

``` php
/* Add field using 3 arguments; field name, field type and field options */
$form->addField('first_name', 'text', array('required' => false));
```

There are numerous form field types pre-defined in Nibble Forms, the below 
fields can be added with no field options array:

* [text][2]
* [textarea][3]
* [password][4]
* [email][5]
* [url][6]
* [number][14]
* [file][7]
* [captcha][8]
* [hidden][9]

There are also 4 choice style fields that require an array of choices with
the array key "choices" in the field options array 
`array('choices' => array('one', 'two', 'three'))`:

* [radio][10]
* [checkbox][11]
* [select][12]
* [multipleSelect][13]

Now that the form has form fields it can be rendered:

``` php
<? /* Render whole form */ ?>
<?php echo $form->render() ?>

<? /* Or render form elements individually with elements or whole rows */ ?>
<?php echo $form->openForm() ?>
<?php echo $form->renderHidden() ?>
<?php echo $form->renderLabel('field_one') ?>
<?php echo $form->renderField('field_one') ?>
<?php echo $form->renderError('field_one') ?>
<?php echo $form->renderRow('field_two') ?>
<button type="submit">Submit</button>
<?php echo $form->closeForm() ?>
````

Finally once the data is submitted, validate the form:

``` php
/* In order to render errors, this method must be called before the form is rendered */
$form->validate();
```

An example form instantition (without rendering) will look something like:
``` php
<?php

/* Require Nibble Forms 2 */
require_once dirname(__FILE__) . '/NibbleForm.class.php';

/* Get Nibble Forms 2 instance called mega_form */
$form = \NibbleForms\NibbleForm::getInstance('mega_form');

/* Text field with custom class and max length attribute */
$form->addField('text_field', 'text', array(
    'class' => 'testy classes',
    'max_length' => 20
));

/* Email field, not required and custom label text */
$email = $form->addfield('email', 'email', array(
    'required' => false,
    'label' => 'Please enter your email address'
        ));
/* Email confirmation field which must match the value for email */
$email->addConfirmation('confirm_email', array(
    'label' => 'Please confirm your email address'
));

/* Radio button field with two options, first option has an additional attribute */
$form->addField('choice', 'radio', array(
    'choices' => array(
        "one" => array('data-example' => 'data-attribute-value', 'Choice One'),
        "two" => "Choice Two"),
    'false_values' => array("two")
));

/* If the form is valid, do something */
if ($form->validate()) {
    echo "Form has validated";
}
```

[1]: http://nibble-development.com
[2]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/text.php
[3]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/textarea.php
[4]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/password.php
[5]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/email.php
[6]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/url.php
[14]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/number.php
[7]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/file.php
[8]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/captcha.php
[9]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/hidden.php
[10]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/radio.php
[11]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/checkbox.php
[12]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/select.php
[13]: https://github.com/LRotherfield/Nibble-Forms/blob/master/Field/multipleSelect.php