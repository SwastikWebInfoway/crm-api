<?php

	class General extends Helper{
		public function __construct($respType = null){
			parent::__construct($respType);
		}

		public function last_version(){
			
			$this->sql = 'SELECT NOW() as `current_time`';
			$this->query();
			$data = $this->single();

			$this->api_data = $this->requestData;
		}
	}