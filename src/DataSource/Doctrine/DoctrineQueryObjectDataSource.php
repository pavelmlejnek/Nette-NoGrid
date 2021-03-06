<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\DataSource\IDataSource;
use Carrooi\NoGrid\NoGrid;
use Carrooi\NoGrid\NotImplementedException;
use Doctrine\ORM\AbstractQuery as DoctrineAbstractQuery;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineQueryObjectDataSource implements IDataSource
{


	/** @var \Kdyby\Persistence\Queryable */
	private $repository;

	/** @var \Kdyby\Doctrine\QueryObject */
	private $query;

	/** @var \Kdyby\Doctrine\ResultSet */
	private $resultSet;

	/** @var int */
	private $hydrationMode = DoctrineAbstractQuery::HYDRATE_OBJECT;


	/**
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @param \Kdyby\Doctrine\QueryObject $query
	 */
	public function __construct(Queryable $repository, QueryObject $query)
	{
		$this->repository = $repository;
		$this->query = $query;
	}


	/**
	 * @param \Carrooi\NoGrid\NoGrid $grid
	 */
	public function configure(NoGrid $grid)
	{
	}


	/**
	 * @return int
	 */
	public function getHydrationMode()
	{
		return $this->hydrationMode;
	}


	/**
	 * @param int $hydrationMode
	 * @return $this
	 */
	public function setHydrationMode($hydrationMode)
	{
		$this->hydrationMode = $hydrationMode;
		return $this;
	}


	/**
	 * @return \Kdyby\Doctrine\QueryObject
	 */
	public function getQueryObject()
	{
		return $this->query;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->getResultSet()->getTotalCount();
	}


	/**
	 * @return \Kdyby\Doctrine\QueryObject
	 */
	public function &getData()
	{
		return $this->query;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		return $this->getResultSet()->toArray();
	}


	/**
	 * @param \Carrooi\NoGrid\Condition[] $conditions
	 * @throws \Carrooi\NoGrid\NotImplementedException
	 */
	public function filter(array $conditions)
	{
		throw new NotImplementedException('Filters are not yet supported for DoctrineQueryObjects.');
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->getResultSet()->applyPaging($offset, $limit);
	}


	/**
	 * @return \Kdyby\Doctrine\ResultSet
	 */
	private function getResultSet()
	{
		if (!$this->resultSet) {
			$this->resultSet = $this->query->fetch($this->repository, $this->hydrationMode);
		}

		return $this->resultSet;
	}

}
