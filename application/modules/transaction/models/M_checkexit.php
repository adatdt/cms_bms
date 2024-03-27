<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_checkexit extends CI_Model {

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
		$search= $get['search'];
		$shelter= decode($this->input->get('shelter'));
		$po= decode($this->input->get('po'));
		
		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($shelter))
		{
			$where .="and (a.shelter_id=$shelter)";
		}

		if (!empty($po))
		{
			$where .="and (e.id_seq=$po)";
		}

		if (!empty($search))
		{
			$where .="and( d.plate_number ilike '%".$search."%'
							or d.route_info ilike '%".$search."%' or d.uid ilike '%".$search."%'
							or d.driver_name ilike '%".$search."%' or h.type ilike '%".$search."%
						)";
		}

		$sql =  "select h.type,g.bus_name,d.driver_name, e.po_name, d.plate_number, d.route_info, c.shelter_name, b.terminal_name, a.* from trx.t_trx_check_exit a 
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
				left join trx.t_trx_tap_out d on a.tap_out_id=d.id_seq
				left join master.t_mtr_po e on d.po_id=e.id_seq
				left join master.t_mtr_bus g on d.plate_number=g.plate_number
				left join master.t_mtr_bus_type h on g.bus_type_id=h.id_seq
				$where order by a.id_seq desc";

		return $this->db->query($sql);
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$shelter = decode($this->input->post('shelter'));
		$po = decode($this->input->post('po'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}

		if (!empty($shelter))
		{
			$where .="and (a.shelter_id=$shelter)";
		}

		if (!empty($po))
		{
			$where .="and (e.id_seq=$po)";
		}

		if (!empty($search))
		{
			$where .="and( d.plate_number ilike '%".$search."%'
							or d.route_info ilike '%".$search."%' or d.uid ilike '%".$search."%'
							or d.driver_name ilike '%".$search."%' or h.type ilike '%".$search."%'
						)";
		}

		$sql =  "select h.type,g.bus_name,d.driver_name, e.po_name, d.plate_number, d.route_info, c.shelter_name, b.terminal_name, a.* from trx.t_trx_check_exit a 
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				left join master.t_mtr_shelter c on a.shelter_id=c.id_seq
				left join trx.t_trx_tap_out d on a.tap_out_id=d.id_seq
				left join master.t_mtr_po e on d.po_id=e.id_seq
				left join master.t_mtr_bus g on d.plate_number=g.plate_number
				left join master.t_mtr_bus_type h on g.bus_type_id=h.id_seq
				$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {
	    	$r['created_on']=format_dateTime($r['created_on']);
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

}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */