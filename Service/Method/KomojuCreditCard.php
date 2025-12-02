<?php

namespace Plugin\KomojuPayment43\Service\Method;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\Payment\PaymentResult;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Plugin\KomojuPayment43\Service\KomojuService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class KomojuCreditCard implements PaymentMethodInterface
{
    /**
     * @var KomojuService
     */
    private $komojuService;

    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var PurchaseFlow
     */
    private $purchaseFlow;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var FormInterface
     */
    private $form;

    public function __construct(
        KomojuService $komojuService,
        OrderStatusRepository $orderStatusRepository,
        PurchaseFlow $purchaseFlow,
        EntityManagerInterface $entityManager
    ) {
        $this->komojuService = $komojuService;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->purchaseFlow = $purchaseFlow;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function verify()
    {
        $result = new PaymentResult();
        $result->setSuccess(true);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function checkout()
    {
        $result = new PaymentResult();
        try {
            $session = $this->komojuService->createSession($this->order);
            $sessionUrl = $session['session_url'];

            $result->setSuccess(true);
            $result->setResponse(new RedirectResponse($sessionUrl));
        } catch (\Exception $e) {
            $result->setSuccess(false);
            $result->setErrors(['message' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PENDING);
        $this->order->setOrderStatus($OrderStatus);
        $this->purchaseFlow->prepare($this->order, new PurchaseContext());
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormType(FormInterface $form)
    {
        $this->form = $form;
    }
}
