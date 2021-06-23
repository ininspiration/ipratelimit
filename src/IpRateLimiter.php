<?php

namespace ininspiration\ipratelimit;

use Closure;
use Yii;
use yii\filters\RateLimitInterface;
use yii\web\TooManyRequestsHttpException;

class IpRateLimiter extends \yii\filters\RateLimiter implements RateLimitInterface
{
    public $ipmodel = "";

    //500times per 60s
    public $rate = [500, 60];

    /**
     * override
     *
     * @param \yii\base\Action $action
     * @return bool
     * @throws \Throwable
     * @throws \yii\web\TooManyRequestsHttpException
     */
    public function beforeAction($action)
    {
        if ($this->user === null && Yii::$app->getUser()) {
            $this->user = Yii::$app->getUser()->getIdentity(false);
        }

        if ($this->user instanceof Closure) {
            $this->user = call_user_func($this->user, $action);
        }

        if ($this->user instanceof RateLimitInterface) {
            Yii::debug('Check rate limit', __METHOD__);
            $this->checkRateLimit($this->user, $this->request, $this->response, $action);
        } else {
            if ($this->ipmodel) {
                Yii::info('user not logged in.using IP Rate limit.', __METHOD__);

                $this->checkIpRateLimit($this->request, $this->response, $action);
            } else {
                Yii::info('Rate limit skipped: "user" does not implement RateLimitInterface.', __METHOD__);
            }
        }

        return true;
    }

    public function checkIpRateLimit($request, $response, $action)
    {
        list($limit, $window) = $this->getRateLimit($request, $action);
        list($allowance, $timestamp) = $this->loadAllowance($request, $action);

        $current = time();

        $allowance += (int) (($current - $timestamp) * $limit / $window);
        if ($allowance > $limit) {
            $allowance = $limit;
        }

        if ($allowance < 1) {
            $this->saveAllowance($request, $action, 0, $current);
            $this->addRateLimitHeaders($response, $limit, 0, $window);
            throw new TooManyRequestsHttpException($this->errorMessage);
        }

        $this->saveAllowance($request, $action, $allowance - 1, $current);
        $this->addRateLimitHeaders($response, $limit, $allowance - 1, (int) (($limit - $allowance + 1) * $window / $limit));
    }

    public function getRateLimit($request, $action)
    {
        // $rateLimit requests per second
        //return [5, 60];
        return $this->rate;
    }

    public function loadAllowance($request, $action)
    {
        $model = $this->ipmodel::find()->where([
            "client_ip" => $_SERVER["REMOTE_ADDR"]
        ])->one();

        if (!$model) {
            return $this->getRateLimit($request, $action);
        }

        return [$model->allowance, $model->allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $model = $this->ipmodel::find()->where([
            "client_ip" => $_SERVER["REMOTE_ADDR"]
        ])->one();

        if (!$model) {
            $model = new $this->ipmodel();
            $model->client_ip = $_SERVER["REMOTE_ADDR"];
        }

        $model->allowance = $allowance;
        $model->allowance_updated_at = $timestamp;
        $model->save();
    }
}