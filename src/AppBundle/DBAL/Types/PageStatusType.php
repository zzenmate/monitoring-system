<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class PageStatusType
 */
final class PageStatusType extends AbstractEnumType
{
    const NEW_PAGE = 'new_page';
    const CHANGED_PAGE = 'changed_page';
    const DELETED_PAGE = 'deleted_page';

    protected static $choices = [
        self::NEW_PAGE => 'New page',
        self::CHANGED_PAGE => 'Changed page',
        self::CHANGED_PAGE => 'Deleted page',
    ];
}
