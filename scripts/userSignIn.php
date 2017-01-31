<?php
require("../db/MySQLDAO.php");  // Libreria realizar la conexion con la BD, Consultas y CRUD de la aplicacion

// AGREGAR NIVEL DE SEGURIDAD ARCHIVO DE NUESTRA DB
$config = parse_ini_file('../../swiftRegisterApp.ini');  //Tiene parametros de nuestra DB al igual que Conn.php

$returnValue = array();

// Parametros introducidos en la aplicacion Inicio de Sesion
if(empty($_REQUEST["userEmail"]) || empty($_REQUEST["userPassword"]))
{
  $returnValue["status"] = "400";
  $returnValue["message"] = "Missing required information";

  echo json_encode($returnValue);
  return;
}

// GUARDAMOS LOS VALORES  email  y password para realizar la consulta en la BD
$userEmail = htmlentities($_REQUEST["userEmail"]);
$userPassword = htmlentities($_REQUEST["userPassword"]);


// REALIZAMOS LA CONEXION CON LA BD "user"

// get the attributes from SwiftRegisterApp.ini
$dbhost = trim($config["dbhost"]);
$dbuser = trim($config["dbuser"]);
$dbpass = trim($config["dbpass"]);
$dbname = trim($config["dbname"]);

// INSTANCIA PARA ESTABLECER LA CONEXION CON LOS PARAMETROS DE NUESTRA BASE DE DATOS
$dao = new MySQLDAO($dbhost , $dbuser, $dbpass, $dbname);  // Parametros leidos desde Conn.php, acceder variable estatica Clase::$Variable
$dao->openConnection();  //ESTABLECEMOS CONEXION

$userDetails =$dao->getUserDetails($userEmail);  // Si es encontrado el Email en la base de datos indicada en DAO lo almacena en $userDatails

if(empty($userDetails)){  // Verificamos $userDetails : contenga un registro para poder usuarlo CASO VACIO
  $returnValue["status"] = "403";
  $returnValue["message"] = "User not found data base";

  echo json_encode($returnValue);
  return;
}

// SE OBTUVO UN REGISTRO EN de la BD "user" almacenado en $userDetails
$userSecuredPassword = $userDetails["user_password"];
$userSalt = $userDetails["salt"];  // openssl_random_pseudo_bytes(16): genera un numero aleatorio de 16 digitos


// Verificamos password de la BD con la introducida por el usuario concatenando el salt añadido
// IT NEVER IS TRUE, I DON NOT WHY
if($userSecuredPassword === sha1($userPassword . $userSalt))
{

  // GUARDAMSO EN EL ARRAY LOS ATRIBUTOS DEL USUARIO Y ESTADOS
  $returnValue["status"] = "200";
  $returnValue["userFirstName"] = $userDetails["first_name"];
  $returnValue["userLastName"] = $userDetails["last_name"];
  $returnValue["userEmail"] = $userDetails["email"];
  $returnValue["userId"] = $userDetails["user_id"];

}else{     // CASO NO ENCONTRADO
  $returnValue["status"] = "403";
  $returnValue["message"] = "User not found";

  echo json_encode($returnValue);
  return;
}


$dao->closeConnection(); // CERRAMOS LA CONEXION
echo json_encode($returnValue);  // MOSTRAMOS LOS RESULTADOS OBTENIDOS FORMATO JSON


?>
