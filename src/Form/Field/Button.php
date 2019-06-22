<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/7 0007
 * Time: 10:30
 */

namespace Future\Admin\Form\Field;
use Future\Admin\Form\Field;

/**
 * 按钮
 * Class Button
 */
class Button extends Field
{
    protected $buttonName = [];
    /**
     * 按钮模板
     * @var string
     */
    protected $view = <<<EOF
     <button  <%elementAttribute%>><%buttonName%></button>
EOF;

    public function __construct($form)
    {
        unset($this->elementOption['class']);
        $this->form = $form;
    }
    public function submit($name, $option = [])
    {
        if (empty($option['class'])) {
            $option['class'] = [
                'btn-success',
                'btn-embossed',
                'btn'
            ];
        }
        $option['type']='submit';
        $this->buttonName[]   = $name;
        $this->elementOption[] = $option;
        return $this;
    }

    public function reset($name, $option = [])
    {
        if (empty($option['class'])) {
            $option['class'] = [
                'btn-default',
                'btn-embossed',
                'btn'
            ];
        }
        $option['type']='reset';
        $this->buttonName[]   = $name;
        $this->elementOption[] = $option;
        return $this;
    }

    /**
     * 属性
     * @param $type
     * @param $name
     * @param array $option
     * @return $this
     */
    public function addButton($name, $option = ['class' => 'btn-primary btn'])
    {
        $option['type']='button';
        $this->buttonName[]   = $name;
        $this->elementOption[] = $option;
        return $this;
    }



}