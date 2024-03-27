<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_user_group extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function check_data($field,$value,$input='')
	{
		if ($input !='') {
			return $this->db->query('SELECT * FROM core.t_mtr_user_group WHERE UPPER('.$field.') = UPPER(\''.$value.'\') AND status=1 AND id_seq != '.$input.'')->result();
		}else{
			return $this->db->query('SELECT * FROM core.t_mtr_user_group WHERE UPPER('.$field.') = UPPER(\''.$value.'\') AND status=1')->result();
		}
	}

	public function get_edit($id)
	{
		return $this->db->query('SELECT * FROM core.t_mtr_user_group WHERE id_seq='.$id.'')->result();
	}

	public function get_detail($id)
	{
		return $this->db->query("
			select  b.access_type, a.* from core.t_mtr_user_group a
				left join core.t_mtr_access_type b on a.access_type_id=b.id_seq
				where a.id_seq=$id
			");
	}

	public function insert($data)
	{
		return $this->db->insert('core.t_mtr_user_group', $data);
	}

	public function update($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_user_group', $data);
	}

	public function delete($id,$data)
	{
		$this->db->where('id_seq', $id);
		return $this->db->update('core.t_mtr_user_group', $data);
	}

	public function get()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'desc';

		$where = 'WHERE a.status=1';

		if (!empty($search))
		{
			$where .="and (a.group_name ilike '%$search%' or b.access_type ilike '%$search%')";
		}

		// $sql =  'SELECT * FROM core.t_mtr_user_group '.$where.' ORDER BY '.$sort.' '.$order.'';

		$sql="select b.access_type, a.* from core.t_mtr_user_group a
				left join core.t_mtr_access_type b on a.access_type_id=b.id_seq
				$where order by $sort $order
				";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','delete');

	    	$checkUser=$this->m_global->getDataById("core.t_mtr_user","user_group_id=".$r['id_seq'])->num_rows();

	    	$action = '';

	    	if($edit){
	    		if ($checkUser>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, user group already paired to user'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit_group(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    	}

	    	if($delete){

	    		// pengecekan user group apabila ada user yang sudah ada tidak bisa di delete	
	    		// $checkUserGroup=$this->m_global->getdataById("core.t_mtr_user","user_group_id=".$r['id_seq'])->num_rows();

	    		// if ($checkUserGroup<1)
	    		// {
		    	// 	$action .= '<button onClick="delete_group(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		// }

	    		if($checkUser>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot delete, user group already paired to user'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="delete_group(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    	}

	    	$r['id_seq'] 	= encode($r['id_seq']);
	    	$r['access_type'] 	= strtoupper($r['access_type']);
	    	$r['action'] 	= $action;
	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;

		return $data;
	}

}

/* End of file M_user_group.php */
/* Location: ./application/models/M_user_group.php */