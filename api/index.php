<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require 'gastos/vendor/autoload.php';

$app = new \Slim\Slim;

$app->get('/hello/:name', function ($name) use ($app) {
    
    include 'bbdd/conectar.php';

    $ultimos_gastos = $gastos_db->select("gastos_privados",
            [
                "[>]familias_privadas(f)"=>["familia"=>"id"]
            ],
            [
                "gastos_privados.fecha",
                "gastos_privados.id",
                "gastos_privados.importe",
                "f.nombre(familia)"
            ],
            [
                "gastos_privados.usuario" => $_SESSION['usuario'],
                "ORDER" => ["id" => "DESC"],
                "LIMIT" => 4
            ]
        );
    
    echo json_encode($ultimos_gastos);
    
    //$app->response->write($salida);
    
});

$app->get('/viajes', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';

    $trips = $gastos_db->select("viajes",
            "*",
            [
                "ORDER" => ["fecha_inicio" => "DESC"]
            ]
        );
    
    $viajes = array();
    foreach($trips as $trip) {
        $viajes[] = [
            "id" => $trip['id'],
            "nombre" => $trip['nombre'],
            "fecha_inicio" => transformar_fecha($trip['fecha_inicio'], "-"),
            "fecha_fin" => transformar_fecha($trip['fecha_fin'], "-"),
            "total" => $gastos_db->sum("viajes_gastos", "importe", ["viaje" => $trip['id']])
        ];
    }
    
    $gastos = $gastos_db->sum("viajes_gastos", "importe");
    
    $app->response->write(json_encode(["total"=>$gastos, "viajes"=>$viajes]));
    
});

$app->get('/viaje', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';

    $viaje = $gastos_db->get("viajes",
            "*",
            [
                "id" => $app->request->get('id')
            ]
        );
    
    $viaje['total'] = $gastos_db->sum("viajes_gastos", "importe", ["viaje" => $viaje['id']]);
    
    $app->response->write(json_encode($viaje));
    
});

$app->post('/viaje', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $nombre = $app->request->post('nombre');
    $fecha_inicio = transformar_fecha($app->request->post('fecha_inicio'),"/");
    $fecha_fin = transformar_fecha($app->request->post('fecha_fin'),"/");
    
    if($gastos_db->has("viajes",["nombre" => $nombre])) {
        $app->response->setStatus(406);
        $app->response->write(json_encode(["mensaje"=>'Ya existe un viaje con ese nombre']));
    } else {
        $gastos_db->insert("viajes", [
            "nombre" => $nombre,
            "fecha_inicio" => $fecha_inicio,
            "fecha_fin" => $fecha_fin
        ]);
    
        $app->response->write(json_encode(["mensaje"=>'El viaje ha sido creado.']));
    }
    
});

$app->get('/gasto', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';

    $tabla = $app->request->get('tabla');
    $gasto = $app->request->get('gasto');
    $tabla_tickets = $app->request->get('tabla_tickets');
    
    $output = array();
    $output['gasto'] = array();
    $output['productos'] = array();
    
    $output['gasto'] = $gastos_db->get($tabla,["id","usuario","familia","fecha","importe","tiene_ticket"],["id"=>$gasto]);
    
    if($output['gasto']['fecha'] != '') {
        $aux = explode("-", $output['gasto']['fecha']);
        $output['gasto']['fecha'] = $aux[2]."/".$aux[1]."/".$aux[0];
    }
    
    if($output['gasto']['tiene_ticket'] == 'S') {
        $ticket = $gastos_db->select($tabla_tickets,["producto","importe"],["gasto"=>$gasto]);
        foreach ($ticket as $linea) {
            $output['productos'][] = array("producto"=>$linea['producto'], "t_importe"=>$linea['importe']);
        }
    }
    
    $app->response->write(json_encode($output));
    
});

$app->post('/gasto', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tipo = $app->request->post('tipo');
    $viaje = $app->request->post('viaje');
    parse_str($app->request->post('gastos'), $gastos);
    parse_str($app->request->post('ticket'), $t);
    $ticket = $t['ticket'];
    
    $tablas = getTablas($tipo, $gastos['usuario'], $gastos['familia'], $gastos['fecha'], $gastos['importe'], (isset($ticket[0]) && $ticket[0]['t_importe'] > 0 ? 'S':'N'), $viaje) ;
    
    $gastos_db->insert($tablas['gastos'], $tablas['columnas']);
    
    $last_id = $gastos_db->id();
    
    if($last_id > 0) {
        foreach ($ticket as $linea) {
            if($linea['t_importe'] > 0) {
                
                if($gastos_db->has($tablas['productos'],["nombre"=>$linea['producto']])) {
                    $id_producto = $gastos_db->get($tablas['productos'],"id",["nombre"=>$linea['producto']]);
                } else {
                    $gastos_db->insert($tablas['productos'],["nombre"=>$linea['producto']]);
                    $id_producto = $gastos_db->id();
                }
                
                $gastos_db->insert($tablas['ticket'],[

                    "fecha"=>$tablas['columnas']['fecha'],
                    "producto"=>$linea['producto'],
                    "id_producto"=>$id_producto,
                    "importe"=>$linea['t_importe'],
                    "gasto"=>$last_id

                ]);
            }
        }
    }
    
    $app->response->write(json_encode(["mensaje"=>'Se ha guardado un nuevo gasto.']));
    
});

$app->put('/gasto', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla = $app->request->put('tabla');
    parse_str($app->request->put('gastos'), $gastos);
    parse_str($app->request->put('ticket'), $t);
    $usuario = $app->request->put('usuario');
    $familia = $app->request->put('familia');
    $importe = $app->request->put('importe');
    $fecha = $app->request->put('fecha');
    
    $aux = explode("/",$fecha);
    $fecha = $aux[2]."-".$aux[1]."-".$aux[0];
    
    $gastos_db->update($tabla,[
        
        "usuario"=>$_POST['usuario'],
        "familia"=>$_POST['familia'],
        "fecha"=>$fecha,
        "importe"=>$_POST['importe'],
        "tiene_ticket"=>(isset($_POST['ticket'][0]) && $_POST['ticket'][0]['t_importe'] > 0 ? 'S':'N')
        
    ],["id"=>$_POST['gasto_id']]);
    
    if(count($_POST['ticket']) > 0) {
        foreach ($_POST['ticket'] as $key=>$linea) {
            if($linea['t_importe'] > 0) {
                
                if($key == 0) $db->delete("tickets",["gasto"=>$_POST['gasto_id']]);
                
                if($db->has("productos",["nombre"=>$linea['producto']])) {
                    $id_producto = $db->get("productos","id",["nombre"=>$linea['producto']]);
                } else {
                    $db->insert("productos",["nombre"=>$linea['producto']]);
                    $id_producto = $db->id();
                }
                
                $db->insert("tickets",[

                    "fecha"=>$fecha,
                    "producto"=>$linea['producto'],
                    "id_producto"=>$id_producto,
                    "importe"=>$linea['t_importe'],
                    "gasto"=>$_POST['gasto_id']

                ]);
            }
        }
    }
    
    echo $_POST['gasto_id'];
    
    
    
    
    
    $app->response->write(json_encode(["mensaje"=>'Se ha guardado un nuevo gasto.']));
    
});

$app->get('/todos_gastos', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla = $app->request->get('tabla');
    $tabla_familias = $app->request->get('tabla_familias');
    $filtro = $app->request->get('filtro');
    $valor_filtro = $app->request->get('valor_filtro');
    
    if($tabla == '') $tabla = $app->request->headers->get('Tabla');
    if($tabla_familias == '') $tabla_familias = $app->request->headers->get('Tabla_Familias');
    if($filtro == '') $filtro = $app->request->headers->get('Filtro');
    if($valor_filtro == '') $valor_filtro = $app->request->headers->get('Valor_Filtro');
           
    $datos = [
        $tabla.".id", $tabla.".fecha",$tabla.".importe",$tabla.".tiene_ticket",
        "u.nombre(usuario)",
        "f.nombre(familia)"
    ];
    $filtros = [
        "[>]usuarios(u)"=>["usuario"=>"id"],
        "[>]".$tabla_familias."(f)"=>["familia"=>"id"]
    ];
    $where["ORDER"] = ["fecha" => "DESC"];
    $where[$filtro] = $valor_filtro;
    
    $todos_gastos = $gastos_db->select($tabla, $filtros, $datos, $where);

    $app->response->write(json_encode($todos_gastos));
    
});

$app->get('/drilldown', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla_familias = $app->request->get('tabla_familias');
    $familia = $app->request->get('familia');
    $privado = $app->request->get('privado');
    $tabla_gastos = $app->request->get('tabla_gastos');
    $filtro = $app->request->get('filtro');
    $filtro_nombre = $app->request->get('filtro_nombre');
    $gastos_comunes = $app->request->get('comunes');
    $tabla_comunes = $app->request->get('tabla_comunes');
    $nombre = $app->request->get('nombre');
    
    if($familia == 0) {
        
        $pie_data = array();
    
        if($privado == 'S') {
            $familias = $gastos_db->select($tabla_familias,["id","nombre"],["AND"=>["usuario"=>$_SESSION['usuario'],"padre"=>$familia]]);
        } else {
            $familias = $gastos_db->select($tabla_familias,["id","nombre"],["padre"=>$familia]);
        }
        foreach ($familias as $familia) {
            $subfamilias = $gastos_db->select($tabla_familias,"id",["padre"=>$familia['id']]);
            if($privado == 'S') {
                $gasto = $gastos_db->sum($tabla_gastos,"importe",["AND"=>["usuario"=>$_SESSION['usuario'],$filtro_nombre=>$filtro,"familia"=>$subfamilias]]);
            } else {
                $gasto = $gastos_db->sum($tabla_gastos,"importe",["AND"=>[$filtro_nombre=>$filtro,"familia"=>$subfamilias]]);
            }
            $pie_data[] = array("name"=>$familia['nombre'],"y"=>round($gasto, 2),"drilldown"=>$familia['id']);
        }

        if($gastos_comunes == 'S') {
            $gasto_comun = $gastos_db->sum($tabla_comunes,"importe",["usuario"=>$_SESSION['usuario']]);
            $pie_data[] = array("name"=>'Gastos Comunes',"y"=>round($gasto_comun, 2),"drilldown"=>0);
        }
    } else {
        $pie_data = array("name" => $nombre, "id" => $familia, "data" => array());

        if($privado == 'S') {
            $familias = $gastos_db->select($tabla_familias,["id","nombre"],["AND"=>["usuario"=>$_SESSION['usuario'], "padre"=>$familia]]);
        } else {
            $familias = $gastos_db->select($tabla_familias,["id","nombre"],["padre"=>$familia]);
        }
        
        foreach ($familias as $familia) {
            if($privado == 'S') {
                $gasto = $gastos_db->sum($tabla_gastos,"importe",["AND"=>["usuario"=>$_SESSION['usuario'],"familia"=>$familia['id']]]);
            } else {
                $gasto = $gastos_db->sum($tabla_gastos,"importe",["AND"=>["familia"=>$familia['id']]]);
            }
            $pie_data["data"][] = array($familia['nombre'],round($gasto, 2));
        }  
    }
    
    $app->response->write(json_encode($pie_data));
    
});

$app->post('/categoria', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla = $app->request->post('tabla');
    $nombre_categoria = $app->request->post('nombre_categoria');
    
    if($gastos_db->has($tabla,["nombre" => $nombre_categoria])) {
        $app->response->setStatus(406);
        $app->response->write(json_encode(["mensaje"=>'Ya existe una categoría con ese nombre']));
    } else {
        $gastos_db->insert($tabla, ["nombre" => $nombre_categoria]);
        
        $familias = getFamilias($tabla);
    
        $app->response->write(json_encode([
            "mensaje" => 'Se ha guardado la nueva categoría.',
            "familias" => $familias
        ]));
    }
    
});

$app->post('/familia', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla = $app->request->post('tabla');
    $categoria = $app->request->post('categoria');
    $nombre_familia = $app->request->post('nombre_familia');
    $tiene_ticket = $app->request->post('tiene_ticket');
    
    if($gastos_db->has($tabla,["AND" => ["padre" => $categoria, "nombre" => $nombre_familia]])) {
        $app->response->setStatus(406);
        $app->response->write(json_encode(["mensaje"=>'Ya existe una familia con ese nombre para esa categoría.']));
    } else {
        
        if (isset($tiene_ticket) && $tiene_ticket == 'S') $tiene_ticket = 'S';
        else $tiene_ticket = 'N';
        
        $gastos_db->insert($tabla, ["nombre" => $nombre_familia, "padre" => $categoria, "ticket" => $tiene_ticket]);
        
        $familias = getFamilias($tabla);
    
        $app->response->write(json_encode([
            "mensaje" => 'Se ha guardado la nueva familia.',
            "familias" => $familias
        ]));
    }
    
});

$app->get('/familias', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla = $app->request->get('tabla');
    
    $familias = getFamilias($tabla);
    
    $app->response->write(json_encode($familias));
    
});

$app->get('/ticket', function () use ($app) {
    
    include 'bbdd/conectar.php';
    include 'funciones.php';
    
    $tabla_tickets = $app->request->get('tabla_tickets');
    $ticket = $app->request->get('ticket');
    
    $productos = $gastos_db->select($tabla_tickets,["producto","importe"],["gasto"=>$ticket]);
    
    getTicket($productos);
    
    $app->response->write(getTicket($productos));
    
});

$app->run();