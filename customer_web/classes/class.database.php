<?php
    class Database{
        public $host    = DB_HOST;
        public $user    = DB_USERNAME;
        public $pass    = DB_PASSWORD;
        public $dbname  = DB_NAME;
        public $dbh;
        public $error;
        public $stmt;

        public function __construct() {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT                => true,
                PDO::ATTR_ERRMODE                   => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES          => true,
                PDO::MYSQL_ATTR_INIT_COMMAND        => "SET NAMES utf8mb4;",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => true
            );
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            } catch(PDOException $e) {
                echo $this->error = $e->getMessage();
            }
        }

        public function query($flg = '') {
            $this->stmt = $this->dbh->prepare($this->sql);

            if(is_array($this->sqlBind)){
                foreach($this->sqlBind AS $key=>$bind) {
                    $this->bind(':' . $key, $bind);
                }
            }else{
                $binds = array_map('trim', explode(',', $this->sqlBind));

                foreach($binds AS $bind) {
                    $this->bind(':' . $bind, $_POST[$bind]);
                }
            }

    		if($flg == 'debug')
    		    $this->debugQuery();
        }

        public function bind($param, $value = null, $type = null) {
    		if (is_null($type)) {
    			switch (true) {
    				case is_int($value):
    					$type = PDO::PARAM_INT;
    					break;
    				case is_bool($value):
    					$type = PDO::PARAM_BOOL;
    					break;
    				case is_null($value):
    					$type = PDO::PARAM_NULL;
    					break;
    				default:
    					$type = PDO::PARAM_STR;
    			}
    		}
    		$this->stmt->bindValue($param, $value, $type);
        }

        public function execute() {
            return $this->stmt->execute();
        }

        public function resultset() {
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function single() {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function rowCount() {
            return $this->stmt->rowCount();
        }

        public function lastInsertId() {
            return $this->dbh->lastInsertId();
        }

        public function beginTransaction() {
            return $this->dbh->beginTransaction();
        }

        public function endTransaction() {
            return $this->dbh->commit();
        }

        public function cancelTransaction() {
            return $this->dbh->rollBack();
        }

        public function debugDumpParams() {
            //return $this->stmt->debugDumpParams();
            echo '<pre>Debug Start>>><br />';
            print_r($this->stmt->debugDumpParams());
            echo '</pre><br />Debug End<<<<hr />';
        }

    	public function debugQuery($param = 'exit') {
    		$query = $this->sql;

            if(is_array($this->sqlBind)){
                foreach($this->sqlBind AS $key=>$bind) {
                    $query = str_replace(':' . $key, '"'.$bind.'"', $query);
                }
            }else{
                $binds = array_map('trim', explode(',', $this->sqlBind));

                foreach($binds AS $bind) {
                    $query = str_replace(':' . $bind, '"'.$_POST["$bind"].'"', $query);
                }
            }

    		echo $query;
            
    		if($param == 'exit')
    			exit;
    	}

        public static function debugQuery_old($query, $params = null) {
            if($params !== null) {
                foreach ($params as $key => $value) {
                    $query = str_replace($key, '"'.$value.'"', $query);
                }
            }
            echo $query;
        }
    }
