<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
 */

namespace PgqManager\ConfigBundle\Validator;


use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use PgqManager\ConfigBundle\Entity\Database;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DBALValidator extends ConstraintValidator
{

    private $factory;

    function __construct(ConnectionFactory $dbFactory)
    {
        $this->factory = $dbFactory;
    }


    /**
     * Checks if the passed value is valid.
     *
     * @param Database $database The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($database, Constraint $constraint)
    {
        try {
            $this->factory->createConnection(array(
                'driver'   => $database->getDriver(),
                'user'     => $database->getUser(),
                'password' => $database->getPassword(),
                'host'     => $database->getHost(),
                'dbname'   => $database->getName(),
                'port'     => $database->getPort()
            ))->connect();
        } catch (\PDOException $e) {
            $this->context->addViolation($constraint->message, array('%string%' => $e->getMessage()));
        }
    }
}