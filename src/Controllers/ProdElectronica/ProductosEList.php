<?php

namespace Controllers\ProdElectronica;

use Controllers\PublicController;
use Dao\ProdElectronica\Productos as ProductosDao;
use Views\Renderer;

class ProductosEList extends PublicController
{
    public function run(): void
    {
        $viewData = [];
        $viewData["ProductosElectronica"] = ProductosDao::getAll();
        Renderer::render("ProdElectronica/list", $viewData);
    }
}
