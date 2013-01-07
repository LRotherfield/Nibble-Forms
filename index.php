<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
session_name('nibble');
ini_set('session.gc_maxlifetime', 30 * 60);
session_set_cookie_params(30 * 60);
session_start();

include_once __DIR__.('/form_example.php');
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
      <?php echo $form->renderHidden() ?>
      <?php echo $form->renderLabel('email') ?>
      <?php echo $form->renderField('email') ?>
      <?php echo $form->renderError('email') ?>
      <?php echo $form->renderRow('confirm_email') ?>
      <?php echo $form->renderRow('example_select') ?>
      <?php echo $form->renderRow('first_name') ?>
      <button type="submit">Submit</button>
      <?php echo $form->closeForm() ?>
  </body>
</html>
