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

class Switching extends Field
{
    protected $view = <<<EOF
 <input type="hidden" <%elementAttribute%>>
 <a href="javascript:;" data-toggle="switcher"class="btn-switcher" data-input-id="<%data-input-id%>" data-yes="1" data-no="0">
 <i class="fa fa-toggle-on text-success <%status%> fa-2x"></i>
</a>
EOF;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $value      = $this->elementOption['value'];
//        dd($value);
        $this->view = str_replace(['<%data-input-id%>','<%status%>'],['c-'.$this->column,(!$value?'fa-flip-horizontal text-gray':'')],$this->view);
        return parent::render();
    }
}