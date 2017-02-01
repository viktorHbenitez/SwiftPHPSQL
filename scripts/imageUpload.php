<?php

  // ESCRIBIR Y GUARDAR LA IMAGEN DEL PERFIL DEL USUARIO
  $user_id = $_POST["user_id"];
  $target_dir = "/Applications/XAMPP/xamppfiles/htdocs/swiftAppRegister/profile-pictures/" . $user_id; // direccion para almacenar la imagen de perfil del usuario

  if(!file_exists($target_dir))  //Crea el path en caso de no existir
  {
    mkdir($target_dir, 0777, true);
  }

  $target_dir = $target_dir . "/" . basename($_FILES["file"]["name"]);  // Crea otra carpeta usando nombre del archivo

/*
 * move_uploaded_file — Mueve un archivo subido a una nueva ubicación
 * bool move_uploaded_file ( string $filename , string $destination )
 *
 * $_FILES['fichero_usuario']['tmp_name']
 * El nombre temporal del fichero en el cual se almacena el fichero subido en el servidor.
*/

  if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir))
  {
    echo json_encode([
      "Message" => "The file". basename($_FILES["file"]["name"]) . "has been upload.",
      "Status" => "OK",
      "user_id" => $user_id
    ]);

  }else{
      echo json_encode([
         "Message" => "Sorry there was an error uploading your file. ",
          "Status" => "Error",
          "user_id" => $user_id
      ]);
  }

 ?>
