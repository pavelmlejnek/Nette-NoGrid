[![Build Status](https://img.shields.io/travis/Carrooi/Nette-NoGrid.svg?style=flat-square)](https://travis-ci.org/Carrooi/Nette-NoGrid)
[![Donate](https://img.shields.io/badge/donate-PayPal-brightgreen.svg?style=flat-square)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T9SAY3LZL3YAA)

# NoGrid

Definitely not a grid, just a simple control for printing data in customized templates with paginator.

It's not a good thing to always just show some automatically generated grid with data, mainly in frontend and that 
package is for that moments.

## BC Break!

Be careful, this package was completely rewritten with version 2.0.0. Please read the new readme.

## Features

**It has:**

* Paginator with custom templates option
* Latte macros for simplified templates
* Views (eg. for archived and not archived data)
* Different data sources
* Filtering

**It hasn't:**

* Default grid template
* CSS styles
* JS scripts
* Sorting
* Forms

It may get some of these features in future.

## Installation

```
$ composer require carrooi/no-grid
```

Now you can register Nette's extension

```yaml
extensions:
    grid: Carrooi\NoGrid\DI\NoGridExtension
```

## Configuration

```yaml
grid:
    itemsPerPage: 20
    paginator:
        template: %appDir%/paginator.latte
        templateProvider: App\Grid\TemplateProvider
```

* `itemsPerPage`: default is 10
* `paginator/template`: not required
* `paginator/templateProvider`: class name for template provider, must be an instance of `Carrooi\NoGrid\IPaginatorTemplateProvider` interface. Not required

## Definition

```php

use Carrooi\NoGrid\DataSource\ArrayDataSource;
use Nette\Application\UI\Presenter;

class BooksPresenter extends Presenter
{

	/** @var \Carrooi\NoGrid\INoGridFactory @inject */
	public $gridFactory;
	
	protected function createComponentBooksGrid()
	{
		$dataSource = new ArrayDataSource([			// Read more about data sources below
			['title' => 'Lord of the Rings'],
			['title' => 'Harry Potter'],
			['title' => 'Narnia'],
		]);
	
		$grid = $this->gridFactory->create($dataSource);
		
		return $grid;
	}

}
```

## Transform data loaded from data source

```php
$grid->transformData(function($line) {
	return [
		'id' => $line->getId(),
		'title' => $line->getTitle(),
	];
});
```

## Printing

```smarty
<table n:no-grid="booksGrid">
	<thead>
		<tr>
			<th>Title</th>
		</tr>
	</thead>
	<tbody>
		<tr n:no-grid-data-as="$line">
			<td>{$line[title]}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th>Display {$noGrid->getCount()} items from {$noGrid->getTotalCount()}</th>
		</tr>
		<tr>
			<td n:no-grid-not-empty>{control booksGrid:paginator}</td>
			<td n:no-grid-empty>No data found...</td>
		</tr>
	</tfoot>
</table>
```

### Latte macros

* `no-grid`: Begin grid rendering (similar to [{form}](http://doc.nette.org/cs/2.3/forms#toc-manualni-vykreslovani) macro)
* `no-grid-data-as`: Iterate over data from data source and save current line to given variable
* `no-grid-views-as`: Iterate over views from current NoGrid and save view data to given variable (see more about views below)
* `no-grid-not-empty`: Content will be processed only if there are some data
* `no-grid-empty`: Content will be processed only if there are no data
* `no-grid-has-paginator`: Content will be processed only if paginator should be rendered

Also you can see that paginator can be rendered with `{control booksGrid:paginator}`.

These latte macros can't be used as "non-attribute" macros, so there are also variants written in camelCase:

* `noGrid`
* `noGridDataAs`
* `noGridViewsAs`
* `noGridNotEmpty`
* `noGridEmpty`
* `noGridHasPaginator`

## Views

Imagine that you want to create for example two tabs - "Active books" and "Sold books". This can be easily done with 
views.

**Definition:**

```php
protected function createComponentBooksGrid()
{
	$dataSource = new ArrayDataSource([			// Read more about data sources below
		[
			'title' => 'Lord of the Rings,
			'sold' => true,
		'],
		[
			'title' => 'Harry Potter,
			'sold' => false,
		'],
		[
			'title' => 'Narnia,
			'sold' => false,
		'],
	]);

	$grid = $this->gridFactory($dataSource);
	
	$grid->addView('active', 'Active', function(array &$data) {
		$data = array_filter($data, function($book) {
			return !$book['sold'];
		});
	});
	
	$grid->addView('sold', 'Sold out', function(array &$data) {
		$data = array_filter($data, function($book) {
			return $book['sold'];
		});
	});
	
	return $grid;
}
```

**Display:**

```smarty
<div n:no-grid="booksGrid">
	<ul>
		<li n:no-grid-views-as="$view" n:class="$view->isCurrent() ? active">
			<a href="{$view->getLink()}">
				{$view->getTitle()}
			</a>
		</li>
	</ul>
	<table>
		<!-- same like previous example -->
	</table>
</div>
```

## Filtering

**Supported conditions:**

* `Condition::SAME` (default)
* `Condition::NOT_SAME`
* `Condition::IS_NULL`
* `Condition::IS_NOT_NULL`
* `Condition::LIKE`

```php
$form = new Form;
$form->addText('name');
$form->addSubmit('search', 'Search!');

$grid->setFilteringForm($form);

// setting filters is not required
$grid->addFilter('name', Condition::LIKE, [
	Condition::CASE_INSENSITIVE => true,
], function($name) {

	// update value before sending query to database
	return '%'. $name. '%';
});
```

Now you only have to display form inputs in your template.

**Do not render beginning and end of the array, it is rendered automatically!**

## Data sources

* `Carrooi\NoGrid\DataSource\ArrayDataSource(array)`
* `Carrooi\NoGrid\DataSource\Doctrine\DataSource(Doctrine\ORM\QueryBuilder)`
* `Carrooi\NoGrid\DataSource\Doctrine\QueryObjectDataSource(Kdyby\Persistence\Queryable, Kdyby\Doctrine\QueryObject)`
* `Carrooi\NoGrid\DataSource\Doctrine\QueryFunctionDataSource(Kdyby\Persistence\Queryable, Carrooi\NoGrid\DataSource\DoctrineQueryFunction)`
