<?php

namespace app\models;

use app\utils\MongodbUtil;
use yii\mongodb\ActiveRecord;

class BaseModel extends ActiveRecord
{
    const DELETED = true;
    const NOT_DELETED = false;

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'createdAt', 'updatedAt', 'isDeleted'];
    }

    public static function findByPk($id, $condition = [])
    {
        $condition = array_merge(['_id' => $id], $condition);

        return static::findOne($condition);
    }

    public static function findOne($condition = [])
    {
        $condition = array_merge($condition, ['isDeleted' => self::NOT_DELETED]);

        return static::find()->where($condition)->limit(1)->one();
    }

    public function insert($runValidation = true, $attributes = null)
    {
        if (empty($this->createdAt)) {
            $this->createdAt = MongodbUtil::convertToMongoDate();
        }

        if (empty($this->updatedAt)) {
            $this->updatedAt = MongodbUtil::convertToMongoDate();
        }

        $this->isDeleted = self::NOT_DELETED;

        return parent::insert($runValidation, $attributes);
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        $this->updatedAt = MongodbUtil::convertToMongoDate();

        return parent::update($runValidation, $attributeNames);
    }

    public function delete()
    {
        $result = false;
        if ($this->beforeDelete()) {
            if ($this->hasAttribute('isDeleted')) {
                $this->isDeleted = self::DELETED;
                $result = $this->update();
            } else {
                $result = $this->deleteInternal();
            }
            $this->afterDelete();
        }

        return $result;
    }
}
