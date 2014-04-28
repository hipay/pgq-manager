<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Validator\Constraints;


use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use PgqManager\ConfigBundle\Entity\Database;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DBAL
 * @package PgqManager\ConfigBundle\Validator\Constraints
 * @Annotation
 */
class DBAL extends Constraint
{

    public $message = '%string%';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'DBALConnection';
    }

}