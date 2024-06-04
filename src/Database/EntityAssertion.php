<?php

declare(strict_types=1);

namespace Alms\Testing\Database;

use Closure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use function sprintf;

/**
 * @template TEntity
 */
class EntityAssertion
{
    protected QueryBuilder $query;

    /**
     * @param class-string<TEntity> $entity
     */
    public function __construct(
        protected readonly string        $entity,
        protected EntityManagerInterface $em,
    )
    {
        $this->query = $em->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($entity, 'e');
    }

    public function withFilter(string $filter): self
    {
        $self = clone $this;
        $self->em->getFilters()->enable($filter);

        return $self;
    }

    public function where(mixed $where): self
    {
        $self = clone $this;
        $self->query->where($where);

        return $self;
    }


    public function withoutFilter(string $filter): self
    {
        $self = clone $this;
        $self->em->getFilters()->disable($filter);

        return $self;
    }

    /**
     * Assert that the number of entities in the table for the current query is equal to the expected number.
     */
    public function assertCount(int $total): void
    {
        $actual = $this->count();

        TestCase::assertSame(
            $total,
            $actual,
            sprintf('Expected %s entities in the table, actual are %s.', $total, $actual),
        );
    }

    /**
     * Assert that at least one entity is present in the table for the current query.
     */
    public function assertExists(): void
    {
        TestCase::assertTrue($this->count() > 0, sprintf('Entity [%s] not found.', $this->entity));
    }

    /**
     * Assert that no entities are present in the table for the current query.
     */
    public function assertMissing(): void
    {
        TestCase::assertSame(0, $this->count(), sprintf('Entity [%s] found.', $this->entity));
    }

    /**
     * Assert that no entities are present in the table for the current query.
     */
    public function assertEmpty(): void
    {
        $this->assertCount(0);
    }

    /**
     * Count entities in the table for the current query.
     */
    public function count(): int
    {
        return $this->query->getQuery()->getSingleScalarResult();
    }

    public function __clone()
    {
        $this->query = clone $this->query;
    }
}
