<?php

namespace Royalcms\Component\Pay\PayVendor\Wechat\Gateways;

use Royalcms\Component\Pay\PayVendor\Wechat\Wechat;
use Royalcms\Component\Pay\Log;
use Royalcms\Component\Support\Collection;
use Royalcms\Component\Pay\Exceptions\GatewayException;
use Royalcms\Component\Support\Str;
use Royalcms\Component\Pay\PayVendor\Wechat\Support;

class TransferQueryGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param \Royalcms\Component\Pay\PayVendor\Wechat\Orders\TransfersQueryOrder $payload
     *
     * @throws \Royalcms\Component\Pay\Exceptions\GatewayException
     * @throws \Royalcms\Component\Pay\Exceptions\InvalidArgumentException
     * @throws \Royalcms\Component\Pay\Exceptions\InvalidSignException
     *
     * @return Collection
     */
    public function pay($endpoint, $payload)
    {
        if (! ($payload instanceof \Royalcms\Component\Pay\PayVendor\Wechat\Orders\TransfersQueryOrder)) {
            throw new GatewayException('The payload must is "\Royalcms\Component\Pay\PayVendor\Wechat\Orders\TransfersQueryOrder" instance!');
        }

        $api = $this->getMethod();

        /*
        if ($this->mode === Wechat::MODE_SERVICE) {

        }*/

        $payload->setAppid($this->config->get('mpapp_id')); //公众账号ID
        $payload->setMchId($this->config->get('mch_id')); //商户号
        $payload->setNonceStr(Str::random()); //随机字符串

        $sign = Support::generateSign($payload->toArray(), $this->config->get('key'));

        $payload->setSign($sign); //签名

        $params = $payload->toArray();

        Log::debug('Query A Transfer Order:', [$endpoint, $params]);

        return Support::requestApi(
            $api,
            $params,
            $this->config->get('key'),
            ['cert' => $this->config->get('cert_client'), 'ssl_key' => $this->config->get('cert_key')]
        );
    }

    /**
     * Get method config.
     *
     * @return string
     */
    protected function getMethod()
    {
        return 'mmpaymkttransfers/gettransferinfo';
    }

    /**
     * Get trade type config.
     *
     * @return string
     */
    protected function getTradeType()
    {
        return '';
    }
}
