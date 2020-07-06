<?php

require_once '../../funciones/db_pager.php';
require_once '../../funciones/db_query.php';
require_once('../../funciones/query_loader.php');
checklogin();
/*
$n_importe_2 = $_POST['n_importe_2'];
$n_importe_1 = $_POST['n_importe_1'];
$f_vto_pago_1 = $_POST['f_vto_pago_1'];
$f_vto_pago_2 = $_POST['f_vto_pago_2'];
$n_cuit = $_POST['n_cuit'];
$id_boleta = $_POST['id_boleta'];
$c_tipo_imponible1 = $_POST['c_tipo_imponible1'];
$c_tributo1 = $_POST['c_tributo1'];
$c_tipo_objeto1 = $_POST['c_tipo_objeto1'];
$n_objeto = $_POST['n_objeto'];
$n_pos_fiscal = $_POST['n_pos_fiscal'];
$n_cuota = $_POST['n_cuota'];
$c_estado = $_POST['c_estado'];
*/
$m_autoquery = $_POST['m_autoquery'];
$par =  json_decode($_POST['param']);

//die(var_dump($_POST['param']));
if ($m_autoquery == 'N'){
		$cond = " WHERE 1 = 2 ";
}else{
		$cond = " WHERE 1=1 ";
}

if($par->n_cuit != null){
		$cond .= " and b.n_cuit=:n_cuit";
}
if($par->id_boleta != null){
		$cond .= " and b.id_boleta=:id_boleta";
}
if($par->c_tipo_imponible1 != null){
		$cond .= " and :c_tipo_imponible1 = b.c_tipo_imponible ";		
}

if($par->c_tributo1 != null){
		$cond .= " and :c_tributo1 = b.c_tributo ";		
}

if($par->f_vto_pago_1 != null and $par->f_vto_pago_2 != null){
		$cond .= " and  trunc(b.f_emision) between :f_vto_pago_1 and :f_vto_pago_2";
}

if($par->c_tipo_objeto1 != null and $par->n_objeto != null){
		$cond .= " and  exists (SELECT 1
								FROM obligaciones o, boletas_agr_det bd
								WHERE o.id_obligacion = bd.id_obligacion
								AND bd.id_boleta = b.id_boleta
								AND o.c_tipo_objeto = :c_tipo_objeto1
								AND o.d_objeto_hecho =:n_objeto
							   )
					 or exists (SELECT 1
								FROM obligaciones_tmp o, boletas_agr_det bd
								WHERE b.id_boleta = bd.id_boleta
								AND bd.id_obligacion_tmp = o.id_obligacion_tmp
								AND o.c_tipo_objeto = :c_tipo_objeto1
								AND o.d_objeto_hecho =:n_objeto
							   )
						";
}


if($par->c_estado != null){
		$cond .= " and  upper(:c_estado) = decode((select count(1)
																		from pagos_efectuados pe
																		where pe.c_tipo_form = 'BA'
																		and pe.n_comprobante = to_char(b.id_boleta)),0,'IMPAGA','PAGADA')";
}

if($par->n_pos_fiscal != null){
		$cond .= " and  :n_pos_fiscal = (SELECT max(n_posicion_fiscal) FROM obligaciones o join boletas_agr_det bd on o.id_obligacion = bd.id_obligacion WHERE bd.id_boleta = b.id_boleta)";
}

if($par->n_cuota != null){
		$cond .= " and  :n_cuota = (SELECT max(n_cuota_anticipo) FROM obligaciones o join boletas_agr_det bd on o.id_obligacion = bd.id_obligacion WHERE bd.id_boleta = b.id_boleta)";
}

if($par->n_importe_1 != null and $par->n_importe_2 != null){
		$cond .= " and  (n_importe_1 between nvl(fun_convierte_a_numero(:n_importe_1),0) and nvl(fun_convierte_a_numero(:n_importe_2),9999999999999999.99))";
}

/*die("select b.id_boleta,
					b.id_contribuyente,
					b.d_denominacion,
					b.f_emision,
					b.f_vto_pago_1,
					fun_formato_numerico(b.n_importe_1),
					b.f_vto_pago_2,
					fun_formato_numerico(b.n_importe_2),
					b.d_cod_barras,
					b.d_cod_banelco,
					DECODE ( (SELECT COUNT (1)
							 FROM pagos_efectuados pe
							 WHERE   PE.C_TIPO_FORM = 'BA' 
							 AND PE.N_COMPROBANTE = TO_CHAR (b.id_boleta)), 0, 'IMPAGA', 'PAGADA') c_estado
			FROM boletas_agr_cab b
			".$cond
			);*/
 
 
$db_query = new DB_Query(
			"select b.id_boleta,
					(SELECT b.c_tipo_imponible||'-'||tg.d_dato
							FROM tablas_generales tg
							WHERE tg.n_tabla=3 and tg.c_dato=b.c_tipo_imponible)c_tipo_imponible,
					(SELECT b.c_tributo||'-'||t.d_descrip
                            FROM tributos t
                            WHERE b.c_tipo_imponible=t.c_tipo_imponible
                            and b.c_tributo=t.c_tributo) c_tributo,
					b.id_contribuyente,
					b.d_denominacion,
					b.f_emision,
					b.f_vto_pago_1,
					fun_formato_numerico(b.n_importe_1),
					b.f_vto_pago_2,
					fun_formato_numerico(b.n_importe_2),
					b.d_cod_barras,
					b.d_cod_banelco,
					DECODE ( (SELECT COUNT (1)
							 FROM pagos_efectuados pe
							 WHERE   PE.C_TIPO_FORM = 'BA' 
							 AND PE.N_COMPROBANTE = TO_CHAR (b.id_boleta)), 0, 'IMPAGA', 'PAGADA') c_estado,
					b.c_usuarioalt usuario
			FROM boletas_agr_cab b
			".$cond
			);


$db_pager = new DB_Pager($db_query,$m_autoquery,$_POST['page'],$_POST['rows'],$_POST['sidx'],$_POST['sord']);
$response = $db_pager->do_pager($par);

echo json_encode($response);

?>
