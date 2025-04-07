<?php
class User extends Helper{

    public function __construct($respType = null){
        parent::__construct($respType);
    }

    public function add_user(){

        if (!$this->validateMethod('POST')) return;

        $data = $_POST;
        $email = isset($data['email']) && !empty($data['email']) ? $data['email'] : NULL;
        $password = isset($data['password']) && !empty($data['password']) ? $data['password'] : NULL;
        $firstname = isset($data['firstname']) && !empty($data['firstname']) ? $data['firstname'] : NULL;
        $lastname = isset($data['lastname']) && !empty($data['lastname']) ? $data['lastname'] : NULL;
        $role = isset($data['role']) && !empty($data['role']) ? $data['role'] : NULL;
        $phonenumber = isset($data['phonenumber']) && !empty($data['phonenumber']) ? $data['phonenumber'] : NULL;
        $address = isset($data['address']) && !empty($data['address']) ? $data['address'] : NULL;
        $role = isset($data['role']) && !empty($data['role']) ? (int)$data['role'] : 3;
        $userId = $GLOBALS['app_request']['user_id'] ?? NULL;

        if(empty($email)){
            http_response_code(400);
            $this->api_status = 0;
            $this->api_message = 'l_email_required';
        }else if(empty($password)){
            http_response_code(400);
            $this->api_status = 0;
            $this->api_message = 'l_password_required';
        }else if(empty($firstname)){
            http_response_code(400);
            $this->api_status = 0;
            $this->api_message = 'l_firstname_required';
        }else if(empty($lastname)){
            http_response_code(400);
            $this->api_status = 0;
            $this->api_message = 'l_lastname_required';
        }else{

            // Check if user already exists
            $userExists = $this->getTableData(['id'],'user',['email' => $email],1);
            
            if(!empty($userExists)){
                http_response_code(409);
                $this->api_status = 0;
                $this->api_message = 'l_user_already_exists';
            }else{
                $user['firstname'] = $firstname;
                $user['lastname'] = $lastname; 
                $user['email'] = $email;
                $user['phonenumber'] = $phonenumber;
                $user['role'] = $role;
                $user['password'] = password_hash($password, PASSWORD_DEFAULT);
                $user['client_id'] = $userId;
                $user['profile_image'] = isset($data['profile_image']) && !empty($data['profile_image']) ? $data['profile_image'] : NULL;
                $user['address'] = isset($data['address']) && !empty($data['address']) ? $data['address'] : NULL;
                $id = $this->addTableData($user,'user');

                if($_FILES['profile_image']['name']){
                    $fileName = $this->uploadFile($_FILES['profile_image'], 'user/'.$id);
                    if($fileName){
                        $this->updateTable('user',['id' => $id],['profile_image' => $fileName]);
                        $user['profile_image'] = IMAGE_URL.'/user/'.$id.'/'.$fileName;
                    }
                }

                $user['id'] = $id;
                unset($user['password']);
                http_response_code(201);
                $this->api_status = 1;
                $this->api_message = 'User added successfully!';
                $this->api_data = $user;
            }
        }
        
    }

    public function get_user(){

        if (!$this->validateMethod('GET')) return;

        $data = $_POST;
        $userId = $GLOBALS['app_request']['user_id'] ?? NULL;

        // Check if user exists
        $userExists = $this->getTableData(['id','client_id','firstname','lastname','email','phonenumber','profile_image','address','role'],'user',['client_id' => $userId]);
        foreach ($userExists as  $key => $user) {
            $userExists[$key]['profile_image_url'] = IMAGE_URL.'/user/'.$user['id'].'/'.$user['profile_image'];
        }

        if(empty($userExists)){
            http_response_code(204);
            $this->api_status = 0;
            $this->api_message = 'l_user_not_found';
        }else{
            http_response_code(200);
            $this->api_status = 1;
            $this->api_data = $userExists;
        }
    }
}