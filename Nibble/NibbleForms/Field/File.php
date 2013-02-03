<?php
namespace Nibble\NibbleForms\Field;

use Nibble\NibbleForms\Field;

class File extends Field
{

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

    public function __construct($label, $type = 'all', $required = true, $max_size = 2097152, $width = 1600, $height = 1600, $min_width = 0, $min_height = 0)
    {
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
            if (isset($this->mime_types[$type])) {
                $this->mime_types = $this->mime_types[$type];
            } else {
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

    public function returnField($form_name, $name, $value = '')
    {
        $class = !empty($this->error) ? ' class="error"' : '';

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s_%s"%s>%s</label>', $form_name, $name, $class, $this->label),
            'field' => sprintf('<input type="file" name="%2$s[%1$s]" id="%2$s_%1$s"/>', $name, $form_name),
            'html' => $this->html
        );
    }

    public function validate($val)
    {
        if ($this->required) {
            if ($val['error'] != 0 || $val['size'] == 0) {
                $this->error[] = 'is required';
            }
        }
        if ($val['error'] == 0) {
            if ($val['size'] > $this->max_size) {
                $this->error[] = sprintf('must be less than %sMb', $this->max_size / 1024 / 1024);
            }
            if ($this->type == 'image') {
                $image = getimagesize($val['tmp_name']);
                if ($image[0] > $this->width || $image[1] > $this->height) {
                    $this->error[] = sprintf('must contain an image no more than %s pixels wide and %s pixels high', $this->width, $this->height);
                }
                if ($image[0] < $this->min_width || $image[1] < $this->min_height) {
                    $this->error[] = sprintf('must contain an image at least %s pixels wide and %s pixels high', $this->min_width, $this->min_height);
                }
                if (!in_array($image['mime'], $this->mime_types)) {
                    $this->error[] = $this->error_types[$this->type];
                }
            } elseif (!in_array($val['type'], $this->mime_types)) {
                $this->error[] = $this->error_types[$this->type];
            }
        }

        return !empty($this->error) ? false : true;
    }

}
