<?php

namespace App\Tests\Functional\Traits;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

trait DbTrait
{
    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        /* @noinspection PhpUndefinedMethodInspection */
        return $entityManager = self::$container->get(EntityManagerInterface::class);
    }

    /**
     * @param array $entities
     *
     * @throws DBALException
     */
    private function truncateEntities(array $entities): void
    {
        /** @var EntityManagerInterface $entityManager */
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $entityManager = static::$container->get(EntityManagerInterface::class);
        $connection = $entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $supportsForeignKeys = $databasePlatform->supportsForeignKeyConstraints();

        if ($supportsForeignKeys) {
            $connection->query('SET FOREIGN_KEY_CHECKS = 0');
        }

        foreach ($entities as $entity) {
            $tableName = $entityManager->getClassMetadata($entity)->getTableName();
            $query = $databasePlatform->getTruncateTableSQL($tableName);
            $connection->executeUpdate($query);
        }

        if ($supportsForeignKeys) {
            $connection->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
