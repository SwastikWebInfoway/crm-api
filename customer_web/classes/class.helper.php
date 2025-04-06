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
	public $sqlBind = [];
	public $mysqlTimezone;
	public $mysqlDatePattern 			= "%m/%d/%Y";
	public $mysqlDatePattern_WithTime 	= "%m-%d-%Y %h:%i %p";
	public $mysqlPage_Size 				= 10;
	public $paginationLength			= 10;
	public $validAttachments;
	public $myrHelpers;

	// General & Default Methods/Functions - START

	public function __construct($respType = "PHP_ARRAY")
	{
		parent::__construct();

		// Valid file extensions
		$this->validAttachments 	= array();
		$this->validAttachments[] 	= "txt";
		$this->validAttachments[]	= "doc";
		$this->validAttachments[]	= "docx";
		$this->validAttachments[]	= "xls";
		$this->validAttachments[]	= "xlsx";
		$this->validAttachments[]	= "ppt";
		$this->validAttachments[]	= "pptx";
		$this->validAttachments[]	= "odt";
		$this->validAttachments[]	= "jpg";
		$this->validAttachments[]	= "jpeg";
		$this->validAttachments[]	= "png";
		$this->validAttachments[]	= "pdf";

		// Response
		$this->responseType		= $respType;
		$this->api_status 	= 0;
		$this->api_message 	= "";
		$this->api_data 	= array();
		$this->api_extra	= array();
		$this->responseError	= '';
		$this->responseReturn	= array();
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
}