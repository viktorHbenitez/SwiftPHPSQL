<?php

  /*

    DAO data access object : En la practica es la libreria que se encarga de crear conexion con la base de datos y el CRUD
    (metodo de consulta y actualizacion de la informacion contenida en una base datos)

     var $dbuser = nombre del usuario principal bd;
     var $dbpass = password;
     var $conn = para establecer los cambios en la conexion;
     var $dbname = nombre de la base de datos;
     var $result = Resultado de las peticiones;
   */

  class MySQLDAO
  {
    var $dbhost = null;
    var $dbuser = null;
    var $dbpass = null;
    var $conn = null;
    var $dbname = null;
    var $result = null;

    // Constructor
    function __construct($dbhost, $dbuser, $dbpass, $dbname)
    {
      # code...
      $this->dbhost = $dbhost;
      $this->dbuser = $dbuser;
      $this->dbpass = $dbpass;
      $this->dbname = $dbname;

    }

    // Conexion con la base de datos SwiftAppRegister.sql
    public function openConnection(){
      $this->conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname); //Creacion de un objeto mysqli parametros obtenidos por las variables estatidas  de Conn.php
      if (mysqli_connect_errno())
        throw new Exception("Could not establish connection with database");

        //echo "Conexion establecida MySQLDAO";
        $this->conn->set_charset("utf8"); /*Asegurarnos que los caracteres esten en formato utf8*/

    }

    // Cerrar la conexion con la Base de datos SwiftAppRegister.sql
    public function closeConnection(){
      if ($this->conn != null) {  //Si la conexion esta establecida
        $this->conn->close();
      }
    }


    public function getUserDetails($email)
    {
      $returnValue = array();
      $sql = "SELECT * FROM user WHERE email = '" . $email . "'";

      $result = $this->conn->query($sql);
      if($result != null && (mysqli_num_rows($result) >= 1 )){
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if(!empty($row)){
          $returnValue = $row;
        }

        return $returnValue;
      }


    }

  }
 ?>
