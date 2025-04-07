<?php
class Helper extends Database
{
	public $api_status;
	public $api_message;
	public $api_data;
	public $api_extra;
	public $responseError;
	public $responseReturn;
	public $responseType;
	public $sql;
	public $sqlBind;
	public $requestData;

	// General & Default Methods/Functions - START

	public function __construct($respType = "PHP_ARRAY")
	{
		parent::__construct();
		// Response
		$this->responseType		= $respType;
		$this->api_status 	= 0;
		$this->api_message 	= "";
		$this->api_data 	= array();
		$this->api_extra	= array();
		$this->responseError	= '';
		$this->responseReturn	= array();
		$this->sqlBind 	= array();
		$this->requestData = $GLOBALS['app_request'];
	}

	public function response()
	{
		$this->responseReturn = array(
			"status" 	=> $this->api_status,
			"message" 	=> $this->api_message,
			"data" 		=> $this->api_data,
			"extra" 	=> $this->api_extra
		);

		return $this->responseType == "JSON_ARRAY" ? json_encode($this->responseReturn) : $this->responseReturn;
	}

	public function getTableData($columnArray = array(),$tableName = '',$condition = array(),$singleRow = 0,$inCondition = array()){
        if(!empty($tableName) && (!empty($condition) || !empty($inCondition))){

            $fields = !empty($columnArray) ? implode(',',$columnArray) : ' * ';

            $whereCondition = '';

            foreach($condition as $key => $value){
                $whereCondition .= !empty($whereCondition) ? ' AND ' : '';
                $whereCondition .= $key.' = "'.$value.'"';
            }

            foreach($inCondition as $key => $value){
                $whereCondition .= !empty($whereCondition) ? ' AND ' : '';
                $whereCondition .= $key.' IN ('.$value.')';
            }

            $this->sql = 'SELECT '.$fields.' FROM '.$tableName.' WHERE '.$whereCondition;
            $this->query();
            return $singleRow == 1 ? $this->single() : $this->resultset();
        }
    }

	public function addTabledata($insertDetails = array(),$tableName = ''){

		if(!empty($tableName) && !empty($insertDetails)){

			$field = '';
			$bindParam = array();

			foreach($insertDetails as $key => $fieldDetails){
				$field .= !empty($field) ? ',' : '';
				$field .= ' `'.$key.'` = :'.$key;
				$bindParam[$key] = $fieldDetails;
			}

			$this->sql = 'INSERT INTO  `'.$tableName.'` SET '.$field;
			$this->sqlBind = $bindParam;
			$this->query();
			$this->execute();
			return $this->lastInsertId();
		}
	}

	public function updateTable($tableName = '',$conditionArray = array(), $updatDetails = array()){

		if(!empty($tableName) && !empty($conditionArray) && !empty($updatDetails)){

			$where = '';
			$field = '';
			$bindParam = array();

			foreach($updatDetails as $key => $fieldDetails){
				$field .= !empty($field) ? ',' : '';
				$field .= ' '.$key.' = :'.$key;
				$bindParam[$key] = $fieldDetails;
			}

			foreach ($conditionArray as $key => $value) {
				$where .= !empty($where) ? ' AND ' : '';
				$where .= ' `'.$key.'` = '.$value;
			}

			$this->sql = 'UPDATE `'.$tableName.'` SET '.$field.' WHERE '.$where;
			$this->sqlBind = $bindParam;
			$this->query();
			$this->execute();
		}
	}

	public function validateRequest(){

		require_once(ROOT_CLASS."/".VERSION."/class.jwt.php");		
		$headers = $this->getAuthorizationHeader();
		$jwt = new JwtHandler();

		if (isset($headers)) {
			$token = str_replace('Bearer ', '', $headers);
			$decoded = $jwt->verifyToken($token);

			if ($decoded) {
				$data = $jwt->getValueFromToken($token);
				if(!empty($data)){
					$data = json_decode(json_encode($data), true);
					$GLOBALS['app_request'] = $data;
					return $data;
				}else{
					$this->api_status = 0;
					$this->api_message = 'Invalid token';
					return false;
				}
			} else {
				$this->api_status = 0;
				$this->api_message = 'Invalid token';
				return false;
			}
		} else {
			$this->api_status = 0;
			$this->api_message = 'Authorization header not found';
			return false;
		}
	}

	public function getAuthorizationHeader() {

		$headers = null;
		
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER['Authorization']);
		} 
		elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
			$headers = trim($_SERVER['HTTP_AUTHORIZATION']);
		} 
		elseif (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			$requestHeaders = array_combine(
				array_map('ucwords', array_keys($requestHeaders)),
				array_values($requestHeaders)
			);
			
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		
		return $headers;
	}

	public function validateMethod($method = ''){
		
		if($_SERVER['REQUEST_METHOD'] != $method){
			$this->api_status = 0;
			$this->api_message = 'Invalid request method';
			return false;
		}else{
			return true;
		}
		
	}

	public function uploadFile($file, $uploadDir = '../uploads/') {

		$uploadDir = '../uploads/' . $uploadDir.'/';
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0755, true);
		}
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return false;
		}
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
		$uniqueName = md5(uniqid(rand(), true)) . '.' . $extension;
		$destination = $uploadDir . $uniqueName;
		if (move_uploaded_file($file['tmp_name'], $destination)) {
			return $uniqueName;
		} 
	}
}