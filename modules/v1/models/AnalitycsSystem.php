<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "analitycs_system".
 *
 * @property string $id
 * @property string $platform
 * @property string $language
 * @property string $unityVersion
 * @property string $os
 * @property string $osFamily
 *
 * @property AnalitycsBitacora[] $analitycsBitacoras
 */
class AnalitycsSystem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'analitycs_system';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platform', 'language', 'unityVersion', 'os', 'osFamily'], 'required'],
            [['platform', 'osFamily'], 'string'],
            [['language', 'unityVersion'], 'string', 'max' => 32],
            [['os'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform' => 'Platform',
            'language' => 'Language',
            'unityVersion' => 'Unity Version',
            'os' => 'Os',
            'osFamily' => 'Os Family',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnalitycsBitacoras()
    {
        return $this->hasMany(AnalitycsBitacora::className(), ['sys_id' => 'id']);
    }
}
