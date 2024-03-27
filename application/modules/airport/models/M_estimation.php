<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_estimation extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_estimated_arrival_time WHERE id_seq=$id")->row();
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
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'E.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where=' WHERE E.status=1 AND E.id_seq IS NOT NULL ';


		if (!empty($search))
		{
			$where .="and( origin_name ilike  '%".$search."%' or destination_name ilike '%".$search."%')";
		}

		$sql =  "SELECT
		DISTINCT(E.id_seq) AS nomor,
		(select shelter_name FROM master.t_mtr_shelter WHERE id_seq::varchar=E.origin) AS origin_name,
		(select shelter_name FROM master.t_mtr_shelter WHERE id_seq::varchar=E.destination) AS destination_name,
		E.* 
		FROM
		master.t_mtr_estimated_arrival_time E
		JOIN master.t_mtr_shelter S ON S.id_seq::varchar=E.origin OR S.id_seq::varchar=E.destination
		$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/estimation','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/estimation','delete');
	    	$action = '';

	    	if($edit){
	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    	};

	    	$origin = $r['origin_name'];

	    	if (strtoupper($r['origin']) == strtoupper("P")) {
	    		$origin = "Pengendapan";
	    	}

	    	$r['action']=$action;
	    	$r['id_seq']=encode($r['id_seq']);
	    	$r['origin_name']=$origin;
	    	$r['duration_time']=$r['duration_time'] .= " Minutes";

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