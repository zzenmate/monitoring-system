<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Log Entity
 *
 * @ORM\Table(name="logs", indexes={
 *     @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *     @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *     @ORM\Index(name="log_user_lookup_idx", columns={"username"}),
 *     @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 */
class Log extends AbstractLogEntry
{
}
