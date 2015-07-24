<?php

namespace Carrooi\NoGrid\DataSource;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IDataSource
{


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
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit);

}