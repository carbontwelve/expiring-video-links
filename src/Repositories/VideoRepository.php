<?php

namespace App\Repositories;

use App\Entities\Video;
use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository
{
    /**
     * @param string $uri
     * @param int $mode
     * @param int $parent
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCommentsByUri(string $uri, int $mode, int $parent = null): int
    {
        $q = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->innerJoin('c.thread', 't')
            ->where('t.uri = :uri')
            ->andWhere('c.mode = :mode')
            ->setParameters([
                'uri' => $uri,
                'mode' => $mode
            ]);

        if (!is_null($parent)) {
            $q = $q->andWhere('c.parent = :parent');
            $q = $q->setParameter('parent', $parent);
        }else {
            $q = $q->andWhere('c.parent IS NULL');
        }

        return $q->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $code
     * @return Video
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getVideoByCode(string $code)
    {
        $q = $this->createQueryBuilder('v')
            ->where('v.code = :code')
            ->andWhere('v.watches < v.maxWatches')
            ->setParameters([
                'code' => $code
            ]);

        return $q->getQuery()
            ->getSingleResult();
    }
}
