<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Closure;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\Annotation as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\ToolsException;
use Doctrine\Tests\OrmFunctionalTestCase;
use const PHP_INT_MAX;
use function call_user_func_array;
use function debug_backtrace;

/**
 * @group DDC-3634
 */
class DDC3634Test extends OrmFunctionalTestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $metadata = $this->em->getClassMetadata(DDC3634Entity::class);

        if (! $metadata->getValueGenerationPlan()->containsDeferred()) {
            $this->markTestSkipped('Need a post-insert ID generator in order to make this test work correctly');
        }

        try {
            $this->schemaTool->createSchema([
                $metadata,
                $this->em->getClassMetadata(DDC3634JTIBaseEntity::class),
                $this->em->getClassMetadata(DDC3634JTIChildEntity::class),
            ]);
        } catch (ToolsException $e) {
            // schema already in place
        }
    }

    public function testSavesVeryLargeIntegerAutoGeneratedValue() : void
    {
        $veryLargeId = PHP_INT_MAX . PHP_INT_MAX;

        $entityManager = EntityManager::create(
            new DDC3634LastInsertIdMockingConnection($veryLargeId, $this->em->getConnection()),
            $this->em->getConfiguration()
        );

        $entity = new DDC3634Entity();

        $entityManager->persist($entity);
        $entityManager->flush();

        self::assertSame($veryLargeId, $entity->id);
    }

    public function testSavesIntegerAutoGeneratedValueAsString() : void
    {
        $entity = new DDC3634Entity();

        $this->em->persist($entity);
        $this->em->flush();

        self::assertInternalType('string', $entity->id);
    }

    public function testSavesIntegerAutoGeneratedValueAsStringWithJoinedInheritance() : void
    {
        $entity = new DDC3634JTIChildEntity();

        $this->em->persist($entity);
        $this->em->flush();

        self::assertInternalType('string', $entity->id);
    }
}

/** @ORM\Entity */
class DDC3634Entity
{
    /** @ORM\Id @ORM\Column(type="bigint") @ORM\GeneratedValue(strategy="AUTO") */
    public $id;
}

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorMap({
 *  DDC3634JTIBaseEntity::class  = DDC3634JTIBaseEntity::class,
 *  DDC3634JTIChildEntity::class = DDC3634JTIChildEntity::class,
 * })
 */
class DDC3634JTIBaseEntity
{
    /** @ORM\Id @ORM\Column(type="bigint") @ORM\GeneratedValue(strategy="AUTO") */
    public $id;
}

/** @ORM\Entity */
class DDC3634JTIChildEntity extends DDC3634JTIBaseEntity
{
}

class DDC3634LastInsertIdMockingConnection extends Connection
{
    /** @var Connection */
    private $realConnection;

    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct($identifier, Connection $realConnection)
    {
        $this->realConnection = $realConnection;
        $this->identifier     = $identifier;
    }

    private function forwardCall()
    {
        $trace = debug_backtrace(0, 2)[1];

        return call_user_func_array([$this->realConnection, $trace['function']], $trace['args']);
    }

    public function getParams() : array
    {
        return $this->forwardCall();
    }

    public function getDatabase() : string
    {
        return $this->forwardCall();
    }

    public function getHost()
    {
        return $this->forwardCall();
    }

    public function getPort()
    {
        return $this->forwardCall();
    }

    public function getUsername()
    {
        return $this->forwardCall();
    }

    public function getPassword()
    {
        return $this->forwardCall();
    }

    public function getDriver() : Driver
    {
        return $this->forwardCall();
    }

    public function getConfiguration() : Configuration
    {
        return $this->forwardCall();
    }

    public function getEventManager() : EventManager
    {
        return $this->forwardCall();
    }

    public function getDatabasePlatform() : AbstractPlatform
    {
        return $this->forwardCall();
    }

    public function getExpressionBuilder() : ExpressionBuilder
    {
        return $this->forwardCall();
    }

    public function connect() : void
    {
        $this->forwardCall();
    }

    public function isAutoCommit() : bool
    {
        return $this->forwardCall();
    }

    public function setAutoCommit($autoCommit) : void
    {
        $this->forwardCall();
    }

    public function setFetchMode($fetchMode) : void
    {
        $this->forwardCall();
    }

    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        return $this->forwardCall();
    }

    public function fetchArray($statement, array $params = [], array $types = [])
    {
        return $this->forwardCall();
    }

    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        return $this->forwardCall();
    }

    public function isConnected() : bool
    {
        return $this->forwardCall();
    }

    public function isTransactionActive() : bool
    {
        return $this->forwardCall();
    }

    public function delete($tableExpression, array $identifier, array $types = []) : int
    {
        return $this->forwardCall();
    }

    public function close() : void
    {
        $this->forwardCall();
    }

    public function setTransactionIsolation($level) : void
    {
        $this->forwardCall();
    }

    public function getTransactionIsolation() : int
    {
        return $this->forwardCall();
    }

    public function update($tableExpression, array $data, array $identifier, array $types = []) : int
    {
        return $this->forwardCall();
    }

    public function insert($tableExpression, array $data, array $types = []) : int
    {
        return $this->forwardCall();
    }

    public function quoteIdentifier($str) : string
    {
        return $this->forwardCall();
    }

    public function quote($input, $type = null) : string
    {
        return $this->forwardCall();
    }

    public function fetchAll($sql, array $params = [], $types = []) : array
    {
        return $this->forwardCall();
    }

    public function prepare(string $statement) : Statement
    {
        return $this->forwardCall();
    }

    public function executeQuery(string $query, array $params = [], $types = [], ?QueryCacheProfile $qcp = null) : ResultStatement
    {
        return $this->forwardCall();
    }

    public function executeCacheQuery($query, $params, $types, QueryCacheProfile $qcp) : ResultStatement
    {
        return $this->forwardCall();
    }

    public function project($query, array $params, Closure $function) : array
    {
        return $this->forwardCall();
    }

    public function query(string $sql) : ResultStatement
    {
        return $this->forwardCall();
    }

    public function executeUpdate(string $query, array $params = [], array $types = []) : int
    {
        return $this->forwardCall();
    }

    public function exec(string $statement) : int
    {
        return $this->forwardCall();
    }

    public function getTransactionNestingLevel() : int
    {
        return $this->forwardCall();
    }

    public function errorCode()
    {
        return $this->forwardCall();
    }

    public function errorInfo()
    {
        return $this->forwardCall();
    }

    public function lastInsertId($seqName = null) : string
    {
        return $this->identifier;
    }

    public function transactional(Closure $func)
    {
        return $this->forwardCall();
    }

    public function setNestTransactionsWithSavepoints($nestTransactionsWithSavepoints) : void
    {
        $this->forwardCall();
    }

    public function getNestTransactionsWithSavepoints() : bool
    {
        return $this->forwardCall();
    }

    protected function getNestedTransactionSavePointName()
    {
        return $this->forwardCall();
    }

    public function beginTransaction() : void
    {
        $this->forwardCall();
    }

    public function commit() : void
    {
        $this->forwardCall();
    }

    public function rollBack() : void
    {
        $this->forwardCall();
    }

    public function createSavepoint($savepoint) : void
    {
        $this->forwardCall();
    }

    public function releaseSavepoint($savepoint) : void
    {
        $this->forwardCall();
    }

    public function rollbackSavepoint($savepoint) : void
    {
        $this->forwardCall();
    }

    public function getWrappedConnection() : \Doctrine\DBAL\Driver\Connection
    {
        return $this->forwardCall();
    }

    public function getSchemaManager() : AbstractSchemaManager
    {
        return $this->forwardCall();
    }

    public function setRollbackOnly() : void
    {
        $this->forwardCall();
    }

    public function isRollbackOnly() : bool
    {
        return $this->forwardCall();
    }

    public function convertToDatabaseValue($value, $type)
    {
        return $this->forwardCall();
    }

    public function convertToPHPValue($value, $type)
    {
        return $this->forwardCall();
    }

    public function resolveParams(array $params, array $types) : array
    {
        return $this->forwardCall();
    }

    public function createQueryBuilder() : QueryBuilder
    {
        return $this->forwardCall();
    }

    public function ping() : void
    {
        $this->forwardCall();
    }
}
