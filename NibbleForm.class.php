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
namespace NibbleForms;

class NibbleForm {

    private $action, $method, $submit_value, $fields, $sticky, $format, $message_type, $flash, $multiple_errors;
    private $valid = true;
    private $messages = '';
    private $data = array();
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
    public static $instance;

    public function __construct($action, $submit_value, $method, $sticky, $message_type, $format, $multiple_errors)
    {
        $this->fields = new \stdClass();
        $this->action = $action;
        $this->method = $method;
        $this->submit_value = $submit_value;
        $this->sticky = $sticky;
        $this->format = $format;
        $this->message_type = $message_type;
        $this->multiple_errors = $multiple_errors;
        if ($message_type == 'flash') {
            $this->flash = Flash::getInstance();
        }
        if ($message_type == 'list') {
            $this->messages = array();
        }
    }

    /**
     * 
     * @param string $action
     * @param string $submit_value
     * @param string $method
     * @param boolean $sticky
     * @param string $message_type
     * @param string $format
     * @param string $multiple_errors
     * @return \Nibble\NibbleForm
     */
    public static function getInstance($action = '/', $submit_value = 'Submit', $method = 'post', $sticky = true, $message_type = 'list', $format = 'list', $multiple_errors = false) {
        if (!self::$instance) {
            self::$instance = new NibbleForm($action, $submit_value, $method, $sticky, $message_type, $format, $multiple_errors);
        }
        return self::$instance;
    }

    public static function nibbleLoader($class)
    {
        $namespace = explode('\\',$class);
        if($namespace[0] == "NibbleForms"){
            array_shift($namespace);
        }
        require dirname(__FILE__).'/'.implode('/', $namespace).'.php';
    }

    public function __set($name, $value)
    {
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
        if ($this->sticky) {
            $this->addData($_POST);
        }
        foreach ($this->fields as $key => $value) {
            if (!$value->validate((isset($_POST[$key]) ? $_POST[$key] : (isset($_FILES[$key]) ? $_FILES[$key] : '')))) {
                $this->valid = false;
            }
        }
        return $this->valid;
    }

    private function setMessages($message, $title) {
        $title = preg_replace('/_/', ' ', ucfirst($title));
        if ($this->message_type == 'flash') {
            $this->flash->message(ucfirst($message), $title, 0, true);
        } elseif ($this->message_type == 'list') {
            $this->messages[] = array('title' => $title, 'message' => ucfirst($message));
        }
    }

    private function buildMessages() {
        $messages = '<ul class="error">';
        foreach ($this->messages as $message_array) {
            $messages .= sprintf('<li>%s: %s</li>%s', ucfirst(preg_replace('/_/', ' ', $message_array['title'])), ucfirst($message_array['message']), "\n");
        }
        $this->messages = $messages . '</ul>';
    }

    public function render() {
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = array();
        }
        $_SESSION['token'][] = \NibbleForms\Useful::randomString(20);
        $fields = '';
        $error = $this->valid ? '' : '<p class="error">Sorry there were some errors in the form, problem fields have been highlighted</p>';
        $format = (object) $this->formats[$this->format];

        foreach ($this->fields as $key => $value) {
            $format = (object) $this->formats[$this->format];
            $temp = isset($this->data[$key]) ? $value->returnField($key, $this->data[$key]) : $value->returnField($key);
            $fields .= $format->open_field;
            if ($temp['label']) {
                $fields .= $format->open_html . $temp['label'] . $format->close_html;
            }
            if (isset($temp['messages'])) {
                foreach ($temp['messages'] as $message) {
                    if ($this->message_type == 'inline') {
                        $fields .= "$format->open_html <p class=\"error\">$message</p> $format->close_html";
                    } else {
                        $this->setMessages($message, $key);
                    }
                    if (!$this->multiple_errors) {
                        break;
                    }
                }
            }
            $fields .= $format->open_html . $temp['field'] . $format->close_html . $format->close_field;
        }

        if (!empty($this->messages)) {
            $this->buildMessages();
        } else {
            $this->messages = false;
        }
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

    public function renderField($name) {
        return $this->getFieldData($name, 'field');
    }

    public function renderLabel($name) {
        return $this->getFieldData($name, 'label');
    }

    public function renderError($name) {
        $errors = '';
        foreach ($this->getFieldData($name, 'messages') as $error) {
            $errors .= $error;
        }
        return $errors;
    }

    private function getFieldData($name, $key) {
        $field = $this->$name;
        if (isset($this->data[$name])) {
            $field = $field->returnField($name, $this->data[$name]);
        } else {
            $field = $field->returnField($name);
        }
        return $field[$key];
    }

    public function openForm() {
        $multipart = false;
        foreach ($this->fields as $field) {
            if (get_class($field) == 'File') {
                $multipart = true;
            }
        }
        return "<form class=\"form\" action=\"$this->action\" method=\"$this->method\"" . ($multipart ? 'enctype="multipart/form-data"' : '') . ">";
    }

    public function closeForm() {
        return "</form>";
    }

    public function renderHidden() {
        $fields = array();
        foreach ($this->fields as $name => $field) {
            if (get_class($field) == 'Hidden') {
                if (isset($this->data[$name])) {
                    $fields_data = $field->returnField($name, $this->data[$name]);
                } else {
                    $fields_data = $field->returnField($name);
                }
                $fields[] = $field_data['field'];
            }
        }

        return implode("\n", $fields);
    }

    public function renderErrors() {
        $errors = '';
        foreach (array_keys($this->fields) as $name) {
            foreach ($this->getFieldData($name, 'messages') as $error) {
                $errors .= "<li>$error</li>\n";
            }
        }
        return "<ul>$errors</ul>";
    }

}
spl_autoload_register(__NAMESPACE__ ."\NibbleForm::nibbleLoader");

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
