<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/17 0017
 * Time: 13:41
 */

namespace Future\Admin\Form\Field;


use Future\Admin\Form\Field;

class Textarea extends Field
{
    protected $areaValue = '';
    protected $view = <<<EOF
<textarea <%elementAttribute%>><%textareaValue%></textarea>
EOF;

    public function __construct($form)
    {
        $this->form                  = $form;
        $this->elementOption['cols'] = '5';
        $this->elementOption['rows'] = '5';
    }

    /**
     * textarea重构
     * @return mixed|string
     */
    public function render()
    {
        $this->areaValue = $this->elementOption['value'];
        unset($this->elementOption['value']);
        $html = parent::render();
        unset($this->form->form[count($this->form->form) - 1]);
        $this->form->form   = array_values($this->form->form);
        $html               = str_replace('<%textareaValue%>', $this->areaValue, $html);
        $this->form->form[] = $html;
        return $html;
    }
}