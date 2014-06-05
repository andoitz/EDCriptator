######################################################
######################## INFO ########################
######################################################

	*** Script created by Andoitz Jordán ***
	Please visit http://www.andoitz.com for get more information.

	¿How to use it?
	1. Configure Mysql VARS:
		$this->config["dbu"] = "root"; //MYSQL USER
		$this->config["dbp"] = ""; //MYSQL PASSWORD
		$this->config["dbo"] = "EDCriptator"; //MYSQL DATABASE
		$this->config["dbh"] = "localhost"; //MYSQL HOST
		
	2. Configure User Table used by your users:
		$this->config["t"] = "{tableName}"; //MYSQL TABLE USER
		$this->config["tu"] = "{userColumn}"; //MYSQL TABLE USER COLUMN => [VARCHAR]
		$this->config["tp"] = "{passwordColumn}"; //MYSQL TABLE PASSWORD COLUMN	=> [VARCHAR]
		$this->config["tk"] = "{temporalKeyColumn}"; //MYSQL TABLE TEMPORAL KEY COLUMN => [VARCHAR]	
		$this->config["tig"] = "{idGeneralPasswordColumn}"; //MYSQL TABLE ID GENEREAL PASSWORD KEY COLUMN => [INTEGER]
	
	3. Configure The General Key. IMPORTANT: YOU CAN ADD A NEW GENERAL KEY WHEN YOU NEED REFRESH YOUR SECURITY
		$this->config["generalKey"] = array( 0=> 'ClaveGeneral123/()', 1 => 'newClave123()',2 => 'otraClave123()'); //ENCRYPTATION/DECRYPTATION CODE
	
	4. Create the class instance and use the functions
		$EDCriptator = new EDCriptator(); => Create instance
		$EDCriptator->setPassword('{user}','{password}'); => Set user password
		$EDCriptator->testPassword('{user}','{password}'); => Returns boolean. True = Password is OK
		$EDCriptator->decryptPassword('{user}'); => Returns the decrypted password
		$EDCriptator->updatePassword('{user}'); => Refresh the password allocated in the database with a new temporalKey and the last GeneralKey
		$EDCriptator->regenerateAllPasswords(); => Refresh the password allocated in the database with a new temporalKey and the last GeneralKey (for all users)
	
	
	- EVERY USER HAVE 1 UNIQUE temporalKey
	- ALL USERS MUST HAVE THE SAME GENERAL KEY FOR SECURITY BUT THEY CAN HAVE OTHER GENERAL KEY
		seed = enc(temporalKey,generalKey);
	- SEED IS CORRECT WITH THE ASSIGNED GENERAL KEY
	- GENERAL ID KEY IS RENEW TO THE LAST GENERAL KEY ID WHEN THE USER TRIES TO LOGIN
	- NEW GENERAL KEY MEANS THAT WE WILL CHANGE ALL PASSWORDS (SECURITY SYSTEM)
		savedPassword = enc(realPassword,seed);

################################
########### EXAMPLES ###########
################################

*** Script created by Andoitz Jordán ***
Please visit http://www.andoitz.com for get more information.

	$EDCriptator = new EDCriptator();
	//$password = $EDCriptator->setPassword('andoitz','prueba');
	//if($EDCriptator->testPassword('andoitz','prueba')) echo 'Correct Password';
	//else echo 'Incorrect Password';
	//echo $EDCriptator->decryptPassword('andoitz');
	//$EDCriptator->updatePassword('andoitz');
	//$EDCriptator->regenerateAllPasswords();
