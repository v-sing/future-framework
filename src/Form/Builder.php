<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/5/10 0010
 * Time: 15:37
 */

namespace Future\Admin\Form;

use Future\Admin\Interfaces\BuilderInterface;

class Builder
{
    /**
     * 元素
     * @var
     */
    protected $elementClass;
    protected $field = null;

    public function __construct($field)
    {
        $this->field = $field;
    }

//    public function date()
//    {
//        // TODO: Implement date() method.
//    }
//
//    public function dateRange()
//    {
//        // TODO: Implement dateRange() method.
//    }
//
//    public function input()
//    {
//        // TODO: Implement input() method.
//    }
//
//    public function image()
//    {
//        // TODO: Implement image() method.
//    }
//
//    public function images()
//    {
//        // TODO: Implement images() method.
//    }
//
//    public function file()
//    {
//        // TODO: Implement file() method.
//    }
//
//    public function fieldList()
//    {
//        // TODO: Implement fieldList() method.
//    }
//
//    public function files()
//    {
//        // TODO: Implement files() method.
//    }
//
//    public function number()
//    {
//        // TODO: Implement number() method.
//    }
//
//    public function editor()
//    {
//        // TODO: Implement editor() method.
//    }
//
//    public function select()
//    {
//        // TODO: Implement select() method.
//    }
//
//    public function selects()
//    {
//        // TODO: Implement selects() method.
//    }
//
//    public function checkbox()
//    {
//        // TODO: Implement checkbox() method.
//    }
//
//    public function cityArea()
//    {
//        // TODO: Implement cityArea() method.
//    }
//
//    public function color()
//    {
//        // TODO: Implement color() method.
//    }
//
//    public function radio()
//    {
//        // TODO: Implement radio() method.
//    }
//
//    public function text()
//    {
//        // TODO: Implement text() method.
//    }
//
//    public function button()
//    {
//
//    }
    public function form()
    {
        $option                  = [];
        $option['<%formAttribute%>'] =implode(' ',$this->attributes($this->field->getFormOption())) ;
        return $option;
    }

    /**
     *
     * @param $attributes
     * @return array
     * @throws \Exception
     */
    protected function attributes($attributes)
    {
        $html = array();
        foreach ((array)$attributes as $key => $value) {
            if (is_numeric($key) && is_array($value)) {
                $html1 = [];
                foreach ($value as $key1 => $value1) {
                    $element = $this->attributeElement($key1, is_array($value1) ? implode(' ', $value1) : $value1);
                    if (!is_null($element)) $html1[] = $element;
                }
                if (!is_null($element)) $html[] = implode(' ', $html1);
            } else {
                $element = $this->attributeElement($key, is_array($value) ? implode(' ', $value) : $value);
                if (!is_null($element)) $html[] = $element;
            }
        }
        return $html;
    }

    /**
     * Build a single attribute element.
     * @param $key
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }

        if (!is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
    }

    public function __call($name, $arguments)
    {
        $option                     = [];
        $option['elementAttribute'] = $this->field->getButtonName() === false ? [implode(' ', $this->attributes($this->field->getElementOption()))] : $this->attributes($this->field->getElementOption());
        $option['labelAttribute']   = implode(' ', $this->attributes($this->field->getLabelOption()));
        $option['outerAttribute']   = implode(' ', $this->attributes($this->field->getOuterOption()));
        $option['withoutAttribute']=implode(' ',$this->attributes($this->field->getWithoutOption()));
        $option['beforeHtml']       = $this->field->getBeforeHtml();
        $option['afterHtml']        = $this->field->getAfterHtml();
        $option['labelName']        = $this->field->getLabelName();
        $option['display']          = !$this->field->getDisplay() ? implode(' ', $this->attributes(['style' => ['display:none;']])) : "";
        $option['buttonName']       = $this->field->getButtonName();
        return $option;
    }
}