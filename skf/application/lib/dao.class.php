<?php

namespace skf;

/**
 *
 * Data Access Object.
 * This class provides a full suite of tools to create and utilize
 * Data Access Objects in PHP.
 * @author Kevin Waterson <kevin@phpro.org>
 * @copyright Copyright (c) 2010 Kevin Waterson
 * @version $Id:
 *
 */
class dao {

    /**
     * @var schema_name
     * @access	public
     */
    public $schema_name = null;
    public $db, $db_type, $table_name, $table_names, $key;

    /**
     * @constructor
     *
     * @param	object		$db
     * @param	string		$table_name
     *
     */
    public function __construct(\PDO $db = null, $table_name = null) {
        // logger::debugLog( "Loading DAO class", 200, __METHOD__, __LINE__ );
        if ($db instanceof \PDO) {
            // logger::debugLog( "Using db connection passed to __construct()", 200, __METHOD__, __LINE__ );
            $this->db = $db;
        } else {
            logger::debugLog("db connection not instance of PDO\n", 200, __METHOD__, __LINE__);
        }
        if (is_string($table_name)) {
            $this->table_name = $table_name;
        }
    }

    /**
     *
     * populate the dao object
     * @access	public
     *
     */
    public function load($id = null) {
        if ($id != null) {
            $id = $id;
        } elseif (isset($this->id)) {
            $id = $this->id;
        }
        $table_name = str_replace('skf\dao_', '', get_class($this));
        $sql = "SELECT * FROM $table_name WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res[0] as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     *
     * Settor
     *
     * @access	public
     *
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Gettor
     * @access	public
     * @return	mixed
     *
     */
    public function __get($name) {
        return $this->$name;
    }

    /**
     * Create the class object definition
     * @return string
     */
    public function createObject() {
        $column_names = $this->getColumnNames();

        $definition = "<?php\n\n";
        $definition .= "namespace skf;\n\n";
        $definition .= "class dao_$this->table_name extends \skf\dao{\n\n";
        // $definition .= 'public $table_name = \''.$this->table_name."';\n" ;
        $definition .= "\t" . 'public $primary_key = \'' . $this->__primary_key . "';\n";
        foreach ($column_names as $col) {
            $definition .= "\t" . 'public $' . $col . ";\n";
        }
        $definition .= "\n\t" . 'public function __construct( $db ){' . "\n";
        $definition .="\t\t" . 'parent::__construct( $db );' . "\n";

        // $definition .= '// load up other necessary dao stuff'."\n";
        // $definition .= "\t\t".'$this->load();'."\n";
        $definition .= "\t}\n";
        /*
          $definition .= '/**'."\n";
          $definition .= "*\n";
          $definition .= "* Load up any other required DAO stuff, Over rides parent load()\n";
          $definition .= "*\n";
          $definition .= '**\/'."\n\n";
          $definition .= 'public function load(){}'."\n\n";
         */

        // there may be other custom functions in existing class definitions
        // and these need to be preserved
        // make sure we have a path to write to
        // $this->object_path = isset( $this->object_path ) ? $this->object_path : getcwd() . '/objects';
        // $filename = $this->object_path.'/dao.';

        $this->object_path = APP_PATH . '/lib/objects/';

        $filename = APP_PATH . '/lib/objects/';

        $filename .= is_null($this->schema_name) ? '' : "$this->schema_name.";
        $filename .= 'dao_' . $this->table_name . '.class.php';

        // write the object(s)
        // if the file exists get the bottom containing the custom functions
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
            // if the file does exist, we need to save everything below the text block
            $pos = strpos($contents, $this->textBlock());
            $length = strlen($contents);
            $bottom = substr($contents, $pos, $length);
            // now join the new definition and the bottom containing the custom functions
            $definition .= $bottom;
        } else {
            // add text block only if this is a new file
            $definition .= $this->textBlock();

            // add the the closing endo of class curly brace if this is a new file
            $definition .= $this->endOfClass();
        }

        // attempt to write the file
        if (!@file_put_contents($filename, $definition)) {
            throw new \Exception("Unable to write to $filename\n");
        }
    }

    /**
     * Generate the Data Access Ojbects
     * @access	public     
     */
    public function generateDAO() {
        logger::debugLog("Generating DAO", 200, __METHOD__, __LINE__);
        $class_name = get_class($this);
        $table_names = $this->getTableNames();

        logger::debugLog("Tables Names" . print_r($table_names, 1), 200, __METHOD__, __LINE__);

        foreach ($table_names as $value) {
            $this->table_name = $value;
            $this->createObject();
        }
    }

    /**
     *
     * This block is need several times, so it gets its own function
     *
     * @access	private
     * @return`	string
     *
     */
    private function textBlock() {
        $definition = "\n\n\t\t#################################################\n";
        $definition .= "\t\t### CUSTOM FUNCTIONS MUST GO BELOW THIS BLOCK ###\n";
        $definition .= "\t\t#################################################\n\n";
        return $definition;
    }

    /**
     *
     * Insert a new record into table
     * @access	public
     * @return	INT     The last insert ID
     *
     */
    public function insert() {
        $__fields = get_defined_vars($this);

        /* cast to array */
        $fields = (array) $__fields['this'];
        unset($fields['schema_name']);
        unset($fields['db']);
        unset($fields['table_name']);
        unset($fields['table_names']);
        unset($fields['key']);
        unset($fields['db_type']);
        // $new = array_pop( $fields );

        $table_name = str_replace('skf\dao_', '', get_class($this));
        $sql = "INSERT INTO $table_name ";

        $columns = '';
        $params = '';

        $obj = new \CachingIterator(new \ArrayIterator($fields));

        foreach ($obj as $column => $value) {
            if ($column != 'primary_key') {
                if ($column != $this->primary_key) {
                    // add the parameter binding hooks
                    $columns .= $column;
                    $params .= ":$column";
                    if ($obj->hasNext()) {
                        $columns .= ',';
                        $params .= ',';
                    }
                }
            }
        }

        // create the query with the columns and parameters
        $sql = $sql . "( $columns ) VALUES ( $params )";

        // prepare
        $stmt = $this->db->prepare($sql);

        // back tot he begining of the stack
        $obj->rewind();

        // bind each value to the params
        foreach ($obj as $column => $value) {
            if ($column != 'primary_key') {
                if ($column != $this->primary_key) {
                    $stmt->bindValue($column, $value);
                }
            }
        }
        // run the query
        $stmt->execute();

        switch ($this->db->db_type) {
            case 'mysql':
                // return the ID of the insert
                return $this->db->lastInsertID();
                break;

            case 'pgsql':
                $sequence = $table_name . '_id_seq';
                return $this->db->lastInsertID($sequence);
                break;

            default: throw new \Exception("Invalid DB Type");
        }
    }

    /**
     *
     * Update a record
     *
     */
    public function update() {
        $table = str_replace('skf\dao_', '', get_class($this));

        $__fields = get_defined_vars($this);
        $fields = (array) $__fields['this'];
        unset($fields['schema_name']);
        unset($fields['db']);
        unset($fields['table_name']);
        unset($fields['table_names']);

        $new = array_pop($fields);
        $sql = "UPDATE $table SET\n";
        $obj = new \CachingIterator(new \ArrayIterator($fields));

        foreach ($obj as $column => $value) {
            if ($column != $this->primary_key && $value != '' && $column != 'primary_key') {
                $sql .= $column . "='$value'";
                if ($obj->hasNext()) {
                    $sql .= ",\n";
                }
            }
        }

        // hack to remove the trailing comma
        // $sql = substr( $sql, 0, -2 );
        $sql .= "\n";
        $pk = (string) $this->primary_key;
        $pk = $this->$pk;
        $sql .= " WHERE $this->primary_key = $pk ";

        logger::debugLog("$sql", 200, __METHOD__, __LINE__);
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }

    /**
     *
     * Delete a record
     *
     */
    public function delete() {
        $table = str_replace('skf\dao_', '', get_class($this));

        $__fields = get_defined_vars($this);
        $fields = (array) $__fields['this'];
        unset($fields['schema_name']);
        unset($fields['db']);
        unset($fields['table_name']);

        $sql = "DELETE FROM $table WHERE $this->primary_key = $this->id";
        $stmt = $this->db->prepare($sql);
        // need to bind params here
        $stmt->execute();
    }

    /**
     *
     * Fetch a single record
     *
     * @access	public
     *
     */
    public function fetch() {
        $class = get_class($this);
        $table = str_replace('skf\dao_', '', $class);
        $pk = (string) $this->primary_key;
        $pk = $this->$pk;
        $sql = "SELECT * FROM $table WHERE $this->primary_key = $pk";
        $stmt = $this->db->query($sql);
        return $stmt->fetchALL(\PDO::FETCH_CLASS, $class, array($this->db));
    }

    /**
     * 
     * Fetch all ALL records from table
     *
     * @access	public
     * @return	array an array of class objects
     *
     */
    public function fetchAll() {
        // the table name is the class name
        $class = get_class($this);
        // strip off the namespace and hte dao_ prefix from the table name..
        $table = str_replace('skf\dao_', '', $class);
        $sql = "SELECT * FROM $table";

        logger::debugLog("$sql", 200, __METHOD__, __LINE__);
        $stmt = $this->db->query($sql);
        // fetches an array of objects of type $class, constructor must be fed the db
        return $stmt->fetchALL(\PDO::FETCH_CLASS, $class, array($this->db));
    }

    public function getTableNames() {
        switch ($this->db->db_type) {
            case 'mysql':
                logger::debugLog("Getting Table Names", 200, __METHOD__, __LINE__);
                // mysql has no idea of schemas
                $sql = "SHOW TABLES";
                break;

            case 'pgsql':
                // currently supports only public schema FIxme
                $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
                break;

            default: throw new Exception("Invalid database type");
        }

        $result = $this->db->query($sql);
        $arr = array();
        while ($row = $result->fetch(\PDO::FETCH_NUM)) {
            $arr[] = $row[0];
        }
        return $arr;
    }

    /**
     *
     * Get the column names from a mysql table
     *
     * @access	public
     * @return       array
     *
     */
    public function getColumnNames() {
        switch ($this->db->db_type) {
            case 'mysql':
                $sql = "SHOW COLUMNS FROM $this->table_name";
                $primary_key_col = $this->getMySQLPrimaryKey($this->table_name);
                break;

            case 'pgsql':
                $sql = "SELECT column_name 
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = '$this->table_name'";
                $primary_key_col = $this->getPgPrimaryKey($this->table_name);
                break;

            default: throw new Exception("Invalid DB Type");
        }
        // query the database
        $result = $this->db->query($sql);
        // Get the number of columns
        // $Cols = $result->columnCount();
        // Loop through the results
        $arr = array();

        $this->__primary_key = $primary_key_col;
        while ($row = $result->fetch(\PDO::FETCH_BOTH)) {
            $arr[] = $row[0];
        }

        return $arr;
    }

    /**
     * 
     * get primary key of MySQL table
     *
     * @access	public
     * @return	string
     *
     */
    public function getMySQLPrimaryKey($table_name) {
        $sql = "SELECT column_name 
			FROM information_schema.key_column_usage 
			WHERE  table_schema = schema() 
			AND constraint_name = 'PRIMARY'
			AND table_name = '$table_name'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        return $res;
    }

    /**
     * 
     * get primary key of pgsql table
     *
     * @access	public
     * @return	string
     *
     */
    public function getPgPrimaryKey($table_name) {
        $sql = "SELECT c.column_name
			FROM information_schema.table_constraints tc 
			JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name) 
			JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema 
			AND tc.table_name = c.table_name AND ccu.column_name = c.column_name
			WHERE constraint_type = 'PRIMARY KEY' and tc.table_name = '$table_name'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        return $res;
    }

    /**
     *
     * End of class Text provides the closing curly brace and comment
     *
     * @access       private
     * @return       string
     *
     */
    private function endOfClass() {
        return "} // end of class\n?>";
    }

}

// end of dao class
