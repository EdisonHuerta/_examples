<?php

namespace skf;

/**
 * DatabaseFactory
 * Creates an DataBaseBindings object for use from the application layer
 * @depends    PostgreSQL, MySQL
 */

class db extends \PDO
{
	/**
	 * factory
	 *
	 * Sets an DataBaseBinding compatible object based on the type of database
	 * defined in $config_values['database']['db_type']
	 */
	private $conn;

	public $db_type;

	public function  __construct( $db_type, $db_host, $db_name, $db_user, $db_pass, $db_port )
	{
		// set this for use elsewhere
		$this->db_type = $db_type;

		switch( $db_type )
		{
			case 'pgsql':
			case 'postgresql':
			case 'mysql':
				$db_type = strtolower( $db_type );
				parent::__construct( "$db_type:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass );
				$this->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			break;

			case 'sqlite':
				try{
				$path = APP_PATH . $config->config_values['database']['db_path'].'/'.$config->config_values['database']['db_name'].'.sq3';
				$this->conn = new \PDO( "sqlite:$path" );
				$this->conn-> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				}
				catch( \PDOException $e )
				{
					echo $path;
				}
			break;

			default:
			throw new \Exception('Database type not supported');
		}
	}
}
?>
