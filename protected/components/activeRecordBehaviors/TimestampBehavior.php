<?
 
class TimestampBehavior extends ActiveRecordBehavior
{
    public function beforeSave($event)
    {
        if (parent::beforeSave($event))
        {
            $model = $this->getOwner();
            if ($model->isNewRecord)
            {
                if (array_key_exists('date_create', $model->attributes) && !$model->attributes['date_create'])
                {
                    $model->date_create = new CDbExpression('NOW()');
                }
            }
            else
            {
                if (array_key_exists('date_update', $model->attributes))
                {
                    $model->date_update = new CDbExpression('NOW()');
                }
            }
        }

        return true;
    }
}
