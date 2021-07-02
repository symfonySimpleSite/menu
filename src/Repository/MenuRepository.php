<?php

namespace SymfonySimpleSite\Menu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use SymfonySimpleSite\Common\Traits\AliasRepositoryTrait;
use SymfonySimpleSite\Common\Traits\CommonRepository;
use SymfonySimpleSite\Common\Traits\GetQueryBuilderRepositoryTrait;
use SymfonySimpleSite\Menu\Entity\Config;
use SymfonySimpleSite\Menu\Entity\Interfaces\MenuInterface;
use SymfonySimpleSite\Menu\Entity\Menu;
use SymfonySimpleSite\NestedSets\Entity\NodeInterface;
use SymfonySimpleSite\NestedSets\Repository\NestedSetsCreateDeleteInterface;
use SymfonySimpleSite\NestedSets\Repository\NestedSetsMoveItemsInterface;
use SymfonySimpleSite\NestedSets\Repository\NestedSetsMoveUpDownInterface;

class MenuRepository extends ServiceEntityRepository
    implements NestedSetsMoveItemsInterface,
               NestedSetsMoveUpDownInterface,
               NestedSetsCreateDeleteInterface
{
    use AliasRepositoryTrait, GetQueryBuilderRepositoryTrait;
    
    private NestedSetsMoveItemsInterface $nestedSetsMoveItems;
    private NestedSetsMoveUpDownInterface $nestedSetsMoveUpDown;
    private NestedSetsCreateDeleteInterface $nestedSetsCreateDelete;

    public function __construct(
        ManagerRegistry $registry,
        NestedSetsMoveItemsInterface $nestedSetsMoveItems,
        NestedSetsMoveUpDownInterface $nestedSetsMoveUpDown,
        NestedSetsCreateDeleteInterface $nestedSetsCreateDelete
    ) {
        parent::__construct($registry, Menu::class);

        $this->nestedSetsMoveItems = $nestedSetsMoveItems;
        $this->nestedSetsMoveUpDown = $nestedSetsMoveUpDown;
        $this->nestedSetsCreateDelete = $nestedSetsCreateDelete;

        $this->nestedSetsMoveUpDown->setEntityClassName(Menu::class);
        $this->nestedSetsMoveItems->setEntityClassName(Menu::class);
        $this->nestedSetsCreateDelete->setEntityClassName(Menu::class);
        $this->setAlias('m');
    }


    public function getItemsByIds(int $parentId, ?int $maxDeep = null): array
    {
        $parent = $this->find($parentId);

        if (empty($parent)) {
            return [];
        }

        $result = [
            'parent' => $parent
        ];

        $queryBuilder = $this->getAllSubItemsQueryBuilder($parent);
        if ($maxDeep !== null) {
            $queryBuilder
                ->andWhere("{$this->getAlias()}.lvl=:lvl")
                ->setParameter('lvl', $parent->getLvl() + $maxDeep);
        }
        $result['items'] = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    public function create(NodeInterface $node, ?NodeInterface $parent = null): NodeInterface
    {
        return $this->nestedSetsCreateDelete->create($node, $parent);
    }

    public function delete(NodeInterface $node, bool $isSafeDelete = true): void
    {
        $this->nestedSetsCreateDelete->delete($node, $isSafeDelete);
    }

    public function getAllQueryBuilder(?NodeInterface $menu = null): QueryBuilder
    {
        $queryBuilder = $this
            ->getQueryBuilder()
            ->orderBy($this->getAlias() . ".tree", "ASC")
            ->addOrderBy($this->getAlias() . ".lft", "ASC");

        if ($menu !== null) {
            $queryBuilder
                ->andWhere("{$this->getAlias()}.tree=:tree")
                ->setParameter('tree', $menu->getTree())
            ;
        }

        return $queryBuilder;
    }

    public function getAllSubItemsQueryBuilder(NodeInterface $menu, ?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $this->getQueryBuilder($queryBuilder)
            ->andWhere($this->getAlias() . ".tree=:tree")->setParameter("tree", $menu->getTree())
            ->andWhere($this->getAlias() . ".lft>:lft")->setParameter("lft", $menu->getLft())
            ->andWhere($this->getAlias() . ".rgt<:rgt")->setParameter("rgt", $menu->getRgt());
    }

    public function getAllRootsQueryBuilder(): QueryBuilder
    {
        return $this
            ->getQueryBuilder()
            ->andWhere($this->getAlias() . ".lft=:lft")
            ->setParameter("lft", 1);
    }

    public function move(NodeInterface $node, ?NodeInterface $parent): void
    {
        try {
            $this->nestedSetsMoveItems->move($node, $parent);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function upDown(NodeInterface $node, bool $isUp = true): void
    {
        $this->nestedSetsMoveUpDown->upDown($node, $isUp);
    }

    public function findOneByNameQueryBuilder(string $name): QueryBuilder
    {
        return $this
            ->getQueryBuilder()
            ->leftJoin($this->getAlias() . ".config", "config")
            ->andWhere($this->getAlias() . ".name=:name")
            ->setParameter("name", $name);
    }

    public function getParentByItemId(int $id): ?MenuInterface
    {
        $sql = "SELECT parent_id FROM `".$this->getClassMetadata()->getTableName()."` WHERE `id`=:id";
        $parentId = $this->getEntityManager()->getConnection()->fetchOne($sql, ["id" => $id]);
        return $this->find($parentId);
    }

    public function getParentsByItemQueryBuilder(MenuInterface $menu): QueryBuilder
    {
        $alias = $this->getAlias();

        return $this
            ->getQueryBuilder()
            ->andWhere("{$alias}.lft<:lft")->setParameter('lft', $menu->getLft())
            ->andWhere("{$alias}.rgt>:rgt")->setParameter('rgt', $menu->getRgt())
            ->andWhere("{$alias}.tree=:tree")->setParameter('tree', $menu->getTree());
    }


}
