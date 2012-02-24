<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
session_name('nibble');
ini_set('session.gc_maxlifetime', 30 * 60);
session_set_cookie_params(30 * 60);
session_start();
include dirname(__FILE__) . '/NibbleForm.class.php';
$form = NibbleForm::getInstance('', 'Submit this form', 'post', true, 'list');
$form->username = new Text('Please enter your username', array(
            'class' => 'testy classes',
            'max_length' => 20), '/[a-zA-Z0-9]+/');
$form->email = new Email('Please enter your email', array('required' => false));
$form->email->addConfirmation('Please confirm your email');
if (isset($_POST['submit'])) {
  if ($form->validate()) {
    echo 'Valid';
  } else {
    echo 'Invalid';
  }
}
?>
<!doctype html>
<html>
  <head>

    <title>Example flash messaging</title>
    <script src="http://www.google.com/jsapi" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="style.css" />

  </head>
  <body>
    <?php echo $form->render() ?>
  </body>
</html>
