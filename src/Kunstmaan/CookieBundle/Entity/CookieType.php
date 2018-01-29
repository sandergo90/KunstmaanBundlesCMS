<?php

namespace Kunstmaan\CookieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CookieType
 *
 * @ORM\Table(name="kuma_cookie_types")
 * @ORM\Entity(repositoryClass="Kunstmaan\CookieBundle\Repository\CookieTypeRepository")
 */
class CookieType extends AbstractEntity implements OverviewNavigationInterface
{
    /**
     * @var string
     * @Gedmo\Translatable()
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Translatable()
     *
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     * @Gedmo\Translatable()
     *
     * @ORM\Column(name="long_description", type="text", nullable=true)
     */
    private $longDescription;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="internal_name", type="string", length=255, nullable=true)
     */
    private $internalName;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="always_on", type="boolean")
     */
    private $alwaysOn = false;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CookieType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return CookieType
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     *
     * @param string $longDescription
     *
     * @return CookieType
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * Get longDescription
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * Set internalName
     *
     * @param string $internalName
     *
     * @return CookieType
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * Get internalName
     *
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return CookieType
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getOverViewRoute()
    {
        return 'kunstmaancookiebundle_admin_cookietype';
    }



    /**
     * Set alwaysOn
     *
     * @param boolean $alwaysOn
     *
     * @return CookieType
     */
    public function setAlwaysOn($alwaysOn)
    {
        $this->alwaysOn = $alwaysOn;

        return $this;
    }

    /**
     * Get alwaysOn
     *
     * @return boolean
     */
    public function isAlwaysOn()
    {
        return $this->alwaysOn;
    }
}
