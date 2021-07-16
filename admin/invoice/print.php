<?php 
require ('../../config.php');
?>
<!DOCTYPE html>
<html lang="en">
<?php include "../inc/header.php" ?>
<body>
<?php
$type = isset($_GET['type']) ? $_GET['type'] : 1 ;
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `invoice_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
$tax_rate = isset($tax_rate) ? $tax_rate : $_settings->info('tax_rate');
?>
<style>
table th, table td{
	padding:5px 3px!important;
}
</style>
<h1 class="text-center"><b>Factura ConfiguroWeb</b></h1>
<table class="table">
    <colgroup>
        <col width="50%">
        <col width="50%">
    </colgroup>
    <tr>
        <td>
        <img src="<?php echo validate_image($_settings->info('logo')) ?>" class="img-thumbnail" style="height:75px;width:75px;object-fit:contain" alt="">
        <p>Factura de: <u><?php echo $_settings->info('name') ?></u></p>
        <p>Factura a: <u><?php echo $customer_name ?></u></p>
        </td>
        <td>
        <p><b>Fecha Factura:</b> <?php echo date("F d, Y G:i",strtotime($date_created)) ?></p>
        <p><b>Código Factura:</b> <?php echo $invoice_code ?></p>
        </td>
    </tr>
</table>
<hr>
<table class="table table-bordered">
    <colgroup>
        <col width="10%">
        <col width="15%">
        <col width="35%">
        <col width="15%">
        <col width="15%">
    </colgroup>
    <thead>
        <tr>
            <th class="text-center">CANT</th>
            <th class="text-center">UNIDAD</th>
            <th class="text-center">Producto/Servicio</th>
            <th class="text-center">Costo</th>
            <th class="text-center">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if($type == 1)
            $items = $conn->query("SELECT i.*,p.description,p.id as pid,p.product as `name`,p.category_id as cid FROM invoices_items i inner join product_list p on p.id = i.form_id where i.invoice_id = '{$id}' ");
        else
            $items = $conn->query("SELECT i.*,s.description,s.id as `sid`,s.`service` as `name`,s.category_id as cid FROM invoices_items i inner join service_list s on s.id = i.form_id where i.invoice_id = '{$id}' ");
        while($row=$items->fetch_assoc()):
            $category = $conn->query("SELECT * FROM `category_list` where id = {$row['cid']}");
            $cat_count = $category->num_rows;
            $res = $cat_count > 0 ? $category->fetch_assoc(): array();
            $cat_name = $cat_count > 0 ? $res['name'] : "N/A";
            $description = stripslashes(html_entity_decode($row['description']));
        ?>
        <tr>
            <td class="text-center"><?php echo $row['quantity'] ?></td>
            <td class="text-center"><?php echo $row['unit'] ?></td>
            <td class="">
            <p class="m-0"><small><b>Categoria:</b> <?php echo $cat_name ?></small></p>
            <p class="m-0"><small><b>Nombre:</b> <?php echo $row['name'] ?></small></p>
            <div>
                <?php echo $description ?>
            </div>
            </td>
            <td class="text-right"><?php echo number_format($row['price']) ?></td>
            <td class="text-right"><?php echo number_format($row['total']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr class="bg-foot" style="background-color:#95b3e8 !important;">
            <th class="text-right" colspan="4">Sub Total</th>
            <th class="text-right" id="sub_total"><?php echo number_format($sub_total) ?></th>
        </tr>
        <tr class="bg-foot" style="background-color:#95b3e8 !important;">
            <th class="text-right" colspan="4">Tasa de impuesto</th>
            <th class="text-right" id="tax_rate"><?php echo $tax_rate ?>%</th>
        </tr>
        <tr class="bg-foot" style="background-color:#95b3e8 !important;">
            <th class="text-right" colspan="4">Impuesto</th>
            <th class="text-right" id="tax"><?php echo number_format($sub_total * ($tax_rate/100) ) ?></th>
        </tr>
        <tr class="bg-foot" style="background-color:#95b3e8 !important;">
            <th class="text-right" colspan="4">Gran total</th>
            <th class="text-right" id="gtotal"><?php echo number_format($total_amount) ?></th>
        </tr>
    </tfoot>
</table>
<hr>
<p><b>Observaciones:</b></p>
<?php echo $remarks ?>
</body>
</html>