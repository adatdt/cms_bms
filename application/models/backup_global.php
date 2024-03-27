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
		$query = $this->db->query("SELECT DISTINCT(M.id),P.menu_id,M.parent_id,M.name,M.slug,M.icon,M.order FROM t_mtr_privilege P
			JOIN t_mtr_menu M ON M.id = P.menu_id AND P.status = 1 AND M.status = 1
			JOIN t_mtr_menu_detail MD ON MD.menu_id = M.id AND MD.status = 1
			JOIN t_mtr_user U ON U.user_group_id = P.user_group_id
			JOIN t_mtr_menu_action MA ON MA.id = MD.action_id AND MA.name = 'view'
			WHERE U.user_group_id = $user_group_id ORDER BY M.order");
		
		$data = array();

		foreach ($query->result() as $key => $value) {
			$value->action = $this->menuAction($user_group_id,$value->id);
			$data[$value->parent_id][]=$value; 
		}

		return $data;

		// return $query->result();
	}

	public function menuAction($user_group_id,$menu_id)
	{
		$data = array();
		$query = $this->db->query("SELECT DISTINCT(MA.id),M.order, MA.name AS action FROM t_mtr_privilege P JOIN t_mtr_menu M ON M.id = P.menu_id AND P.status = 1 AND M.status = 1 AND M.id = $menu_id JOIN t_mtr_menu_detail MD ON MD.menu_id = M.id AND MD.status = 1 JOIN t_mtr_user U ON U.user_group_id = P.user_group_id JOIN t_mtr_menu_action MA ON MA.id = MD.action_id WHERE U.user_group_id = $user_group_id ORDER BY M.order ASC");

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
			( M.ID ),
			P.menu_id,
			M.parent_id,
			M.NAME,
			M.slug,
			M.icon,
			M.ORDER,
			P.status
			FROM
			t_mtr_privilege P
			JOIN t_mtr_menu_detail MD ON MD.menu_id = P.menu_id AND p.menu_detail_id = MD.id
			AND MD.status = 1 
			AND P.status = 1
			JOIN t_mtr_user U ON U.user_group_id = P.user_group_id
			JOIN t_mtr_menu_action MA ON MA.ID = MD.action_id 
			AND MA.NAME = '$action' 
			JOIN t_mtr_menu M ON M.ID = P.menu_id
			AND P.status = 1 
			AND M.status = 1 
			AND M.slug = '$slug'
			WHERE
			U.user_group_id = $user_group_id 
			AND P.status = 1
			ORDER BY
			M.ORDER ASC");
		return $query->result();
	}
}

/* End of file M_global.php */
/* Location: ./application/models/M_global.php */