<?php
// clase Entity que representa un usuario en la base de datos, la diferencia con los DTOs es que esta clase refleja directamente la estructura de la tabla
class Usuario {
    public $id;
    public $username;
    public $password_hash;  // hash de la contraseña ya que esta no se guarda en texto plano
    public $nombres;
    public $apellidos;
}
