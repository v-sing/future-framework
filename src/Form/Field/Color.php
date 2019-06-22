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

class Color extends Field
{
    protected $view = <<<EOF

<input type="color"  class="form-control" <%elementAttribute%>>
EOF;

    public function __construct($form)
    {
        $this->form = $form;
    }
}