<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_fare extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function get_master($table)
	{
		return $this->db->query("SELECT * FROM $table WHERE status=1")->result();
	}

	public function get_fare()
	{
		return $this->db->query("SELECT * FROM master.t_mtr_fare WHERE status=1")->result();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_fare WHERE id_seq=$id")->row();
	}

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$po=decode($this->input->post('po'));
		$busType=decode($this->input->post('busType'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'F.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where F.id_seq is not null and (F.status=1 OR F.status=-1)';

		if(!empty($search))
		{
			$where .="and (R.route_info ilike '%".$search."%')";
		}
		if(!empty($po))
		{
			$where .="and (F.po_id=$po)";
		}
		if(!empty($busType))
		{
			$where .="and (F.bus_type_id=$busType)";
		}

		$sql =  "SELECT
		PO.po_name,
		R.route_info,
		BT.type,
		F.* 
		FROM
		master.t_mtr_fare F
		JOIN master.t_mtr_po PO ON PO.id_seq=F.po_id
		JOIN master.t_mtr_route R ON R.id_seq=F.route_id
		JOIN master.t_mtr_bus_type BT ON BT.id_seq=F.bus_type_id
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/fare','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/fare','delete');
	    	
	    	$checkTrx=$this->checkTrx($r['po_id'],$r['bus_type_id'],$r['route_id']," and a.status='1' ")->num_rows();
	    	
	    	$checkTrx2=$this->checkTrx($r['po_id'],$r['bus_type_id'],$r['route_id'])->num_rows();
	    	$action = '';

	    	if($edit){

	    		if($checkTrx>0)
	    		{
	    			$action .= '<button onClick="validasi('."'fare in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		
	    	}

	    	if($delete){

	    		if($checkTrx>0 or $checkTrx2>0)
	    		{
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/fare/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/fare/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    		}
	    	};

	    	$r['id_seq']=encode($r['id_seq']);
	    	$r['price']=number_format($r['price'],0,",",".");
	    	$r['action']=$action;

	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}

	public function checkTrx($po_id,$bus_type_id, $route_id, $status="")
	{
		return $this->db->query("select f.type,e.bus_name, d.terminal_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_out a 
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code 
				left join master.t_mtr_bus e on a.plate_number=e.plate_number
				left join master.t_mtr_bus_type f on e.bus_type_id=f.id_seq 
				where a.po_id=$po_id and e.bus_type_id=$bus_type_id and a.route_id=$route_id $status ");
	}

	public function getFare($id)
	{
		return $this->input->post("SELECT
		PO.po_name,
		R.route_info,
		BT.type,
		F.* 
		FROM
		master.t_mtr_fare F
		JOIN master.t_mtr_po PO ON PO.id_seq=F.po_id
		JOIN master.t_mtr_route R ON R.id_seq=F.route_id
		JOIN master.t_mtr_bus_type BT ON BT.id_seq=F.bus_type_id
		where F.id_seq=$id
		")->row();
	}

	public function select($table,$order)
	{
		$this->db->query("select * from ".$table." order by ".$order);
	}

	function insert($table,$data)
	{
        $this->db->insert($table,$data);

	}

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);

    }
}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */