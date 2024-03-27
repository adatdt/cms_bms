<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_login extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function check_login($username)
	{
		// $query = $this->db->query("SELECT * FROM t_mtr_user WHERE username='$nik' AND password='$password'");
		$query = $this->db->query("SELECT
	MTR.id_seq,MTR.user_group_id,MTR.username,MTR.password,MTR.email,MTR.status,
	UG.group_name, MTR.first_name, MTR.last_name
FROM
	core.t_mtr_user MTR
	LEFT JOIN core.t_mtr_user_group UG ON UG.id_seq = MTR.user_group_id
WHERE
	UPPER(MTR.username) = UPPER('$username') AND MTR.status=1
	");
// AND MTR.PASSWORD = '$password'
		// $query= $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			return false;
		}
	}

	public function checkPriv($menu, $user_group_id)
	{
		return $this->db->query("select * from core.t_mtr_privilege a
		join core.t_mtr_menu b on a.menu_id=b.id_seq and upper(b.name)=upper('".$menu."') and b.status=1
		join core.t_mtr_user_group c on a.user_group_id=c.id_seq and c.id_seq=$user_group_id
		where a.status=1");
	}

}

/* End of file m_login.php */
/* Location: ./application/models/m_login.php */