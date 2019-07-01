<?php

namespace app\models;

use app\utils\MongodbUtil;

class BackendUser extends BaseModel
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'backendUser';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['name', 'password', 'avator', 'accountRouter']
        );
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                ['password', 'default', 'value' => 'abc123_'],
                ['accountRouter', 'default', 'value' => []],
                ['avator', 'default', 'value' => '']
            ]
        );
    }

    public function fields()
    {
        return array_merge(
            parent::attributes(),
            [
              'name', 'avator', 'accountRouter',
                'createdAt' => function () {
                    return MongodbUtil::MongoDate2String($this->createdAt);
                },
                'updatedAt' => function () {
                    return MongodbUtil::MongoDate2String($this->updatedAt);
                }
            ]
        );
    }
}
