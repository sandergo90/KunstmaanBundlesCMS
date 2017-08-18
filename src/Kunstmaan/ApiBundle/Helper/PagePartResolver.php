<?php

namespace Kunstmaan\ApiBundle\Helper;

use Kunstmaan\ApiBundle\Model\ApiPage;
use Kunstmaan\ApiBundle\Model\ApiPagePart;

/**
 * Class PagePartResolver
 */
class PagePartResolver
{
    /**
     * @param ApiPage $page
     *
     * @return array
     */
    public function resolve(ApiPage $page)
    {
        $pageParts = [];

        /** @var ApiPagePart $apiPagePart */
        foreach ($page->getPageParts() as $apiPagePart) {
            $pageParts[$apiPagePart->getType()] = $apiPagePart->getPagePart();
        }

        return $pageParts;
    }
}