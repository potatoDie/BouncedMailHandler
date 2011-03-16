<?php
	/*
	 * BouncedMailHandler
	 * potatoDie 2011
	 */
 
	define ('MAILBOX', "{imap.byte.nl:143}" );
	define ('USERNAME', '');
	define ('PASSWORD', '');
	
	class BouncedMailHandler
	{
		var $mbox;
		
		function __construct()
		{
			$this->mbox = imap_open( MAILBOX, USERNAME, PASSWORD ); 
		}
		
		function __destruct()
		{
			imap_close ( $this->mbox );
		}
		
		// Returns an array of failed recipients
		public function getRecipients ()
		{
			$recipients = array();
			$n = imap_num_msg ( $this->mbox );
			for ( $i = 1; $i <= $n; $i++ )	// Yes: starts with 1, not 0
			{
				$recipients[$i] = $this->getRecipient ( $i );
			}	
			
			return $recipients;
		}
		
		private function getRecipient ( $i )
		{
			$body = $this->getBody ( $i );
			$address = $this->parseRecipientAddress ( $body ); 
			return $address;
		} 
		
		private function parseRecipientAddress ( $s )
		{
			$validEmailExpr =  "[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*" .
	                     "@[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*(\.[a-z]{2,4})";
			$pattern = "/To: {$validEmailExpr}/i"; 
			preg_match( $pattern, $s, $matches );
			
			$match = ( isset ( $matches[0] )) ? substr ( $matches[0], 4 ) : "?";
			return $match;
		}
		
		public function getBody ( $i )
		{
			return imap_body( $this->mbox, $i );
		}
		
		public function delete ( $i )
		{
			imap_delete( $this->mbox, $i);
			imap_expunge( $this->mbox );
		}
	}
?>