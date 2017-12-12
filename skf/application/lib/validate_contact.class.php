<?php

namespace skf;

class validate_contact extends \skf\validation{

        public function loadRules()
        {
                $this->addValidator( array( 'name'=>'name', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>40 ) );
                $this->addValidator( array( 'name'=>'surname', 'type'=>'string', 'required'=>false, 'min'=>0, 'max'=>0 ) );
                $this->addValidator( array( 'name'=>'subject', 'type'=>'string', 'required'=>false, 'min'=>1, 'max'=>125 ) );
                $this->addValidator( array( 'name'=>'email', 'type'=>'email', 'required'=>false, 'min'=>1, 'max'=>125 ) );
                $this->addValidator( array( 'name'=>'text', 'type'=>'string', 'required'=>false, 'min'=>10, 'max'=>1500 ) );
        }

} // end of class

