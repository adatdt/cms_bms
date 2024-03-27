<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_global extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function getMenu($user_group_id)
	{
		// $query = $this->db->query("SELECT M.id,M.parent_id,M.name,M.slug,M.icon FROM t_mtr_privilege P
		// 	JOIN t_mtr_menu M ON M.id = P.menu_id AND P.status = 1
		// 	JOIN t_mtr_menu_detail MD ON MD.menu_id = M.id
		// 	JOIN t_mtr_user U ON U.user_group_id = P.user_group_id
		// 	JOIN t_mtr_menu_action MA ON MA.id = MD.action_id AND MA.name = 'view'
		// 	WHERE U.user_group_id = $user_group_id");
		$query = $this->db->query("SELECT DISTINCT(M.id_seq),P.menu_id,M.parent_id,M.name,M.slug,M.icon,M.menu_order FROM core.t_mtr_privilege P
			JOIN core.t_mtr_menu M ON M.id_seq = P.menu_id AND P.status = 1 AND M.status = 1
			JOIN core.t_mtr_menu_detail MD ON MD.id_seq = P.menu_detail_id AND MD.status = 1
			JOIN core.t_mtr_user U ON U.user_group_id = P.user_group_id
			JOIN core.t_mtr_menu_action MA ON MA.id_seq = MD.action_id AND MA.name = 'view'
			WHERE U.user_group_id = $user_group_id ORDER BY M.menu_order");
		
		$data = array();

		foreach ($query->result() as $key => $value) {
			$value->action = $this->menuAction($user_group_id,$value->id_seq);
			$data[$value->parent_id][]=$value; 
		}

		return $data;

		// return $query->result();
	}

	public function menuAction($user_group_id,$menu_id)
	{
		$data = array();
		$query = $this->db->query("SELECT DISTINCT(MA.id_seq),M.menu_order, MA.name AS action FROM core.t_mtr_privilege P JOIN core.t_mtr_menu M ON M.id_seq = P.menu_id AND P.status = 1 AND M.status = 1 AND M.id_seq = $menu_id JOIN core.t_mtr_menu_detail MD ON MD.menu_id = M.id_seq AND MD.status = 1 JOIN core.t_mtr_user U ON U.user_group_id = P.user_group_id JOIN core.t_mtr_menu_action MA ON MA.id_seq = MD.action_id WHERE U.user_group_id = $user_group_id ORDER BY M.menu_order ASC");

		foreach ($query->result() as $key => $value) {
			$data[] = $value->action;
		}

		return $data;
	}

	public function menuAccess($user_group_id,$slug,$action)
	{
		// $query = $this->db->query("SELECT DISTINCT(M.id),P.menu_id,M.parent_id,M.name,M.slug,M.icon,M.order,P.status FROM t_mtr_privilege P
		// 	LEFT JOIN t_mtr_menu M ON M.id = P.menu_id AND P.status = 1 AND M.status = 1  AND M.slug = '$slug' JOIN t_mtr_menu_detail MD ON MD.menu_id = M.id AND MD.status = 1 AND P.status =1 JOIN t_mtr_user U ON U.user_group_id = P.user_group_id JOIN t_mtr_menu_action MA ON MA.id = MD.action_id AND MA.name = '$action' WHERE U.user_group_id = $user_group_id AND P.status=1 ORDER BY M.order ASC");
		$query = $this->db->query("SELECT DISTINCT
			( M.id_seq ),
			P.menu_id,
			M.parent_id,
			M.NAME,
			M.slug,
			M.icon,
			M.menu_order,
			P.status
			FROM
			core.t_mtr_privilege P
			JOIN core.t_mtr_menu_detail MD ON MD.menu_id = P.menu_id AND p.menu_detail_id = MD.id_seq
			AND MD.status = 1 
			AND P.status = 1
			JOIN core.t_mtr_user U ON U.user_group_id = P.user_group_id
			JOIN core.t_mtr_menu_action MA ON MA.id_seq = MD.action_id 
			AND MA.NAME = '$action' 
			JOIN core.t_mtr_menu M ON M.id_seq = P.menu_id
			AND P.status = 1 
			AND M.status = 1 
			AND M.slug = '$slug'
			WHERE
			U.user_group_id = $user_group_id 
			AND P.status = 1
			ORDER BY
			M.menu_order ASC");
		return $query->result();
	}


	function getData($table,$order)
	{
		return $this->db->query("select * from $table $order")->result();
	}

	function getDataById($table,$where)
	{
		return $this->db->query("select * from $table where $where");
	}

	function insert($table,$data)
	{
		$this->db->trans_begin();

        $this->db->insert($table,$data);

        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } 
        else 
        {
            $this->db->trans_commit();
            return true;
        }
	}

	public function update($table,$data,$where)
    {
    	$this->db->trans_begin();
        $this->db->where($where);
        $this->db->update($table,$data);

        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return FALSE;
        }
        else
        {
                $this->db->trans_commit();
                return TRUE;
        }
    }

    public function deleteData($table,$where)
    {
    	$this->db->trans_begin();
    	$this->db->query("delete from $table where $where");

    	if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return FALSE;
        }
        else
        {
                $this->db->trans_commit();
                return TRUE;
        }
    }

    public function masterDisable($table,$id,$data)
    {
    	$this->db->where('id_seq', $id);
		return $this->db->update($table, $data);
    }

}

/* End of file M_global.php */
/* Location: ./application/models/M_global.php */