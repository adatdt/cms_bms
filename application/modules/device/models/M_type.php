<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_type extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_device_terminal_type WHERE id_seq=$id")->row();
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

		$where='where id_seq is not null and status=1 ';


		if (!empty($search))
		{
			$where .="and( terminal_type_name ilike  '%".$search."%')";
		}

		$sql =  "SELECT * FROM master.t_mtr_device_terminal_type
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/type','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/type','delete');
	    	$action = '';

	    	$checkExist = $this->m_global->getDataById("master.t_mtr_device_terminal", "terminal_type_id='".$r['id_seq']."' AND status=1");

	    	if($edit){
	    		if($checkExist->num_rows() > 0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, this device paired'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		} else {
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		
	    	}

	    	if($delete){
	    		if($checkExist->num_rows() > 0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot delete, this device paired'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		} else {
	    			$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
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

}