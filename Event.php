<?php

namespace Plugin\KomojuPayment43;

use Eccube\Event\TemplateEvent;
use Eccube\Repository\PaymentRepository;
use Plugin\KomojuPayment43\Service\Method\KomojuCreditCard;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'Shopping/index.twig' => 'onShoppingIndexTwig',
        ];
    }

    public function onShoppingIndexTwig(TemplateEvent $event): void
    {
        $payment = $this->paymentRepository->findOneBy(['method_class' => KomojuCreditCard::class]);
        if (!$payment) {
            return;
        }

        $parameters = $event->getParameters();
        $parameters['KomojuPayment'] = [
            'payment_id' => $payment->getId(),
        ];
        $event->setParameters($parameters);

        $event->addSnippet('@KomojuPayment43/default/Shopping/index/description.twig');
    }
}
