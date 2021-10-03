<?php
require_once('../helpers/database.php');
require_once('../helpers/validator.php');
require_once('../models/agenda.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se instancia la clase correspondiente.
    $agenda = new Agenda;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['id_empleado'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $agenda->readAll()) {
                    $result['status'] = 1;
                } else {
                    if (Database::getException()) {
                        $result['exception'] = Database::getException();
                    } else {
                        $result['exception'] = 'Aún no hay tareas para este día';
                    }
                }
                break;
            case 'readClientes':
                if ($result['dataset'] = $agenda->readClientes()) {
                        $result['status'] = 1;
                } else {
                        if (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'No hay clientes registrados';
                        }
                    }
                break; 
            case 'search':
                $_POST = $agenda->validateForm($_POST);
                if ($_POST['search'] != '') {
                    if ($result['dataset'] = $agenda->searchRows($_POST['search']) or $agenda->setFechaProvisional($_POST['fecha_search'])) {
                        $result['status'] = 1;
                        $rows = count($result['dataset']);
                        if ($rows > 1) {
                            $result['message'] = 'Se encontraron ' . $rows . ' coincidencias';
                        } else {
                            $result['message'] = 'Solo existe una coincidencia';
                        }
                    } else {
                        if (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'No hay coincidencias';
                        }
                    }
                } else {
                    $result['exception'] = 'Ingrese un valor para buscar';
                }
                break;
            case 'create':
                $_POST = $agenda->validateForm($_POST);
                    if($agenda->setIdCliente($_POST['id_cliente'])){
                        if($agenda->setIdEmpleado($_SESSION['id_empleado'])){
                            if($agenda->setFechaProgramacion($_POST['fecha_pro'])){
                                if($agenda->setHoraProgramacion($_POST['hora_pro'])){
                                    if($agenda->setFechaProvisional($_POST['fecha_nal'])){
                                        if($agenda->setHoraProvisional($_POST['hora_nal'])){
                                            if($agenda->setTarea($_POST['tarea'])){
                                                if($agenda->setEstado($_POST['tarea-select'])){
                                                    if($agenda->setObservaciones($_POST['comentario'])){
                                                        if ($agenda->createRow()){
                                                            $result['status'] = 1;
                                                            $result['message'] = 'Tarea ingresada correctamente';
                                                        } else {
                                                            $result['exception'] = Database::getException();
                                                        }
                                                    } else{
                                                        $result['exception'] = 'Observaciones incorrectas';
                                                    }
                                                } else{
                                                    $result['exception'] = 'Estado incorrecto';
                                                }
                                            } else{
                                                $result['exception'] = 'Tarea incorrecta';
                                            }
                                        } else{
                                            $result['exception'] = 'Hora de provisional incorrecta';
                                        }
                                    } else{
                                        $result['exception'] = 'Fecha de provisional incorrecta';
                                    }
                                } else{
                                    $result['exception'] = 'Hora de programación incorrecta';
                                }
                            } else{
                                $result['exception'] = 'Fecha de programación incorrecta';
                            }
                        } else{
                            $result['exception'] = 'Empleado incorrecto';
                        }
                    } else{
                        $result['exception'] = 'Cliente incorrecto';
                    }
                
                break;
            case 'readOne':
                if ($agenda->setId($_POST['id_agenda'])) {
                    if ($result['dataset'] = $agenda->readOne()) {
                        $result['status'] = 1;
                    } else {
                        if (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'Tarea inexistente';
                        }
                    }
                } else {
                    $result['exception'] = 'Tareaincorrecto';
                }
                break;
            case 'update':
                $_POST = $agenda->validateForm($_POST);
                if ($agenda->setId($_POST['id_agenda'])) {
                    if ($data = $agenda->readOne()) {
                            if($agenda->setIdCliente($_POST['id_cliente2'])){
                                if($agenda->setIdEmpleado($_SESSION['id_empleado'])){
                                    if($agenda->setFechaProgramacion($_POST['fecha_pro2'])){
                                        if($agenda->setHoraProgramacion($_POST['hora_pro2'])){
                                            if($agenda->setFechaProvisional($_POST['fecha_nal2'])){
                                                if($agenda->setHoraProvisional($_POST['hora_nal2'])){
                                                    if($agenda->setTarea($_POST['tarea2'])){
                                                        if($agenda->setEstado($_POST['tarea-select2'])){
                                                            if($agenda->setObservaciones($_POST['comentario'])){
                                                                if ($agenda->updateRow()){
                                                                    $result['status'] = 1;
                                                                    $result['message'] = 'Tarea modificada correctamente';
                                                                } else {
                                                                    $result['exception'] = Database::getException();
                                                                }
                                                            } else{
                                                                $result['exception'] = 'Observaciones incorrectas';
                                                            }
                                                        } else{
                                                            $result['exception'] = 'Estado incorrecto';
                                                        }
                                                    } else{
                                                        $result['exception'] = 'Tarea incorrecta';
                                                    }
                                                } else{
                                                    $result['exception'] = 'Hora de provisional incorrecta';
                                                }
                                            } else{
                                                $result['exception'] = 'Fecha de provisional incorrecta';
                                            }
                                        } else{
                                            $result['exception'] = 'Hora de programación incorrecta';
                                        }
                                    } else{
                                        $result['exception'] = 'Fecha de programación incorrecta';
                                    }
                                } else{
                                    $result['exception'] = 'Empleado incorrecto';
                                }
                            } else{
                                $result['exception'] = 'Cliente incorrecto';
                            }
                        
                    } else {
                        $result['exception'] = 'Tarea inexistente';
                    }
                } else {
                    $result['exception'] = 'Tarea incorrecta';
                }        
                break;
            case 'delete':
                if ($agenda->setId($_POST['id_agenda'])) {
                    if ($data = $agenda->readOne()) {
                        if ($agenda->deleteRow()) {
                            $result['status'] = 1;
                            $result['message'] = 'Tarea eliminada correctamente';

                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else {
                        $result['exception'] = 'Tarea inexistente';
                    }
                } else {
                    $result['exception'] = 'Tarea incorrecto';
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
        }
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
?>