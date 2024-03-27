<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_pc extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function check_data($po_id,$pc_id,$id_seq = "")
	{
		if ($id_seq != "") {
			return $this->db->query("SELECT id_seq,status FROM master.t_mtr_payment_channel_detail WHERE po_id = $po_id AND payment_channel_id = $pc_id")->row();
		}else{
			return $this->db->query("SELECT id_seq,status FROM master.t_mtr_payment_channel_detail WHERE po_id = $po_id AND payment_channel_id = $pc_id")->row();
		}
	}

	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$po = decode($this->input->post('po'));
		$pc = decode($this->input->post('pc'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'PO.po_name';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'ASC';

		$where = ' WHERE PCD.status IN(1,-1)';

		if (!empty($search))
		{
			$where .=" and PCD.mid ilike '%".$search."%'";
		}

		if (!empty($po))
		{
			$where .=" AND PCD.po_id = $po";
		}

		if (!empty($pc))
		{
			$where .=" AND PCD.payment_channel_id = $pc";
		}

		$sql =  "SELECT
		PO.po_name,
		PC.payment_channel,
		PCD.* 
		FROM
		master.t_mtr_payment_channel_detail PCD 
		JOIN master.t_mtr_payment_channel PC ON PC.id_seq = PCD.payment_channel_id
		JOIN master.t_mtr_po PO ON PO.id_seq = PCD.po_id
		$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	$edit = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/payment_channel','edit');
	    	$delete = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'po/payment_channel','delete');;

	    	$action = '';

	    	if ($r['status'] == 1) {
	    		$action .= ' <button onClick="masterDisable(\''.(site_url('po/payment_channel/disable/'). encode($r['id_seq'])).'\')" class="updated btn btn-grey btn-icon btn-xs btn-dtgrid" title="Disable">Disable</button> ';
	    	}elseif ($r['status'] == -1) {
	    		$action .= ' <button onClick="masterEnable(\''.(site_url('po/payment_channel/enable/'). encode($r['id_seq'])).'\')" class="updated btn btn-success btn-icon btn-xs btn-dtgrid" title="Enable">Enable</button> ';
	    	}

	    	if($edit){
	    		$action .= '<button onClick="edit(\''.(encode($r['id_seq'])).'\')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button> ';
	    	}

	    	if($delete){
	    		$action .= '<button onClick="deleteData(\''.(encode($r['id_seq'])).'\')" class="updated btn btn-danger btn-icon btn-xs btn-dtgrid" title="Delete">Delete</button>';
	    	};

	    	$r['action'] = $action;
	    	$r['status'] = ($r['status'] == 1) ? '<span class="label label-flat border-success text-success-600">Active</span>' : '<span class="label label-flat border-danger text-danger-600">Not Active</span>' ;;

	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}

	public function get_edit($id)
	{
		return $this->db->query("SELECT
		PO.po_name,
		PC.payment_channel,
		PCD.* 
		FROM
		master.t_mtr_payment_channel_detail PCD 
		JOIN master.t_mtr_payment_channel PC ON PC.id_seq = PCD.payment_channel_id
		JOIN master.t_mtr_po PO ON PO.id_seq = PCD.po_id WHERE PCD.id_seq = $id")->row();
	}

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }
}