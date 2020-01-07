<?php
namespace Xarenisoft\ORM\Adapters;

use Xarenisoft\ORM\Model;

class PostgresAdapter {
    /**
	 * @see QueryWriter::getTables
	 */
	public function getTables()
	{
		return $this->adapter->getCol( 'SELECT table_name FROM information_schema.tables WHERE table_schema = ANY( current_schemas( FALSE ) )' );
    }
    
    /**
	 * @see QueryWriter::createTable
	 */
	public function createTable(Model $table )
	{
		$table = $this->esc( $table );

		$this->adapter->exec( " CREATE TABLE {$table->getTableName()} ({$table->getPrimaryIdName()} SERIAL PRIMARY KEY,); " );
	}

}