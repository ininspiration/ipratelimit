<?php

namespace ininspiration\ipratelimit\models;

class IpRatelimit extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors["TimestampBehavior"] = [
            "class" => \yii\behaviors\TimestampBehavior::class,
            'updatedAtAttribute' => false,
        ];

        return $behaviors;
    }

    public static function tableName()
    {
        return "ip_ratelimit";
    }

    public function rules()
    {
        return [
            ["client_ip", "trim", ],
            ["client_ip", "string", "max" => 22, ],
            ["allowance", "number", ],
            ["created_at", "number", ],
            ["allowance_updated_at", "number", ],
        ];
    }
}