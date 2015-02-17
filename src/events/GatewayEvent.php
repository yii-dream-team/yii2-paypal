<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace yiidreamteam\paypal\events;

use PayPal\Api\Payment;
use yii\base\Event;
use yii\db\ActiveRecord;

class GatewayEvent extends Event
{
    const EVENT_PAYMENT_REQUEST = 'eventPaymentRequest';
    const EVENT_PAYMENT_SUCCESS = 'eventPaymentSuccess';

    /** @var Payment */
    public $payment;
    /** @var ActiveRecord|null */
    public $invoice;
    /** @var array */
    public $gatewayData = [];
}