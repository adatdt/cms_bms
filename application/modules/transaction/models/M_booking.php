<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_booking extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function  download()
	{
		
		$get = $this->input->get();

		$sortFrom= $get['sortFrom'];
		$sortTo= $get['sortTo'];
		$po= decode($this->input->get('po'));
		$status= decode($this->input->get('status'));
		$shelter= decode($this->input->get('shelter'));
		$search= $get['search'];
		
		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(b.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(b.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(b.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($search))
		{
			$where .="and(b.booking_code ilike '%".$search."%' or a.ticket_code ilike '%".$search."%' or a.route_info ilike '%".$search."%' or f.terminal_name ilike '%".$search."%' 
				or d.type ilike '%".$search."%' )";
		}

		if (!empty($po))
		{
			$where .="and(c.id_seq=$po)";
		}

		if (!empty($shelter))
		{
			$where .="and(e.id_seq=$shelter)";
		}

		if (!empty($status))
		{
			$where .="and(a.status=$status)";	
		}

		$sql="
				select g.status_name, g.status_code, g.status_name, b.created_on as trx_date, f.terminal_name, e.shelter_name, d.type, c.po_name, b.booking_code,a.* from trx.t_trx_booking_detail a
				left join  trx.t_trx_booking b on a.booking_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_bus_type d on a.bus_type_id=d.id_seq
				left join master.t_mtr_shelter e on a.shelter_id=e.id_seq
				left join master.t_mtr_device_terminal f on b.terminal_code=f.terminal_code
				left join master.t_mtr_ticket_status g on a.status=g.status_code
				$where ORDER BY a.id_seq desc
		";

		return $this->db->query($sql);
	}

	private function ticket_status($status_id,$status_name)
	{
		if ($status_id == 1) {
			return '<span class="label label-flat border-danger text-danger-600">'.$status_name.'</span>';
		}

		if ($status_id == 2) {
			return '<span class="label label-flat border-success text-success-600">'.$status_name.'</span>';
		}
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$po = decode($this->input->post('po'));
		$shelter = decode($this->input->post('shelter'));
		$status=decode($this->input->post('status'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(b.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(b.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(b.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($search))
		{
			$where .="and(b.booking_code ilike '%".$search."%' or a.ticket_code ilike '%".$search."%' or a.route_info ilike '%".$search."%' or f.terminal_name ilike '%".$search."%' 
				or d.type ilike '%".$search."%')";
		}

		if (!empty($po))
		{
			$where .="and(c.id_seq=$po)";
		}

		if (!empty($shelter))
		{
			$where .="and(e.id_seq=$shelter)";
		}

		if (!empty($status))
		{
			$where .="and(a.status=$status)";	
		}

		$sql="
				select g.status_code, g.status_name, b.created_on as trx_date, f.terminal_name, e.shelter_name, d.type, c.po_name, b.booking_code,a.* from trx.t_trx_booking_detail a
				left join  trx.t_trx_booking b on a.booking_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_bus_type d on a.bus_type_id=d.id_seq
				left join master.t_mtr_shelter e on a.shelter_id=e.id_seq
				left join master.t_mtr_device_terminal f on b.terminal_code=f.terminal_code
				left join master.t_mtr_ticket_status g on a.status=g.status_code
				$where ORDER BY $sort $order
		";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$action = "";

	    	$detail=$this->m_global->menuAccess($this->session->userdata('user_group_id'),'transaction/booking','detail');

	    	if ($detail) {
				$action .= "<button type='button' class='btn bg-angkasa2 btn-icon btn-xs btn-dtgrid' title='Detail' onclick='clickDetail(".'"'.encode($r['id_seq']).'"'.")' >Detail</button> ";
			}

			if($r['status_code']==1)
			{
				$status = '<span class="label label-flat border-danger text-danger-600">'.$r['status_name'].'</span>';
			}
			else if ($r['status_code']==2)
			{
				$status = '<span class="label label-flat border-success text-success-600">'.$r['status_name'].'</span>';
			}
			else
			{
			$status = '<span class="label label-flat border-info text-info-600">'.$r['status_name'].'</span>';
			}

			empty($r['terminal_name'])?$terminal_name="B2B":$terminal_name=$r['terminal_name'];

	    	$r['status']=$status;
	    	$r['terminal_name']=$terminal_name;
	    	$r['trx_date']=format_dateTime($r['trx_date']);
	    	$r['price']=idr_currency($r['price']);
	    	
	    	$data_rows[] = $r;
	    }

	    $data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}

	public function get_booking_code($id)
	{
		$query = $this->db->query("SELECT booking_code FROM trx.t_trx_booking WHERE id_seq=$id")->row();
		return $query->booking_code;
	}

	public function getDetail($id)
	{
		return $this->db->query("SELECT
			TS.status_name,
			PO.po_name,
			R.route_info,
			A.airport_name,
			BT.type,
			S.shelter_name,
			BOD.*
			FROM
			trx.t_trx_booking_detail BOD
			JOIN master.t_mtr_po PO ON PO.id_seq = BOD.po_id
			JOIN master.t_mtr_airport A ON A.id_seq = BOD.airport_id
			JOIN master.t_mtr_shelter S ON S.id_seq = BOD.shelter_id
			JOIN master.t_mtr_bus_type BT ON BT.id_seq = BOD.bus_type_id
			JOIN master.t_mtr_route R ON R.route_code = BOD.route_code
			JOIN master.t_mtr_ticket_status TS ON TS.status_code = BOD.status
			WHERE BOD.booking_id=$id")->result();
	}


	public function select($table,$order)
	{
		$this->db->query("select * from ".$table." order by ".$order);
	}

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */