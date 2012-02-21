<?php

/*
 * Nibble forms library
 * Copyright (c) 2010 Luke Rotherfield, Nibble Development
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

abstract class FormField {

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
  abstract public function returnField($name, $value='');

  /**
   * Validate the current field
   */
  abstract public function validate($val);

  /**
   * Apply custom error message from user to field
   */
  public function errorMessage($message) {
    $this->custom_error[] = $message;
  }

  /**
   * Apply custom html to open and close of field
   */
  public function customHtml($open_field = false, $close_field = false, $open_html = false, $close_html = false) {
    $this->html = array('open_field' => $open_field, 'close_field' => $close_field, 'open_html' => $open_html, 'close_html' => $close_html);
  }

}

class NibbleForm {

  private $action;
  private $method;
  private $submit_value;
  private $fields;
  private $data = array();
  private $sticky;
  private $format;
  private $valid = true;
  private $message_type;
  private $flash;
  private $messages = '';
  private $formats = array(
      'list' => array(
          'open_form' => '<ul>',
          'close_form' => '</ul>',
          'open_form_body' => '',
          'close_form_body' => '',
          'open_field' => '',
          'close_field' => '',
          'open_html' => "<li>\n",
          'close_html' => "</li>\n",
          'open_submit' => "<li>\n",
          'close_submit' => "</li>\n"
      ),
      'table' => array(
          'open_form' => '<table>',
          'close_form' => '</table>',
          'open_form_body' => '<tbody>',
          'close_form_body' => '</tbody>',
          'open_field' => "<tr>\n",
          'close_field' => "</tr>\n",
          'open_html' => "<td>\n",
          'close_html' => "</td>\n",
          'open_submit' => '<tfoot><tr><td>',
          'close_submit' => '</td></tr></tfoot>'
      )
  );
  private $multiple_errors;
  public static $instance;

  public function __construct($action, $submit_value, $method, $sticky, $message_type, $format, $multiple_errors) {
    $this->fields = new stdClass();
    $this->action = $action;
    $this->method = $method;
    $this->submit_value = $submit_value;
    $this->sticky = $sticky;
    $this->format = $format;
    $this->message_type = $message_type;
    $this->multiple_errors = $multiple_errors;
    if ($message_type == 'flash')
      $this->flash = Flash::getInstance();
    if ($message_type == 'list')
      $this->messages = array();
  }

  public static function getInstance($action = '/', $submit_value = 'Submit', $method = 'post', $sticky = true, $message_type = 'list', $format = 'list', $multiple_errors = false) {
    if (!self::$instance)
      self::$instance = new NibbleForm($action, $submit_value, $method, $sticky, $message_type, $format, $multiple_errors);
    return self::$instance;
  }

  public function __set($name, $value) {
    $this->fields->$name = $value;
  }

  public function __get($name) {
    return $this->fields->$name;
  }

  public function checkField($field) {
    return isset($this->fields->$field);
  }

  public function addData($data) {
    $this->data = array_merge($this->data, $data);
  }

  public function validate() {
    if ((isset($_SESSION['token']) && !in_array($_POST['token'], $_SESSION['token'])) || !isset($_SESSION['token']) || !isset($_POST['token'])) {
      $this->setMessages('CRSF token invalid', 'CRSF error');
      $this->valid = false;
    }
    $_SESSION['token'] = array();
    if ($this->sticky)
      $this->addData($_POST);
    foreach ($this->fields as $key => $value)
      if (!$value->validate((isset($_POST[$key]) ? $_POST[$key] : (isset($_FILES[$key]) ? $_FILES[$key] : ''))))
        $this->valid = false;
    return $this->valid;
  }

  private function setMessages($message, $title) {
    $title = preg_replace('/_/', ' ', ucfirst($title));
    if ($this->message_type == 'flash')
      $this->flash->message(ucfirst($message), $title, 0, true);
    elseif ($this->message_type == 'list')
      $this->messages[] = array('title' => $title, 'message' => ucfirst($message));
  }

  private function buildMessages() {
    $messages = '<ul class="error">';
    foreach ($this->messages as $message_array) {
      $messages .= sprintf('<li>%s: %s</li>%s', ucfirst(preg_replace('/_/', ' ', $message_array['title'])), ucfirst($message_array['message']), "\n");
    }
    $this->messages = $messages . '</ul>';
  }

  public function render() {
    if (!isset($_SESSION['token']))
      $_SESSION['token'] = array();
    $_SESSION['token'][] = Useful::randomString(20);
    $fields = '';
    $error = $this->valid ? '' : '<p class="error">Sorry there were some errors in the form, problem fields have been highlighted</p>';
    $format = (object) $this->formats[$this->format];

    foreach ($this->fields as $key => $value) {
      $format = (object) $this->formats[$this->format];
      $temp = isset($this->data[$key]) ? $value->returnField($key, $this->data[$key]) : $value->returnField($key);
      if ($temp['html']['close_field'] !== false)
        $format->close_field = $temp['html']['close_field'];
      if ($temp['html']['close_html'] !== false)
        $format->close_html = $temp['html']['close_html'];
      if ($temp['html']['open_field'] !== false)
        $format->open_field = $temp['html']['open_field'];
      if ($temp['html']['open_html'] !== false)
        $format->open_html = $temp['html']['open_html'];
      $fields .= $format->open_field;
      if ($temp['label'])
        $fields .= $format->open_html . $temp['label'] . $format->close_html;
      if (isset($temp['messages'])) {
        foreach ($temp['messages'] as $message) {
          if ($this->message_type == 'inline')
            $fields .= $format->open_html . '<p class="error">This field ' . $message . '</p>' . $format->close_html;
          else
            $this->setMessages($message, $key);
          if (!$this->multiple_errors)
            break;
        }
      }
      $fields .= $format->open_html . $temp['field'] . $format->close_html . $format->close_field;
    }

    if (!empty($this->messages))
      $this->buildMessages();
    else
      $this->messages = false;
    $token = $_SESSION['token'][count($_SESSION['token']) - 1];
    self::$instance = false;
    return <<<FORM
    $error
    $this->messages
    <form class="form" action="$this->action" method="$this->method" enctype="multipart/form-data">
      $format->open_form
        $format->open_form_body
          <input type="hidden" value="$token" name="token" />
          $fields
        $format->close_form_body
        $format->open_submit
          <input type="submit" name="submit" value="$this->submit_value" />
        $format->close_submit
      $format->close_form
    </form>
FORM;
  }

}

class Text extends FormField {

  protected $label;
  protected $required;
  protected $max_length;
  protected $content;
  public $error = array();
  public $field_type = 'text';

  public function __construct($label, $required = true, $max_length = 255, $content = '/.*/') {
    $this->label = $label;
    $this->required = $required;
    $this->max_length = $max_length;
    $this->content = $content;
  }

  public function returnField($name, $value = '', $type='text') {
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => sprintf('<input type="%5$s" name="%1$s" id="%1$s" value="%2$s" maxlength="%3$s"%4$s />', $name, $value, $this->max_length, $class, $this->field_type),
        'html' => $this->html
    );
  }

  public function validate($val) {
    if ($this->required)
      if (Useful::stripper($val) === false)
        $this->error[] = 'is required';
    if (Useful::stripper($val) !== false)
      if (!preg_match($this->content, $val))
        $this->error[] = 'is not valid';
    return!empty($this->error) ? false : true;
  }

}

class Url extends Text {

  public function validate($val) {
    if (!empty($this->error))
      return false;
    if (parent::validate($val))
      if (Useful::stripper($val) !== false) {
        if (!filter_var($val, FILTER_VALIDATE_URL))
          $this->error[] = 'must be a valid URL';
      }
    return!empty($this->error) ? false : true;
  }

  public function returnField($name, $value = '') {
    $this->field_type = 'url';
    return parent::returnField($name, $value);
  }

}

class Email extends Text {

  private $confirm = false;

  public function validate($val) {
    if (!empty($this->error))
      return false;
    if (parent::validate($val))
      if (Useful::stripper($val) !== false) {
        if (!filter_var($val, FILTER_VALIDATE_EMAIL))
          $this->error[] = 'must be a valid email address';
      }
    if ($this->confirm) {
      if ($val != $_POST[$this->confirm]) {
        $form = NibbleForm::getInstance();
        $form->{$this->confirm}->error[] = 'must match email';
      }
    }
    return!empty($this->error) ? false : true;
  }

  public function addConfirmation($label, $open_field = false, $close_field = false, $open_html = false, $close_html = false) {
    $form = NibbleForm::getInstance();
    if ($form->checkField('confirm_email')) {
      $i = 2;
      while ($form->checkField('confirm_email_' . $i))
        $i++;
      $form->{'confirm_email_' . $i} = new Email($label, $this->required, $this->max_length, $this->content);
      $form->{'confirm_email_' . $i}->customHtml($open_field, $close_field, $open_html, $close_html);
      $this->confirm = 'confirm_email_' . $i;
    } else {
      $form->confirm_email = new Email($label, $this->required, $this->max_length, $this->content);
      $form->confirm_email->customHtml($open_field, $close_field, $open_html, $close_html);
      $this->confirm = 'confirm_email';
    }
  }

  public function returnField($name, $value = '') {
    $this->field_type = 'email';
    return parent::returnField($name, $value);
  }

}

class Password extends Text {

  private $confirm = false;
  private $min_length;
  private $alphanumeric;

  public function __construct($label, $min_length = 6, $alphanumeric = true, $required = true, $max_length = 255, $content = '/.*/') {
    parent::__construct($label, $required, $max_length, $content);
    $this->alphanumeric = $alphanumeric;
    $this->min_length = $min_length;
  }

  public function validate($val) {
    if (!empty($this->error))
      return false;
    if (parent::validate($val)) {
      if (Useful::stripper($val) !== false) {
        if (strlen($val) < $this->min_length)
          $this->error[] = sprintf('must be more than %s characters', $this->min_length);
        if ($this->alphanumeric && (!preg_match("#[A-Za-z]+#", $val) || !preg_match("#[0-9]+#", $val)))
          $this->error[] = 'must have at least one alphabetic character and one numeric character';
      }
    }
    if ($this->confirm) {
      if ($val != $_POST[$this->confirm]) {
        $form = NibbleForm::getInstance();
        $form->{$this->confirm}->error[] = 'must match password';
      }
    }
    return!empty($this->error) ? false : true;
  }

  public function returnField($name, $value = '') {
    $this->field_type = 'password';
    return parent::returnField($name, $value);
  }

  public function addConfirmation($label, $open_field = false, $close_field = false, $open_html = false, $close_html = false) {
    $form = NibbleForm::getInstance();
    if ($form->checkField('confirm_password')) {
      $i = 2;
      while ($form->checkField('confirm_password_' . $i))
        $i++;
      $form->{'confirm_password_' . $i} = new Password($label, $this->min_length, $this->alphanumeric, $this->required, $this->max_length, $this->content);
      $form->{'confirm_password_' . $i}->customHtml($open_field, $close_field, $open_html, $close_html);
      $this->confirm = 'confirm_password_' . $i;
    } else {
      $form->confirm_password = new Password($label, $this->min_length, $this->alphanumeric, $this->required, $this->max_length, $this->content);
      $form->confirm_password->customHtml($open_field, $close_field, $open_html, $close_html);
      $this->confirm = 'confirm_password';
    }
  }

}

class TextArea extends Text {

  private $class;
  private $rows;
  private $cols;

  public function __construct($label, $class = '', $required = true, $rows = 10, $cols = 60, $content = '/.*/') {
    parent::__construct($label, $required, false, $content);
    $this->class = $class;
    $this->rows = $rows;
    $this->cols = $cols;
  }

  public function returnField($name, $value = '') {
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => sprintf('<textarea name="%1$s" id="%1$s" class="%2$s" rows="%4$s" cols="%5$s">%3$s</textarea>', $name, $this->class, $value, $this->rows, $this->cols),
        'html' => $this->html
    );
  }

}

abstract class Options extends FormField {

  protected $label;
  protected $options;
  protected $required;
  protected $false_values;
  public $error = array();

  public function __construct($label, $options, $required = true, $false_values = array()) {
    $this->label = $label;
    $this->options = (array) $options;
    $this->required = $required;
    $this->false_values = $false_values;
  }

  public function validate($val) {
    if ($this->required)
      if (Useful::stripper($val) === false)
        $this->error[] = 'is required';
    if (in_array($val, $this->false_values))
      $this->error[] = 'is not a valid selection';
    return!empty($this->error) ? false : true;
  }

}

class Radio extends Options {

  public function returnField($name, $value = '') {
    $field = '';
    foreach ($this->options as $key => $val)
      $field .= sprintf('<input type="radio" name="%1$s" id="%3$s" value="%2$s" %4$s/>' .
              '<label for=%3$s>%5$s</label>'
              , $name, $key, Useful::slugify($name) . '_' . Useful::slugify($key), ((string) $key === (string) $value ? 'checked="checked"' : ''), $val);
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<p%s>%s</p>', $class, $this->label),
        'field' => $field,
        'html' => $this->html
    );
  }

}

class Select extends Options {

  protected $show_size;

  public function __construct($label, $options, $show_size = false, $required = true, $false_values = array()) {
    parent::__construct($label, $options, $required, $false_values);
    $this->show_size = $show_size;
  }

  public function returnField($name, $value = '') {
    $field = sprintf('<select name="%1$s" id="%1$s" %2$s>', $name, ($this->show_size ? "size='$this->show_size'" : ''));
    foreach ($this->options as $key => $val)
      $field .= sprintf('<option value="%s" %s>%s</option>', $key, ((string) $key === (string) $value ? 'selected="selected"' : ''), $val);
    $field .= '</select>';
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => $field,
        'html' => $this->html
    );
  }

}

abstract class MultipleOptions extends FormField {

  protected $label;
  protected $options;
  protected $required;
  protected $minimum_selected;
  public $error = array();

  public function __construct($label, $options, $required = true, $minimum_selected = false) {
    $this->label = $label;
    $this->options = (array) $options;
    $this->required = $required;
    $this->minimum_selected = $minimum_selected;
  }

  public function validate($val) {
    if (is_array($val)) {
      if ($this->minimum_selected && count($val) < $this->minimum_selected)
        $this->error[] = sprintf('at least %s options must be selected', $this->minimum_selected);
    } elseif ($this->required)
      $this->error[] = 'is required';
    return!empty($this->error) ? false : true;
  }

}

class Checkbox extends MultipleOptions {

  public function returnField($name, $value = '') {
    $field = '';
    foreach ($this->options as $key => $val)
      $field .= sprintf('<input type="checkbox" name="%1$s[]" id="%3$s" value="%2$s" %4$s/>' .
              '<label for=%3$s>%5$s</label>'
              , $name, $key, Useful::slugify($name) . '_' . Useful::slugify($key), (is_array($value) && in_array((string) $key, $value) ? 'checked="checked"' : ''), $val);
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<p%s>%s</p>', $class, $this->label),
        'field' => $field,
        'html' => $this->html
    );
  }

}

class MultipleSelect extends MultipleOptions {

  protected $show_size;

  public function __construct($label, $options, $show_size = false, $required = true, $minimum_selected = false) {
    parent::__construct($label, $options, $required, $minimum_selected);
    $this->show_size = $show_size;
  }

  public function returnField($name, $value = '') {
    $field = sprintf('<select name="%1$s[]" id="%1$s" %2$s multiple="multiple">', $name, ($this->show_size ? "size='$this->show_size'" : ''));
    foreach ($this->options as $key => $val)
      $field .= sprintf('<option value="%s" %s>%s</option>', $key, (is_array($value) && in_array((string) $key, $value) ? 'selected="selected"' : ''), $val);
    $field .= '</select>';
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => $field,
        'html' => $this->html
    );
  }

}

class File extends FormField {

  private $label;
  private $type;
  private $required;
  private $max_size;
  public $error = array();
  private $height;
  private $width;
  private $min_height;
  private $min_width;
  private $mime_types = array(
      'image' => array(
          'image/gif', 'image/gi_', 'image/png', 'application/png', 'application/x-png',
          'image/jp_', 'application/jpg', 'application/x-jpg', 'image/pjpeg', 'image/jpeg'
      ),
      'document' => array(
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          'application/vnd.openxmlformats-officedocument.presentationml.presentation',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
          'application/mspowerpoint', 'application/powerpoint', 'application/vnd.ms-powerpoint',
          'application/x-mspowerpoint', 'application/plain', 'text/plain', 'application/pdf',
          'application/x-pdf', 'application/acrobat', 'text/pdf', 'text/x-pdf', 'application/msword',
          'pplication/vnd.ms-excel', 'application/msexcel', 'application/doc',
          'application/vnd.oasis.opendocument.text', 'application/x-vnd.oasis.opendocument.text',
          'application/vnd.oasis.opendocument.spreadsheet', 'application/x-vnd.oasis.opendocument.spreadsheet',
          'application/vnd.oasis.opendocument.presentation', 'application/x-vnd.oasis.opendocument.presentation'
      ),
      'archive' => array(
          'application/x-compressed', 'application/gzip-compressed', 'gzip/document',
          'application/x-zip-compressed', 'application/zip', 'multipart/x-zip',
          'application/tar', 'application/x-tar', 'applicaton/x-gtar', 'multipart/x-tar',
          'application/gzip', 'application/x-gzip', 'application/x-gunzip', 'application/gzipped'
      )
  );
  private $error_types = array(
      'image' => 'must be an image, e.g example.jpg or example.gif',
      'archive' => 'must be and archive, e.g example.zip or example.tar',
      'document' => 'must be a document, e.g example.doc or example.pdf',
      'all' => 'must be a document, archive or image',
      'custom' => 'is invalid'
  );

  public function __construct($label, $type = 'all', $required = true, $max_size = 2097152, $width = 1600, $height = 1600, $min_width = 0, $min_height = 0) {
    $this->label = $label;
    $this->required = $required;
    $this->max_size = $max_size;
    $this->width = $width;
    $this->height = $height;
    $this->min_width = $min_width;
    $this->min_height = $min_height;
    if (is_array($type)) {
      $this->mime_types = $type;
      $this->type = 'custom';
    } else {
      $this->type = $type;
      if (isset($this->mime_types[$type]))
        $this->mime_types = $this->mime_types[$type];
      else {
        $temp = array();
        foreach ($this->mime_types as $mime_array)
          foreach ($mime_array as $mime_type)
            $temp[] = $mime_type;
        $this->mime_types = $temp;
        $this->type = 'all';
        unset($temp);
      }
    }
  }

  public function returnField($name, $value = '') {
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => sprintf('<input type="file" name="%1$s" id="%1$s"/>', $name),
        'html' => $this->html
    );
  }

  public function validate($val) {
    if ($this->required)
      if ($val['error'] != 0 || $val['size'] == 0)
        $this->error[] = 'is required';
    if ($val['error'] == 0) {
      if ($val['size'] > $this->max_size)
        $this->error[] = sprintf('must be less than %sMb', $this->max_size / 1024 / 1024);
      if ($this->type == 'image') {
        $image = getimagesize($val['tmp_name']);
        if ($image[0] > $this->width || $image[1] > $this->height)
          $this->error[] = sprintf('must contain an image no more than %s pixels wide and %s pixels high', $this->width, $this->height);
        if ($image[0] < $this->min_width || $image[1] < $this->min_height)
          $this->error[] = sprintf('must contain an image at least %s pixels wide and %s pixels high', $this->min_width, $this->min_height);
        if (!in_array($image['mime'], $this->mime_types))
          $this->error[] = $this->error_types[$this->type];
      } elseif (!in_array($val['type'], $this->mime_types))
        $this->error[] = $this->error_types[$this->type];
    }
    return!empty($this->error) ? false : true;
  }

}

class Captcha extends FormField {

  public $error = array();
  protected $label;

  public function __construct($label = 'Humanity Check') {
    $this->label = $label;
  }

  public function returnField($name, $value = '') {
    $field = <<<FIELD
  <script type="text/javascript"
     src="http://www.google.com/recaptcha/api/challenge?k=%1\$s">
  </script>
  <noscript>
     <iframe src="http://www.google.com/recaptcha/api/noscript?k=%1\$s"
         height="300" width="500" frameborder="0"></iframe><br>
     <textarea name="recaptcha_challenge_field" rows="3" cols="40">
     </textarea>
     <input type="hidden" name="recaptcha_response_field"
         value="manual_challenge">
  </noscript>
FIELD;
    $class = !empty($this->error) ? ' class="error"' : '';
    return array(
        'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
        'label' => $this->label == false ? false : sprintf('<label for="%s"%s>%s</label>', $name, $class, $this->label),
        'field' => sprintf($field, 'YOUR_PUBLIC_KEY'),
        'html' => $this->html
    );
  }

  public function validate($val) {

    $url = 'http://www.google.com/recaptcha/api/verify';
    $data = array(
        'privatekey' => urlencode('YOUR_PRIVATE_KEY'),
        'remoteip' => urlencode($_SERVER['REMOTE_ADDR']),
        'challenge' => urlencode($_REQUEST['recaptcha_challenge_field']),
        'response' => urlencode($_REQUEST['recaptcha_response_field']),
    );

    $data_string = '';
    foreach ($data as $key => $val) {
      $data_string .= $key . '=' . $val . '&';
    }
    $data_string = rtrim($data_string, '&');

    $host = 'www.google.com';
    $http_request = "POST /recaptcha/api/verify HTTP/1.0\r\n";
    $http_request .= "Host: $host\r\n";
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: " . strlen($data_string) . "\r\n";
    $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
    $http_request .= "\r\n";
    $http_request .= $data_string;

    $response = '';
    if (false == ( $fs = @fsockopen($host, 80, $errno, $errstr, 10) )) {
      die('Could not open socket');
    }

    fwrite($fs, $http_request);

    while (!feof($fs))
      $response .= fgets($fs, 1160); // One TCP-IP packet
    fclose($fs);
    $response = explode("\r\n\r\n", $response);
    $response = explode("\n",$response[1]);
    if(!isset($response[0])||$response[0] != 'true')
      $this->error[] = 'You failed to prove you humanity';
    //curl_close($ch);
    return empty($this->error) ? true : false;
  }

}

class Useful {

  public static function stripper($val) {
    foreach (array(' ', '&nbsp;', '\n', '\t', '\r') as $strip)
      $val = str_replace($strip, '', (string) $val);
    return $val === '' ? false : $val;
  }

  public static function slugify($text) {
    return strtolower(trim(preg_replace('/\W+/', '-', $text), '-'));
  }

  public static function randomString($length = 10, $return = '') {
    $string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    while ($length-- > 0)
      $return .= $string[mt_rand(0, strlen($string) - 1)];
    return $return;
  }

}
