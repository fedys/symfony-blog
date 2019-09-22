<?php

namespace App\Tests\Functional\Extension;

use App\Kernel;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Exception;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Runner\BeforeTestHook;

class CreateDbHook implements BeforeTestHook
{
    use TestListenerDefaultImplementation;

    /**
     * @var bool
     */
    private $dbInitRun = false;

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function executeBeforeTest(string $test): void
    {
        if ($this->dbInitRun) {
            return;
        }

        if ($_SERVER['TEST_SUPPRESS_DB_CREATE']) {
            return;
        }

        if (0 === strpos($test, 'App\\Tests\\Functional\\')) {
            echo 'Creating database schema ... (Hint: TEST_SUPPRESS_DB_CREATE=1 disables this action.)'.PHP_EOL;

            $this->dbInitRun = true;

            /** @var Kernel $kernel */
            $kernelClass = $_SERVER['KERNEL_CLASS'];
            $kernel = new $kernelClass('test', true);
            $kernel->boot();

            /** @noinspection MissingService */
            $container = $kernel->getContainer()->get('test.service_container');

            /** @var EntityManagerInterface $entityManager */
            $entityManager = $container->get(EntityManagerInterface::class);
            $connection = $entityManager->getConnection();
            $params = $connection->getParams();
            $name = $params['dbname'];

            // drop/create database
            unset($params['dbname'], $params['path'], $params['url']);
            $tmpConnection = DriverManager::getConnection($params);
            $tmpConnection->connect();
            $tmpConnection->getSchemaManager()->dropAndCreateDatabase($name);

            // update schema
            $schemaTool = new SchemaTool($entityManager);
            $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
            $schemaTool->updateSchema($metadata);

            echo 'Creating database schema finished.'.PHP_EOL.PHP_EOL;
        }
    }
}
