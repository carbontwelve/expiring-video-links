<?php

namespace App\Repositories;

use App\Entities\Video;
use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository
{
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
