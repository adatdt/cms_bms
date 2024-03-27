<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_bus extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	// public function check_data($field,$data)
	// {
	// 	return $this->db->query('SELECT * FROM dbo."t_mtr_customer" WHERE '.$field.'= \''.$data.'\' AND status=1')->row();

	// 			return $this->db->query('SELECT * FROM dbo."t_mtr_customer" WHERE '.$field.'= \''.$data.'\' AND status=1')->row();
	// }

	// public function check_vehicle($field,$data)
	// {
	// 	return $this->db->query('SELECT * FROM dbo."t_mtr_vehicle" WHERE UPPER('.$field.')= UPPER(\''.$data.'\') AND status=1')->row();
	// }

	// public function get_serial_input($id)
	// {
	// 	$query = $this->db->query("SELECT serial FROM dbo.t_mtr_obu WHERE id = $id")->row();
	// 	return $query->serial;
	// }

	// public function insert($table,$data)
	// {
	// 	$this->db->insert($table, $data);
	// 	return $this->db->insert_id();
	// }



	public function get()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'desc';

		$where = 'WHERE a.status=1';
		

		// if (!empty($search))
		// {
		// 	$where .= "and ( concat(a.first_name,' ',a.last_name) ilike '%".$search."%' or a.identity_number ilike '%".$search."%' or c.serial ilike '%".$search."%' or a.phone_number ilike '%".$search."%' or d.no_plat ilike '%".$search."%')";
		// }

		$sql="
			select * from master.t_mtr_bus
		";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);
		$data_rows = array();

	    foreach ($query->result_array() as $r) {
	  // 		$slug='customer';
	  // 		$action = '';

	  //   	$force_blacklist = $this->check_force_blacklist($r['force_blacklist'],encode($r['obu_id']));

	  //   	$detail=$this->m_global->menuAccess($this->session->userdata('user_group_id'),$slug,'detail');
			// $acc_fb = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'customer','whitelist');
			// $acc_bl = $this->m_global->menuAccess($this->session->userdata('user_group_id'),'customer','blacklist');

			// if ($detail) {
			// 	$action .= "<button type='button' class='btn bg-astra btn-icon btn-xs btn-dtgrid' title='Detail' onclick='detail(".'"'.encode($r['id']).'"'.")' ><i class='icon-zoomin3'></i></button> ";
			// }

			// if ($acc_bl) {
			// 	if (!$force_blacklist) {
			// 		$action .= '<button onClick="blacklisted(\''.(encode($r['obu_id'])).'\')" class="btn btn-danger btn-icon btn-xs btn-dtgrid" title="Blacklist"><i class="icon-cancel-circle2"></i></button> ';
			// 	}
			// }

			// if ($acc_fb) {
			// 	$action .= $force_blacklist;
			// }

	    	$data_rows[] = $r;
	    }
	    
	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;

		return $data;
	}

	public function status_blacklist($blacklist,$force_blacklist)
	{
		if ($blacklist == 0 && $force_blacklist == 0) {
			return '<span class="label label-flat border-success text-success-600">Whitelist</span>';
		}

		if ($blacklist == 1 && $force_blacklist == 	0) {
			return '<span class="label label-flat bg-danger text-default-600">Debt Blacklist</span>';
		}

		if ($force_blacklist == 1) {
			return '<span class="label label-flat border-danger text-danger-600">Force Blacklist</span>';
		}
	}

	public function action($id)
	{	

		return "
			<button type='button' class='btn btn-xs bg-astra' onclick='detail(".'"'.encode($id).'"'.")' ><i class='icon-zoomin3'></i></button>
			";
	}

	public function getDetail($id)
	{
		$data=$this->db->query("select c.obu_number,d.id_golongan , d.no_plat, d.no_stnk, d.merek, d.warna, c.serial , c.blacklist ,c.force_blacklist, 
			c.last_balance,c.debt, a.* from dbo.t_mtr_customer a
			left join dbo.t_mtr_paired_obu_customer_vehicle b on a.id=b.customer_id
			left join dbo.t_mtr_obu c on b.obu_id = c.id
			left join dbo.t_mtr_vehicle d on b.vehicle_id=d.id
			where a.id=$id")->row();

		$result=array();
		$data->last_balance=idr_currency($data->last_balance);
		$data->debt=idr_currency($data->debt);
		$data->status=$this->status_blacklist($data->blacklist,$data->force_blacklist);

		$result=$data;
		
		return  $result;
	}

	// Pindahan m_obu

	public function get_serial_obu($id)
	{
		$qry = $this->db->query('SELECT serial FROM dbo."t_mtr_obu" WHERE id='.$id.'')->row();
		return $qry->serial;
	}

	public function insert_blacklist_obu($data)
	{
		return $this->db->insert('dbo.t_trx_obu_blacklist', $data);
	}

	public function update_obu($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->update($table,$data);

		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
		   return true;
		}
	}
}

/* End of file M_customer.php */
/* Location: ./application/models/M_customer.php */