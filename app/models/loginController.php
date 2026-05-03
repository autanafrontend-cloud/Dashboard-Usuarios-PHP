<?php

namespace app\controllers;
use app\models\mainModel;

class loginController extends mainModel{

    /*---------- Controlador iniciar sesion ----------*/
    public function iniciarSesionControlador(){

        // ⚠️ SOLO PARA DEBUG (quitar cuando funcione)
        // echo "ENTRA AL LOGIN";

        if(!isset($_POST['login_usuario']) || !isset($_POST['login_clave'])){
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se recibieron datos del formulario'
                });
            </script>";
            return;
        }

        $usuario = trim($this->limpiarCadena($_POST['login_usuario']));
        $clave   = trim($this->limpiarCadena($_POST['login_clave']));

        # Verificar campos vacíos
        if($usuario=="" || $clave==""){
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debes llenar todos los campos'
                });
            </script>";
            return;
        }

        # Validar usuario
        if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)){
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Formato de usuario incorrecto'
                });
            </script>";
            return;
        }

        # Validar clave
        if($this->verificarDatos("[a-zA-Z0-9$@.-]{4,100}", $clave)){
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Formato de clave incorrecto'
                });
            </script>";
            return;
        }

        # Buscar usuario (más seguro con consulta preparada simple)
        $sql = $this->conectar()->prepare("SELECT * FROM usuario WHERE usuario_usuario = :usuario");
        $sql->bindParam(":usuario", $usuario);
        $sql->execute();

        if($sql->rowCount() == 1){

            $datos = $sql->fetch();

            # Verificar contraseña
            if(password_verify($clave, $datos['usuario_clave'])){

                session_start();

                $_SESSION['id'] = $datos['usuario_id'];
                $_SESSION['nombre'] = $datos['usuario_nombre'];
                $_SESSION['apellido'] = $datos['usuario_apellido'];
                $_SESSION['usuario'] = $datos['usuario_usuario'];
                $_SESSION['foto'] = $datos['usuario_foto'];

                if(headers_sent()){
                    echo "<script> window.location.href='".APP_URL."dashboard/'; </script>";
                }else{
                    header("Location: ".APP_URL."dashboard/");
                }
                exit();

            }else{
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Usuario o contraseña incorrectos'
                    });
                </script>";
            }

        }else{
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Usuario o contraseña incorrectos'
                });
            </script>";
        }
    }


    /*---------- Cerrar sesión ----------*/
    public function cerrarSesionControlador(){

        session_start();
        session_destroy();

        if(headers_sent()){
            echo "<script> window.location.href='".APP_URL."login/'; </script>";
        }else{
            header("Location: ".APP_URL."login/");
        }
        exit();
    }
}