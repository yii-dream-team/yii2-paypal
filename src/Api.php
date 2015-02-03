<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace yiidreamteam\paypal;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Api
 * @package yiidreamteam\paypal
 *
 * @property ApiContext $context
 */
class Api extends Component
{
    const MODE_LIVE = 'live';
    const MODE_SANDBOX = 'sandbox';

    const LOG_LEVEL_FINE = 'FINE';
    const LOG_LEVEL_INFO = 'INFO';
    const LOG_LEVEL_WARN = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';

    public $clientId;
    public $clientSecret;

    public $mode = self::MODE_SANDBOX;

    public $logEnabled = true;
    public $logPath = '@runtime/logs/paypal.log';
    public $logLevel = self::LOG_LEVEL_WARN;

    public $config = [];

    protected $context;

    /**
     * @inheritdoc
     */
    public function init()
    {
        assert(isset($this->clientId));
        assert(isset($this->clientSecret));

        parent::init();
    }

    /**
     * Getter for the $context variable
     *
     * @return ApiContext
     */
    public function getContext()
    {
        if (empty($this->context))
            $this->initContext();
        return $this->context;
    }

    /**
     * Initializes API context
     */
    protected function initContext()
    {
        $this->context = new ApiContext(new OAuthTokenCredential(
            $this->clientId,
            $this->clientSecret
        ));

        $this->context->setConfig(ArrayHelper::merge([
            'mode' => $this->mode,
            'log.LogEnabled' => $this->logEnabled,
            'log.FileName' => \Yii::getAlias($this->logPath),
            'log.LogLevel' => $this->logLevel,
        ], $this->config));
    }


}