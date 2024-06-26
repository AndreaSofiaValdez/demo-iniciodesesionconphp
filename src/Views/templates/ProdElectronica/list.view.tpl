<h2>Listado de Productos de Electronica</h2>
<section class="WWList">
<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Marca</th>
            <th>Fecha Lanzamiento</th>
            <th>
                <a href="index.php?page=ProdElectronica_ProductosEForm&mode=INS" class="btn">
                    Nuevo
                </a>
             
            </th>
        </tr>
    </thead>
    <tbody>
        {{foreach ProductosElectronica}}
        <tr>
            <td>{{id_producto}}</td>
            <td>{{nombre}}</td>
            <td>{{tipo}}</td>
            <td>{{precio}}</td>
            <td>{{marca}}</td>
            <td>{{fecha_lanzamiento}}</td>
            <td>
                <a href="index.php?page=ProdElectronica_ProductosEForm&mode=DSP&id_producto={{id_producto}}">
                    Ver
                </a>&nbsp;
                <a href="index.php?page=ProdElectronica_ProductosEForm&mode=UPD&id_producto={{id_producto}}">
                    Editar
                </a>&nbsp;
                <a href="index.php?page=ProdElectronica_ProductosEForm&mode=DEL&id_producto={{id_producto}}">
                    Eliminar
                </a>&nbsp;
            </td>
        </tr>
        {{endfor ProductosElectronica}}
    </tbody>
</table>
</section>