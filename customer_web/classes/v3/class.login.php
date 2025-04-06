<?php

	class Login extends Helper{
		public function __construct($respType = null){
			parent::__construct($respType);
		}

		public function login(){
			
			$data = $_POST;
            $email = isset($data['email']) && !empty($data['email']) ? $data['email'] : NULL;
            $password = isset($data['password']) && !empty($data['password']) ? $data['password'] : NULL;

            if(empty($email)){
                $this->api_status = 0;
                $this->api_message = 'l_email_required';
            }else if(empty($password)){
                $this->api_status = 0;
                $this->api_message = 'l_password_required';
            }else{

                
                $user = $this->getTableData(['id','client_id','firstname','lastname','email','phonenumber','profile_image','address','role'],'user',['email' => $email, 'password' => $password],1);

                if(empty($user)){
                    $this->api_status = 0;
                    $this->api_message = 'l_email_password_invalid';
                }else{
                    $this->api_status = 1;
                    $this->api_data = $user;
                }
            }
		}
	}