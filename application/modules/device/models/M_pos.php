<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pos extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	function insert_id($table,$data)
	{
		
        $this->db->insert($table,$data);
        return $this->db->insert_id();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_device_terminal WHERE id_seq=$id")->row();
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

		$where='WHERE T.id_seq is not null and T.status in (1,-1) and DT.id_seq=2 ';

		if (!empty($search))
		{
			$where .="and( or terminal_name ilike '%".$search."%' or DT.terminal_type_name ilike '%".$search."%')";
		}

		if (!empty($shelter))
		{
			$where .=" and (T.shelter_id = $shelter)";
		}

		$sql =  "SELECT
		DT.terminal_type_name,
		A.airport_name,
		S.shelter_name,
		T.*
		FROM
		master.t_mtr_device_terminal T
		LEFT JOIN master.t_mtr_device_terminal_type DT ON DT.id_seq=T.terminal_type_id
		LEFT JOIN master.t_mtr_shelter S ON S.id_seq=T.shelter_id
		LEFT JOIN master.t_mtr_airport A ON A.id_seq=T.airport_id
		$where ORDER BY shelter_id ASC, $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/pos','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/pos','delete');

	    	$checkUserPos=$this->m_global->getDataById("master.t_mtr_user_pos","terminal_id=".$r['id_seq'])->num_rows();

	    	$action = '';

	    	if($edit){

	    		// if($checkUserPos>0)
	    		// {
	    		// 	$action .= '<button onClick="validasi('."'Cannot update, device POS already paired to user'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		// }
	    		// else
	    		// {
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		// }

	    		// $action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){

	    		if($checkUserPos>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, device POS already paired to user'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			if ($r['status']=='1') {
	    				$action .= '<button onClick="masterDisable(\''.(site_url('device/pos/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';
	    			}else{
	    				$action .= '<button onClick="masterEnable(\''.(site_url('device/pos/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    	}

	    	if($r['status']=='1')
	    	{
	    		$status='<span class="label label-flat border-success text-success-600">Active</span>';
	    	}
	    	else
	    	{
	    		$status='<span class="label label-flat border-danger text-danger-600">Not Active</span>';	
	    	}

	    	$r['status']=$status;
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

	public function generateCode($codeId){

		$max =$this->db->query("select max(terminal_code) as max_code from master.t_mtr_device_terminal
		where left (terminal_code,4)='".$codeId."'")->row();
		
		$kode=$max->max_code;
		
		$noUrut = (int) substr($kode,4,2);
		$noUrut++;
		$char = $codeId;
		$deviceCode = $char . sprintf("%02s", $noUrut);
		return $deviceCode;
	}

	public function getDevice()
	{
		return $this->db->query("SELECT * FROM master.t_mtr_device_terminal WHERE id_seq=$id")->row();
	}
}


