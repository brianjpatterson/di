<?php

class DwarpDB {

	public function __construct(array $parameters = array()) {
		$this->parameters = $parameters;
	}

	public function setCfg(array $cfg = array()) {
		return $this->cfg = $cfg;
	}

	public function connect() {

		$dsn = "mysql:host=".$this->cfg['dbihost'].";".
				"dbname=".$this->cfg['dbiname'].";".
				"charset=utf8mb4";

		$options = [
					PDO::ATTR_ERRMODE => 
						PDO::ERRMODE_EXCEPTION,
  					PDO::ATTR_DEFAULT_FETCH_MODE =>
  					 PDO::FETCH_ASSOC
  				   ];

  		try {

  			$this->dbi = new PDO($dsn, 
  				$this->cfg['dbiuser'], 
  				$this->cfg['dbipass'], 
  				$options);

		} catch (Exception $e) {

  			error_log($e->getMessage());
  			return false;
		}

		return true;
	}
	
	private function hSelect($t, $c, $s, $e) {

		$q = "SELECT ".$s." FROM ".$t." WHERE ";
		$q .= $c . " " . $e;

		return $q;
	}

	private function hInsert($t, $d) {

		$q = "INSERT INTO ".$t." (";
		$qq = ") VALUES(";

		end($d); $lkey = key($d); reset($d);
		foreach($d as $k => $v) {
			$q .= $k;
			$q .= $k === $lkey ? '' : ", ";
			$qq .= '?';
			$qq .= $k === $lkey ? '' : ", ";
		}

		$q .= $qq . ")";

		return $q;
	}

	public function hUpdate($t, $c, $d) {

		$q = "gfy";
		return $q;


	}

	public function select($t, $c = null, $s = null, $e = null, $v = null) {
		
		$c = empty($c) ? '1=1' : $c;
		$s = empty($s) ? '*' : $s;
		$e = empty($e) ? '' : $e;
		$v = empty($v) ? array(null) : $v;

		$q = $this->hSelect($t, $c, $s, $e);
//$this->dbi->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
		//$this->dbi->lastInsertId();

		$this->stmt = $this->dbi->prepare($q);

		if ($this->stmt->execute($v)) {
			//while($row = $this->stmt->fetch()) {
			//	print_r($row);
			//}

			$result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return $result;
		}
									
	}

	public function insert($t, $d) {

		$q = $this->hInsert($t, $d);

		try {
    		
    		$this->dbi->beginTransaction();
    		$this->stmt = $this->dbi->prepare($q);

    		foreach ($d as $v) {
        		$this->stmt->execute([$v]);
    		}
    	
    		$this->dbi->commit();

		} catch (Exception $e) {
    		$this->dbi->rollback();
    		throw $e;
		}
	}

	public function update($t, $c = null, $d = null) {

		if (empty($d) || !is_array($d)) { return false; }
		$c = empty($c) ? '1=1' : $c;

		$q = $this->hUpdate($t, $c, $d);

		try {
    		
    		$this->dbi->beginTransaction();
    		$this->stmt = $this->dbi->prepare($q);

    		foreach ($d as $v) {
        		$this->stmt->execute([$v]);
    		}
    	
    		$this->dbi->commit();

		} catch (Exception $e) {
    		$this->dbi->rollback();
    		throw $e;
		}

	}	

}