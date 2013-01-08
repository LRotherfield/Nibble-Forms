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
      <?php echo $form->renderLabel('text_field') ?>
      <?php echo $form->renderField('text_field') ?>
      <?php echo $form->renderError('text_field') ?>
      <?php echo $form->renderRow('email') ?>
      <?php echo $form->renderRow('confirm_email') ?>
      <?php echo $form->renderRow('choice') ?>
      <p>
        <button type="submit">Submit</button>
      </p>
      <?php echo $form->closeForm() ?>
  </body>
</html>
