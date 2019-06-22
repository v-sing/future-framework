<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/17 0017
 * Time: 14:39
 */

namespace Future\Admin\Form\Field;


use Future\Admin\Form\Field;

class Select extends Field
{
    protected $value = '';
    protected $view = <<<EOF
 <select <%elementAttribute%>><%option%></select>
EOF;

    public function __construct($form)
    {
        $this->form                     = $form;
        $this->elementOption['class'][] = 'selectpicker';
    }

    public function data($data=[])
    {
        $this->data = !empty($data)?$data:[];
        return $this;
    }

    public function render()
    {
        $this->value = $this->elementOption['value'];
        unset($this->elementOption['value']);
        $html = parent::render();
        unset($this->form->form[count($this->form->form) - 1]);
        $this->form->form = array_values($this->form->form);
        $default          = "\n";
        foreach ($this->data as $key => $value) {
            $selected = $key == $this->value ? ' selected ' : '';
            $default  .= "<option value='{$key}'{$selected}>{$value}</option>\n";
        }
        $html               = str_replace('<%option%>', $default, $html);
        $this->form->form[] = $html;
        return $html;
    }
}