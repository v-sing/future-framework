<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/10 0010
 * Time: 11:29
 */

namespace Future\Admin;


use Future\Admin\Form\Builder;
use Future\Admin\Form\Field;
use Illuminate\Contracts\Support\Renderable;
use Closure;

class Form implements Renderable
{
    public $form = [];
    protected $initCallback = null;
    protected $formOption = [
        'class'       => 'form-horizontal',
        'role'        => 'form',
        'data-toggle' => 'validator',
        'methods'     => 'POST'
    ];
    protected $formView = <<<EOF
<form <%formAttribute%>>
<%form%>
</form>

EOF;

    public function option($option = [])
    {
        $this->formOption = array_merge($this->formOption, $option);
        return $this;
    }

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function action(Closure $callback)
    {
        $this->initCallback = new Field();
        $this->initCallback->init($this);
        if ($callback instanceof Closure) {
            $callback($this->initCallback);
        }
        return $this;
    }

    public function render()
    {
        $form = implode("\n", $this->form);

        $builder          = new Builder($this);
        $data             = $builder->form();
        $data['<%form%>'] = $form;
        $view             = str_replace(array_keys($data), array_values($data), $this->formView);

        return $view;
    }

    public function __call($name, $arguments)
    {
        preg_match('/^get(.*)/s', $name, $m);
        if (!empty($m[0]) && !empty($m[1])) {
            $param = lcfirst($m[1]);
            if (property_exists($this, $param)) {
                return $this->$param;
            } else {
                return false;
            }
        }
    }
}