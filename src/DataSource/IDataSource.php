<?php

namespace Carrooi\NoGrid\DataSource;

use Carrooi\NoGrid\NoGrid;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IDataSource
{


	/**
	 * @param \Carrooi\NoGrid\NoGrid $grid
	 */
	public function configure(NoGrid $grid);


	/**
	 * @return int
	 */
	public function getCount();


	/**
	 * @return mixed
	 */
	public function &getData();


	/**
	 * @return array
	 */
	public function fetchData();


	/**
	 * @param \Carrooi\NoGrid\Condition[] $conditions
	 */
	public function filter(array $conditions);


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit);

}
