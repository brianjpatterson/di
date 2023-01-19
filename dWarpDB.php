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
	
	
}
