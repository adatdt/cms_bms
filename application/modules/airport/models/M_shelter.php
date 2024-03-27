<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_shelter extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT * FROM master.t_mtr_shelter WHERE id_seq=$id")->row();
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
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'S.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='WHERE S.id_seq is not null and S.status=1 ';


		if (!empty($search))
		{
			$where .="and( shelter_code ilike  '%".$search."%' or shelter_name ilike '%".$search."%')";
		}

		$sql =  "SELECT
		A.airport_name,
		S.*
		FROM
		master.t_mtr_shelter S
		LEFT JOIN master.t_mtr_airport A ON A.id_seq = S.airport_id	$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/shelter','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'airport/shelter','delete');
	    	$action = '';

	    	//check transaction data
			$checkAccessBoarding = $this->m_global->getDataById("trx.t_trx_access_boarding", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkBoarding = $this->m_global->getDataById("trx.t_trx_boarding", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkBookingDetail = $this->m_global->getDataById("trx.t_trx_booking_detail", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkExit = $this->m_global->getDataById("trx.t_trx_check_exit", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkIn = $this->m_global->getDataById("trx.t_trx_check_in", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkOut = $this->m_global->getDataById("trx.t_trx_check_out", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkJourney = $this->m_global->getDataById("trx.t_trx_journey_cycle", "shelter_id='".$r['id_seq']."'")->num_rows();
			$checkPayment = $this->m_global->getDataById("trx.t_trx_payment", "shelter_id='".$r['id_seq']."'")->num_rows();

	    	if($edit){
	    		// if($checkAccessBoarding>0 or $checkBoarding>0 or $checkBookingDetail>0 or $checkExit>0 or $checkIn>0 or $checkOut>0 or $checkJourney>0 or $checkPayment>0)
	    		// {
	    		// 	$action .= '<button onClick="validasi('."'Cannot edit, user in transaction'".')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		// }
	    		// else
	    		// {
	    		// 	$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    		// }

	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		if($checkAccessBoarding>0 or $checkBoarding>0 or $checkBookingDetail>0 or $checkExit>0 or $checkIn>0 or $checkOut>0 or $checkJourney>0 or $checkPayment>0)
	    		{
	    			$action .= '<button onClick="validasi('."'Cannot delete, user in transaction'".')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button> ';
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

	function insert($table,$data)
	{
        $this->db->insert($table,$data);
        return $this->db->insert_id();
	}

	public function insertQueue($data)
	{
		$this->db->insert_batch('master.t_mtr_po_queue', $data); 
	}

	public function update($table,$data,$where)
    {
    	$this->db->trans_begin();
        $this->db->where($where);
        $this->db->update($table,$data);

        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return FALSE;
        }
        else
        {
                $this->db->trans_commit();
                return TRUE;
        }
    }

}