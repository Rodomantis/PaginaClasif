<?php
header('Access-Control-Allow-Origin: *');
require 'vendor/autoload.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */

$dsn = "mysql:dbname=bdclasificados;host=localhost";
$username = "root";
$password = "";
$pdo = new PDO($dsn, $username, $password);
$db = new NotORM($pdo);
$app = new \Slim\Slim(array(
    "MODE" => "development",
    "TEMPLATES.PATH" => "./templates"
));

$app->get("/", function() {
    echo "<h1>Hello Slim World</h1>";
});
$app->get("/anuncios/", function () use ($app, $db) {
    $anuncios = array();
    foreach ($db->anuncios() as $anuncio) {
        $anuncios[]  = array(
            "IDAnuncio" => $anuncio["IDAnuncio"],
            "TituloAnuncio" => $anuncio["TituloAnuncio"],
            "DetalleAnuncio" => $anuncio["DetalleAnuncio"],
            "idTipoAnuncio" => $anuncio["idTipoAnuncio"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($anuncios);
});
$app->get("/usuarios/", function () use ($app, $db) {
    $usuarios = array();
    foreach ($db->usuario() as $usuario) {
        $usuarios[]  = array(
            "IDUsuario" => $usuario["IDUsuario"],
			"IDPersonaUS" => $usuario["IDPersonaUS"],
            "Password" => $usuario["Password"],
            "TipoUsuario" => $usuario["TipoUsuario"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($usuarios);
});
$app->get("/personas/", function () use ($app, $db) {
    $personas = array();
    foreach ($db->persona() as $persona) {
        $personas[]  = array(
            "IDPersona" => $persona["IDPersona"],
            "Nombres" => $persona["Nombres"],
            "Apellidos" => $persona["Apellidos"],
            "Telefono" => $persona["Telefono"],
			"Correo" => $persona["Correo"]
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($personas);
});
$app->get("/anuncio/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $anuncio = $db->anuncios()->where("IDAnuncio", $id);
    if ($data = $anuncio->fetch()) {
        echo json_encode(array(
            "IDAnuncio" => $data["IDAnuncio"],
            "TituloAnuncio" => $data["TituloAnuncio"],
            "DetalleAnuncio" => $data["DetalleAnuncio"],
            "idTipoAnuncio" => $data["idTipoAnuncio"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
});
$app->get("/usuario/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $usuario = $db->usuario()->where("IDPersonaUS", $id);
    if ($data = $usuario->fetch()) {
        echo json_encode(array(
            "IDPersonaUS" => $data["IDPersonaUS"],
            "IDUsuario" => $data["IDUsuario"],
            "Password" => $data["Password"],
            "TipoUsuario" => $data["TipoUsuario"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
});
$app->get("/login/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $usuario = $db->usuario()->where("IDUsuario", $id);
    if ($data = $usuario->fetch()) {
        echo json_encode(array(
            "IDPersonaUS" => $data["IDPersonaUS"],
            "IDUsuario" => $data["IDUsuario"],
            "Password" => $data["Password"],
            "TipoUsuario" => $data["TipoUsuario"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
});
$app->get("/persona/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $persona = $db->persona()->where("IDPersona", $id);
    if ($data = $persona->fetch()) {
        echo json_encode(array(
            "IDPersona" => $data["IDPersona"],
            "Nombres" => $data["Nombres"],
            "Apellidos" => $data["Apellidos"],
            "Telefono" => $data["Telefono"],
			"Correo" => $data["Correo"]
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book ID $id does not exist"
            ));
    }
});
//$usuario = array("id" => $result["idpersonz"]),
$app->post("/persona/", function () use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $persona = $app->request()->post();
    $result = $db->persona->insert($persona);
    echo json_encode(array("IDPersona" => $result["IDPersona"]));
});
$app->post("/usuario/", function () use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $usuario = $app->request()->post();
    $result = $db->usuario->insert($usuario);
    echo json_encode(array("IDPersonaUS" => $result["IDPersonaUS"]));
});
$app->post("/anuncio/", function () use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $anuncio = $app->request()->post();
    $result = $db->anuncios->insert($anuncio);
    echo json_encode(array("IDAnuncio" => $result["IDAnuncio"]));
});
$app->put("/anuncio/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $anuncio = $db->anuncios()->where("IDAnuncio", $id);
    if ($anuncio->fetch()) {
        $post = $app->request()->put();
        $result = $anuncio->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Book updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});
$app->delete("/anuncio/:id", function ($id) use($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $anuncio = $db->anuncios()->where("IDAnuncio", $id);
    if ($anuncio->fetch()) {
        $result = $anuncio->delete();
        echo json_encode(array(
            "status" => true,
            "message" => "Book deleted successfully"
        ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Book id $id does not exist"
        ));
    }
});
$app->run();