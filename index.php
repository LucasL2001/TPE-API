<?php
require_once './app/models/peliculaModel.php';
require_once './app/views/peliculaView.php';

class PeliculasController
{
    private $model;
    private $view;
    private $data;


    function __construct()
    {
  
        // lee el body del request
        $this->data = file_get_contents("php://input");
    }
    private function getData()
    {
        return json_decode($this->data);
    }

    public function obtenerTodasLasPeliculas($params = null)
    {
        if (isset($_GET['ordenarPor']) || isset($_GET['orden'])) {
            if (isset($_GET['ordenarPor']) && isset($_GET['orden'])) {
                $peliculas = $this->model->conseguirTodasLasPeliculas($_GET['ordenarPor'], $_GET['orden']);
                if (!empty($peliculas)) {
                    $this->view->respuesta($peliculas);
                } else {
                    $this->view->respuesta("No se han encontrado peliculas", 404);
                }
            } else {
                $this->view->respuesta("Complete ambos campos", 400);
            }
        } else {
            $peliculas = $this->model->conseguirTodasLasPeliculas();
            if (!empty($peliculas)) {
                $this->view->respuesta($peliculas);
            }
        }
    }
    public function obtenerUnaPelicula($params = null)
    {
        $id = $params[':ID'];
        if (!is_numeric($id)) {
            $this->view->respuesta("Parametro solo numerico", 404);
        } else {
            $pelicula = $this->model->conseguirPeliculaDB($id);

            if ($pelicula) {
                $this->view->respuesta($pelicula);
            } else {
                $this->view->respuesta("La pelicula con el id=$id no existe", 404);
            }
        }
 
}

    
    public function borrarUnaPelicula($params = null)
    {
        $id = $params[':ID'];
        $peliculaABorrar = $this->model->conseguirPeliculaDB($id);

        if ($peliculaABorrar) {
            $this->model->borrarPelicula($id);
            $this->view->respuesta($peliculaABorrar);
        } else {
            $this->view->respuesta("La pelicula con el id=$id no existe", 404);
        }
    }

    public function insertarPelicula($params = null)
    {
        $datosDelForm = $this->getData();
        if (empty($datosDelForm->nombre) || empty($datosDelForm->anio) || empty($datosDelForm->id_genero)) {
            $this->view->respuesta("Complete los datos", 400);
        } else if (is_numeric($datosDelForm->anio) || is_numeric($datosDelForm->id_genero)){
            $id = $this->model->insertarPeliculaDB($datosDelForm->nombre, $datosDelForm->anio, $datosDelForm->produccion, $datosDelForm->recaudacion, $datosDelForm->id_genero);
            $peliculaCreada = $this->model->conseguirPeliculaDB($id);
            $this->view->respuesta($peliculaCreada, 201);
        }else{
            $this->view->respuesta("Los campos año y id_genero deben ser de tipo numerico", 404);
        }
    }

    public function editarPelicula($params = null)
    {
        $id = $params[':ID'];
        $peliculaAEditar = $this->model->conseguirPeliculaDB($id);
        if ($peliculaAEditar) {
            $datosDelForm = $this->getData();
            $nombre = $datosDelForm->nombre;
            $anio = $datosDelForm->anio;
            $produccion = $datosDelForm->produccion;
            $recaudacion = $datosDelForm->recaudacion;
            $id_genero = $datosDelForm->id_genero;
            $id_peliculas = $datosDelForm->id_peliculas;
            $peliculaEditada = $this->model->editarPelicula($nombre, $anio, $produccion, $recaudacion, $id_genero, $id_peliculas);
            $peliculaEditada = $this->model->conseguirPeliculaDB($id);
            $this->view->respuesta($peliculaEditada, 200);
        } else {
            $this->view->respuesta("No puede dejar estos campos sin completar", 400);
        }
    }
}
