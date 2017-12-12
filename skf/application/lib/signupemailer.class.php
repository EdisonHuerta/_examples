<?php

namespace skf;

class signupEmailer extends mailer implements \SplObserver{

	public $signup_message;

        public function update( \SplSubject $SplSubject )
        {
                $status = $SplSubject->getStatus();
                switch ( $status[0] ) {

                        case signup::USERNAME_TAKEN:
                                $this->signup_message =  __CLASS__ . ": Username taken!.\n";
                                break;

                        case signup::USERNAME_TOO_LONG:
                                $this->signup_message =  __CLASS__ . ": Username too long.\n";
                                break;

                        case signup::USERNAME_TOO_SHORT:
                                $this->signup_message =  __CLASS__ . ": Username is too short.\n";
                                break;

                        case signup::ALLOW:
                                $this->signup_message =   __CLASS__ . ": Username is good, Emailing admin success\n";
                                break;

                        default: throw new Exception( "Invalid status\n" );
                }
        }
} // end of mailer class
