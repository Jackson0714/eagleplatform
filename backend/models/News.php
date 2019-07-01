<?php

namespace app\models;

use app\utils\MongodbUtil;

class News extends BaseModel
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'News';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['title', 'contentUrl', 'imgs', 'startDate', 'endDate']
        );
    }
    public function safeAttributes()
    {
        return array_merge(
            parent::attributes(),
            ['title', 'contentUrl', 'imgs', 'startDate', 'endDate']
      );
    }

    public function fields()
    {
        return array_merge(
            parent::fields(),
            ['title', 'contentUrl', 'imgs', 'startDate', 'endDate']
        );
    }
}
