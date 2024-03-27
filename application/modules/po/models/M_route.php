<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_route extends CI_Model {

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
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null and (a.status=1 OR a.status=-1)';


		if (!empty($search))
		{
			$where .="and(
						b.airport_name ilike '%".$search."%' or a.route_code ilike '%".$search."%'
						 or a.route_info ilike '%".$search."%'
					 )";
		}

		$sql =  "
				select b.airport_name,a.* from master.t_mtr_route a
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/route','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/route','delete');

	    	$action = '';

	    	// cek data jika sudah pernah transaksi
	    	$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","route_id=".$r['id_seq']." ")->num_rows();
	    	$checkFare=$this->m_global->getDataById("master.t_mtr_fare","route_id=".$r['id_seq']." and status=1")->num_rows();

	    	if($edit){

	    		if($checkTrx>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot edit, route in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    	}

	    	if($delete){

	    		if($checkTrx>0)
	    		{
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/route/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/route/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else if($checkFare>0)
	    		{
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/route/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/route/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
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

	public function checkRoute($where)
	{
		return $this->db->query("select upper(route_info) from master.t_mtr_route where $where");
	}

	public function getTag()
	{
		$tag=$this->db->query("select tag_name from master.t_mtr_tag where status=1 order by tag_name asc")->result();

		$data=array();
		foreach ($tag as $tag)
		{
			$r=$tag->tag_name;
			$data[]=$r;
		}
		return json_encode($data);
	}

	function insert($table,$data)
	{
        $this->db->insert($table,$data);
        return $this->db->insert_id();

	}
}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */