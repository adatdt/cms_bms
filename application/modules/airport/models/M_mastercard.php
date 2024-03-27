<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_mastercard extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_shelter WHERE id_seq=$id")->row();
	}

	public function get_master($table)
	{
		return $this->db->query("SELECT * FROM $table WHERE status=1")->result();
	}	

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='WHERE id_seq is not null and status=1 ';


		if (!empty($search))
		{
			$where .="and( ticket_code ilike  '%".$search."%' or pic_name ilike '%".$search."%'
						or pic_phone ilike '%".$search."%' 
					)";
		}

		$sql =  "
			select * from master.t_mtr_qr_boarding
			$where order by $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/shelter','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/shelter','delete');
	    	$action = '';

	    	if($edit){
	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    	};
	    	$r['action']=$action;
	    	$r['id_seq']=encode($r['id_seq']);

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

	public function  download()
	{
		
		$get = $this->input->get();

		$search= $get['search'];
		
		$where='WHERE id_seq is not null and status=1 ';


		if (!empty($search))
		{
			$where .="and( ticket_code ilike  '%".$search."%' or pic_name ilike '%".$search."%'
						or pic_phone ilike '%".$search."%' 
					)";
		}

		$sql =  "
			select * from master.t_mtr_qr_boarding
				$where ORDER BY id_seq desc";

		return $this->db->query($sql);
	}
}