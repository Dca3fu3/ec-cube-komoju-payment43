<?php

namespace Plugin\KomojuPayment43;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Payment;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\PaymentRepository;
use Plugin\KomojuPayment43\Entity\Config;
use Plugin\KomojuPayment43\Service\Method\KomojuCreditCard;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function enable(array $meta, ContainerInterface $container)
    {
        $this->registerConfig($container);
        $this->registerPaymentMethod($container);
    }

    private function registerConfig(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        $configRepository = $entityManager->getRepository(Config::class);
        $config = $configRepository->get();

        if (!$config) {
            $config = new Config();
            $entityManager->persist($config);
            $entityManager->flush();
        }
    }

    private function registerPaymentMethod(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $entityManager->getRepository(Payment::class);

        $payment = $paymentRepository->findOneBy(['method_class' => KomojuCreditCard::class]);

        if (!$payment) {
            $payment = new Payment();
            $payment->setMethod('Komoju決済');
            $payment->setMethodClass(KomojuCreditCard::class);
            $payment->setVisible(true);
            $payment->setCharge(0);
            $payment->setSortNo(1);
            $entityManager->persist($payment);
            $entityManager->flush();
        }
    }

    public function disable(array $meta, ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();
        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $entityManager->getRepository(Payment::class);

        $payment = $paymentRepository->findOneBy(['method_class' => KomojuCreditCard::class]);

        if ($payment) {
            $payment->setVisible(false);
            $entityManager->persist($payment);
            $entityManager->flush();
        }
    }
}
