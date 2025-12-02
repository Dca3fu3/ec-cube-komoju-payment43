<?php

namespace Plugin\KomojuPayment43\Repository;

use Eccube\Repository\AbstractRepository;
use Plugin\KomojuPayment43\Entity\Config;
use Doctrine\Persistence\ManagerRegistry;

class ConfigRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @return Config
     */
    public function get()
    {
        $config = $this->findOneBy([]);
        if ($config) {
            return $config;
        }

        $config = new Config();
        $this->getEntityManager()->persist($config);
        $this->getEntityManager()->flush();

        return $config;
    }
}
