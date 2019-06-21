<?php
/**
 * GoodOne_Model - base class for all model classes
 */
abstract class GoodOne_Model
{

    /** 抽象类中可以定义变量 */
    protected $value1 = 0;
    private $value2 = 1;
    public $value3 = 2;

    /**
     * Construct new model class
     */
    public function __construct()
    {
        //
    }

    public function __get_create_at(){
        return time() + ( $_settings_data['server-info']['gmt_offset'] * 3600 );
    }

    /**
     * 大多数情况下，抽象类至少含有一个抽象方法。抽象方法用abstract关键字声明，其中不能有具体内容。
     * 可以像声明普通类方法那样声明抽象方法，但是要以分号而不是方法体结束。
     * 也就是说抽象方法在抽象类中不能被实现，也就是没有函数体“{some codes}”。
     */
    abstract protected function my_func();

}
