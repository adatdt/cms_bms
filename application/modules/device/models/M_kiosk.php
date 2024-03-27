<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kiosk extends CI_Model {

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
		// $search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$search=trim($this->input->post('search'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null and b.status in (1,-1)';


		if (!empty($search))
		{
			$where .="and(  c.shelter_name ilike  '%".$search."%' or b.username ilike '%".$search."%' or b.first_name ilike '%".$search."%')";
		}

		$sql =  "select d.terminal_name,c.shelter_name, b.* from master.t_mtr_user_kiosk a
					left join core.t_mtr_user b on a.user_id=b.id_seq 
					left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
					left join master.t_mtr_device_terminal d on a.terminal_id=d.id_seq
				 $where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/kiosk','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/kiosk','delete');
	    	$change_password = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'device/kiosk','change_password');

			// check jika user kiosk sudah perbah melakukan transaksi
			$checkBooking=$this->m_global->getDataById("trx.t_trx_booking","upper(created_by)=upper('".$r['username']."')")->num_rows();
	    	$action = '';

	    	if($edit){
	    		
	    		if($checkBooking>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot update, user in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		}
	    	}
	    	
	    	if($change_password){
	    		$action .= '<button onClick="change_password(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Change Password">Reset Password</button> ';
	    	}

	    	if($delete){
	    		if($r['status']=='1')
	    		{	
	    			if ($checkBooking>0) {
	    				$action .= '<button onClick="disable_user(\''.(encode($r['id_seq'])).'\','."'Disable'".','."'disable'".')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button>';
	    			}else{
	    				$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    			}
	    		}
	    		else
	    		{
	    			$action .= '<button onClick="disable_user(\''.(encode($r['id_seq'])).'\','."'Enable'".','."'enable'".')" class="updated btn bg-success btn-icon btn-xs btn-dtgrid" title="Delete">Enable</button>';	
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

	function detail($id)
	{
		return $this->db->query("select  c.airport_id,a.shelter_id, b.* from master.t_mtr_user_kiosk a
							left join core.t_mtr_user b on a.user_id=b.id_seq 
							left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
							left join master.t_mtr_device_terminal d on a.terminal_id=d.id_seq
							where b.id_seq=$id
							")->row();
	}
	function insert($table,$data)
	{
		
        $this->db->insert($table,$data);
        return $this->db->insert_id();
	}

	public function update($table,$data,$where)
    {
    	
        $this->db->where($where);
        $this->db->update($table,$data);
    }

	public function generateCode($codeId)
	{
		$max =$this->db->query("select max(terminal_code) as max_code from master.t_mtr_device_terminal
		where left (terminal_code,4)='".$codeId."'")->row();
		
		$kode=$max->max_code;
		
		$noUrut = (int) substr($kode,4,2);
		$noUrut++;
		$char = $codeId;
		$deviceCode = $char . sprintf("%02s", $noUrut);
		return $deviceCode;
	}


	// public function select($table,$order)
	// {
	// 	$this->db->query("select * from ".$table." order by ".$order);
	// }

}