<?php
/**
* Generic validation class for validation from most sources
* 
* @copyright	2013-03-29
* @link		http://phpro.org
* @author	Kevin Waterson
* @version:	$ID$
*
*/

namespace skf;

class validation
{
	/**
	* @var		$errors	The array of errors
	* @access	public
	*/
	public $errors = array();

	/**
	* @var	$validators	The array of validators
	* @access	public
	*/
	public $validators = array();

	/**
	*
	* @var	$sanitized	The array of sanitized values
	* @access	public
	*/
	public $sanitized = array();
	/**
	*
	* Settor
	*
	* @access	public
	* @param	string	$name
	* @param	mixed	$value
	*
	*/
	public function __set( $name, $value )
	{
		switch( $name )
		{
			case 'source':
			if( !is_array( $value ) )
			{
				throw new \Exception( 'Source must be an array' );
			}
			$this->source = $value;
			break;

			default:
			$this->name = $value;
		}
	}

	/**
	*
	* Getter
	*
	* @access	public
	* @param	string	$name
	* @return	string
	*
	*/
	public function __get( $name )
	{
		return $this->$name;
	}

	/**
	* Add a rule for a validator
	*
	* @access	public
	* @param	array	$validator [name, type, required, [min], [max] ]
	* @return	object	Instance of self to allow chaining
	*
	*/
	public function addValidator( $validator )
	{
		/*** set the validator name if it does not exist ***/
		if( !isset( $this->validators[$validator['name']] ) )
		{
			$this->validators[$validator['name']] = array();
		}
		$val = array();
		foreach( $validator as $key=>$value )
		{
			$val[$key] = $value;
		}
		$this->validators[$validator['name']][] = $val;

		return $this;
	}


	/**
	*
	* Run the validations
	*
	*/
	public function run()
	{
		// loop over the validators
		foreach( $this->validators as $key=>$val )
		{
			// each validator may contain multiple rules
			foreach( $val as $key=>$options )
			{
				// check if the field is required
				$this->checkRequired( $options );

				// run the validation
				switch( $options['type'] )
				{
					case 'string':
					$this->validateString( $options );
					break;

					case 'length':
					$this->validateStringLength( $options );
					break;

					case 'numeric':
					$this->validateNumeric( $options );
					break;

					case 'regex':
					$this->validateRegex( $options );
					break;

					case 'float':
					$this->validateFloat( $options );
					break;

					case 'date':
					$this->validateDate( $options );
					break;

					case 'url':
					$this->validateUrl( $options );
					break;

					case 'email':
					$this->validateEmail( $options );
					break;

					case 'injection':
					$this->validateEmailInjection( $options );
					break;

					case 'ipv4':
					$this->validateIpv4( $options );
					break;

					case 'ipv6':
					$this->validateIpv6( $options );
					break;

					case 'callback':
					$this->validateCallback( $options );
					break;

					case 'compare':
					$this->validateCompare( $options );
					break;

					case 'checkbox':
					$this->validateCheckbox( $options );
					break;

					case 'multiple':
					$this->validateMultiple( $options );
					break;

					case 'file':
					$this->validateFile( $options );
					break;

					case 'radio':
					$this->validateRadio( $options );
					break;

					case 'select':
					$this->validateSelect( $options );
					break;

					default:
					throw new \Exception( "Invalid Type( $options[type] )" );
				}
			}
		}
	}

	/**
	* Check if a field is required
	*
	* @access	private
	* @param	array	bool
	*
	*/
	private function checkRequired( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is a required field';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( isset( $options['required'] ) && $options['required'] === true )
		{
			if( !isset( $this->source[$options['name']] ) || is_null( $this->source[$options['name']] ) || $this->source[$options['name']] == '' )
			{
				$this->errors[$options['name']] = $message;
			}
		}
		else
		{
			// echo '<h1>'.$options['name'] . ' is not required </h1>';
		}
	}

	

	/**
	*
	* Validate a string
	*
	* @access	private
	* @param	array	$options
	* @return	bool
	*
	*/
	private function validateString( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$min = 0;

		if( strlen( $options['required'] ) == false && strlen( $this->source[$options['name']] ) == 0 )
		{
			$min = 0;
		}

		if( strlen( $options['required'] ) == false && strlen( $this->source[$options['name']] ) > 0 )
		{
			$min = $options['min'];
		}

		$message = $this->parseCamelCase( $options['name'] ) . ' length is Invalid';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		$name = $options['name'];
		if( isset( $this->source[$name] ) && $options['required'] == true && !is_string( $this->source[$name] ) )
		{
			$this->errors[$name] = $message;
			$this->sanitized[$name] = '';
		}
		elseif( isset( $options['name'] ) && strlen( $options['name'] ) > $options['max'] || strlen( $options['name'] ) < $min )
		{
			if( $options['min'] == $options['max'] )
			{
				$message = $this->parseCamelCase( $options['name'] ). ' must be exactly ' . $options['min'] . ' characters.';
				$message = isset( $options['message'] ) ? $options['message'] : $message;
			}
			else
			{
				$message = $this->parseCamelCase( $options['name'] ). ' must be between ' . $options['min'] . ' and ' . $options['max'] . ' characters.';
				$message = isset( $options['message'] ) ? $options['message'] : $message;
			}
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
				$this->sanitized[$name] = filter_var( $this->source[$name], FILTER_SANITIZE_STRING);
		}
	}

	/**
	*
	* Check the length of a string
	*
	* @access	private
	* @param	$options	The array of options
	* @return	bool
	* 
	*/
	private function validateStringLength( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		if( $options['min'] == $options['max'] )
		{
			$message = $this->parseCamelCase( $options['name'] ). ' must be exactly ' . $options['min'] . ' characters.';
		}
		else
		{
			$message = $this->parseCamelCase( $options['name'] ). ' must be between ' . $options['min'] . ' and ' . $values['max'] . ' characters long';
		}
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( strlen( $this->source[$options['name']] ) > $options['max'] || strlen( $this->source[$options['name']] ) < $options['min'] )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* Validate by Regular Expression
	*
	* @access	private
	* @param	array	$options
	* 
	*/
	public function validateRegex( $options )
	{
		// apply trim if set
		$this->vTrim( $options );
		
		$default_message = $this->parseCamelCase( $options['name'] ) . ' does not match the required pattern';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;

		if( !preg_match( "'".$options['pattern']."'", $this->source[$options['name']] ) )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			 $this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* Validate a number is numeric
	*
	* @access	private
	* @param	array	$options
	*/
	private function validateNumeric( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$default_message = $this->parseCamelCase( $options['name'] ) . ' must be a number';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_INT ) === false )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		elseif( $this->source[$options['name']] < $options['min'] )
		{
			$this->errors[$options['name']] = "Invalid Number(".$this->source[$options['name']]."): Below Min allowed value";
		}
		elseif( $this->source[$options['name']] > $options['max'] )
		{
			$this->errors[$options['name']] = "Invalid Number(".$this->source[$options['name']]."): Over Max allowed value";
		}
		else
		{
			$this->sanitized[$options['name']] = filter_var( $this->source[$options['name']], FILTER_VALIDATE_INT );
		}
	}

	/**
	*
	* Validate an email address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateEmail( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$default_message = $this->parseCamelCase( $options['name'] ) . ' is not a valid email address';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;

		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_EMAIL ) === FALSE )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = filter_var( $this->source[$options['name']], FILTER_SANITIZE_EMAIL);
		}
	}

	/**
	* Check an email for email injection characters
	*
	* @access	private
	* @param	$options
	*
	*/
	private function validateEmailInjection( $options )
	{
		$default_message = $this->parseCamelCase( $options['name'] ) . ' contains injection characters';
		$message = isset( $options['message'] ) ? $options['message'] : $default_message;
		if ( preg_match( '((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , $this->source[$options['name']] ) )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = filter_var( $this->source[$options['name']], FILTER_SANITIZE_EMAIL);
		}
	}

	/**
	*
	* Validate a value is a floating point number
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateFloat( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid floating point number';
		$message = isset( $options['message'] ) ? $options['message'] : $message;
		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_FLOAT ) === false )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			// $this->source[$options['name']] = 123.45;
			$this->sanitized[$options['name']] = filter_var( $this->source[$options['name']], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		}
	}

	/**
	*
	* Parse CamelCase or camel_case to Camel Case
	*
	* @access	private
	* @param	string	$string
	* @return	string
	*
	*/
	private function parseCamelCase( $string )
	{
		$cc = preg_replace('/(?<=[a-z])(?=[A-Z])/',' ',$string);
		$cc = ucwords( str_replace( '_', ' ', $cc ) );
		return $cc;
	}

	/**
	*
	* Validate a URL
	*
	* @access       private
	* @param	array	$options
	*
	*/
	private function validateUrl( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid URL';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if(filter_var( $this->source[$options['name']], FILTER_VALIDATE_URL) === FALSE )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = filter_var( $this->source[$options['name']],  FILTER_SANITIZE_URL);
		}
	}

	/**
	* Validate a date
	*
	* @access	private
	* @param	array	options
	*
	*/
	private function validateDate( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid date';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		switch( $options['format'] )
		{
			case 'YYYY/MM/DD':
			case 'YYYY-MM-DD':
			list( $y, $m, $d ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'YYYY/DD/MM':
			case 'YYYY-DD-MM':
			list( $y, $d, $m ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'DD-MM-YYYY':
			case 'DD/MM/YYYY':
			list( $d, $m, $y ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'MM-DD-YYYY':
			case 'MM/DD/YYYY':
			list( $m, $d, $y ) = preg_split( '/[-\.\/ ]/', $this->source[$options['name']] );
			break;

			case 'YYYYMMDD':
			$y = substr( $this->source[$options['name']], 0, 4 );
			$m = substr( $this->source[$options['name']], 4, 2 );
			$d = substr( $this->source[$options['name']], 6, 2 );
			break;

			case 'YYYYDDMM':
			$y = substr( $this->source[$options['name']], 0, 4 );
			$d = substr( $this->source[$options['name']], 4, 2 );
			$m = substr( $this->source[$options['name']], 6, 2 );
			break;

			default:
			throw new \Exception( "Invalid Date Format" );
		}
		if( checkdate( $m, $d, $y ) == false )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* Validate an ipv4 IP address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateIpv4( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid ipv4 address';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === FALSE)
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* Validate an ipv6 IP address
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function validateIpv6( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is not a valid ipv6 address';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( filter_var( $this->source[$options['name']], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* Custom or external validator
	*
	* @access	private
	* @param	array	$options
	*
	*/
	public function validateCallback( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$message =  $this->parseCamelCase( $options['name'] ) . ' is invalid';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( isset( $options['class'] ) )
		{
			$class = $options['class'];
			$func = $options['function'];
			$obj = new $class;
			// the callback function MUST return bool
			if( $obj->$func( $this->source[$options['name']] ) == true )
			{
				$this->errors[$options['name']] = $message;
			}
		}
		else
		{
			$func = $options['function'];
			if( $func( $this->source[$options['name']] ) == true )
			{
				$this->errors[$options['name']] = $message;
			}
		}
	}


	/**
	*
	* Compare two values
	*
	* @access	public
	* @param	array	$options
	* @return	bool
	*
	*/
	public function validateCompare( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ).' and '.$this->parseCamelCase( $options['compare_to'] ) . ' do not match';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		if( $this->source[$options['name']] !== $this->source[$options['compare_to']] )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
			$this->sanitized[$options['compare_to']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/*
	* Validate that a checkbox is checked
	*
	* @access	public
	* @param	array	$options
	* @return	bool
	*
	*/
	public function validateCheckbox( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ).' is a mandatory field';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		// if( isset( $this->source[$options['name']] ) && $this->source[$options['required']] === true && $this->source[$options['name']] !== 'on')
		if( isset( $this->source[$options['name']] ) && $options['required'] === true && $this->source[$options['name']] !== 'on')
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = 'checked';
		}
	}


	/**
	*
	* Validate a field with multiple values
	*
	* @access	public
	* @param	array	$options
	* @return	bool
	*
	*/
	public function validateMultiple( $options )
	{
		switch( $options['val_type'] )
		{
			case 'numeric':
			foreach( $this->source[$options['name']] as $opt )
			{
				if( !is_numeric( $opt ) )
				{
					$message =  $this->parseCamelCase( $options['name'] ).' values must be numeric';
					$this->errors->$options['name'] = $message;
					break;
				}
				else
				{
				}
			}
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}


	/**
	*
	* Validate if at least one of many fields is set
	*
	* @access	public
	* @param	array	$options
	* @return	bool
	*
	*/
	public function oneOfMany( $options )
	{
		// array of fields
		foreach( $options['fields'] as $field )
		{
			if( isset( $this->source[$field] ) && $this->source[$field] != '' )
			{
				return true;
			}
		}
		// is any of the fields present in source?
	}


	/**
	*
	* Validate a file upload
	*
	* @access       public
	* @param	array   $options
	* @return       bool
	*
	*/
	public function validateFile( $options )
	{
		// apply trim if set
		$this->vTrim( $options );

		$name = $options['name'];
		$max_size = $options['max_size'];
		$allowed_types = $options['allowed_types'];

		$ini_max = str_replace('M', '', ini_get('upload_max_filesize'));
		$upload_max = $ini_max * 1024;

		$tmp = $_FILES[$name]["tmp_name"];
		$filename = $_FILES[$name]["name"];
		$file_extension = pathinfo( $filename, PATHINFO_EXTENSION );

		if( !in_array( $file_extension, $allowed_types ) )
		{
			$this->errors[$options['name']] = "File '$file_extension' type not supported";
		}
		// check a file has been uploaded
		elseif(!is_uploaded_file( $_FILES[$name]['tmp_name'] ) )
		{
			$this->errors[$options['name']] = 'No file uploaed';
		}
		elseif( $_FILES['userfile']['size'] > $max_size )
		{
			// if the file sizse is greater than the max size
			$this->errors[$options['name']] = 'File size exceeds upload limit';
		}
		elseif( $_FILES['userfile']['size'] > $upload_max )
		{
			$this->errors[$options['name']] = 'File exceeds system upload max size';
		}

		$this->sanitized[$options['name']] = $this->source[$options['name']];
	}

	/*
	*
	* Check if this validation has errors
	*
	* @access	public
	* @return	bool
	*
	*/
	public function isValid()
	{
		return sizeof( $this->errors ) == 0;
	}
	

	/**
	*
	* If a trim option is set, remove trailing and leading whitespace
	*
	* @access	private
	* @param	array	$options
	* @return	array	The options array 
	*
	*/
	private function vTrim( $options )
	{
		// if the trim option is set, strip leading and trailing whitespace
		if( isset( $options['trim'] ) && $options['trim'] == true )
		{
			$this->source[$options['name']] = trim( $this->source[$options['name']] );
		}
		return $options;
	}

	/**
	*
	* Define the error message
	*
	* @access	private
	* @param	array	$options
	*
	*/
	private function errorMessage( $options )
	{

	}

	/**
	*
	* Validate radio buttons
	* example Usage
	* $this->addValidator( array( 'name'=>'payment_method', 'type'=>'radio', 'required'=>true, 'data_type'=>'string', 'values'=>array( 'cc', 'paypal'), 'message'=>'Payment method is not a supported type'  ) );
	*
	* @access	private
	* @param	array	$options
	*
	*/
	public function validateRadio( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is an invalid selection';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		$values = $options['values'];

		if( sizeof( $values ) == 0 )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		elseif( !isset( $this->source[$options['name']] ) || !in_array( $this->source[$options['name']], $values ) )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}


	/**
	*
	* Validate radio buttons
	* example Usage
	* $this->addValidator( array( 'name'=>'payment_method', 'type'=>'select', 'required'=>true, 'data_type'=>'string', 'values'=>array( 'cc', 'paypal'), 'message'=>'Payment method is not a supported type'  ) );
	*
	* @access	private
	* @param	array	$options
	*
	*/
	public function validateSelect( $options )
	{
		$message =  $this->parseCamelCase( $options['name'] ) . ' is an invalid selection';
		$message = isset( $options['message'] ) ? $options['message'] : $message;

		$values = $options['values'];

		if( sizeof( $values ) == 0 )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		elseif( !in_array( $this->source[$options['name']], $values ) )
		{
			$this->errors[$options['name']] = $message;
			$this->sanitized[$options['name']] = '';
		}
		else
		{
			$this->sanitized[$options['name']] = $this->source[$options['name']];
		}
	}

	/**
	*
	* create an html list form the errors array
	* @access public
	* @return	string
	*
	*/
	public function makeErrorList()
	{
		$list = "<ul>\n";
		foreach( $this->errors as $v )
		{
			$list .= "<li>$v</li>\n";
		}
		$list .= '</ul>';
		return $list;
	}
} // end of validation class

/**
 Example usage

$array = array( 'one'=>'     dingo', 'two'=>'    wombat', 'three'=>'stever irwin', 'four'=>'platypus', 'five'=>'koala' );
$val = new validation;
$val->source = $array;
$val->addValidator( array( 'name'=>'one', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>100, 'trim'=>true ) );
$val->addValidator( array( 'name'=>'two', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>100 ) );
$val->addValidator( array( 'name'=>'three', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>100 ) );
$val->addValidator( array( 'name'=>'four', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>100 ) );
$val->addValidator( array( 'name'=>'five', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>100 ) );
$val->run();
if( $val->isValid() )
{
	foreach( $val->sanitized as $good )
	{
		echo "$good\n";
	}
}
else
{
	foreach( $val->errors as $err )
	{
		echo "$err\n";
	}
}
*/
?>
