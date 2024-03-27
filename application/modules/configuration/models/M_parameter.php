<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_parameter extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function getData()
	{
		return $this->db->query("SELECT * FROM master.t_mtr_config WHERE status=1;")->result();
	}

	public function action_add($data)
	{
		return $this->db->update_batch('master.t_mtr_config', $data, 'name');
	}

}

/* End of file M_parameter.php */
/* Location: ./application/models/M_parameter.php */