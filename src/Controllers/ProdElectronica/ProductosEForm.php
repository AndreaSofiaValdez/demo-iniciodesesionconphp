<?php

namespace Controllers\ProdElectronica;

use Controllers\PublicController;
use Views\Renderer;
use Dao\ProdElectronica\Productos;

class ProductosEForm extends PublicController
{
    private $viewData = [];
    private $mode = "DSP";

    private $crf_token = "";

    private $id_producto = 0;
    private $nombre = "";
    private $tipo = "";
    private $precio = 0;
    private $marca = "";
    private $fecha_lanzamiento = 0;

    private $isReadOnly = "readonly";
    private $hasErrors = false;
    private $errors = [];
    private $showActions = true;
    private $cxfToken = "";

    private $modeOptions = [
        "INS" => "Crear Nuevo Producto",
        "UPD" => "Actualizar Producto (%s %s)",
        "DEL" => "Eliminar Producto (%s %s)",
        "DSP" => "Ver Producto (%s %s)"
    ];

    private $productostipo = [
        "CEL" => "CELULAR",
        "AUD" => "AUDIFONOS",
        "LPT" => "LAPTOP"
    ];



    private function throwError($message, $scope = "global")
    {
        $this->hasErrors = true;
        error_log($message);
        if (!isset($this->errors[$scope])) {
            $this->errors[$scope] = [];
        }
        $this->errors[$scope][] = $message;
    }

    private function cargar_datos()
    {
        $this->id_producto = isset($_GET["id_producto"]) ? intval($_GET["id_producto"]) : 0;
        $this->mode = isset($_GET["mode"]) ? $_GET["mode"] : "DSP";

        if ($this->id_producto > 0) {
            $producto = \Dao\ProdElectronica\Productos::getProducto($this->id_producto);
            if ($producto) {
                $this->nombre = $producto["nombre"];
                $this->tipo = $producto["tipo"];
                $this->precio = $producto["precio"];
                $this->marca = $producto["marca"];
                $this->fecha_lanzamiento = $producto["fecha_lanzamiento"];
            }
        }
    }

    private function getPostData()
    {
        if (!$this->validateCsfrToken()) {
            $this->throwError("Error de aplicación, Token CSRF Inválido");
        }
        $tmp_mode = isset($_POST["mode"]) ? $_POST["mode"] : "DSP";
        if ($tmp_mode !== $this->mode) {
            $this->throwError("Error de aplicación, Modo de formulario incorrecto");
        }
        $tmp_id_producto = isset($_POST["id_producto"]) ? intval($_POST["id_producto"]) : 0;
        if ($this->mode === "INS") {
            if ($tmp_id_producto !== 0) {
                $this->throwError("No se puede insertar con un valor de producto", "id_producto_error");
            }
        } else {
            if ($tmp_id_producto != $this->id_producto) {
                $this->throwError("Error de Aplicación, no se puede modificar el valor del Identificador del producto");
            }
        }
        $this->id_producto = $tmp_id_producto;

        $tmp_nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
        if (preg_match("/^\s*$/", $tmp_nombre)) {
            $this->throwError("Debe ingresar el nombre del producto", "nombre_error");
        }
        if (!preg_match("/^[a-zA-Z0-9áéíóúüÁÉÍÓÚÜñÑ ]*$/", $tmp_nombre)) {
            $this->throwError("El nombre del producto solo puede contener letras y números", "nombre_error");
        }
        $this->nombre = $tmp_nombre;

        $tmp_tipo = isset($_POST["tipo"]) ? $_POST["tipo"] : "";

        if (!isset($this->productostipo[$tmp_tipo])) {
            $this->throwError("Debe seleccionar un estado para la categoria");
        }
        $this->tipo = $tmp_tipo;

        $tmp_precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
        if (preg_match("/^\s*$/", $tmp_precio)) {
            $this->throwError("Debe ingresar el precio del producto", "precio_error");
        }
        if (!preg_match("/^[a-zA-Z0-9áéíóúüÁÉÍÓÚÜñÑ ]*$/", $tmp_precio)) {
            $this->throwError("El precio del producto solo puede contener letras y números", "precio_error");
        }
        $this->precio = $tmp_precio;

        $tmp_marca = isset($_POST["marca"]) ? $_POST["marca"] : "";
        if (preg_match("/^\s*$/", $tmp_marca)) {
            $this->throwError("Debe ingresar la marca del producto", "marca_error");
        }
        if (!preg_match("/^[a-zA-Z0-9áéíóúüÁÉÍÓÚÜñÑ ]*$/", $tmp_marca)) {
            $this->throwError("La marca del producto solo puede contener letras y números", "marca_error");
        }
        $this->marca = $tmp_marca;
    }

    private function processAction()
    {
        switch ($this->mode) {
            case "INS":
                $inserted = \Dao\ProdElectronica\Productos::insertProducto(
                    $this->id_producto,
                    $this->nombre,
                    $this->tipo,
                    $this->marca,
                    $this->fecha_lanzamiento,
                );
                if ($inserted) {
                    \Utilities\Site::redirectToWithMsg(
                        "index.php?page=ProdElectronica_ProductosEList",
                        "Producto Guardado"
                    );
                } else {
                    $this->throwError("Error al insertar el producto");
                }
                break;
            case "UPD":
                $updated = \Dao\ProdElectronica\Productos::UpdateProducto(
                    $this->id_producto,
                    $this->nombre,
                    $this->tipo,
                    $this->marca,
                    $this->fecha_lanzamiento,
                );
                if ($updated) {
                    \Utilities\Site::redirectToWithMsg(
                        "index.php?page=ProdElectronica_ProductosEList",
                        "Producto Actualizado"
                    );
                } else {
                    $this->throwError("Error al actualizar ");
                }
                break;
            case "DEL":
                $deleted = \Dao\ProdElectronica\Productos::deleteProducto($this->id_producto);
                if ($deleted) {
                    \Utilities\Site::redirectToWithMsg(
                        "index.php?page=ProdElectronica_ProductosEList",
                        "Producto Eliminado"
                    );
                } else {
                    $this->throwError("Error al eliminar");
                }
                break;
        }
    }
    private function prepareViewData()
    {
        $viewData["mode"] = $this->mode;
        $viewData["modeDesc"] = sprintf($this->modeOptions[$this->mode], $this->id_producto, $this->id_producto);
        $viewData["id_producto"] = $this->id_producto;
        $viewData["nombre"] = $this->nombre;
        $viewData["tipo"] = $this->tipo;
        $viewData["marca"] = $this->marca;
        $viewData["fecha_lanzamiento"] = $this->fecha_lanzamiento;


        if ($this->mode === "INS" || $this->mode === "UPD") {
            $this->isReadOnly = "";
        }
        $viewData["isReadOnly"] = $this->isReadOnly;

        $viewData["showAction"] = $this->mode !== "DSP";

        foreach ($this->productostipo as $value => $text) {
            $viewData["productos_tipo_list"][] = [
                "value" => $value,
                "text" => $text,
                "selected" => ($value === $this->tipo) ? "selected" : ""
            ];
        }

        $this->crf_token = $this->csfrToken();
        $viewData["crf_token"] = $this->crf_token;
        $viewData["hasErrors"] = $this->hasErrors;
        $viewData["errors"] = $this->errors;

        $this->viewData = $viewData;
    }

    private function csfrToken()
    {
        $token = md5(uniqid(microtime(), true));
        $_SESSION["category_form_token"] = $token;
        return $token;
    }

    private function validateCsfrToken()
    {
        if (!isset($_POST["crf_token"])) {
            return false;
        }
        if ($_POST["crf_token"] !== $_SESSION["producto_form_token"]) {
            return false;
        }
        return true;
    }

    public function run(): void
    {

        $this->cargar_datos();

        if ($this->isPostBack()) {
            $this->getPostData();
            if (!$this->hasErrors) {
                $this->processAction();
            }
        }

        $this->prepareViewData();
        Renderer::render("ProdElectronica/ProductosEForm", $this->viewData);
    }
}
