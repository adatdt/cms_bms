<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_bus extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$po =decode($this->input->post('po'));
		$type=decode($this->input->post('type'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null and (a.status=1 OR a.status=-1) ';


		if (!empty($po))
		{
			$where .="and(a.po_id=$po)";
		}

		if (!empty($type))
		{
			$where .="and( a.bus_type_id=$type)";
		}

		if (!empty($search))
		{
			$where .="and(
						a.bus_name ilike '%".$search."%' or a.plate_number ilike '%".$search."%'
					 )";
		}

		$sql =  "
				select c.po_name,b.type, a.* from master.t_mtr_bus a
				left join master.t_mtr_bus_type b on a.bus_type_id= b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/bus','delete');
	    	
	    	$checkTrx=$this->m_global->getDataById('trx.t_trx_tap_out',"plate_number='".$r['plate_number']."' and status=1")->num_rows();

	    	$checkTrxIn=$this->m_global->getDataById('trx.t_trx_tap_in',"plate_number='".$r['plate_number']."' and status=1")->num_rows();

	    	//pengecekan jika data pernah transaksi
	    	$checkTrxIn2=$this->m_global->getDataById('trx.t_trx_tap_in',"plate_number='".$r['plate_number']."' ")->num_rows();
	    	$checkTrx2=$this->m_global->getDataById('trx.t_trx_tap_out',"plate_number='".$r['plate_number']."' ")->num_rows();

			$checkDriver=$this->m_global->getDataById('master.t_mtr_driver',"bus_id=".$r['id_seq']." and status=1")->num_rows();
	    	$action = '';

	    	if($edit){

	    		if($checkTrx>0 or $checkTrxIn>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, bus in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    	}

	    	if($delete){
	    		if($checkTrx2>0  or $checkTrxIn2>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, bus in transaction'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/bus/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/bus/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else if($checkDriver>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, bus already paired to driver'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/bus/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/bus/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
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

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */