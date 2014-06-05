<?php

Class EDCriptator{
	private $temporalKey;//PERSONAL SEED
	private $seed;//ENCRIPTATION SEED
	private $conn;//CONN RESOURCE
	public $password;
	
	private $config = array();
	
	public function __construct(){
		$this->configurate();
		$this->connect();
	}
	public function configurate(){
		//CONFIGURATION INFO
		$this->config["generalKey"] = array( 0=> 'ClaveGeneral123/()', 1 => 'newClave123()',2 => 'otraClave123()'); //ENCRYPTATION/DECRYPTATION CODE
		$this->config["dbu"] = "root"; //MYSQL USER
		$this->config["dbp"] = ""; //MYSQL PASSWORD
		$this->config["dbo"] = "EDCriptator"; //MYSQL DATABASE
		$this->config["dbh"] = "localhost"; //MYSQL HOST
		$this->config["t"] = "EDCriptator"; //MYSQL TABLE USER
		$this->config["tu"] = "user"; //MYSQL TABLE USER COLUMN
		$this->config["tp"] = "pass"; //MYSQL TABLE PASSWORD COLUMN	
		$this->config["tk"] = "temporalkey"; //MYSQL TABLE TEMPORAL KEY COLUMN		
		$this->config["tig"] = "idgeneralpassword"; //MYSQL TABLE ID GENEREAL PASSWORD KEY COLUMN				
	}
	public function connect(){
		$this->conn = mysql_connect($this->config["dbh"],$this->config["dbu"],$this->config["dbp"]) or die('CONNECTION ERROR');
		mysql_select_db($this->config["dbo"],$this->conn) or die('CONNECTION ERROR');
	}
	public function __destruct(){
		$this->resetConfiguration();
	}
	public function decryptPassword($user){
		$sql = "SELECT ".$this->config["tk"].", ".$this->config["tp"]." , ".$this->config["tig"]." 
							FROM ".$this->config["t"]." 
						WHERE ".$this->config["tu"]." = '".$user."' 
					LIMIT 1";
		$rs = mysql_query($sql,$this->conn);
		if(mysql_num_rows($rs)>0){
			while($r=mysql_fetch_array($rs)){
				$this->password = $this->decryptThat($r[$this->config["tp"]],$r[$this->config["tk"]],$r[$this->config["tig"]]);
			}
		}
		return $this->password;
	}
	public function decryptThat($password,$temporalKey,$tig){
		$this->seed = $this->encrypt($temporalKey,$this->config["generalKey"][$tig]);
		$password = $this->decrypt($password,$this->seed);
		return $password;
	}
	public function updatePassword($user){
		$this->resetConfiguration();
		$pass = $this->decryptPassword($user);
		$this->setPassword($user,$pass);
	}
	public function generatePassword($pass,$tk=false,$usePrev=false){
		if(!$tk) $this->temporalKey = date('Ymdhis');
		$gk = $usePrev ? $this->getPrevGeneralKey() : $this->config["generalKey"][(count($this->config["generalKey"])-1)];
		$this->seed = $this->encrypt($this->temporalKey,$gk);
		$this->password = $this->encrypt($pass,$this->seed);
		return $this->password;
	}
	private function encrypt($data, $key){
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$key,$data,MCRYPT_MODE_CBC,"\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"));
	}
	private function decrypt($data, $key){
		$decode = base64_decode($data);
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key,$decode,MCRYPT_MODE_CBC,"\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}
	public function setPassword($user,$password){
		$this->resetConfiguration();
		$password = $this->generatePassword($password,false,false);
		$sql = "UPDATE ".$this->config["t"]." 
						SET ".$this->config["tp"]."='".trim($password)."',"
							.$this->config["tk"]."='".$this->temporalKey."' ,"
							.$this->config["tig"]."='".(count($this->config["generalKey"])-1)."' 
					WHERE ".$this->config["tu"]." = '".$user."'";
		mysql_query($sql,$this->conn) or die("SET PASSWORD ERROR");
	}
	public function resetConfiguration(){
		unset($this->config);
		unset($this->temporalKey);
		unset($this->seed);
		unset($this->password);
		
		$this->configurate();
	}
	public function testPassword($user,$password){	
		$this->resetConfiguration();
		$result = false;
		$sql = "SELECT ".$this->config["tk"].", ".$this->config["tp"]." , ".$this->config["tig"]." 
							FROM ".$this->config["t"]." 
						WHERE ".$this->config["tu"]." = '".$user."' 
					LIMIT 1";
		$rs = mysql_query($sql,$this->conn);
		if(mysql_num_rows($rs)>0){
			while($r=mysql_fetch_array($rs)){
				$this->temporalKey=$r[$this->config["tk"]];
				$tp=$r[$this->config["tp"]];
				$tig=$r[$this->config["tig"]];
			}
			$realPass = $this->decryptThat($tp,$this->temporalKey,$tig);

			if((count($this->config["generalKey"])-1)>$tig) $this->updatePassword($user);//RENEW KEY
			if(trim($password)==trim($realPass)) $result=true;			
		}
		return $result;
	}
	public function regenerateAllPasswords(){
		$sql = "SELECT ".$this->config["tu"]." FROM ".$this->config["t"];
		$rs = mysql_query($sql,$this->conn);
		if(mysql_num_rows($rs)>0){
			while($r=mysql_fetch_array($rs)){
				$this->updatePassword($r[$this->config["tu"]]);
			}
		}
	}
}
?>