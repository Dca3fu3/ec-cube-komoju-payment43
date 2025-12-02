<?php

namespace Plugin\KomojuPayment43\Service;

use Eccube\Entity\Order;
use GuzzleHttp\Client;
use Plugin\KomojuPayment43\Repository\ConfigRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class KomojuService
{
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(ConfigRepository $configRepository, UrlGeneratorInterface $router)
    {
        $this->configRepository = $configRepository;
        $this->router = $router;
    }

    public function createSession(Order $order)
    {
        $config = $this->configRepository->get();
        $client = new Client();

        $returnUrl = $this->router->generate('plugin_komoju_payment43_return', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $params = [
            'amount' => $order->getTotal(),
            'currency' => $order->getCurrencyCode(),
            'return_url' => $returnUrl,
            'default_locale' => 'ja',
            'external_order_num' => (string) $order->getOrderNo(),
            // 'payment_types' => ['credit_card'],
            'metadata' => [
                'order_id' => (string) $order->getId(),
            ],
            'email' => $order->getEmail(),
        ];

        $response = $client->post('https://komoju.com/api/v1/sessions', [
            'auth' => [$config->getSecretKey(), ''],
            'json' => $params
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getSession($sessionId)
    {
        $config = $this->configRepository->get();
        $client = new Client();

        $response = $client->get('https://komoju.com/api/v1/sessions/' . $sessionId, [
            'auth' => [$config->getSecretKey(), ''],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getAddress(Order $order, $isShipping = false)
    {
        if ($isShipping) {
            $shipping = $order->getShippings()->first();
            if (!$shipping) {
                return null;
            }
            $zipcode = $shipping->getPostalCode();
            $addr01 = $shipping->getAddr01();
            $addr02 = $shipping->getAddr02();
            $pref = $shipping->getPref();
            $name = $shipping->getName01() . ' ' . $shipping->getName02();
        } else {
            $zipcode = $order->getPostalCode();
            $addr01 = $order->getAddr01();
            $addr02 = $order->getAddr02();
            $pref = $order->getPref();
            $name = $order->getName01() . ' ' . $order->getName02();
        }

        return [
            'zipcode' => $zipcode,
            'street_address1' => $addr01 ?: '',
            'street_address2' => $addr02 ?: '',
            'country' => 'JP',
            'state' => $pref ? $pref->getName() : '',
            'city' => $addr01 ?: '',
            'label' => $name,
        ];
    }
}
