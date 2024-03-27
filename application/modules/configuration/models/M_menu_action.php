<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_menu_action extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function get()
	{
		$data = array();
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'ASC';

		$where ="where status=1";

		if (!empty($search))
		{
			$where .="and(name ilike '%$search%')";
		}

		$sql =  'SELECT * FROM core.t_mtr_menu_action '.$where.' ORDER BY '.$sort.' '.$order.'';

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu_action','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu_action','delete');

	    	// cek apakah sudah pernah masuk ke menu detail
	    	$checkMenuDetail=$this->m_global->getDataById("core.t_mtr_menu_detail","action_id=".$r['id_seq'])->num_rows();

	    	$action = '';

	    	if($edit){

	    		if($checkMenuDetail>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, menu action already paired to menu'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit_menu(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    	}

	    	if($delete){

	    		if($checkMenuDetail>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, menu action already paired to menu'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="delete_menu(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    	};

	    	$r['id_seq'] 	= encode($r['id_seq']);
	    	$r['action'] 	= $action;

	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;

		return $data;
	}

	public function update($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_menu_action', $data);
	}

	public function delete($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_menu_action', $data);
	}

	public function check_menu($name,$input='')
	{
		if ($input!= '') {
			return $this->db->query('SELECT * FROM core.t_mtr_menu_action WHERE UPPER(name)=UPPER(\''.$name.'\') AND status=1 AND  UPPER(name) != \''.$input.'\'')->result();
		}else{
			return $this->db->query('SELECT * FROM core.t_mtr_menu_action WHERE UPPER(name)=UPPER(\''.$name.'\') AND status=1')->result();
		}
	}

	public function getname($id)
	{
		$qry = $this->db->query('SELECT name FROM core.t_mtr_menu_action WHERE id_seq='.$id.'')->row();
		return $qry->name;
	}

	public function insert($data)
	{
		return $this->db->insert('core.t_mtr_menu_action', $data);
	}       

}

/* End of file M_menu_detail.php */
/* Location: ./application/models/M_menu_detail.php */