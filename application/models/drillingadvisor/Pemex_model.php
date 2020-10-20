<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pemex_model extends CI_Model
{
    private $dbdrilling;
    public function __construct()
    {
        parent::__construct();
        $this->dbdrilling = $this->load->database('drillingadvisor', true);
    }

    public function problemTime($campo)
    {
        $query = "	SELECT
					CASE GREATEST(
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 0 AND START_MD < 1000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 1001 AND END_MD < 2000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 2001 AND END_MD < 3000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 3001 AND END_MD < 4000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 4001 AND END_MD < 5000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
				                    (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 5001 AND END_MD < 6000 AND DESC_CAMPO LIKE '%" . $campo . "%'),
									(SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 6001 AND END_MD < 7000 AND DESC_CAMPO LIKE '%" . $campo . "%')
								)
						WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 0 AND END_MD < 1000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '1000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 1001 AND END_MD < 2000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '2000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 2001 AND END_MD < 3000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '3000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 3001 AND END_MD < 4000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '4000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 4001 AND END_MD < 5000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '5000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 5001 AND END_MD < 6000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '6000'
				        WHEN (SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%LUZ%' AND START_MD > 6001 AND END_MD < 7000 AND DESC_CAMPO LIKE '%" . $campo . "%') THEN '7000'
					END as mayorIntervalo;";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->row();
        return $out;
    }

    public function eficiencia($campo)
    {
        $query = "	SELECT
						WELL_NAME as pozoName,
					    DOD_START_MD as starMD,
					    DOD_END_MD as endMD,
					    EQUIPMENT_NAME as name,
					    MANUFACTURER as manufacter,
					    MODEL_NAME as modelo,
					    RATE_OF_PENETRATION_INPUT as taza
					FROM
					    drill_bit
					WHERE
					    RATE_OF_PENETRATION_INPUT > 100
						AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%'
					ORDER BY RATE_OF_PENETRATION_INPUT DESC
					LIMIT 5;";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }

    public function usePistola($val, $campo)
    {
        $query = "	SELECT
						WELL_NAME as pozoName,
					    TOP_INTERVAL_MD as startMD,
					    BASE_INTERVAL_MD as endMD,
					    LENGTH as longitud,
					    RESERVOIR_NAME as yacimiento,
					    DESC_CAMPO as campo
					FROM
					    perforated_interval
					WHERE
						TOP_INTERVAL_MD >= " . (isset($val[0]) ? $val[0] : 0) . "
					    AND BASE_INTERVAL_MD < " . (isset($val[1]) ? $val[1] : 100) . "
					    AND LENGTH != 0
					    AND RESERVOIR_NAME != ''
					    AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%';";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }

    public function getPozos()
    {
        $query = '	SELECT DISTINCT
					    WELL_NAME AS name, DESC_CAMPO AS campo
					FROM
					    drilling_summary;';
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }

    public function recomendacionBarrena($val, $campo)
    {
        $query = "	SELECT
						WELL_NAME as pozoName,
						DOD_START_MD as startMD,
						DOD_END_MD as endMD,
						EQUIPMENT_NAME as name,
						MANUFACTURER as manufacter,
						MODEL_NAME as modelo,
						RATE_OF_PENETRATION_INPUT as taza
					FROM
						drill_bit
					WHERE
						DOD_START_MD < DOD_END_MD
						AND RATE_OF_PENETRATION_INPUT > 100
					    AND DOD_START_MD >= " . (isset($val[0]) ? $val[0] : 0). "
					    AND DOD_END_MD <= " . (isset($val[1]) ? $val[1] : 1000) . "
						AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%'
					ORDER BY RATE_OF_PENETRATION_INPUT DESC
					LIMIT 10;";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }

    /*function rangoProductor($campo) {
    $query = "    SELECT DISTINCT
    RESERVOIR_NAME as yacimiento,
    (select
    concat(min(BASE_INTERVAL_MD),',',max(TOP_INTERVAL_MD)) as intervalo
    from
    perforated_interval
    WHERE
    CURRENT_STATUS LIKE '%productor%'
    AND DESC_CAMPO LIKE '%".substr($campo,0,3)."%'
    AND RESERVOIR_NAME = yacimiento) as inter
    FROM
    perforated_interval
    WHERE
    CURRENT_STATUS LIKE '%productor%'
    AND DESC_CAMPO LIKE '%".substr($campo,0,3)."%'
    AND RESERVOIR_NAME != '';";
    $resultado = $this->dbdrilling->query($query);
    $out = $resultado->result();
    return $out;
    }*/

    public function rangoProductor($campo)
    {
        $setValues = $this->dbdrilling->query("SELECT  min(TOP_INTERVAL_MD) as min, max(BASE_INTERVAL_MD) as mas, max(BASE_INTERVAL_MD) - min(TOP_INTERVAL_MD) as longitud,ceil((max(BASE_INTERVAL_MD) - min(TOP_INTERVAL_MD))/4) as intervalo FROM perforated_interval WHERE CURRENT_STATUS LIKE '%productor%' AND DESC_CAMPO LIKE '%$campo%';")->row();
        $int = (int) $setValues->intervalo;
        $min = (int) $setValues->min;
        $mas = (int) $setValues->mas;
        $intCero = array('result' => $query = $this->dbdrilling->query("	SELECT
											RESERVOIR_NAME as yacimiento,
											WELL_NAME as pozoName,
										    TOP_INTERVAL_MD,
										    BASE_INTERVAL_MD
										FROM
											perforated_interval
										WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND ($min < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + $int));")->result(),
            'cimabase' => $this->dbdrilling->query("SELECT
															concat('de ', min(TOP_INTERVAL_MD), ' a ',max(BASE_INTERVAL_MD)) as cimabase
														FROM
															perforated_interval
														WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND ($min < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + $int));")->row()->cimabase,
            'match_result' => count($query));

        $intUno = array('result' => $query = $this->dbdrilling->query("	SELECT
											RESERVOIR_NAME as yacimiento,
											WELL_NAME as pozoName,
										    TOP_INTERVAL_MD,
										    BASE_INTERVAL_MD
										FROM
											perforated_interval
										WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+$int+1) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + (2*$int) + 1));")->result(),
            'cimabase' => $this->dbdrilling->query("SELECT
															concat(min(TOP_INTERVAL_MD), ' y ',max(BASE_INTERVAL_MD)) as cimabase
														FROM
															perforated_interval
														WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+$int+1) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + (2*$int) + 1));")->row()->cimabase,

            'match_result' => count($query));
        $intDos = array('result' => $query = $this->dbdrilling->query("	SELECT
											RESERVOIR_NAME as yacimiento,
											WELL_NAME as pozoName,
										    TOP_INTERVAL_MD,
										    BASE_INTERVAL_MD
										FROM
											perforated_interval
										WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+(2*$int)+2) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + (3*$int) + 2));")->result(),
            'cimabase' => $this->dbdrilling->query("SELECT
															concat(min(TOP_INTERVAL_MD), ' y ',max(BASE_INTERVAL_MD)) as cimabase
														FROM
															perforated_interval
														WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+(2*$int)+2) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < ($min + (3*$int) + 2));")->row()->cimabase,

            'match_result' => count($query));
        $intTres = array('result' => $query = $this->dbdrilling->query("	SELECT
											RESERVOIR_NAME as yacimiento,
											WELL_NAME as pozoName,
										    TOP_INTERVAL_MD,
										    BASE_INTERVAL_MD
										FROM
											perforated_interval
										WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+(3*$int)+3) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < $mas);")->result(),
            'cimabase' => $this->dbdrilling->query("SELECT
															concat(min(TOP_INTERVAL_MD), ' y ',max(BASE_INTERVAL_MD)) as cimabase
														FROM
															perforated_interval
														WHERE
											CURRENT_STATUS LIKE '%productor%'
											AND DESC_CAMPO LIKE '%$campo%'
										    AND (($min+(3*$int)+3) < TOP_INTERVAL_MD AND BASE_INTERVAL_MD < $mas);")->row()->cimabase,

            'match_result' => count($query));
        return array('int' => $int, 'cero' => $intCero, 'uno' => $intUno, 'dos' => $intDos, 'tres' => $intTres);
    }
    public function get_delimited($str, $delimiter = '"')
    {
        $escapedDelimiter = preg_quote($delimiter, '/');
        if (preg_match_all('/' . $escapedDelimiter . '(.*?)' . $escapedDelimiter . '/s', $str, $matches)) {
            return $matches[1];
        }
    }
    public function tuberiaRevestimiento($campo)
    {
        $query = "	SELECT DISTINCT
					    NAME AS nombre
					FROM
					    trs
					WHERE
					    DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%';";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        $comilla = array();
        foreach ($out as $k) {
            array_push($comilla, $this->get_delimited($k->nombre));
        }
        return $comilla;
    }

    public function unidadEstratigrafica($campo)
    {
        /*$query = "    SELECT
        gc.WELL_NAME as pozoName,
        group_concat(DISTINCT concat(gc.STRATIGRAPHIC_LAYER_NAME, ' [',gc.BOREHOLE_POINT_MD_TOP,', ',gc.BOREHOLE_POINT_MD_BASE,']') separator '<br>') as estratigrafia
        FROM
        geologic_column gc JOIN trs tr ON(gc.UBHI = tr.UBHI)
        WHERE
        gc.DESC_CAMPO LIKE '%".substr($campo,0,3)."%'
        GROUP BY
        gc.WELL_NAME;";*/
        $query = "	SELECT DISTINCT
						gc.STRATIGRAPHIC_LAYER_NAME as estratigrafia
					FROM
						geologic_column gc JOIN trs tr ON(gc.UBHI = tr.UBHI)
					WHERE
						gc.DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%'
					GROUP BY
						gc.WELL_NAME;";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }

    public function porcentaje($campo, $flag)
    {
        switch ($flag) {
            case 0:
                $query = "	SELECT
								((SELECT count(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%PESCA%' OR DAILY_REPORT_SUMMARY LIKE '%MOLINO%' AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%')*100/
								(SELECT count(*) FROM drilling_summary WHERE DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%') ) as porcentaje;";
                $resultado = $this->dbdrilling->query($query);
                $out = $resultado->row();
                break;
            case 1:
                $query = "	SELECT
								(SELECT COUNT(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%PESCA%' AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%') as pezcado,
								(SELECT COUNT(*) FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%MOLINO%' AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%') as molino";
                $resultado = $this->dbdrilling->query($query);
                $out = $resultado->row();
                break;
            case 2:
                $query = "	SELECT DISTINCT
								WELL_NAME as pozoName,
								START_MD as cima,
								DAILY_REPORT_SUMMARY as reporte
							FROM
								drilling_summary
							WHERE
								(DAILY_REPORT_SUMMARY LIKE '%PESCA%'
								OR DAILY_REPORT_SUMMARY LIKE '%MOLINO%')
								AND DESC_CAMPO LIKE '%" . substr($campo, 0, 3) . "%';";
                $resultado = $this->dbdrilling->query($query);
                $out = $resultado->result();
                break;
            default:
                # code...
                break;
        }

        return $out;
    }

    public function problemasCount($campo)
    {
        $query = "SELECT count(*) as tot FROM drilling_summary WHERE DAILY_REPORT_SUMMARY LIKE '%PESCA%' OR DAILY_REPORT_SUMMARY LIKE '%MOLINO%' AND DESC_CAMPO LIKE '" . substr($campo, 0, 4) . "';";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->row();
        return $out;
    }

    public function problemasCountTot($campo)
    {
        $query = "SELECT count(*) as tot from drilling_summary where  DESC_CAMPO LIKE '" . substr($campo, 0, 4) . "';";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->row();
        return $out;
    }

    public function getPozosRetrasos($array, $campo)
    {
        $query = "	SELECT
						WELL_NAME as pozoName
					FROM
						drilling_summary
					WHERE
						DAILY_REPORT_SUMMARY LIKE '%LUZ%'
						AND START_MD > $array[0]
						AND END_MD < $array[1]
						AND DESC_CAMPO LIKE '%" . $campo . "%'";
        $resultado = $this->dbdrilling->query($query);
        $out = $resultado->result();
        return $out;
    }
}
