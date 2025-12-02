<?php

namespace Plugin\KomojuPayment43\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;

/**
 * Config
 *
 * @ORM\Table(name="plg_komoju_config")
 * @ORM\Entity(repositoryClass="Plugin\KomojuPayment43\Repository\ConfigRepository")
 */
class Config extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="secret_key", type="string", length=255, nullable=true)
     */
    private $secret_key;

    /**
     * @var string|null
     *
     * @ORM\Column(name="merchant_id", type="string", length=255, nullable=true)
     */
    private $merchant_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="webhook_secret", type="string", length=255, nullable=true)
     */
    private $webhook_secret;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * @param string|null $secret_key
     * @return Config
     */
    public function setSecretKey($secret_key)
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param string|null $merchant_id
     * @return Config
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebhookSecret()
    {
        return $this->webhook_secret;
    }

    /**
     * @param string|null $webhook_secret
     * @return Config
     */
    public function setWebhookSecret($webhook_secret)
    {
        $this->webhook_secret = $webhook_secret;
        return $this;
    }
}
