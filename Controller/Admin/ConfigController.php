<?php

namespace Plugin\KomojuPayment43\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\KomojuPayment43\Form\Type\Admin\ConfigType;
use Plugin\KomojuPayment43\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/komoju_payment43/config", name="komoju_payment43_admin_config")
     * @Template("@KomojuPayment43/admin/config.twig")
     */
    public function index(Request $request)
    {
        $config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config = $form->getData();
            $this->entityManager->persist($config);
            $this->entityManager->flush();
            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('komoju_payment43_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
