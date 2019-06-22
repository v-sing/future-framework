<?php
/**
 * Created by PhpStorm.
 * Author: 紫云沫雪こ
 * Email:email1946367301@163.com
 * Date: 2019/3/22 0022
 * Time: 15:24
 */

namespace Future\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Model;

class Test extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);
        $class = class_basename(get_class());
        $this->setTable(parse_underline($class));
        parent::__construct($attributes);
    }

}