<?php

namespace skf;

class asset_loader extends \DirectoryIterator{

	private $asset_string = '';

	/**
	*
	* Constructor, duh, load the parent iterator
	*
	* @access	public
	* @param	string	$directory
	*
	*/
	public function construct( $directory )
	{
		parent::_construct( $directory );
	}

	/**
	*
	* Returns a string of all the files concatenated together
	*
	* @access	public
	* @return	string
	*
	*/
	public function __toString()
	{
		while( parent::valid() )
		{
			// add this check for hidden files (such as generated by vim)
			if ( substr( $this->getFilename(), 0, 1 ) != '.' )
			{
				$this->asset_string .= file_get_contents( $this->getpathName() );
			}
			$this->next();
		}
		return $this->asset_string;
	}
} // end of class