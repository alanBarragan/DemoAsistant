<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ductos_model extends CI_Model {
	private $dbductos;
	public function __construct(){
		parent::__construct();
		$this->dbductos = $this->load->database('ductos', true);
	}
	public function getAlerts($data)
	{
		date_default_timezone_set('UTC');
		$hoy = date("Y-m-d");
		$municipio =  isset($data['municipio']) && $data['municipio'] != ''? $data['municipio'] : "";
		$km = isset($data['km']) && $data['km'] != '' ? $data['km'] : "";
		$fecha = isset($data['fecha']) && $data['fecha'] != '' ? $data['fecha']:"";					

		$where = " WHERE ";
		$where .= !empty($municipio) ? " Municipio = '".$municipio."'":"";// consulta si viene municipio
		$where .= (strlen($where) > 7 && !empty($km) ? " AND ": ""). (!empty($km)   ? " km BETWEEN ". ((int)$km - 2) ." AND ". ((int)$km + 2) :""); // consulta si viene km
		if($fecha == $hoy){// fecha maxima si viene fecha actual			
			$sql = "SELECT MAX(DATE(FechaIni)) fecha FROM alertas ".(strlen($where) > 7 ? $where : "");
			$query = $this->dbductos->query($sql);
			$row = $query->row();
			$fecha = $row->fecha;
		}
		$where .= (strlen($where) > 7 && !empty($fecha) ? " AND ": ""). (!empty($fecha) ? " date(FechaIni) = '".$fecha."'":"");	// consulta si viene fecha	
		$order = " ORDER BY FechaIni DESC;";// ordenamos de manera descendente por fecha
		$sql = "SELECT * FROM alertas ".$where .$order;
		
		log_message('error',"SQL a ejecutar: ". $sql);
		if (!empty($municipio) || !empty($fecha) || !empty($km) ) {			
			$resultado = $this->dbductos->query($sql);
			return $resultado->result();
		}else{
			return array();
		}
		
	}
	public function getTomas($municipio, $tipo)
	{
		$this->dbductos->where('municipio',$municipio);
		$this->dbductos->like('hermetica',$tipo);
		$resultado = $this->dbductos->get('reparaciones');
		return $resultado->result();
	}
	public function getMaxDerr(){
		$sql = '
			SELECT * 
			FROM (SELECT estado, municipio, SUM(volderr) AS totalder FROM reparaciones GROUP BY estado, municipio) AS suma
			HAVING suma.totalder = 
				(SELECT MAX(a.total)
				FROM
					(SELECT estado, municipio, SUM(volderr) AS total
					FROM reparaciones GROUP BY estado, municipio) AS a);';
		return $this->dbductos->query($sql)->result();
	}

	public function getDerrMun($municipio){
		$sql = 'SELECT estado, municipio, SUM(volderr) AS totalder FROM reparaciones WHERE municipio LIKE "%' . $municipio . '%" GROUP BY estado, municipio;';
		return $this->dbductos->query($sql)->result();
	}

	public function getTotalDerr(){
		$sql = 'SELECT SUM(volderr) AS totalder FROM reparaciones;';
		return $this->dbductos->query($sql)->result();
	}
}

/* End of file ductos_model.php */
/* Location: ./application/models/ductos_model.php */