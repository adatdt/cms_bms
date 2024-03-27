<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_dashboard extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_tap_in() {
		$sql = 'select count(id_seq) AS count from trx.t_trx_tap_in where date(created_on) = current_date and status = 1';
		return $this->db->query($sql)->row()->count;
	}

	public function get_exit_terminal() {
		$sql = 'select count(id_seq) AS count from trx.t_trx_check_exit where date(created_on) = current_date';
		return $this->db->query($sql)->row()->count;
	}

	public function get_booking() {
		$sql = ' select count(det.id_seq) AS count
								from trx.t_trx_booking book

								join trx.t_trx_booking_detail det on det.booking_id = book.id_seq
								WHERE date(book.created_on) = current_date AND det.status IN (2, 3)';
		return $this->db->query($sql)->row()->count;
	}

	public function get_boarding() {
		$sql = 'select count(id_seq) AS count from trx.t_trx_boarding where date(created_on) = current_date';
		return $this->db->query($sql)->row()->count;
	}

// add status=1
	public function get_booking_passanger() {
		$sql = 'select shel.shelter_name, count(tt.booking_id) as count
						from master.t_mtr_shelter shel
						left join (
								select book.terminal_code, det.shelter_id, book.id_seq as booking_id 
								from trx.t_trx_booking book
								join trx.t_trx_booking_detail det on det.booking_id = book.id_seq
								-- join master.t_mtr_device_terminal term on term.terminal_code =  book.terminal_code
								WHERE date(book.created_on) = current_date AND det.status IN (2)
						) as tt on tt.shelter_id = shel.id_seq
						where  shel.status=1
						group by shel.id_seq, shel.shelter_name
						order by shelter_name';
		return $this->db->query($sql)->result();
	}

	public function getDataDashboardPo($id)
	{
		$data = array();
		// $search = trim(strtoupper($this->db->escape_like_str($this->input->post('search'))));
		$search=trim($this->input->post('search'));
		$po=decode($this->input->post('po'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort     = $this->input->post('sort') ? $this->input->post('sort') : 'route_info';
		$order    = $this->input->post('order') ? $this->input->post('order') : 'ASC';
		$group = " GROUP BY route_info, pool, lp.last_paid, max_wait ";

		$where = " WHERE po_id = 0 AND fre.status = 1";
		$where_po_atas = "";

		// jika tidak milih po
		if(!empty($po))
		{
			$where = " WHERE po_id = $po AND fre.status = 1";
			$where_po_atas = " AND ttti.po_id = $po";
			$po_t_1 = "AND bt2.po_id = $po";
			$po_t_2 = "AND bk2.po_id = $po";
			$po_t_3 = "AND tpo.po_id = $po";
		}
		else
		{
			$where = " WHERE po_id = $id AND fre.status = 1 ";
			$where_po_atas = " AND ttti.po_id = $id";
			$po_t_1 = "AND bt2.po_id = $id";
			$po_t_2 = "AND bk2.po_id = $id";
			$po_t_3 = "AND tpo.po_id = $id";
		}

		if (!empty($search))
		{
			$where .="AND rt.route_info ilike  '%".$search."%' ";
		}

		$sql =  "SELECT
	route_info,
	pool,
	SUM ( t1.COUNT ) AS t1,
	SUM ( t4.COUNT ) AS t2,
	SUM ( t5.COUNT ) AS t3,
	SUM ( pax.COUNT ) AS total_passenger,
	SUM ( cap.COUNT ) AS total_seat,
	MAX ( cex.last_depart ) AS last_depart,
	LP.last_paid,
	MW.max_wait
FROM
	master.t_mtr_fare fre
	JOIN master.t_mtr_route rut ON rut.id_seq = fre.route_id 
	AND rut.status = 1
	LEFT JOIN (
SELECT
	sub.route_id,
	COUNT ( ttti.uid ) AS pool 
FROM
	trx.t_trx_tap_in ttti
	JOIN (
SELECT DISTINCT ON
	( ttti.uid ) ttti.uid,
	ttto.route_id 
FROM
	trx.t_trx_tap_in ttti
	JOIN trx.t_trx_tap_out ttto ON ttto.uid = ttti.uid 
WHERE
	ttti.status = 1 
	$where_po_atas
ORDER BY
	ttti.uid,
	ttto.id_seq DESC 
	) sub ON sub.uid = ttti.uid
	JOIN master.t_mtr_driver D ON D.uid = sub.uid 
	AND D.status = 1
	JOIN master.t_mtr_po PO ON PO.id_seq = D.po_id 
	AND PO.status = 1 
WHERE
	ttti.status = 1 
	$where_po_atas
GROUP BY
	sub.route_id 
	) AS pool ON pool.route_id = rut.id_seq
	LEFT JOIN (
SELECT COUNT
	( pymt2.id_seq ),
	bt2.route_code 
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code 
	AND bt2.shelter_id = 1 
	AND bt2.status = 2 
	$po_t_1
	JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
	$po_t_2
WHERE
	DATE ( pymt2.created_on ) = CURRENT_DATE 
GROUP BY
	route_code 
	) AS t1 ON t1.route_code = rut.route_code
	LEFT JOIN (
SELECT COUNT
	( pymt2.id_seq ),
	bt2.route_code 
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code 
	AND bt2.shelter_id = 4 
	AND bt2.status = 2 
	$po_t_1
	JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
	$po_t_2
WHERE
	DATE ( pymt2.created_on ) = CURRENT_DATE 
GROUP BY
	route_code 
	) AS t4 ON t4.route_code = rut.route_code
	LEFT JOIN (
SELECT COUNT
	( pymt2.id_seq ),
	bt2.route_code 
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code AND bt2.shelter_id = 5 
	AND bt2.status = 2 
	$po_t_1
	JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
	$po_t_2
WHERE
	DATE ( pymt2.created_on ) = CURRENT_DATE 
GROUP BY
	route_code 
	) AS t5 ON t5.route_code = rut.route_code
	LEFT JOIN (
SELECT COUNT
	( bt2.id_seq ),
	bt2.route_code 
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code 
	AND bt2.status = 2JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
WHERE
	DATE ( pymt2.created_on ) = CURRENT_DATE 
	$po_t_1 
GROUP BY
	bt2.route_code 
	) AS pax ON pax.route_code = rut.route_code
	LEFT JOIN (
SELECT COALESCE
	( SUM ( bus.total_seat ), 0 ) AS COUNT,
	tpo.route_id 
FROM
	trx.t_trx_tap_out tpo
	JOIN master.t_mtr_bus bus ON tpo.plate_number = bus.plate_number
	JOIN master.t_mtr_route rt2 ON tpo.route_id = rt2.id_seq 
WHERE
	tpo.status = 1 
	AND DATE ( tpo.created_on ) = CURRENT_DATE 
	$po_t_3
GROUP BY
	tpo.route_id 
	) AS cap ON cap.route_id = rut.id_seq
	LEFT JOIN (
SELECT
	tpo.route_id,
	MAX ( ce.created_on ) AS last_depart
FROM
	trx.t_trx_check_exit ce
	JOIN trx.t_trx_tap_out tpo ON ce.tap_out_id = tpo.id_seq AND tpo.po_id = 1
	JOIN master.t_mtr_route rt2 ON tpo.route_id = rt2.id_seq 
GROUP BY
	tpo.route_id 
	) AS cex ON cex.route_id = rut.id_seq 
	LEFT JOIN(
	SELECT MAX
	( pymt2.created_on ) as last_paid,
	bt2.route_code 
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code
	AND bt2.status = 2 
	$po_t_1
	JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
	$po_t_2 
WHERE
	DATE ( pymt2.created_on ) = CURRENT_DATE 
GROUP BY
	route_code) AS LP ON LP.route_code = rut.route_code
	LEFT JOIN(
	SELECT EXTRACT
	( epoch FROM ( CURRENT_TIMESTAMP - MAX ( pymt2.created_on ) ) ) as max_wait,
	bt2.route_code
FROM
	trx.t_trx_payment pymt2
	JOIN trx.t_trx_booking_detail bt2 ON pymt2.ticket_code = bt2.ticket_code 
	AND bt2.status = 2
	JOIN trx.t_trx_booking bk2 ON bt2.booking_id = bk2.id_seq 
	AND DATE ( pymt2.created_on ) = CURRENT_DATE 
	$po_t_1
	GROUP BY route_code) AS MW ON MW.route_code = rut.route_code
	  $where $group
	 ORDER BY $sort $order";

		$query = $this->db->query($sql);
		// echo $this->db->last_query();exit;
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
		foreach ($query->result_array() as $r) {
			// $action = '';
			if($r['total_passenger']>=$r['total_seat'])
			{
				$status='<span class="label label-flat border-warning text-warning-600">Need More Bus</span>';       
			}
			else
			{
				$status='<span class="label label-flat border-success text-success-600">GO!</span>';
			}
			$r['last_depart']=($r['last_depart']!=null) ? format_dateTimeSlash($r['last_depart']) : null;
			$r['last_paid']=($r['last_paid']!=null) ? format_dateTimeSlash($r['last_paid']) : null;
			$r['max_wait']=($r['max_wait']!=null) ? format_recent($r['max_wait']) : null;
			$r['pool']=($r['pool']!=null) ? $r['pool'] : 0;
			$r['t1']=($r['t1']!=null) ? $r['t1'] : 0;
			$r['t2']=($r['t2']!=null) ? $r['t2'] : 0;
			$r['t3']=($r['t3']!=null) ? $r['t3'] : 0;
			$r['total_passenger']=($r['total_passenger']!=null) ? $r['total_passenger'] : 0;
			$r['total_seat']=($r['total_seat']!=null) ? $r['total_seat'] : 0;
			$r['status']=$status;
			$r['pax_capacity']=$r['total_passenger']." / ".$r['total_seat'];
			$data_rows[] = $r;
		}

		$data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}


	public function getTracking($id = "")
	{
		$data = array();
		$search=trim($this->input->post('search'));
		$po=decode($this->input->post('po'));
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$rows = $this->input->post('rows') ? $this->input->post('rows') : 10;
		$offset = ($page - 1) * $rows;
		$sort     = $this->input->post('sort') ? $this->input->post('sort') : 'route_info';
		$order    = $this->input->post('order') ? $this->input->post('order') : 'ASC';

		$where = "WHERE PO.id_seq = 0";
		$where_po = "";
		$where_pool = "";
		$bus_bandara = "";

		// $where='where a.id_seq is not null and b.status in (1,-1) ';

		$query_t1 = " AND t1.po_id = $po";
		$query_t2 = " AND t2.po_id = $po";
		$query_t3 = " AND t3.po_id = $po";

		if ($id != "") {
			$where = " WHERE PO.id_seq = $id AND F.status=1
								AND B.status=1
								AND R.status=1";
			$where_po = " AND B.po_id = $id";
			$bus_bandara = " AND ttto.po_id = $id";
			$where_pool = " AND ttti.po_id = $id";

			$query_t1 = " AND t1.po_id = $id";
			$query_t2 = " AND t2.po_id = $id";
			$query_t3 = " AND t3.po_id = $id";
		}

		if(!empty($po))
		{
			$where = " WHERE PO.id_seq = $po AND F.status=1
								AND B.status=1
								AND R.status=1";
			$bus_bandara = " AND ttto.po_id = $po";
			$where_po = " AND B.po_id = $po";
			$where_pool = " AND ttti.po_id = $po";
		}
		

		$sql =  "SELECT DISTINCT(sub3.id_seq),
			 sub3.route_info,
			 sub3.pool,
			 sub3.t1,
			 sub3.t2,
			 sub3.t3,
			 sub3.jumlah_bus,
			 sub3.bus_bandara
FROM master.t_mtr_po PO
JOIN master.t_mtr_driver D ON D.po_id = PO.id_seq
JOIN master.t_mtr_fare F ON F.po_id = PO.id_seq AND F.status = 1
JOIN master.t_mtr_route R ON R.id_seq = F.route_id
JOIN master.t_mtr_bus B ON B.po_id = PO.id_seq
RIGHT JOIN
	(SELECT DISTINCT(tmr.id_seq),
					tmr.route_info,
					sub1.pool,
					bus.jumlah_bus,
					t1.jumlah AS T1,
					t2.jumlah AS T2,
					t3.jumlah AS T3,
					bandara.bus_bandara
	 FROM master.t_mtr_route tmr
	 LEFT JOIN master.t_mtr_fare F ON F.route_id = tmr.id_seq
	 JOIN master.t_mtr_po PO ON PO.id_seq = F.po_id
	 JOIN master.t_mtr_bus B ON B.po_id = PO.id_seq
	 LEFT JOIN(SELECT
						 PO.id_seq AS po_id,
             sub.route_id,
             count(ttto.uid) AS bus_bandara
      FROM trx.t_trx_tap_out ttto
      JOIN
        (SELECT DISTINCT ON (ttto.uid) ttto.uid,
                            ttto.route_id
         FROM trx.t_trx_tap_out ttto
         WHERE ttto.status = 1 $bus_bandara
         ORDER BY ttto.uid,
                  ttto.id_seq DESC) sub ON sub.uid = ttto.uid
      JOIN master.t_mtr_driver D ON D.uid = sub.uid
      AND D.status=1
      JOIN master.t_mtr_po PO ON PO.id_seq = D.po_id
      AND PO.status=1
      WHERE ttto.status = 1 
      GROUP BY sub.route_id,
							 PO.id_seq)bandara ON bandara.route_id = tmr.id_seq
	 LEFT JOIN
			(SELECT sub.route_id,
			 count(B.plate_number) AS jumlah_bus
			FROM master.t_mtr_bus B
			JOIN
				(SELECT DISTINCT ON (B.id_seq) B.id_seq,
														ttto.route_id,B.plate_number
				 FROM master.t_mtr_bus B
				 JOIN trx.t_trx_tap_out ttto ON ttto.plate_number = B.plate_number
				 WHERE B.status = 1 $where_po
				 ORDER BY B.id_seq,ttto.id_seq DESC) sub ON sub.plate_number = B.plate_number
			WHERE B.status = 1
			GROUP BY sub.route_id)bus ON bus.route_id = tmr.id_seq
	 LEFT JOIN
		 (SELECT sub.route_id,
						 count(ttti.uid) AS pool
			FROM trx.t_trx_tap_in ttti
			JOIN
				(SELECT DISTINCT ON (ttti.uid) ttti.uid,
														ttto.route_id
				 FROM trx.t_trx_tap_in ttti
				 JOIN trx.t_trx_tap_out ttto ON ttto.uid = ttti.uid
				 WHERE ttti.status = 1 $where_pool
				 ORDER BY ttti.uid,
									ttto.id_seq DESC) sub ON sub.uid = ttti.uid
			JOIN master.t_mtr_driver D ON D.uid = sub.uid
			AND D.status=1
			JOIN master.t_mtr_po PO ON PO.id_seq = D.po_id
			AND PO.status=1
			WHERE ttti.status = 1 $where_pool
			GROUP BY sub.route_id) sub1 ON sub1.route_id = tmr.id_seq
	 LEFT JOIN
		 (SELECT TTO.route_id,TTO.po_id,
						 count(TTCI.id_seq) AS jumlah
			FROM trx.t_trx_check_in TTCI
			JOIN trx.t_trx_tap_out TTO ON TTO.id_seq = TTCI.tap_out_id
			WHERE TTCI.status =1
				AND TTCI.shelter_id=1
			GROUP BY TTO.route_id,TTO.po_id) t1 ON t1.route_id = tmr.id_seq $query_t1
	 LEFT JOIN
		 (SELECT TTO.route_id,TTO.po_id,
						 count(TTCI.id_seq) AS jumlah
			FROM trx.t_trx_check_in TTCI
			JOIN trx.t_trx_tap_out TTO ON TTO.id_seq = TTCI.tap_out_id
			WHERE TTCI.status =1
				AND TTCI.shelter_id=4
			GROUP BY TTO.route_id,TTO.po_id) t2 ON t2.route_id = tmr.id_seq $query_t2
	 LEFT JOIN
		 (SELECT TTO.route_id,TTO.po_id,
						 count(TTCI.id_seq) AS jumlah
			FROM trx.t_trx_check_in TTCI
			JOIN trx.t_trx_tap_out TTO ON TTO.id_seq = TTCI.tap_out_id
			WHERE TTCI.status =1
				AND TTCI.shelter_id=5
			GROUP BY TTO.route_id,TTO.po_id) t3 ON t3.route_id = tmr.id_seq $query_t3
	 ORDER BY tmr.id_seq,route_info ASC) sub3 ON sub3.id_seq = R.id_seq
	 $where
	 ORDER BY $sort $order";

		$query = $this->db->query($sql);
		$total_rows = $query->num_rows();
		$sql .= " LIMIT $rows OFFSET $offset";
		$query = $this->db->query($sql);

		$data_rows = array();
			foreach ($query->result_array() as $r) {
				$r['pool'] = $r['pool'] != NULL ? $r['pool'] : 0 ;
				$r['t1'] = $r['t1'] != NULL ? $r['t1'] : 0 ;
				$r['t2'] = $r['t2'] != NULL ? $r['t2'] : 0 ;
				$r['t3'] = $r['t3'] != NULL ? $r['t3'] : 0 ;
				$r['jumlah_bus'] = $r['jumlah_bus'] != NULL ? $r['jumlah_bus'] : 0 ;
				$r['jumlah_semua'] = $r['bus_bandara']+$r['pool'] . " / " . $r['jumlah_bus'];
				$r['id_seq'] = encode($r['id_seq']);

				$data_rows[] = $r;
			}

			$data['total'] = $total_rows;
		$data['rows'] = $data_rows;
		return $data;
	}

	private function query_pengendapan($id)
	{
		$query = $this->db->query("SELECT
			COUNT(SELECT DISTINCT ON (TTI.uid)) AS jumlah
			FROM
			trx.t_trx_tap_in TTI
			RIGHT JOIN trx.t_trx_tap_out TTO ON TTO.uid = TTI.uid AND TTO.route_id=$id
			WHERE TTI.status=1 AND TTO.status=0 ORDER BY TTI.uid, TTO.id_seq DESC")->row();

		return $query->jumlah;
	}

	// private function query_t1a($id)
	// {
	// 	$query = $this->db->query("SELECT uid FROM trx.t_trx_tap_out WHERE status=1 AND uid='6EFBC534' AND route_id = 2 ORDER BY id_seq DESC LIMIT 1");
	// }

}

/* End of file M_dashboard.php */
/* Location: ./application/models/M_dashboard.php */