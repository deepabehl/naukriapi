<?php

class User_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	
	}

	public function check_identity_already($identity,$table)
	{
		$this->db->select('*');
        $this->db->from($table);
        $this->db->where('email',$identity);
        $query = $this->db->get();
        $num_rows = $query->num_rows();

        return $num_rows;
	}
	public function check_mobile_already($mobile,$table)
	{
		$this->db->select('*');
        $this->db->from($table);
        $this->db->where('mobile',$mobile);
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        
        return $num_rows;
	}
	public function get_last_otp($mobile){
		$this->db->select('*');
		$this->db->from('pp_job_seekers');
		$this->db->where('mobile',$mobile);
		$result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}
	public function verify_otp($id){
		$this->db->set('is_mobile_verified','1');
		$this->db->where('ID',$id);
		$update = $this->db->update('pp_job_seekers');
		if($update==true){
			return true;
		}else{
			return false;
		}
	}
	public function update_users($mobile){
		$this->db->set('verifiedStatus','1');
		$this->db->where('phone_number',$mobile);
		$update = $this->db->update('users');
		if($update==true){
			return true;
		}else{
			return false;
		}
	}

	public function get_my_order($user_id)
	{
		$this->db->select('order_id,user_id,order_name,address_id,discount_id,amount,tax_amount,amount_after_tax,create_date,order_status');
		$this->db->from('orders');
		$this->db->where('user_id',$user_id);
		$this->db->where('order_status','successful');
		$this->db->order_by('order_id','DESC');
        $this->db->limit(1);
		$result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}

	public function get_order_history($user_id)
	{
		$this->db->select('order_id,user_id,order_name,address_id,discount_id,amount,tax_amount,amount_after_tax,create_date,order_status');
		$this->db->from('orders');
		$this->db->where('user_id',$user_id);
		$this->db->where('order_status','successful');
		$this->db->order_by('order_id','DESC');
		$result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array();
		}else{
			return array();
		}

	}
	public function get_my_cart_order($user_id,$oder_id)
	{
		$this->db->select('order_id,user_id,address_id,discount_id,amount,tax_amount,amount_after_tax,create_date,order_status');
		$this->db->from('orders');
		$this->db->where('user_id',$user_id);
		if(!empty($order_id))
		{
			$this->db->where('order_id',$order_id);
		}
		$this->db->where('order_status','pending');
		$this->db->order_by('order_id','DESC');
		$result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}

	
	public function get_order_details($order_id)
	{
		
		$this->db->select('oi.order_item_id,oi.order_id,oi.product_id ,oi.quantity,oi.price as order_item_calculatedprice,p.*,pd.size');
		$this->db->from('order_item oi');
		$this->db->join('product_details pd','pd.product_id = oi.product_id','LEFT');
		$this->db->join('products p','p.product_id  = oi.product_id','LEFT');
		$this->db->where('oi.order_id',$order_id);
		$result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array();
		}else{
			return array();
		}
	}

	public function getTermsCondition()
	{
		$this->db->select('*');
		$this->db->from('termsofuses');
		$this->db->where('status',1);
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
        $result = $this->db->get();
		
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}

	public function delete_order($order_id,$user_id)
	{
		$this->db->where('order_id',$order_id);
		$this->db->where('user_id',$user_id);
		$result = $this->db->delete('orders');
		if($result==true){
			return true;
		}else{
			return false;
		}
	}

	public function delete_order_item($order_id)
	{
        $this->db->where('order_id',$order_id);
		$result = $this->db->delete('order_item');
		if($result==true){
			return true;
		}else{
			return false;
		}
	}

	public function get_order_item($order_id)
	{
		$this->db->select('order_id');
		$this->db->from('order_item');
		$this->db->where('order_id ',$order_id);
        $result = $this->db->get();
		return $result->num_rows();
		
	}

	public function delete_last_order_item($order_item_id )
	{
		$this->db->where('order_item_id',$order_item_id);
		$result = $this->db->delete('order_item');
		if($result==true){
			return true;
		}else{
			return false;
		}
	}

	public function get_order_item_price($order_item_id)
	{
		$this->db->select('order_id,price');
		$this->db->from('order_item');
		$this->db->where('order_item_id',$order_item_id);
        $result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}

	public function update_amount($user_id,$order_id,$total_price){
		
		$update = $this->db->query('UPDATE orders SET amount=amount-'.$total_price.' WHERE user_id ='.$user_id.' AND order_id ='.$order_id.'');

		
        if($update==true){
            return true;
        }else{
            return false;
        }
    }

	public function get_address_status($user_id,$address_id)
	{
		$this->db->select('is_default');
		$this->db->from('address');
		$this->db->where('userId',$user_id);
		$this->db->where('address_id',$address_id);
        $result = $this->db->get();
		if($result->num_rows()>0){
			return $result->result_array()[0];
		}else{
			return array();
		}
	}

	public function delete_address($user_id,$address_id)
	{
		$this->db->where('userId',$user_id);
		$this->db->where('address_id',$address_id);
		$result = $this->db->delete('address');
		if($result==true){
			return true;
		}else{
			return false;
		}
	}

	public function make_address_default($user_id)
	{
		$this->db->set('is_default','1');
		$this->db->where('userId',$user_id);
		$this->db->order_by('address_id','DESC');
		$this->db->limit(1);
		$update = $this->db->update('address');
		if($update==true){
			return true;
		}else{
			return false;
		}
	}

}