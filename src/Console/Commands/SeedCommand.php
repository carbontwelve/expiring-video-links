<?php

namespace App\Console\Commands;

use App\Console\ConsoleCommand;
use App\Entities\Video;
use App\Entities\Thread;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SeedCommand extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('db:seed')
            ->setDescription('Seeds database with data.');
    }

    private function emptyTable(EntityManagerInterface $em, $className)
    {
        $cmd = $em->getClassMetadata($className);
        $connection = $em->getConnection();
        $connection->beginTransaction();

        try {
            //$connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $cmd->getTableName());
            // Beware of ALTER TABLE here--it's another DDL statement and will cause
            // an implicit commit.
            //$connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
            return true;
        } catch (\Exception $e) {
            $connection->rollback();
            return $e->getMessage();
        }
    }

    /**
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function fire()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        $entities = [
            Video::class,
        ];

        $progress = new ProgressBar($this->output);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %message% %memory:6s%');
        $progress->setRedrawFrequency(250);

        foreach ($entities as $entity) {
            $clear = $this->emptyTable($em, $entity);
            if ($clear === true) {
                $this->output->writeln('Entity [' . $entity . '] Cleared.');
            } else {
                $this->output->writeln('There was an error clearing [' . $entity . '].');
                $this->output->writeln($clear);
                return 1;
            }
        }

        $progress->start((10*250));

        $em->flush();
        return 0;
    }
}