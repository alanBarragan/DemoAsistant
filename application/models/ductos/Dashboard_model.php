<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

  private $dbductos;
	public function __construct(){
		parent::__construct();
		$this->dbductos = $this->load->database('ductos', true);
	}

	public function coordenadas(){
        $sql = "select Latitud, Longitud, Flujo, FechaIni from alertas order by FechaIni";
        $query = $this->dbductos->query($sql);
        return $query->result();
    }

    public function get_presiones($f_inicial, $f_final) {
    	$sql = "select distinct fecha, presion, estacion from presion_tramo where fecha >= ? and fecha <= ? GROUP BY fecha, estacion order by fecha desc";
      $query = $this->dbductos->query($sql, array($f_inicial, $f_final));
      // log_message('error',"SQL a ejecutar: ". $this->dbductos->last_query());
      return $query->result();
    }

    public function get_kms($f_inicial, $f_final) {
    	$sql = "select DATE_FORMAT(Fecha, '%d-%m-%Y %T') as fecha, km from eventos where Fecha >= ? and Fecha <= ?";
      $query = $this->dbductos->query($sql, array(date("Y-m-d H:i:s", strtotime($f_inicial)), date("Y-m-d H:i:s", strtotime($f_final))));
      return $query->result();
    }

    public function get_lostFlow($estado) {
      $sql = "SELECT km, Flujo FROM alertas WHERE Estado = '". $estado ."'";      
      $query = $this->dbductos->query($sql);
      return $query->result();
    }
}

/* End of file dashboard_model.php */
/* Location: ./application/models/dashboard_model.php */