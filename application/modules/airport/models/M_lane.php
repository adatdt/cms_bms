<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_lane extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_lane WHERE id_seq=$id")->row();
	}

	public function get_master($table)
	{
		return $this->db->query("SELECT * FROM $table WHERE status=1")->result();
	}	

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$shelter = trim(strtoupper($this->db->escape_like_str($this->input->post('shelter'))));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='WHERE L.id_seq is not null and L.status=1 ';


		if (!empty($search))
		{
			$where .="and( shelter_name ilike  '%".$search."%' or lane_name ilike '%".$search."%')";
		}

		if (!empty($shelter))
		{
			$where .=" and shelter_id = $shelter";
		}

		$sql =  "SELECT
		S.shelter_name,
		L.* 
		FROM
		master.t_mtr_lane L
		JOIN master.t_mtr_shelter S ON S.id_seq = L.shelter_id
		$where ORDER BY L.shelter_id, L.lane_name ASC, $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/lane','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/lane','delete');
	    	$action = '';
	    	
	    	// mencari jika data sudah di pairing di user manless shelter
			$checkManlessGate=$this->m_global->getDataById("master.t_mtr_user_manless_gate","lane_id=".$r['id_seq']." and status in (1,-1)")->num_rows();

	    	if($edit){
	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		if($checkManlessGate>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot delete, lane already paired to user manless shelter'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    		else
	    		{
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