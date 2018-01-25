<?php

namespace Kunstmaan\CookieBundle\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\EnumerationFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\CookieBundle\Entity\Cookie;
use Kunstmaan\CookieBundle\Form\CookieAdminType;

/**
 * Class CookieAdminListConfigurator
 *
 * @package Kunstmaan\CookieBundle\AdminList
 */
class CookieAdminListConfigurator extends AbstractDoctrineDBALAdminListConfigurator implements OverviewNavigationInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * CookieAdminListConfigurator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em->getConnection());
        $this->setAdminType(CookieAdminType::class);
        $this->em = $em;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma.cookie.adminlists.cookie.name', true);
        $this->addField('type', 'kuma.cookie.adminlists.cookie.type', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'kuma.cookie.adminlists.cookie.name');
        $this->addFilter('type', new EnumerationFilterType('id', 't'), 'kuma.cookie.adminlists.cookie.type', $this->getCookieTypes());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $params
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = [])
    {
        $queryBuilder
            ->select('b.id, b.name, t.name as type, t.id')
            ->from('kuma_cookies', 'b')
            ->innerJoin('b', 'kuma_cookie_types', 't', 'b.type_id = t.id')
            ->orderBy('b.id', 'DESC');
    }

    /**
     * @return array
     */
    private function getCookieTypes()
    {
        $cookieTypes = [];
        foreach ($this->em->getRepository('KunstmaanCookieBundle:CookieType')->findAll() as $cookieType) {
            $cookieTypes[$cookieType->getId()] = $cookieType->getName();
        }

        return $cookieTypes;
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanCookieBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Cookie';
    }

    /**
     * @return string
     */
    public function getOverViewRoute()
    {
        return 'kunstmaancookiebundle_admin_cookie';
    }

    /**
     * Return the url to edit the given $item
     *
     * @param Cookie $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = ['id' => $item['id']];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_EDIT),
            'params' => $params,
        ];
    }

    /**
     * Get the delete url for the given $item
     *
     * @param Cookie $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = ['id' => $item['id']];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE),
            'params' => $params,
        ];
    }
}
