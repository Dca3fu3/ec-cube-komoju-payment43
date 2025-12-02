<?php

namespace Plugin\KomojuPayment43\Controller;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Plugin\KomojuPayment43\Service\KomojuService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Eccube\Service\CartService;
use Eccube\Service\MailService;

class KomojuPaymentController extends AbstractController
{
    /**
     * @var KomojuService
     */
    private $komojuService;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

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
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var MailService
     */
    private $mailService;

    public function __construct(
        KomojuService $komojuService,
        OrderRepository $orderRepository,
        OrderStatusRepository $orderStatusRepository,
        PurchaseFlow $purchaseFlow,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        CartService $cartService,
        MailService $mailService
    ) {
        $this->komojuService = $komojuService;
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->purchaseFlow = $purchaseFlow;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->cartService = $cartService;
        $this->mailService = $mailService;
    }

    /**
     * @Route("/plugin/komoju/return", name="plugin_komoju_payment43_return")
     */
    public function return(Request $request)
    {
        $sessionId = $request->query->get('session_id');
        if (!$sessionId) {
            $this->logger->error('Komoju Return: Invalid session ID');
            $this->addError('セッションIDが無効です。');
            return $this->redirectToRoute('cart');
        }

        try {
            $this->logger->info('Komoju Return: Processing session', ['session_id' => $sessionId]);
            $session = $this->komojuService->getSession($sessionId);
            $status = $session['status'];
            $orderId = $session['metadata']['order_id'] ?? null;

            if (!$orderId) {
                throw new \Exception('セッションメタデータに注文IDが見つかりません。');
            }

            $order = $this->orderRepository->find($orderId);
            if (!$order) {
                throw new \Exception('注文が見つかりません: ' . $orderId);
            }

            $this->logger->info('Komoju Return: Order found', ['order_id' => $orderId, 'status' => $status]);

            if ($status === 'completed') {
                $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PAID);
                $order->setOrderStatus($OrderStatus);
                $order->setPaymentDate(new \DateTime());
                $order->setOrderDate(new \DateTime());

                $this->purchaseFlow->commit($order, new PurchaseContext());

                // Clear Cart
                $this->cartService->clear();

                // Send Mail
                $this->mailService->sendOrderMail($order);

                $this->entityManager->flush();

                $this->logger->info('Komoju Return: Order updated to PAID, Cart cleared, Mail sent', ['order_id' => $orderId]);

                $request->getSession()->set('eccube.front.shopping.order.id', $order->getId());
                return $this->redirectToRoute('shopping_complete');
            } else {
                $this->logger->warning('Komoju Return: Payment not completed', ['status' => $status]);
                return $this->redirectToRoute('cart');
            }

        } catch (\Exception $e) {
            $this->logger->error('Komoju Return: Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->addError($e->getMessage());
            return $this->redirectToRoute('cart');
        }
    }
}
