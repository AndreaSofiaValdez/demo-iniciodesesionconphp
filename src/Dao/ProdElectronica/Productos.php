<?php

namespace Dao\ProdElectronica;

use Dao\Table;

class Productos extends Table
{
    public static function getAll()
    {
        $sqlstr = "SELECT * FROM ProductosElectronica;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getProducto($id_producto)
    {
        $sqlstr = "SELECT * FROM ProductosElectronica WHERE id =  id_producto = : id_producto;";
        return self::obtenerUnRegistro($sqlstr, [" id_producto" => $id_producto]);
    }

    public static function insertProducto(
        $nombre,
        $tipo,
        $precio,
        $marca,
        $fecha_lanzamiento
    ) {
        $insertsql = "INSERT INTO ProductosElectronica (nombre, tipo, precio, marca, fecha_lanzamiento) VALUES (:nombre, :tipo, :precio, :marca, :fecha_lanzamiento)";
        return self::executeNonQuery($insertsql, [
            "nombre" => $nombre,
            "tipo" => $tipo,
            "precio" => $precio,
            "marca" => $marca,
            "fecha_lanzamiento" => $fecha_lanzamiento
        ]);
    }
    public static function UpdateProducto(
        $nombre,
        $tipo,
        $precio,
        $marca,
        $fecha_lanzamiento
    ) {
        $insertsql = "UPDATE ProductosElectronica SET nombre=:nombre,tipo=:tipo, precio=:precio, marca=:marca, fecha_lanzamiento=:fecha_lanzamiento) VALUES (:nombre, :tipo, :precio, :marca, :fecha_lanzamiento)";
        return self::executeNonQuery($insertsql, [
            "nombre" => $nombre,
            "tipo" => $tipo,
            "precio" => $precio,
            "marca" => $marca,
            "fecha_lanzamiento" => $fecha_lanzamiento
        ]);
    }

    public static function deleteProducto(int $id_producto)
    {
        $sqlstr = "DELETE FROM  ProductosElectronica WHERE id_producto = :id_producto";
        $params = ["id_producto" => $id_producto];
        return self::executeNonQuery($sqlstr, $params);
    }
}
