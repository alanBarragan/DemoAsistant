<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CI_Model
{
    public function getCategorias()
    {
        $query = $this->db->get('cat_categoria');
        return $query->result();
    }

    public function getPortafolio()
    {
        $userID = isset($_SESSION['port_userInfo'][0]->usr_id) ? ", ". $_SESSION['port_userInfo'][0]->usr_id :"";
        $sql = "SELECT *, GROUP_CONCAT(cat_Clase SEPARATOR ' ')Clase, GROUP_CONCAT(cat_Nombre SEPARATOR ' | ')Categoria
        FROM users_portafolio
        JOIN cat_portafolio USING (port_id)
        JOIN categoria_portafolio USING (port_id)
        JOIN cat_categoria USING(cat_id)
        WHERE usr_id IN (1 $userID) AND port_Estatus = 1         
        GROUP BY port_id ORDER BY usr_id DESC;";
        $query = $this->db->query($sql);
        // log_message('error', "SQL getPortafolio: " . $sql);
        return $query->result();
    }

    public function getUser($user, $pass)
    {
        $pass = md5($pass);
        $this->db->where(array('usr_Cuenta' => $user, 'usr_Password'=> $pass, 'usr_Estatus'=> 1));
        $query = $this->db->get('cat_users');
        return $query->result();
    }
}