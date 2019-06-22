<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/17 0017
 * Time: 14:31
 */

namespace Future\Admin\Traits;


trait FieldView
{
    protected $defaultView = <<<EOF
    
<%beforeHtml%>
<div <%withoutAttribute%>>
    <label  <%labelAttribute%>><%labelName%></label>
    <div <%outerAttribute%>>
        <%field%>
    </div>
</div>
<%afterHtml%>

EOF;

    protected $fileView='';
    protected $imageView='';
}