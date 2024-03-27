<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_usercms extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	

	public function getData()
	{
		$data = array();
		// $search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$search=trim($this->input->post('search'));
		$po =decode($this->input->post('po'));
		$type=decode($this->input->post('type'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'b.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'asc';

		// jika ada penambahan user group selain user cms maka kondisi di sini di tambahkan
		$where='where a.id_seq not in (select user_id from master.t_mtr_user_b2b )
				and a.id_seq not in (select user_id from master.t_mtr_user_boarding_gate )
				and a.id_seq not in (select user_id from master.t_mtr_user_kiosk )
				and a.id_seq not in (select user_id from master.t_mtr_user_manless_gate )
				and a.id_seq not in (select user_id from master.t_mtr_user_pos )
				and a.id_seq not in (select user_id from master.t_mtr_user_validator ) ';



		if (!empty($search))
		{
			$where .="and(
						a.username ilike '%".$search."%' or b.group_name ilike '%".$search."%'
						or a.first_name ilike '%".$search."%'
					 )";
		}

		$sql =  "select b.group_name, a.* from core.t_mtr_user a
				 join core.t_mtr_user_group b on a.user_group_id=b.id_seq
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus','delete');
	    	$action = '';

	    	if($edit){
	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    	};
	    	$r['action']=$action;

	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}


	public function select($table,$order)
	{
		$this->db->query("select * from ".$table." order by ".$order);
	}

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */