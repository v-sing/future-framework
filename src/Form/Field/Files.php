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

class Files extends Field
{
    protected $view = <<<EOF
   <input  type="text" size="50" <%elementAttribute%> " data-base64=''><%extend%>  
EOF;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $Choose     = lang('Choose');
        $Upload     = lang('Upload');
        $base64     = '';
        $extend     = " <span><button type=\"button\" id=\"plupload-{$this->column}\"  class=\"btn btn-danger plupload\" data-input-id=\"c-{$this->column}\" 
                    data-multiple=\"true\" data-preview-id=\"p-{$this->column}\"><i  class=\"fa fa-upload\"></i> {$Upload}</button></span> <span><button type=\"button\" id=\"fachoose-{$this->column}\"
                    class=\"btn btn-primary fachoose\" data-input-id=\"c-{$this->column}\" data-multiple=\"true\"><i
                    class=\"fa fa-list\"></i>{$Choose}</button></span>";
        $this->view = str_replace(['<%extend%>'], [$extend], $this->view);
        return parent::render();
    }
}