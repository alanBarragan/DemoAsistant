<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amsa_model extends CI_Model {
	private $dbamsa;
	public function __construct(){
		parent::__construct();
		$this->dbamsa = $this->load->database('amsa', true);
	}
	function getbalance($year,$mes) {
    switch($mes){
      case "enero":
        $mes="01";
        break;
      case "febrero":
        $mes="02";
        break;
      case "marzo":
        $mes="03";
        break;
      case "abril":
        $mes="04";
        break;
      case "mayo":
        $mes="05";
        break;
      case "junio":
        $mes="06";
        break;
      case "julio":
        $mes="07";
        break;
      case "agosto":
        $mes="08";
        break;
      case "septiembre":
        $mes="09";
        break;
      case "octubre":
        $mes="10";
        break;
      case "noviembre":
        $mes="11";
        break;
      case "diciembre":
        $mes="12";
        break;
      default :
        $mes="01";
        break;
    }
    $fecha = $year."-".$mes."-01";
		$query = "SELECT min_procesado, pro_cu, pro_mo, re_cu, re_mo, con_cu_filtrado, fil_cu, con_cu_tmf, con_mo_filtrado, fil_mo, con_mo_tmf 
                    FROM balance 
                    WHERE fecha = '".$fecha."' ;";
		$resultado = $this->dbamsa->query($query);
    $out = $resultado->result();
    return $out;
  }
  function getbalance_rango($fecha_ini,$fecha_fin) {
		$query = "SELECT min_procesado, pro_cu, pro_mo, re_cu, re_mo, con_cu_filtrado, fil_cu, con_cu_tmf, con_mo_filtrado, fil_mo, con_mo_tmf 
                    FROM balance 
                    WHERE fecha between '$fecha_ini' and '$fecha_fin' ;";
		$resultado = $this->dbamsa->query($query);
    $out = $resultado->result();
    return $out;
  }
  function getbalance_rango_resumen($fecha_ini,$fecha_fin) {
		$query = "SELECT select sum(min_procesado) as min_procesado, sum(pro_cu) as pro_cu, sum(pro_mo) as pro_mo, sum(re_cu) as re_cu, sum(re_mo) as re_mo, sum(con_cu_filtrado) as co_cu_filtrado,round(sum(fil_cu),2) as fil_cu , sum(con_cu_tmf) as con_cu_tmf, sum(con_mo_filtrado) as con_mo_filtrado,round(sum(fil_mo),2) as fil_mo, sum(con_mo_tmf) as con_mo_tmf
                    FROM balance 
                    WHERE fecha between '$fecha_ini' and '$fecha_fin' ;";
		$resultado = $this->dbamsa->query($query);
    $out = $resultado->result();
    return $out;
  }
}