<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_bustype extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
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

		$where='where id_seq is not null and (status=1 OR status=-1)';


		if (!empty($search))
		{
			$where .="and(
						type ilike '%".$search."%' 
					 )";
		}

		$sql =  "
				select * from master.t_mtr_bus_type
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus_type','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus_type','delete');
	    	$action = '';

	    	$checkFare2=$this->m_global->getDataById("master.t_mtr_fare","bus_type_id=".$r['id_seq'])->num_rows();

	    	$checkFare=$this->m_global->getDataById("master.t_mtr_fare","status=1 and bus_type_id=".$r['id_seq'])->num_rows();

	    	if($edit){

	    		if ($checkFare>0)
		        {
		        	$action .= '<button onClick="validasi('."'Cannot update, type already paired to fare'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
		        }
		        else
		        {
		        	$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
		        }
	    	}

	    	if($delete){

	    		if ($checkFare>0)
		        {
		        	$tombol = '<button onClick="masterDisable(\''.(site_url('po/bus_type/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/bus_type/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
		        }
		        else
		        {
		        	$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
		        }
	    	}

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