<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace yiidreamteam\paypal\actions;

use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yiidreamteam\paypal\Api;

class WebHookAction extends Action
{
    /** @var Api */
    public $api;

    /**
     * @var string
     * @see https://developer.paypal.com/webapps/developer/applications/myapps
     */
    public $webHookId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        assert(isset($this->api));
        assert(isset($this->webHookId));

        parent::init();
    }

    public function run()
    {
        $this->checkSignature();

        // TODO: incoming event processing
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Checks incoming request for a valid signature
     *
     * @return bool
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    protected function checkSignature()
    {
        $headers = \Yii::$app->request->headers;

        $signature = $headers->get('paypal-transmission-sig');
        if (!$signature)
            throw new BadRequestHttpException;

        $checkString = sprintf('%s|%s|%s|%s',
            $headers->get('paypal-transmission-id'),
            $headers->get('paypal-transmission-time'),
            $this->webHookId,
            crc32(\Yii::$app->request->rawBody)
        );

        $certUrl = $headers->get('paypal-cert-url');
        if (!$certUrl)
            throw new BadRequestHttpException;

        $publicKey = $this->fetchPublicKey($certUrl);
        if (!$publicKey)
            throw new \Exception('Certificate error');

//        $algo = ArrayHelper::getValue($headers, 'paypal-hash-algo');

        $result = openssl_verify($checkString, base64_decode($signature), $publicKey);
        return $result === 1;
    }

    /**
     * Fetches public key from the remote certificate
     *
     * @param $url
     * @return string|false
     */
    protected function fetchPublicKey($url)
    {
        $cache = \Yii::$app->cache;
        $cacheKey = 'paypal-public-key-' . md5($url);

        $publicKey = $cache->get($cacheKey);

        if ($publicKey)
            return $publicKey;

        // trying to fetch certificate
        $cert = @file_get_contents($url);
        if (!$cert)
            return false;

        $key = openssl_pkey_get_public($cert);
        if (!$key)
            return false;

        $keyData = openssl_pkey_get_details($key);
        $result = ArrayHelper::getValue($keyData, 'key', false);

        if (!$result)
            return false;

        $cache->add($cacheKey, $result);
        return $result;
    }
}
