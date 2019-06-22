<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/19
 * Time: 15:41
 */

namespace Future\Admin\Form\Field;

use Future\Admin\Form\Field;

class Hidden extends Field
{
    protected $display = true;
    protected $view = <<<EOF
   <input  type="hidden" size="50" <%elementAttribute%> " data-base64=''><%extend%>  
EOF;

    public function __construct($form)
    {
        $this->form = $form;
    }

}