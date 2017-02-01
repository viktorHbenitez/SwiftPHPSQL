<?php
//require("../db/Conn.php");
require("../db/MySQLDAO.php");

// AGREGAR NIVEL DE SEGURIDAD ARCHIVO DE NUESTRA DB
$config = parse_ini_file('../../swiftRegisterApp.ini');  //Tiene parametros de nuestra DB al igual que Conn.php

$returnValue = array();  // instancia de un array en php tendra informacion estado de la conexion y mensaje de error

// Si se encuentran vacios algunos de los parametros no guardara ningun dato
if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"]) || empty($_REQUEST["userFirstName"]) || empty($_REQUEST["userLastName"]))
{
  $returnValue["status"] = "400";  // el array tendra una llave "status" = Error
  $returnValue["message"] = "Missing required information";
  echo json_encode($returnValue);  //Convierte el arreglo en formato json
  return;
}

/*
  htmlentities — Convierte todos los caracteres aplicables a entidades HTML
  Esta función es idéntica a htmlspecialchars() en todos los aspectos, excepto que con htmlentities(), todos los caracteres que tienen su equivalente HTML son convertidos a estas entidades.
  Si en su lugar se desea decodificar (lo inverso), se puede utilizar html_entity_decode().

  htmlentities : protege de injeccion de codigo
 */

$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);
$userFirstName = htmlentities($_REQUEST["userFirstName"]);
$userLastName = htmlentities($_REQUEST["userLastName"]);

// Ya podemos introducir la informacion en nuestra base de datos pero primero debemos encriptar el password que vamos a introducir en la base de datos
// Generate secure password
$salt = openssl_random_pseudo_bytes(16);  // openssl_random_pseudo_bytes(16): genera un numero aleatorio de 16 digitos
$secured_password = sha1($userPassword . $salt);


// get the attributes from SwiftRegisterApp.ini
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpass = trim($config["dbpass"]);
$dbname = trim($config["dbname"]);

// INSTANCIA PARA ESTABLECER LA CONEXION CON LOS PARAMETROS DE NUESTRA BASE DE DATOS
$dao = new MySQLDAO($dbhost , $dbuser, $dbpass, $dbname);  // Parametros leidos desde Conn.php, acceder variable estatica Clase::$Variable
$dao->openConnection();  //ESTABLECEMOS CONEXION


// CHECK IF USER WITH PROVIDED USERNAME IS AVAILABLE
$userDetails = $dao->getUserDetails($userEmail);  // Si es encontrado el Email en la base de datos indicada en dao lo almacena en $userDatails
if(!empty($userDetails)){
  $returnValue["status"] = "400";
  $returnValue["message"] = "Please choose different email Address";

  echo json_encode($returnValue);
  return;
}

// REGISTER NEW USER
// En caso de no encontrar el email podemos añadirlo a la BD user y establecer la conexion

$result = $dao->registerUser($userEmail, $userFirstName, $userLastName, $secured_password, $salt);

if($result){
  $userDetails = $dao->getUserDetails($userEmail);
  $returnValue["status"] = "200";
  $returnValue["message"] = "Successfully registered new user";
  $returnValue["userId"] = $userDetails["user_id"];
  $returnValue["userFirstName"] = $userDetails["first_name"];
  $returnValue["userLastName"] = $userDetails["last_name"];
  $returnValue["userEmail"] = $userDetails["email"];
}else{   // ERROR DE REGISTRO
  $returnValue["status"] = "400";
  $returnValue["message"] = "Could not register user with provided information";
}

$dao->closeConnection();

echo json_encode($returnValue);

 ?>
