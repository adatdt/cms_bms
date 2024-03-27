<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_privileges extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		getSession();
		
	}

	public function group_name()
	{
		return $this->db->query('SELECT * FROM core."t_mtr_user_group" WHERE status=1')->result();
	}

	public function getDetail()
	{
		$result=array();
		$items=array();
		$user_group_id=$this->input->post('user_group_id');

		$qry=$this->db->query("select * from core.t_mtr_menu where parent_id=0  and status=1 order by ".'"menu_order"'." asc");

		$query=$qry->result();

		if($query)
		{
			foreach ($query as $row)
			{
				$has_child=$this->check_parent($row->id_seq);
				//$row->iconCls = $row->icon;
			//	$row->state	  = 'open';

				if ($has_child)
				{
					$row->children =$this->get_list_children($row->id_seq);
				}

				$parent = $row->parent_id;

		        if($row->parent_id == '' || $row->parent_id = null){
		            $parent = 0;
		        }
	            $row->action  = $this->select_menu_detail($row->id_seq,$parent,$user_group_id);
	          array_push($items, $row);  
			}
		}
		$result['rows']=$items;
		return $result;
	}

	function get_list_children($pi) {
		$items  = array();
		$user_group_id=$this->input->post('user_group_id');

		$sql 	= 'select  * FROM core.t_mtr_menu 
				   WHERE parent_id ='.$pi.' and  status=1 
				   ORDER BY "menu_order" asc';
				   
		$query 	= $this->db->query($sql)->result();

//		$active    = '<span class="label bg-green">Active</span>';
  //      $nonactive = '<span class="label bg-red">Not Active</span>';
		
		if($query){
			foreach ($query as $row){
				$has_child 	  = $this->check_parent($row->id_seq);
				//$row->iconCls = $row->icon;
				//$row->state	  = 'open';

	            if($has_child){
					$row->children = $this->get_list_children($row->id_seq);
				}

				$parent = $row->parent_id;

		        if($row->parent_id == '' || $row->parent_id = null){
		            $parent = 0;
		        }

		        //$row->name="<span style='padding:0px 20px'>".$row->name."</span>";
		        //$row->name=$row->name;
	            $row->action  = $this->select_menu_detail($row->id_seq,$parent,$user_group_id);
					
				array_push($items, $row);
			}
		}
		
		return $items;
	}

	public function check_parent($parent_id)
	{
		$row=$this->db->query("select * from core.t_mtr_menu where parent_id=$parent_id and status=1 order by ".'"menu_order"'." asc")->num_rows();

		if($row>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function select_menu_detail($id,$parent,$user_group_id)
	{
		$data=array();
		$qry="
			select c.id_seq as m_id, a.id_seq as m_detail_id, d.status as privilege_status, d.id_seq as privilege_id, b.name as action_name, c.name as menu_name, * from core.t_mtr_menu_detail a
			left join core.t_mtr_menu_action b on a.action_id=b.id_seq
			left join core.t_mtr_menu c on a.menu_id=c.id_seq
			left join  core.t_mtr_privilege d on a.id_seq=d.menu_detail_id and d.user_group_id=$user_group_id 
			where   a.menu_id=$id and c.parent_id=$parent and a.status=1 
			order by b.id_seq asc
		";

		$html="<div>";
		$result=$this->db->query($qry)->result();

		if($result)
		{
			foreach($result as $row)
			{
				if ($row->privilege_status==1)
				{
					$checkbox='	
					    <div class="material-switch pull-left" style="line-height:30px">'.$row->action_name.'&nbsp; &nbsp;
					    <input id="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" name="someSwitchOption001" type="checkbox" checked onClick="update(\''.encode($row->privilege_id).'\')" />
                        <label for="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" class="label-warning"></label>
                    	</div>
					    ';
				}
				else if ($row->privilege_status==null)
				{
					$checkbox='	
					    <div class="material-switch pull-left" style="line-height:30px">'.$row->action_name.'&nbsp; &nbsp;
					    <input id="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" name="someSwitchOption001" type="checkbox"  onClick="insert(\''.encode($row->m_detail_id).'\',\''.encode($user_group_id).'\',\''.encode($row->m_id).'\')" />
                        <label for="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" class="label-warning"></label>
                    	</div>
					    ';
				}
				else if ($row->privilege_status==-5 || $row->privilege_status==0 )
				{
					
					$checkbox='	
					    <div class="material-switch pull-left" style="line-height:30px">'.$row->action_name.'&nbsp; &nbsp;
					    <input id="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" name="someSwitchOption001" type="checkbox"  onClick="update(\''.encode($row->privilege_id).'\')" />
                        <label for="someSwitchOptionWarning\''.encode($row->m_detail_id).'\'" class="label-warning"></label>
                    	</div>  
					';
				}
				
				$html.=$checkbox;					

				$data[]=$html;
			}

		}
		$html.="</div>";

		return $html;
	}

	public function selectById($table,$where)
	{
		return $this->db->query("select * from $table where $where")->row();
	}

	public function update($table,$data,$where)
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

	public function insert($table,$data)
	{
		$this->db->insert($table,$data);

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

/* End of file M_privileges.php */
/* Location: ./application/models/M_privileges.php */