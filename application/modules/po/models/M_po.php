<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_po extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();	
	}

	public function get_icon_name($id)
	{
		$file_name_query = $this->db->query("SELECT icon FROM master.t_mtr_po WHERE id_seq=$id")->row();
		return $file_name_query->icon;
	}

	public function get_path_icon()
	{
		$path_query = $this->db->query("SELECT value FROM master.t_mtr_config WHERE name='PO_ICON_PATH'")->row();
		return $path_query->value;
	}
	
	public function getIcon($id)
	{
		$path_query = $this->db->query("SELECT value FROM master.t_mtr_config WHERE name='PO_ICON_PATH'")->row();
		$path = $path_query->value;
		$file_name_query = $this->db->query("SELECT icon FROM master.t_mtr_po WHERE id_seq=$id")->row();
		$file_name = $file_name_query->icon;

		return $path . $file_name;
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
			$where .="and( po_code ilike  '%".$search."%' or po_name ilike '%".$search."%' or pic_name ilike '%".$search."%' 
						or pic_phone ilike '%".$search."%' or pic_email ilike '%".$search."%' or address ilike '%".$search."%'
					 )";
		}

		$sql =  "select * from master.t_mtr_po
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$path_query = $this->db->query("SELECT value FROM master.t_mtr_config WHERE name='PO_ICON_PATH'")->row();
	    	$po_icon_path = "";

	    	if ($path_query) {
	    		$po_icon_path = $path_query->value;
	    	}

	    	// $r['po_icon'] = $po_icon_path . $r['icon'];

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/po','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/po','delete');
	    	
	    	//pengecekan jika po sudah pernah transaksi
			$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=".$r['id_seq'])->num_rows();
			$checkTapOut=$this->m_global->getDataById("trx.t_trx_tap_out","po_id=".$r['id_seq'])->num_rows();
			$checkBooking=$this->m_global->getDataById("trx.t_trx_booking","po_id=".$r['id_seq'])->num_rows();

			//pengecekan jika po sedang transaksi
			$checkTapIn2=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=".$r['id_seq']." and status=1")->num_rows();
			$checkTapOut2=$this->m_global->getDataById("trx.t_trx_tap_out","po_id=".$r['id_seq']."and status=1")->num_rows();

			//pengecekan jika po pairing ke sini
			$checkFare=$this->m_global->getDataById("master.t_mtr_fare","po_id=".$r['id_seq']." and status=1")->num_rows();
			$checkDriver=$this->m_global->getDataById("master.t_mtr_driver","po_id=".$r['id_seq']." and status=1")->num_rows();
			$checkBus=$this->m_global->getDataById("master.t_mtr_bus","po_id=".$r['id_seq']." and status=1")->num_rows();

	    	$action = '';

	    	if($edit){
	    		if($checkTapIn2>0 or $checkTapOut2 >0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot update, PO in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}

	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		
	    	}

	    	if($delete){

	    		if($checkTapIn>0 or $checkTapOut>0 or $checkBooking>0)
	    		{
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/po/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/po/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			// $action .= '<button onClick="validasi('."'Cannot delete, PO in transaction '".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			$action .= $tombol;
	    		}
	    		else if($checkFare>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, PO already paired to fare '".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/route/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/route/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else if($checkDriver>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, PO already paired to driver '".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			$tombol = '<button onClick="masterDisable(\''.(site_url('po/route/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';

	    			if ($r['status'] == -1) {
	    				$tombol = '<button onClick="masterEnable(\''.(site_url('po/route/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    			}

	    			$action .= $tombol;
	    		}
	    		else if($checkBus>0)
	    		{
	    			// $action .= '<button onClick="validasi('."'Cannot delete, PO already paired to bus '".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
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
	    	}

	    	if ($r['icon'] != "") {
	    		$r['icon']= '<img style="max-width:100px;max-height:100px;" src="'.$po_icon_path.''.$r['icon'].'">';
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

	public function caountShelter()
	{
		return $this->db->query("select * from master.t_mtr_shelter")->num_rows();
	}

	public function insertQueue($data)
	{
		$this->db->insert_batch('master.t_mtr_po_queue', $data); 
	}

	function insert($table,$data)
	{
		// $this->db->trans_begin();

        $this->db->insert($table,$data);

        // if($this->db->trans_status() === FALSE) {
        //     $this->db->trans_rollback();
        //     return false;
        // } 
        // else 
        // {
        //     $this->db->trans_commit();
        //     return true;
        // }
	}

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */