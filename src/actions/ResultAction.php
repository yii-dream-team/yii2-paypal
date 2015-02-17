<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace yiidreamteam\paypal\actions;

use yii\base\Action;
use yiidreamteam\paypal\Api;

class ResultAction extends Action
{
    /** @var Api */
    public $api;

    public $redirectUrl;

    public $silent = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        assert(isset($this->api));

        parent::init();
    }

    public function run()
    {
        try {
            $this->api->processResult(\Yii::$app->request->get());
        } catch (\Exception $e) {
            if (!$this->silent)
                throw $e;
        }

        if (isset($this->redirectUrl))
            return $this->controller->redirect($this->redirectUrl);
        else
            return $this->controller->goHome();
    }
}