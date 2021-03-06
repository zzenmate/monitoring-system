<?php

namespace AppBundle\EntityListener;

use AppBundle\DBAL\Types\PageStatusType;
use AppBundle\Entity\Page;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageListener
 */
class PageListener
{
    /** @var ContainerInterface $container */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container Container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Post soft delete
     *
     * @param LifecycleEventArgs $args Arguments
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $page = $args->getEntity();
        if ($page instanceof Page) {
            $page->setStatus(PageStatusType::DELETED_PAGE);

            $this->container->get('app.page.manager')->flush();
        }
    }
}
