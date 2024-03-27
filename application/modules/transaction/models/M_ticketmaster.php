<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_ticketmaster extends CI_Model {

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
		
		$where='where a.id_seq is not null ';

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}


		if (!empty($search))
		{
			$where.="and(a.pic_name ilike '%".$search."%' or a.pic_phone ilike '%".$search."%'
						or b.terminal_name ilike '%".$search."%' or a.ticket_code ilike '%".$search."%'
					)";
		}

		$sql="
				select b.terminal_name, a.* from trx.t_trx_access_boarding a
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				$where order by a.id_seq desc";

		return $this->db->query($sql);
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
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


		if (!empty($search))
		{
			$where.="and(a.pic_name ilike '%".$search."%' or a.pic_phone ilike '%".$search."%'
						or b.terminal_name ilike '%".$search."%' or a.ticket_code ilike '%".$search."%'
					)";
		}

		$sql="
				select b.terminal_name, a.* from trx.t_trx_access_boarding a
				left join master.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
				$where ORDER BY $sort $order
		";

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

	public function getDetail($id)
	{
		return $this->db->query("
			select b.shelter_order, e.shelter_name, d.po_name, c.airport_name, a.* from trx.t_trx_tap_out a 
			left join trx.t_trx_journey_cycle b  on a.id_seq=b.tap_out_id
			left join master.t_mtr_airport c on a.airport_id=c.id_seq
			left join master.t_mtr_po d on a.po_id=d.id_seq
			left join master.t_mtr_shelter e on b.shelter_id=e.id_seq
			where a.id_seq=$id and b.status !='-5' order by b.shelter_order asc
			")->result();
	}

	public function getDetail2($id)
	{
		// return $this->db->query("
		// 		select  a.driver_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_out a 
		// 		left join master.t_mtr_airport b on a.airport_id=b.id_seq
		// 		left join master.t_mtr_po c on a.po_id=c.id_seq
		// 		left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code
		// 		left join master.t_mtr_driver e on a.uid=e.uid
		// 		where a.id_seq=$id
		// 	")->row();

		return $this->db->query("
				select f.type,e.bus_name, d.terminal_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_out a 
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code 
				left join master.t_mtr_bus e on a.plate_number=e.plate_number
				left join master.t_mtr_bus_type f on e.bus_type_id=f.id_seq 
				where a.id_seq=$id
			")->row();
	}

	public function getBusId($id)
	{
		return $this->db->query("
			select b.bus_id , a.* from trx.t_trx_tap_out a
			join master.t_mtr_driver b on a.uid=b.uid and b.status=1
			where a.id_seq=$id
			")->row();
	}

	public function getEdit($id)
	{
		return $this->db->query("
				select f.id_seq as type_id, f.type, e.id_seq as bus_id, e.bus_name, d.terminal_name, c.po_name, b.airport_name,a.* from trx.t_trx_tap_out a 
				left join master.t_mtr_airport b on a.airport_id=b.id_seq
				left join master.t_mtr_po c on a.po_id=c.id_seq
				left join master.t_mtr_device_terminal d on a.terminal_code=d.terminal_code 
				left join master.t_mtr_bus e on a.plate_number=e.plate_number
				left join master.t_mtr_bus_type f on e.bus_type_id=f.id_seq 
				where a.id_seq=$id
			");
	}

		public function getEdit2($id)
	{
		return $this->db->query("
				select b.shelter_id, b.shelter_order, e.shelter_name, d.po_name, c.airport_name, a.* from trx.t_trx_tap_out a 
				left join trx.t_trx_journey_cycle b  on a.id_seq=b.tap_out_id
				left join master.t_mtr_airport c on a.airport_id=c.id_seq
				left join master.t_mtr_po d on a.po_id=d.id_seq
				left join master.t_mtr_shelter e on b.shelter_id=e.id_seq
				where a.id_seq=$id order by b.shelter_order asc
			")->result();
	}

	public function getRoute($where)
	{
		return $this->db->query("select b.po_id, b.bus_type_id, a.* from master.t_mtr_route a
					left join master.t_mtr_fare b on a.id_seq=b.route_id
					$where
					"
				);
	}

	public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);
    }

    public function deleteData($table,$where)
    {
    	
    	$this->db->query("delete from $table where $where");
    }

	
}

/* End of file M_gatein.php */
/* Location: ./application/models/M_gatein.php */
