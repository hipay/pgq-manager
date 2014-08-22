<?php
/**
 * Copyright (c) 2014 HiMedia Group
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014 HiMedia Group
 * @author Karl MARQUES <kmarques@hi-media.com>
 * @license Apache License, Version 2.0
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