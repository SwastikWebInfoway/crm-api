<?php

	class Login extends Helper{
		public function __construct($respType = null){
			parent::__construct($respType);
		}

		public function login(){

            if (!$this->validateMethod('POST')) return;
			
			$data = $_POST;
            $email = isset($data['email']) && !empty($data['email']) ? $data['email'] : NULL;
            $password = isset($data['password']) && !empty($data['password']) ? $data['password'] : NULL;

            if(empty($email)){
                http_response_code(400);
                $this->api_status = 0;
                $this->api_message = 'l_email_required';
            }else if(empty($password)){
                http_response_code(400);
                $this->api_status = 0;
                $this->api_message = 'l_password_required';
            }else{

                require_once(ROOT_CLASS."/".VERSION."/class.jwt.php");
                $Jwt = new JwtHandler();
                
                $user = $this->getTableData(['id','client_id','firstname','lastname','email','phonenumber','profile_image','address','role','password'],'user',['email' => $email],1);

                if(empty($user) || !password_verify($password, $user['password'])){
                    http_response_code(400);
                    $this->api_status = 0;
                    $this->api_message = 'l_email_password_invalid';
                }else{
                    http_response_code(200);
                    unset($user['password']);
                    $user['profile_image'] = IMAGE_URL.'/user/'.$user['id'].'/'.$user['profile_image'];

                    $token = $Jwt->createToken(['user_id' => $user['id'], 'client_id' => $user['client_id'], 'role' => $user['role']]);
                    $this->api_status = 1;
                    $this->api_data['details'] = $user;
                    $this->api_data['token'] = $token;
                }
            }
		}
	}