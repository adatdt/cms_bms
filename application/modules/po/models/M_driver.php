<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_driver extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$po=decode($this->input->post('po'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null and a.status in (1,-1)  ';

		if(!empty($po))
		{
			$where .="and(a.po_id=$po)";
		}

		if (!empty($search))
		{
			$where .="and(
						 b.bus_name ilike '%".$search."%' or b.plate_number ilike '%".$search."%'
						or a.uid ilike '%".$search."%' or a.driver_name ilike '%".$search."%'
					 )";
		}

		$sql =  "
				select b.plate_number, d.first_name, c.po_name, b.bus_name, a.* from master.t_mtr_driver a
				left join master.t_mtr_bus b on a.bus_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join core.t_mtr_user d on a.created_by=d.username
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/route','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/route','delete');
	    	$action="";
			// cek data sedang transaksi
			$checkTrx=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$r['uid']."') and status=1")->num_rows();
			$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$r['uid']."') and status=1")->num_rows();

			//cek data jika sudah pernah transaksi
			$checkTrx2=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$r['uid']."') ")->num_rows();
			$checkTapIn2=$this->m_global->getDataById("trx.t_trx_tap_out","upper(uid)=upper('".$r['uid']."') ")->num_rows();

			// check jika ada uid yang sama di driver
			$checkUid=$this->m_global->getDataById("master.t_mtr_driver","upper(uid)=upper('".$r['uid']."') and status=1 ")->num_rows();

	    	if($edit)
	    	{
	    		if($checkTrx>0 or $checkTapIn>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, driver in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';	
	    		}	    		
	    	}

	    	if($delete){

	    		if( $checkTrx2>0 or $checkTapIn2>0)
	    		{
	    			if($r['status']==1)
	    			{
	    				// jika sedang transaksi
	    				if($checkTrx>0 or $checkTapIn>0)
	    				{
	    					$action .= '<button onClick="validasi('."'Cannot disable, driver in transaction'".')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button>';
	    				}
	    				else
	    				{
	    					$action .= '<button onClick="disableData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button>';
	    				}
	    			}

	    			else if($r['status']==-1)
	    			{
	    				if($checkUid>0)
	    				{
	    					$action .= '<button onClick="validasi('."'Cannot enable, UID already active'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Enable">enable</button>';
	    				}
	    				else
	    				{
	    					$action .= '<button onClick="enableData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button>';
	    				}

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
	    		$status='<span class="label label-flat border-danger text-danger-600">Non Active</span>';	
	    	}

	    	$r['created_on']=format_dateTime($r["created_on"]);
	    	$r['action']=$action;
	    	$r['status']=$status;

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

	public function getPo($id)
	{
		return $this->db->query("select * from master.t_mtr_bus a
		left join master.t_mtr_po b on a.po_id=b.id_seq
		where a.id_seq=$id")->row();
	}
	public function getDetail($id)
	{
		return $this->db->query("
			select b.po_name , a.* from master.t_mtr_driver a
			left join master.t_mtr_po b on a.po_id=b.id_seq
			where a.id_seq=$id
			")->row();
	}
}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */