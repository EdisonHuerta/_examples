<?php

/**
 *
 * @Class domain
 *
 * @Purpose: Governs access to single of multiple configurations
 *
 * @Author: Kevin Waterson
 *
 * @copyright PHPRO.ORG (2013)
 *
 * @see config.class.php
 *
 */

namespace skf;

class domain_config
{
	/*
	 * @var array $values; 
	 */
	public $values = array();

	/**
	*
	* Get the config options from the db
	*
	* @access	public
	* @return	array
	*
	*/
	public function __construct()
	{
		$host = strtolower( $_SERVER['HTTP_HOST'] );
		// fetch config data from the db
		$db = new db;
		$sql = "SELECT * FROM skf_domain_config JOIN skf_domains 
			ON
				skf_domain_config.domain_id=skf_domains.domain_id
			WHERE
				domain_name=:host";
		$stmt=$db->conn->prepare( $sql );
		$stmt->bindParam(':host', $host);
		$stmt->execute();
		$this->values = $stmt->fetchAll( \PDO::FETCH_ASSOC, \PDO::PARAM_STR );
	}

} // end of domain class

?>
