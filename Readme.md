if a user doesn't login，then use ipratelimt, otherwise use userratelimit.

#install
composer require ininspiration/ipratelimit

#migration
php yii migrate/up --migrationPath=@ininspiration/ipratelimit/migrations

#behaviors()
```
public function behaviors()
{
    $behaviors = parent::behaviors();

    //override - rateLimiter
    $behaviors["rateLimiter"] = [
        "class" => \ininspiration\ipratelimit\IpRateLimiter::class,
        'ipmodel' => \ininspiration\ipratelimit\models\IpRatelimit::class,
    ];

    return $behaviors;
}
```
