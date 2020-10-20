<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Discovery_model extends CI_Model {
	private $dbdrilling;
	public function __construct(){
		parent::__construct();
		$this->dbdrilling = $this->load->database('drillingadvisor', true);
	}
	
	/*function saveNote($in_fecha,$in_medio,$in_genero,$in_redaccion,$in_tema,$in_actor,$in_gabinete_tematico,$in_dependencia,$in_contenido,$in_calificacion) {

		$query = " 	INSERT INTO 
						inventario_notas (in_fecha, in_medio, in_genero, in_redaccion, in_tema, in_actor, in_gabinete_tematico, in_dependencia, in_contenido, in_calificacion) 
					VALUES 
						('$in_fecha', '$in_medio', '$in_genero', '$in_redaccion', '$in_tema', '$in_actor', '$in_gabinete_tematico', '$in_dependencia', '$in_contenido', '$in_calificacion');";
        $this->dbdrilling->query($query);
	}*/

	function getPromedio() {
		$query = "  SELECT DISTINCT 
						AVG(items_deals_bestPrice_displayPrice) as promedio 
					FROM hoteles_list_list;";
        $resultado = $this->dbdrilling->query($query);
            $out = $resultado->row();
            return $out;
	}

	function getFuente() {
		$query = "	SELECT DISTINCT
						items_deals_alternative_items_name as fuente
					FROM
						hoteles_list_list
					HAVING
						items_deals_alternative_items_name IS NOT NULL
					    AND items_deals_alternative_items_name != 'hoteles_list_list.com';";
		$resultado = $this->dbdrilling->query($query);
            $out = $resultado->result();
            return $out;
	}
}