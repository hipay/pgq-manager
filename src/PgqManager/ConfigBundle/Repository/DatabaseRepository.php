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

namespace PgqManager\ConfigBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DatabaseRepository extends EntityRepository
{
    public function findOne(array $criteria)
    {
        if (isset($criteria['uid'])) {
            $dql = $this->createQueryBuilder('d')
                ->select('d')
                ->join('d.settings', 's')
                ->andWhere('s.uid = :uid')
                ->setParameter('uid', $criteria['uid']);

            if (isset($criteria['name'])) {
                $dql->andWhere('d.name = :name')
                    ->setParameter('name', $criteria['name']);
            }

            return $dql->getQuery()->getSingleResult();
        } else {

            return $this->findOneBy($criteria);
        }
    }
}