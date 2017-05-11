<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OfficialAccount\Payment;

use EasyWeChat\OfficialAccount\Payment\CashCoupon\CashCoupon;
use EasyWeChat\OfficialAccount\Payment\LuckyMoney\LuckyMoney;
use EasyWeChat\OfficialAccount\Payment\MerchantPay\MerchantPay;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['merchant'] = function ($pimple) {
            $config = array_merge(
                ['app_id' => $pimple['config']['app_id']],
                $pimple['config']->get('payment', [])
            );

            return new Merchant($config);
        };

        $pimple['payment'] = function ($pimple) {
            $payment = new Payment($pimple['merchant']);
            $payment->sandboxMode(
                (bool) $pimple['config']->get('payment.sandbox_mode')
            );

            return $payment;
        };

        $pimple['lucky_money'] = function ($pimple) {
            return new LuckyMoney($pimple['merchant']);
        };

        $pimple['merchant_pay'] = function ($pimple) {
            return new MerchantPay($pimple['merchant']);
        };

        $pimple['cash_coupon'] = function ($pimple) {
            return new CashCoupon($pimple['merchant']);
        };
    }
}
