<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Halliburton_model extends CI_Model
{
    public function assemblyQueryPerdidas($data)
    {
        $hoy = date("Y-m-d");
        $pozo = isset($data['pozo']) && $data['pozo'] != '' ? $data['pozo'] : "";
        $campo = isset($data['campo']) && $data['campo'] != '' ? $data['campo'] : "";
        $profundidad = isset($data['profundidad']) && $data['profundidad'] != '' ? $data['profundidad'] : "";
        $fecha = isset($data['fecha']) && $data['fecha'] != '' ? $data['fecha'] : "";
        $perdida_perforando = isset($data['perdida_perforando']) && $data['perdida_perforando'] != '' ? $data['perdida_perforando'] : "";
        $perdida_viajando = isset($data['perdida_viajando']) && $data['perdida_viajando'] != '' ? $data['perdida_viajando'] : "";
        $compania = isset($data['compania']) && $data['compania'] != '' ? $data['compania'] : "";
        $densidad = isset($data['densidad']) && $data['densidad'] != '' ? $data['densidad'] : "";

        $fields = isset($data['fields']) && $data['fields'] != '' ? $data['fields'] : array();
        $orders = isset($data['orders']) && $data['orders'] != '' ? $data['orders'] : array();
        $groups = isset($data['groups']) && $data['groups'] != '' ? $data['groups'] : array();
        $conditions = isset($data['condiciones']) && $data['condiciones'] != '' ? $data['condiciones'] : array();

        $where = " WHERE ";

        $where .= !empty($pozo) ? " Pozo = '" . $pozo . "'" : ""; // consulta si viene pozo
        $where .= (strlen($where) > 7 && !empty($campo) ? " AND " : "") .
            (!empty($campo) ? " Well_Id = '" . $campo . "'" : "");
        $where .= (strlen($where) > 7 && !empty($profundidad) ? " AND " : "") .
            (!empty($profundidad) ? " Profundidad BETWEEN " . $profundidad['min'] . " AND " . $profundidad['max'] : "");
        $where .= (strlen($where) > 7 && !empty($fecha) ? " AND " : "") .
            (!empty($fecha) ? " date(Fecha_Reporte) = '" . $fecha . "'" : ""); // consulta si viene fecha
        $where .= (strlen($where) > 7 && !empty($perdida_perforando) ? " AND " : "") .
            (!empty($perdida_perforando) ? " Perdida_Fluido_Perforando BETWEEN " . $perdida_perforando['min'] . " AND " . $perdida_perforando['max'] : "");
        $where .= (strlen($where) > 7 && !empty($perdida_viajando) ? " AND " : "") .
            (!empty($perdida_viajando) ? " Perdida_Fluido_Viajando BETWEEN " . $perdida_viajando['min'] . " AND " . $perdida_viajando['max'] : "");
        $where .= (strlen($where) > 7 && !empty($compania) ? " AND " : "") .
            (!empty($compania) ? " Compania = '" . $compania . "'" : "");
        $where .= (strlen($where) > 7 && !empty($densidad) ? " AND " : "") .
            (!empty($densidad) ? " densidad BETWEEN " . ((int) $densidad - 2) . " AND " . ((int) $densidad + 2) : "");

        $where .= (strlen($where) > 7 ? " AND " : "") .
            (" (Perdida_Fluido_Perforando + Perdida_Fluido_Viajando >0) ");

        $campos = '*';
        $group = ' GROUP BY ';
        $order = ' ORDER BY ';

        $campos = (count($fields) > 0 ? implode(", ", $fields) : $campos);
        $group = (count($groups) > 0 ? $group . implode(", ", $groups) : '');
        $order = (count($orders) > 0 ? $order . implode(", ", $orders) : '');
        $wheres = (count($conditions) > 0 ? implode(" AND ", $conditions) : '');

        $sql = "SELECT $campos FROM Perdidas_Fluido " .
            (strlen($where) > 7 ? $where : "") .
            (strlen($where) > 7 ? $wheres : "") .
            (strlen($group) > 10 ? $group : "") .
            (strlen($order) > 10 ? $order : "");
        return $sql;
    }

    public function getPerdidas($data)
    {
        $dbHalliburton = $this->load->database('halliburton', true);
        $sql = $this->assemblyQueryPerdidas($data);
        log_message('error', "SQL getPerdidas: " . $sql);
        $resultado = $dbHalliburton->query($sql);
        return $resultado->result();
    }

    public function getListaPozos($data)
    {
        $dbHalliburton = $this->load->database('halliburton', true);
        $sql = $this->assemblyQueryPerdidas($data);
        $sql = str_replace('*', "Pozo", $sql);
        $groupby = " GROUP BY Pozo ";
        $sql .= $groupby;
        log_message('error', "SQL getListaPozos: " . $sql);
        $resultado = $dbHalliburton->query($sql);
        return $resultado->result();
    }

    // Reporte_actividades
    public function assemblyQueryReporte($data)
    {
        $hoy = date("Y-m-d");
        $campo = isset($data['campo']) && $data['campo'] != '' ? $data['campo'] : "";
        $pozo = isset($data['pozo']) && $data['pozo'] != '' ? $data['pozo'] : "";
        $compania = isset($data['compania']) && $data['compania'] != '' ? $data['compania'] : "";
        $actividad = isset($data['actividad']) && $data['actividad'] != '' ? $data['actividad'] : "";
        $clase = isset($data['clase']) && $data['clase'] != '' ? $data['clase'] : "";
        $duracion = isset($data['duracion']) && $data['duracion'] != '' ? $data['duracion'] : "";

        $fields = isset($data['fields']) && $data['fields'] != '' ? $data['fields'] : array();
        $orders = isset($data['orders']) && $data['orders'] != '' ? $data['orders'] : array();
        $groups = isset($data['groups']) && $data['groups'] != '' ? $data['groups'] : array();
        $conditions = isset($data['condiciones']) && $data['condiciones'] != '' ? $data['condiciones'] : array();

        $where = " WHERE ";

        $where .= !empty($pozo) ? " Pozo = '" . $pozo . "'" : ""; // consulta si viene pozo
        $where .= (strlen($where) > 7 && !empty($campo) ? " AND " : "") .
            (!empty($campo) ? " Well_Id = '" . $campo . "'" : "");
        $where .= (strlen($where) > 7 && !empty($compania) ? " AND " : "") .
            (!empty($compania) ? " Compania_Servicios = '" . $compania . "'" : "");
        $where .= (strlen($where) > 7 && !empty($actividad) ? " AND " : "") .
            (!empty($actividad) ? " Actividad = '" . $actividad . "'" : "");
        $where .= (strlen($where) > 7 && !empty($clase) ? " AND " : "") .
            (!empty($clase) ? " Clase = '" . $clase . "'" : "");
        $where .= (strlen($where) > 7 && !empty($duracion) ? " AND " : "") .
            (!empty($duracion) ? " Duracion BETWEEN " . ((int) $duracion - 2) . " AND " . ((int) $duracion + 2) : "");

        $campos = '*';
        $group = ' GROUP BY ';
        $order = ' ORDER BY ';
        $num = count($fields);
        if ($num > 0) {
            $campos = '';
            foreach ($fields as $key => $value) {
                $campos .= $value;
                $campos .= (($num - 1) == $key ? '' : ', ');
            }
        }

        $wheres = (count($conditions) > 0 ? implode(" AND ", $conditions) : '');

        $num = count($orders);
        if ($num > 0) {
            foreach ($orders as $key => $value) {
                $order .= $value;
                $order .= (($num - 1) == $key ? '' : ', ');
            }
        }

        $num = count($groups);
        if (count($groups) > 0) {
            foreach ($groups as $key => $value) {
                $group .= $value;
                $group .= (($num - 1) == $key ? '' : ', ');
            }
        }

        $sql = "SELECT $campos FROM Reporte_Actividades " .
            (strlen($where) > 7 ? $where : "") .
            (strlen($where) > 7 ? $wheres : "") .
            (strlen($group) > 10 ? $group : "") .
            (strlen($order) > 10 ? $order : "");

        return $sql;
    }

    public function getReporteAct($params)
    {
        $dbHalliburton = $this->load->database('halliburton', true);
        $sql = $this->assemblyQueryReporte($params);
        log_message('error', "SQL getReporteAct: " . $sql);
        $resultado = $dbHalliburton->query($sql);
        return $resultado->result();
    }

}
