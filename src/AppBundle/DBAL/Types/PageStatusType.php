<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class PageStatusType
 */
final class PageStatusType extends AbstractEnumType
{
    const NEW_PAGE = 'NP';
    const CHANGED_PAGE = 'GP';
    const DELETED_PAGE = 'DP';

    protected static $choices = [
        self::NEW_PAGE => 'New page',
        self::CHANGED_PAGE => 'Changed page',
        self::CHANGED_PAGE => 'Deleted page',
    ];
}
