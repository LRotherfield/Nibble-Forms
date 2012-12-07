<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
session_name('nibble');
ini_set('session.gc_maxlifetime', 30 * 60);
session_set_cookie_params(30 * 60);
session_start();
include dirname(__FILE__) . '/NibbleForm.class.php';
$form = \NibbleForms\NibbleForm::getInstance('', 'Submit this form', false, 'post', true, 'list');
$form->addField('text field', 'text', array('class' => 'testy classes','max_length' => 20));
$email = $form->addfield('Please enter your email', 'email', array('required' => false));
$email->addConfirmation('Please confirm your email');
if (isset($_POST['submit'])) {
  if ($form->validate()) {
    echo 'Valid';
  } else {
    echo 'Invalid';
  }
}
$form->checkers = new \NibbleForms\Field\MultipleSelect('check boxes dude', array(array('data-luke'=>'2five','Luke'),'Ben'), array('data-price'=>100));
?>
<!doctype html>
<html>
  <head>

    <title>Nibble Forms Demo</title>
    <script src="http://www.google.com/jsapi" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="style.css" />

  </head>
  <body>
      <?php echo $form->openForm() ?>
      <?php echo $form->renderField('please_enter_your_email') ?>
      <?php echo $form->renderLabel('please_enter_your_email') ?>
      <?php echo $form->renderError('please_enter_your_email') ?>
      <?php echo $form->closeForm() ?>
      
    <?php echo $form->render() ?>
  </body>
</html>
