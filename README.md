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

In addition, it is evident that there are some flaws with the originial Nibble
Forms when using it in a large application.  These flaws where the starting 
drive for making Nibble Forms 2:

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

* Add field method:  This function was needed for two reasons; 1) The original 
Nibble Forms used magic getters and setters to make form fields which 
falls down when a field name is used that is also used as a class variable. 2) 
Because of the namespaces, creating a form would require writing the namespace
for each field each time one was added, the addField method makes adding a field
a very simple call.