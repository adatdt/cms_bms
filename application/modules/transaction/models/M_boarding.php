<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_boarding extends CI_Model {

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
		$po= decode($this->input->get('po'));
		$shelter= decode($this->input->get('shelter'));
		$integration= decode($this->input->get('integration'));
		
		// $where="where a.id_seq is not null and (a.prefix_qr='0' or a.prefix_qr is null )";

		$where="where a.id_seq is not null ";

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}
		
		if (!empty($po))
		{
			$where .="and(d.id_seq=$po or g.id_seq=$po)";
		}

		if($integration==1)
		{
			$where .=" and (a.prefix_qr !='0' and a.prefix_qr is not null) ";
		}
		else if ($integration==2)
		{
			$where .=" and (a.prefix_qr='0' or a.prefix_qr is null) ";	
		}


		if (!empty($shelter))
		{
			$where .="and(a.shelter_id=$shelter)";
		}

		if (!empty($search))
		{
			$where .="and( a.ticket_code ilike '%".$search."%' or c.booking_code ilike '%".$search."%'
						or b.route_info ilike '%".$search."%' or i.route_info ilike '%".$search."%' or f.type ilike '%".$search."%' or h.type ilike '%".$search."%'
					)";
		}

		// if (!empty($sortFrom) and !empty($sortTo))
		// {
		// 	$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		// }
		// else if(!empty($sortFrom) or !empty($sortTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		// }
		
		// if (!empty($po))
		// {
		// 	$where .="and(d.id_seq=$po)";
		// }

		// if (!empty($shelter))
		// {
		// 	$where .="and(e.id_seq=$shelter)";
		// }

		// if (!empty($search))
		// {
		// 	$where .="and( a.ticket_code ilike '%".$search."%' or c.booking_code ilike '%".$search."%'
		// 				or b.route_info ilike '%".$search."%' or f.type ilike '%".$search."%'
		// 			)";
		// }

		// $sql =  "
		// 		select f.type, e.shelter_name, c.booking_code, d.po_name, b.route_info, a.ticket_code,b.price,
		// 		a.* from trx.t_trx_boarding a 
		// 		left join trx.t_trx_booking_detail b on a.ticket_code=b.ticket_code
		// 		left join trx.t_trx_booking c on b.booking_id=c.id_seq
		// 		left join master.t_mtr_po d on b.po_id=d.id_seq
		// 		left join master.t_mtr_shelter e on b.shelter_id=e.id_seq
		// 		left join master.t_mtr_bus_type f on b.bus_type_id=f.id_seq
		// 		$where ORDER BY a.id_seq desc";

		$sql="
			select
			(
			 case when h.type is null then 
			 f.type
			 else h.type end
			) as type ,
			e.shelter_name, 
			(
			case when c.booking_code is null then 'Integration'
			else c.booking_code
			end
			) as booking_code, 
			(
			case when d.po_name is null
			then g.po_name
			else d.po_name
			end
			) as po_name
			,
			(
			case when a.prefix_qr is null then '-'
			when a.prefix_qr='0' then '-'
			else 'integration'
			end
			) as status_integration,
			(
			case when b.route_info is null then i.route_info
			else b.route_info
			end
			)as route_info,
			a.ticket_code,
			(
			case when a.price is null  then b.price
			else a.price end
			) as price2,
			a.* from trx.t_trx_boarding a 
			left join trx.t_trx_booking_detail b on a.ticket_code=b.ticket_code
			left join trx.t_trx_booking c on b.booking_id=c.id_seq
			left join master.t_mtr_po d on b.po_id=d.id_seq
			left join master.t_mtr_shelter e on a.shelter_id=e.id_seq
			left join master.t_mtr_bus_type f on b.bus_type_id=f.id_seq
			left join master.t_mtr_po g on a.prefix_qr=g.prefix_qr
			left join master.t_mtr_bus_type h on a.bus_type_id=h.id_seq
			left join master.t_mtr_route i on a.route_id=i.id_seq
			$where ORDER BY a.id_seq desc
		";

		return $this->db->query($sql);
	}


	public function getData()
	{
		$data = array();
		$search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$sortFrom = trim($this->input->post('sortFrom'));
		$sortTo = trim($this->input->post('sortTo'));
		$po = decode($this->input->post('po'));
		$shelter = decode($this->input->post('shelter'));
		$integration = decode($this->input->post('integration'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort 		= $this->input->post('sort') ? $this->input->post('sort') : 'a.id_seq';
		$order 		= $this->input->post('order') ? $this->input->post('order') : 'DESC';

		// $where="where a.id_seq is not null and (a.prefix_qr='0' or a.prefix_qr is null )";

		 $where="where a.id_seq is not null ";

		if (!empty($sortFrom) and !empty($sortTo))
		{
			$where .="and ( to_char(a.created_on,'yyyy-mm-dd') between '".$sortFrom."' and  '".$sortTo."' )";
		}
		else if(!empty($sortFrom) or !empty($sortTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd')='".$sortFrom."' or to_char(a.created_on,'yyyy-mm-dd')='".$sortTo."' )";
		}


		if($integration==1)
		{
			$where .=" and (a.prefix_qr !='0' and a.prefix_qr is not null) ";
		}
		else if ($integration==2)
		{
			$where .=" and (a.prefix_qr='0' or a.prefix_qr is null) ";	
		}

		if (!empty($po))
		{
			$where .="and(d.id_seq=$po or g.id_seq=$po)";
		}

		if (!empty($shelter))
		{
			$where .="and(a.shelter_id=$shelter)";
		}

		if (!empty($search))
		{
			$where .="and( a.ticket_code ilike '%".$search."%' or c.booking_code ilike '%".$search."%'
						or b.route_info ilike '%".$search."%' or i.route_info ilike '%".$search."%' or f.type ilike '%".$search."%' or h.type ilike '%".$search."%'
					)";
		}

		// $sql =  "
		// 		select f.type, e.shelter_name, c.booking_code, d.po_name, b.route_info, a.ticket_code,b.price,
		// 		a.* from trx.t_trx_boarding a 
		// 		left join trx.t_trx_booking_detail b on a.ticket_code=b.ticket_code
		// 		left join trx.t_trx_booking c on b.booking_id=c.id_seq
		// 		left join master.t_mtr_po d on b.po_id=d.id_seq
		// 		left join master.t_mtr_shelter e on b.shelter_id=e.id_seq
		// 		left join master.t_mtr_bus_type f on b.bus_type_id=f.id_seq
		// 		$where ORDER BY $sort $order";

		$sql =  "
					select
			(
			 case when h.type is null then 
			 f.type
			 else h.type end
			) as type ,
			e.shelter_name, 
			(
			case when c.booking_code is null then 'Integration'
			else c.booking_code
			end
			) as booking_code, 
			(
			case when d.po_name is null
			then g.po_name
			else d.po_name
			end
			) as po_name
			,
			(
			case when a.prefix_qr is null then '-'
			when a.prefix_qr='0' then '-'
			else 'integration'
			end
			) as status_integration,
			(
			case when b.route_info is null then i.route_info
			else b.route_info
			end
			)as route_info,
			a.ticket_code,
			(
			case when a.price is null  then b.price
			else a.price end
			) as price2,
			a.* from trx.t_trx_boarding a 
			left join trx.t_trx_booking_detail b on a.ticket_code=b.ticket_code
			left join trx.t_trx_booking c on b.booking_id=c.id_seq
			left join master.t_mtr_po d on b.po_id=d.id_seq
			left join master.t_mtr_shelter e on a.shelter_id=e.id_seq
			left join master.t_mtr_bus_type f on b.bus_type_id=f.id_seq
			left join master.t_mtr_po g on a.prefix_qr=g.prefix_qr
			left join master.t_mtr_bus_type h on a.bus_type_id=h.id_seq
			left join master.t_mtr_route i on a.route_id=i.id_seq
			$where ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
	    foreach ($query->result_array() as $r) {

	    	// if($r['prefix_qr']==0)
	    	// {
	    	// 	$r['status_integration']='Non Integration';
	    	// }
	    	// else
	    	// {
	    	// 	$r['status_integration']='Integration';
	    	// }

	    	$r['created_on']=format_dateTime($r['created_on']);
	    	$r['price2']=idr_currency($r['price2']);

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