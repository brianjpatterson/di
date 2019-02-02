<?php

	class Sanders {

	        private static $_cfg = array();
	        
	        //public function __construct() { }
	        //public function __destruct() { }
	        
	        public function get($item) {
	                return self::$_cfg[$item];
	        }
	        public function set($item, $value) {
	               self::$_cfg[$item] = $value;
	        }
	        
	        public function load($m) {
	          require($m);      
	        }
	}

	class Xml extends Sanders {

        protected $_xml;

        function __construct($file = '_cfg.xml', $path = './') {
               
                $this->set('defaultXmlFilename', $file);
                $this->set('configPath', $path);
                $this->set('xmlData', $this->xmlParse($this->getXml()));
        }

        //function __destruct() {}

        public function getXml($file = null) {
                $file = $file === null ? $this->get('defaultXmlFilename') : $file;
                $thisfile = $this->get('configPath').$file;
                if (file_exists($thisfile)) { 
                        $result = simplexml_load_file($thisfile);
                        return $result;
                } 
        }

        public function xmlParse ( $xmlObject, $out = array () ) {
                foreach ( (array) $xmlObject as $index => $node )
                        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;
                        return $out;
        }

	}

	class Dwarp {

		static protected $box = array();

		public function __construct(array $parameters = array()) {
			$this->parameters = $parameters;
			
		}

		public function getCfg() {
			return $this->parameters['dwarp.cfg'];
		}

		public function getDIC() {
			if (isset(self::$box['self'])) {
				return self::$box['self'];
			}

			$class = $this->parameters['dwarp.class'];

			$dwarp = new $class();
			$dwarp->setCfg($this->getCfg());
			$dwarp->connect();

			return self::$box['self'] = $dwarp;
		}
	}


	$kernel = new Sanders();
	$xml = new Xml();

	$kernel->load('./dWarpDB.php');

	$dwarp = new Dwarp(array(
						'dwarp.class'=>'DwarpDB',
						'dwarp.cfg'=> $xml->get('xmlData')
					  ));

	$dbi = $dwarp->getDIC();

