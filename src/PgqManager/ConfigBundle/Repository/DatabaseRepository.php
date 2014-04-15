<?php
/**
 * @author kmarques
 *
 * @copyright 2014 Hi-media
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