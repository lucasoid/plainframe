# plainframe
plainframe is a simple PHP web app framework. It can serve web pages or web services data. The repo includes a starter app to demonstrate key features.

## install
<ol>
<li>Download or clone into your directory.</li>
<li>Run `composer update` to update dependencies.</li>
</ol>

## data layer
<p>The framework supports connecting to MySQL, SQLite, and SQL Server via PDO. The demo app uses SQLite.</p>
<p>Define global database settings in Config.php. Override them in the classes that extend \Data\Mapper or when instantiating a database object.</p>
<p>Extend the Mapper class for access to dynamically created SELECT, UPDATE, INSERT, and DELETE operations. The Mapper child classes map object changes to database operations. See \Data\MapperBook and \Domain\Book for examples.</p> 

## domain layer
<p>The framework provides an interface for working with domain objects.</p>
<p>Invoke an object using the findById() method in the associated mapper class.</p>
<p>Create or update an object by using the observe() method on the updated properties and then calling the mapper's save() method. I.e., $obj->observe('description', 'lorem ipsum'); $mapper->save($obj);</p>
<p>Retrieve a collection by instantiating a new collection. I.e., $collection = new \plainframe\Domain\Collection('MyObject', $filters, $sortlevels, $range);</p>
<p>Output a collection to JSON (e.g. for web services) using the $collection->toJson()  method.</p>

## REST API
<p>The framework includes an example REST API that has methods associated with GET, PUT, POST, and DELETE calls. See \Controllers\ControllerApi.</p>
