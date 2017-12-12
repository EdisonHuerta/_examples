<?php

namespace skf;
/*
$m = new MIMEMail(); 
// Provide the message body 
$m->add(MIMEMAIL_TEXT, 'An example email message.'); 
// Attach file 'icons/txt.gif', and call it 'text-icon.gif' in the email 
$m->add(MIMEMAIL_ATTACH, 'text-icon.gif', '/var/www/icons/txt.gif'); 
// Send to the author 
$m->send('noreply@oopsilon.com', '"Imran Nazar" <tf@oopsilon.com>', 'Test message');
*/
// define('MIMEMAIL_HTML', 1);
// define('MIMEMAIL_ATTACH', 2);
// define('MIMEMAIL_TEXT', 3);

class xmailer 
{
	const MIMEMAIL_HTML	= 1;
	const MIMEMAIL_ATTACH	= 2;
	const MIMEMAIL_TEXT	= 3;
	const MIMEMAIL_EMBEDDED	= 4;

	/**
	*
	* Set up the environment
	*
	* @access	public
	*
	*/
	public function __construct()
	{
		$this->output = '';
		$this->headers = '';
		$this->boundary = md5(microtime());
		$this->plaintext = 0;
	}

	/**
	*
	* @settor
	*
	* @access	public
	* @param	string	$name
	* @param	string	$value
	* @return	void
	*
	*/
	public function __set( $name, $value )
	{
		switch( $name )
		{
			case 'from_email':
			case 'from_name':
			case 'to_email':
			case 'to_name':
			case 'subject':
			case 'output':
			case 'plaintext':
			case 'headers':
			case 'boundary':
			$this->$name = $value;
			break;

			default: throw new \Exception( "Unable to set $name" );
		}
	}

	/**
	*
	* @gettor
	*
	* @access	public
	* @param	string	$name
	* @return	void
	*
	*/
	public function __get( $name )
	{
		switch( $name )
		{
			case 'from_email':
			case 'from_name':
			case 'to_email':
			case 'to_name':
			case 'subject':
			case 'output':
			case 'plaintext':
			case 'headers':
			case 'boundary':
			return $this->$name;
			break;

			default: throw new \Exception( "Unable to get $name" );
		}
	}


	/**
	*
	* Send the email
	*
	* @access	public
	* @return	bool
	*
	*/
	public function send()
	{
		$this->endMessage();
		$to = '"'.$this->to_name.'" <'.$this->to_email.'>';
		if( mail( $to, $this->subject, $this->output, $this->headers ) !== true )
		{
			throw new \Exception( 'Mail not accepted for delivery' );
		}
	}

	/**
	*
	* Add headers
	*
	* @access	public
	*
	* @param	string	$name
	* @param	string	$value
	* @return	void
	*
	*/
	public function addHeader( $name, $value )
	{
		$this->headers .= "{$name}: {$value}\r\n";
	}

	/**
	* 
	* write partial header
	*
	* @access	public
	* @param	string	$type
	* @param	string	$name
	* @param	string	$mime
	* @return	void
	*
	*/
	public function writePartHeader( $type, $name, $mime='application/octet-stream' )
	{
		$this->output .= "--{$this->boundary}\r\n";
		switch($type)
		{
			case self::MIMEMAIL_HTML:
			$this->output .= "Content-type: {$name}; charset=\"iso8859-1\"\r\n";
			break;

			case self::MIMEMAIL_ATTACH:
			$this->output .= "Content-disposition: attachment; filename=\"{$name}\"\r\n";
			$this->output .= "Content-type: {$mime}; name=\"{$name}\"\r\n";
			$this->output .= "Content-transfer-encoding: base64\r\n";
			break;
		}

		$this->output .= "\r\n";
	}

	/**
	*
	* End of message
	*
	* @access	public
	* @param	string	$from
	* @return	void
	*
	*/
	public function endMessage()
	{
		if(!$this->plaintext)
		{
			$this->output .= "--{$this->boundary}--\r\n";

			$this->headers .= "MIME-Version: 1.0\r\n";
			$this->headers .= "Content-type: multipart/mixed; boundary={$this->boundary}\r\n";
			$this->headers .= "Content-length: ".strlen($this->output)."\r\n";
		}

		// $this->headers .= "From: {".$this->from."}\r\n";
		$this->headers .= "From: ".$this->from_name." <".$this->from_email.">\r\n";
		$this->headers .= "X-Mailer: Two9A's MIME-Mail 0.03, 20070419\r\n\r\n";
	}


	function getContents()	{ return $this->headers . $this->output; }
	function getBody()	{ return $this->output; }
	function getHeaders()	{ return $this->headers; }
	function getBoundary()	{ return $this->boundary; }

	function setBody($b) { $this->output = $b; }

	/**
	*
	* Add function
	*
	* @access	public
	* @param	string	$type
	* @param	string	$name
	* @param	string	$value
	* @param	string	$mime	 optional $mime, as it does not know the mime type of a string
	* @return	void
	*
	*/	
	function add( $type, $name, $value='', $mime='' )
	{
		switch($type)
		{
			case self::MIMEMAIL_TEXT:
			$this->plaintext = (strlen($this->output))?0:1;
			$this->output = "{$name}\r\n" . $this->output;
			break;

			case self::MIMEMAIL_HTML:
			$this->plaintext = 0;
			$this->writePartHeader($type, "text/html");
			$this->output .= "{$name}\r\n";
			break;

			case self::MIMEMAIL_ATTACH:
			$this->plaintext = 0;
			if( is_file( $value ) )
			{
				$mime = trim( exec( 'file -bi '.escapeshellarg( $value ) ) );
				if($mime) $this->writePartHeader( $type, $name, $mime );
				else $this->writePartHeader( $type, $name );
				$b64 = base64_encode( file_get_contents( $value ) );
			}
			else
			{
				$this->writePartHeader( $type, $name, $mime );
				$b64 = base64_encode( $value );
			}

			$i=0;
			while($i < strlen( $b64 ) )
			{
				$this->output .= substr( $b64, $i, 64 );
				$this->output .= "\r\n";
				$i+=64;
			}
			break;

			case self::MIMEMAIL_EMBEDDED:
			break;
		}
	}

	/**
	*
	* Add inline image
	*
	* @access	public
	* @param	string	inline_image
	* @param	string	$sep	The seperator
	* @return	string
	*
	*/
	public function addInlineImage( $inline_image, $sep )
	{
		$content="\r\n--PHP-related-{$sep}\r\n
		Content-Type: image/jpeg\r\n
		Content-Transfer-Encoding: base64\r\n
		Content-ID: <PHP-CID-{$sep}>\r\n
		{$inline_image}\r\n";
		return $content."\r\n";
	}

} // end of class

/*
try
{
	$path = '/home/kevin/Desktop/kev_banner.jpg';
	$sep = sha1( $path );
	$inline_image = chunk_split( base64_encode( file_get_contents( $path ) ) );

	$html = '<html><body><h1>An example email message.</h1><img src="cid:PHP-CID-{'.$sep.'}" /><h2>More example stuff</h2></body></html>';
	

	$m = new xmailer;
	$html .= $m->addInlineImage( $inline_image, $sep );
	$m->add($m::MIMEMAIL_HTML, $html);
	$m->from_email = 'noreply@example.com.au';
	$m->from_name = 'Senders Name';

	// $m->addHeader( 'cc', 'somebody_else@example.com' );

	$m->to_name = 'Kevin Waterson';
	$m->to_email = 'kevin.waterson@gmail.com';

	$m->subject = 'Test Email';
	$m->send();

	echo "Done \n";
}
catch( Exception $e )
{
	echo $e->getMessage()."\n";
}
*/
?>
