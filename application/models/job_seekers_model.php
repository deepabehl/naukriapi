<?php
class Job_Seekers_Model extends CI_Model {
    public function __construct() {
	   $this->load->database();
    }
    
	public function add_job_seekers($data){
  
            $return = $this->db->insert('pp_job_seekers', $data);
            if ((bool) $return === TRUE) {
                return $this->db->insert_id();
            } else {
                return $return;
            }       
			
	}	

    public function add_seeker_prefered_cities($data)
    {
          $return = $this->db->insert('pp_jobseeker_career_profile', $data);
            if ((bool) $return === TRUE) {
                return $this->db->insert_id();
            } else {
                return $return;
            }       
    }

    public function career_add($data)
    {
          $return = $this->db->insert('pp_jobseeker_career_profile', $data);
            if ((bool) $return === TRUE) {
                return $this->db->insert_id();
            } else {
                return $return;
            }      
    }
	
	public function update_job_seeker($mobile_number, $data){
		$this->db->where('mobile', $mobile_number);
		$return=$this->db->update('pp_job_seekers', $data);
		return $return;
	}


    public function update_email_campaign_jobseeker($camp_id,$chunk_id,$email,$sent_status)
    {
        $this->db->set('sent',$sent_status);
        $this->db->where('camp_id', $camp_id);
        $this->db->where('chunks_id', $chunk_id);
        $this->db->where('jobseeker_email', $email);
        $return = $this->db->update('pp_campaign_status');

        return $return;
        
    }

    public function update_chunk_success($camp_id,$chunk_id,$chunk_data)
    {
        $this->db->where('camp_id',$camp_id);
        $this->db->where('id',$chunk_id);
        $return = $this->db->update('email_chunks_champign',$chunk_data);
        return $return;

    }
	
	public function update($id, $data){

		$this->db->where('ID', $id);
		$return=$this->db->update('pp_job_seekers', $data);

		return $return;
	}

    public function update_mobile($id,$mobile)
    {
      $this->db->where('ID', $id);
      $this->db->set('mobile',$mobile);
      $this->db->update('pp_job_seekers');
    }

    public function career_update($id, $data){

        $this->db->where('seeker_id', $id);
        $return=$this->db->update('pp_jobseeker_career_profile', $data);
        return $return;
    }

    public function career_exist($seeker_id)
    {
        $this->db->where('seeker_id',$seeker_id);
        $query = $this->db->get('pp_jobseeker_career_profile');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    

    public function experience_update($id,$years,$months)
    {

        $this->db->where('ID', $id);
        $this->db->set('experience_years',$years);
        $this->db->set('experience_months',$months);
        $return=$this->db->update('pp_job_seekers');
      
        return $return;
    }
	
	public function delete_job_seeker($id){
		$this->db->where('ID', $id);
		$this->db->delete('pp_job_seekers');
	}
	
	public function authenticate_job_seeker($user_name, $password) {
        $this->db->select('*');
        $this->db->from('pp_job_seekers');
        $this->db->where('email', $user_name);
		$this->db->where('password', $password);
		$this->db->limit(1);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function authenticate_job_seeker_email_address($user_name) {
        $this->db->select('*');
        $this->db->from('pp_job_seekers');
        $this->db->where('email', $user_name);
		$this->db->limit(1);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function authenticate_job_seeker_by_id_password($ID, $password) {
        $this->db->select('*');
        $this->db->from('pp_job_seekers');
        $this->db->where('ID', $ID);
		$this->db->where('password', $password);
		$this->db->limit(1);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_job_seekers_experience_by_ID($seeker_id)
    {

       $new_array = array();
       $this->db->select('*');
       $this->db->from('pp_seeker_experience');
       $this->db->where('seeker_ID',$seeker_id);
       $q= $this->db->get();
       $data = $q->result_array();
       $experience_count = count($data);
       if($q->num_rows == 1)
       {
         
         $datanew['start_date'] = $data[0]['start_date'];
        // echo isset($data[0]['end_date']); DIE();
         if(isset($data[0]['end_date']))
         {
            $datanew['end_date'] = $data[0]['end_date'];
           
         }
         else
         {
            $datanew['end_date'] =  date("Y-m-d");
         }


         
       }
       else
       {
        
        $datanew['start_date'] = $data[0]['start_date'];
          if(isset($data[$experience_count]['end_date']))
         {
            $datanew['end_date'] = $data[$experience_count]['end_date'];
          
         }
         else
         {
            $datanew['end_date'] =  date("Y-m-d");
         }
       }

       $datanew['seeker_ID'] = $seeker_id;

          
          return $datanew;
      
   
        
    }

    public function get_all_job_seekers_new($seeker_ID,$start_date,$end_date)
    {
           $Q = $this->db->query("SELECT TIMESTAMPDIFF(YEAR, '$start_date','$end_date')AS years,TIMESTAMPDIFF(MONTH,'$start_date' + INTERVAL TIMESTAMPDIFF(YEAR, '$start_date', '$end_date') YEAR, '$end_date') AS months FROM (`pp_job_seekers`) where ID = ".$seeker_ID);
         
   
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	
 public function get_all_job_seekers($per_page, $page) {


     
        $this->db->select("pp_job_seekers.*,pp_seeker_skills.skill_name");
        $this->db->from('pp_job_seekers');
    
        $this->db-> group_by ('pp_job_seekers.ID');
/*        $this->db-> join ('pp_seeker_experience','pp_seeker_experience.seeker_ID =pp_job_seekers.ID','left');
*/        $this->db->order_by("pp_job_seekers.ID", "DESC"); 
       $this->db->join('pp_seeker_skills','pp_seeker_skills.seeker_ID = pp_job_seekers.ID','left');

		if(!empty($per_page))
        {
            $this->db->limit($per_page, $page);
        }

        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_job_seekers_script()
    {
        $this->db->select("pp_job_seekers.*");
        $this->db->from('pp_job_seekers');
        $this->db->join('pp_seeker_experience','pp_seeker_experience.seeker_ID=pp_job_seekers.ID');
          $this->db->order_by("pp_job_seekers.ID", "DESC"); 
           $Q = $this->db->get();
           if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function record_count($table_name) {
		return $this->db->count_all($table_name);
       

    }
   

    public function company_matching($table_name) {
        return $this->db->count_all($table_name);
       

    }
	
	public function get_job_seeker_by_id($id) {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
		$this->db->where('pp_job_seekers.ID', $id);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_job_seeker_by_email($email)
    {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        $this->db->where('pp_job_seekers.email', $email);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_job_seeker_carrer_by_id($seeker_id)
    {
        $this->db->select('*');
        $this->db->from('pp_jobseeker_career_profile');
        $this->db->where('seeker_id', $seeker_id);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    //deepa 6-10-2021 for get percentage master

    public function get_jobseeker_percentage()
    {
       $this->db->select('*');
        $this->db->from('action_peroform');
        $this->db->where('type','jobseeker');
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_pending_emails($camp_id,$chunk_id)
    {
        $this->db->select('jobseeker_email');
        $this->db->from('pp_campaign_status');
        $this->db->where('pp_campaign_status.camp_id', $camp_id);
        $this->db->where('pp_campaign_status.chunks_id', $chunk_id);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_sent_emails($camp_id,$chunk_id)
    {
        $this->db->select('jobseeker_email');
        $this->db->from('pp_campaign_status');
        $this->db->where('pp_campaign_status.camp_id', $camp_id);
        $this->db->where('pp_campaign_status.chunks_id', $chunk_id);
        $this->db->where('sent',1);
        $Q = $this->db->get();
       
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_bounce_emails($camp_id,$chunk_id)
    {
        $this->db->select('jobseeker_email');
        $this->db->from('pp_campaign_status');
        $this->db->where('pp_campaign_status.camp_id', $camp_id);
        $this->db->where('pp_campaign_status.chunks_id', $chunk_id);
        $this->db->where('sent',0);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function get_job_seeker_by_old_id($id) {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
		$this->db->where('pp_job_seekers.old_id', $id);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function search_all_job_seekers($per_page, $page, $search_parameters) {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
		$this->db->like($search_parameters);
		$this->db->order_by("pp_job_seekers.ID", "DESC"); 
		$this->db->limit($per_page, $page);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
		//echo $this->db->last_query(); exit;
        return $return;
    }
	
	public function search_record_count($table_name, $search_parameters) {
		//return $this->db->count_all($table_name);
		$this->db->like($search_parameters);
		$this->db->from($table_name);
		return $this->db->count_all_results();
    }

    public function get_job_seeker_data_unfilled_cv($per_page,$page)
    {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        $this->db->join('pp_seeker_resumes','pp_seeker_resumes.seeker_ID != pp_job_seekers.ID');
        //$this->db->group_by('pp_seeker_resumes.seeker_ID');
        $this->db->order_by("pp_job_seekers.ID", "DESC"); 
        if(!empty($per_page))
        {
            $this->db->limit($per_page, $page);
        }
       
        $Q = $this->db->get();


        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        //echo $this->db->last_query(); exit;
        return $return;
    }

    public function get_job_seeker_data_unfilled_cv_total()
    {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        $this->db->join('pp_seeker_resumes','pp_seeker_resumes.seeker_ID != pp_job_seekers.ID');
        //$this->db->group_by('pp_seeker_resumes.seeker_ID');
        $this->db->order_by("pp_job_seekers.ID", "DESC"); 
       
        $Q = $this->db->get();

        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        //echo $this->db->last_query(); exit;
        return $return;
    }

    public function get_job_seeker_data_filled_cv($per_page,$page)
    {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        $this->db->join('pp_seeker_resumes','pp_seeker_resumes.seeker_ID = pp_job_seekers.ID');
        $this->db->order_by("pp_job_seekers.ID", "DESC"); 
       if(!empty($per_page))
        {
            $this->db->limit($per_page, $page);
        }
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        //echo $this->db->last_query(); exit;
        return $return;
    }
	
	public function count_records($table_name, $db_field_name, $value) {
		$this->db->where($db_field_name, $value);
		$this->db->from($table_name);
		return $this->db->count_all_results();
    }

    public function count_resume($table_name, $db_field_name, $value) {
        $this->db->where($db_field_name, $value);

        $this->db->from($table_name);
    
        return $this->db->count_all_results();
         // $this->db-> join ('pp_seeker_resumes','pp_seeker_resumes.seeker_ID = pp_job_seekers.ID');
    }
	
	public function get_all_applied_jobs_by_seekers_ID($employer_id, $per_page, $page) {
        $Q = $this->db->query("CALL get_applied_jobs_by_seeker_id($employer_id, $page, $per_page)");
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_applied_resume_by_ID($ID, $per_page, $page) {
        $Q = $this->db->query("CALL  get_applied_jobs_by_seeker_id($ID, $page, $per_page)");
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_company($ID, $per_page, $page) {
        $Q = $this->db->query("CALL  get_applied_jobs_by_seeker_id($ID, $page, $per_page)");
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	public function get_experience_by_jobseeker_id($jobseeker_id) {
        $Q = $this->db->query("CALL get_experience_by_jobseeker_id($jobseeker_id)");
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
		$Q->next_result();
        $Q->free_result();
        return $return;
    }
	
	public function get_qualification_by_jobseeker_id($jobseeker_id) {
        $Q = $this->db->query("CALL get_qualification_by_jobseeker_id($jobseeker_id)");
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
		$Q->next_result();
        $Q->free_result();
        return $return;
    }
	
	public function get_grouped_skills_by_seeker_id($seeker_id){
		$Q = $this->db->query("SELECT GROUP_CONCAT(skill_name SEPARATOR ', ') as skills FROM `pp_seeker_skills` where seeker_ID='".$seeker_id."'");	
		if ($Q->num_rows > 0) {
            $return = $Q->row('skills');
        } else {
            $return = 0;
        }
       // echo $this->db->last_query(); die();
        return $return;
	}

    public function isEmailCodeExist($seeker_id, $email_code)
    {

        $this->db->query("Update pp_job_seekers set is_email_verified = 1  WHERE ID = '".$seeker_id."' AND email_verification_code = '".$email_code."' AND send_email_code_date > now() - interval 30 minute");
        $update = $this->db->affected_rows();
    
        if($update == true){
            return true;
        }else{
            return false;
        }
    }

    public function isMobileCodeExist($seeker_id, $mobile_otp)
    {
        $this->db->query("Update pp_job_seekers set is_mobile_verified = 1  WHERE ID = '".$seeker_id."' AND mobile_verification_code = '".$mobile_otp."' AND send_mobile_otp_date > now() - interval 30 minute");
        $update = $this->db->affected_rows();
    //echo "<pre>"; print_r($this->db->last_query()); die();
        if($update == true){
            return true;
        }else{
            return false;
        }
    }

    public function isEmailCodeSent($seeker_id)
    {
        $Q = $this->db->query("Select * from pp_job_seekers Where ID = '".$seeker_id."' AND (email_verification_code != '')  AND ((dated > now() - interval 30 minute) OR (send_email_code_date > now() - interval 30 minute))");
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        //echo $this->db->last_query(); die();
        return $return;
    }

    public function isMobileCodeSent($seeker_id)
    {
        $Q = $this->db->query("Select * from pp_job_seekers Where ID = '".$seeker_id."' AND (mobile_verification_code != '') AND ((dated > now() - interval 30 minute) OR (send_mobile_otp_date > now() - interval 30 minute));");
        if ($Q->num_rows > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
       
        return $return;
    }

    function Is_already_register($id)
    {
      $this->db->where('google_id', $id);
      $query = $this->db->get('pp_job_seekers');
      if($query->num_rows() > 0)
      {
       return true;
      }
      else
      {
       return false;
      }
    }

    function Update_user_data($data, $id)
    {
      $this->db->where('google_id', $id);
      $this->db->update('pp_job_seekers', $data);
       
    }

    function Insert_user_data($data)
    {
     
      $this->db->insert('pp_job_seekers', $data);
       return $this->db->insert_id();
    }

    function insert_email_campaign($data)
    {
        $this->db->insert('pp_email_campaign_master', $data);
        return $this->db->insert_id();
    }

    function insert_emails_chunk_data($data)
    {
        $this->db->insert('email_chunks_champign', $data);
        return $this->db->insert_id();
    }
    
    function insert_email_campaign_jobseeker($data)
    {
       $this->db->insert_batch('pp_campaign_status', $data);
      
    }

    function get_job_seeker_data($google_id)
    {
        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        $this->db->where('pp_job_seekers.google_id', $google_id);
        $Q = $this->db->get();
        return $Q->row();
    }

    public function get_job_seeker_career($seeker_id)
    {
        $this->db->select('*');
        $this->db->from('pp_jobseeker_career_profile');
        $this->db->where('seeker_id', $seeker_id);
        $Q = $this->db->get();
        return $Q->row();
    }

    function is_login_from_google($user_id)
    {
        $this->db->select('pp_job_seekers.google_id');
        $this->db->from('pp_job_seekers');
        $this->db->where('pp_job_seekers.ID', $user_id);
        $Q = $this->db->get();
        return $Q->row();
    }

    public function get_all_campaign_records($per_page,$page)
    {
        
        $this->db->select('*');
        $this->db->from('pp_email_campaign_master');
        $this->db->order_by("id", "DESC");
        
        $this->db->limit($per_page,$page);
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;     
    }

    function get_sub_data($camp_id)
    {
        $this->db->select('*');
        $this->db->from('email_chunks_champign');
        $this->db->where('camp_id',$camp_id);
       // $this->db->order_by("id", "DESC");
        
        
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
       
        return $return;     
    }

    function get_camp_data($camp_id)
    {
        $this->db->select('*');
        $this->db->from('pp_email_campaign_master');
        $this->db->where('id', $camp_id);
        $Q = $this->db->get();
        return $Q->row();
    }
    function getSkills($skills_keyword){

     $response = array();

     if(isset($skills_keyword) ){
       // Select record
       $this->db->select('*');
       $this->db->where("skill_name like '".$skills_keyword."%' ");

       $records = $this->db->get('pp_skills')->result();

       foreach($records as $row ){
          $response[] = array("id"=>$row->ID ,"name"=>$row->skill_name);
       }

     }

     return $response;
    }

    function job_seeker_city($city_keyword)
    {
        $response = array();

        if(isset($city_keyword) ){
           // Select record
            $this->db->distinct();
          // $this->db->select('*');
           $this->db->where("city_name like '".$city_keyword."%' ");

           $records = $this->db->get('pp_cities')->result();
     // /echo $this->db->last_query(); die();
           foreach($records as $row ){
              $response[] = array("id"=>$row->ID ,"name"=>$row->city_name);
           }

         }

         return $response;
    }
/*Date - 24-09-2021
 Deepa
advance search*/
    function get_advance_search($search_resume,$minyears,$maxyears,$cities,$skills,$to_date,$from_date)
    {
         if($search_resume == 'unfilled')
        {
            $this->db->select('pp_job_seekers.ID');
            $this->db->from('pp_job_seekers');
            $this->db->join('pp_seeker_resumes','pp_seeker_resumes.seeker_ID = pp_job_seekers.ID');
            $this->db->group_by('pp_job_seekers.ID');
            $query = $this->db->get();
            $filled_seekers_ids = $query->result_array();
            $filled_seekers_ids = array_column($filled_seekers_ids, 'ID');
          
        }
        
        if(isset($skills))
        {
            $skill_id = explode(',',$skills);
            //$skill_id = array_values($skill_id);
           
            $this->db->select('skill_name');
            $this->db->from('pp_skills');
            $this->db->where_in('ID',$skill_id);
            $query = $this->db->get();
           
            $skills_data = $query->result_array();
           

        }
        if(isset($cities))
        {
            $city_id = explode(',',$cities);
            $this->db->select('city_name');
            $this->db->from('pp_cities');
            $this->db->where_in('ID',$city_id);
            $query = $this->db->get();
           
            $cities_data = $query->result_array();
        }
        

        $this->db->select('pp_job_seekers.*');
        $this->db->from('pp_job_seekers');
        if($search_resume == 'filled')
        {

            $this->db->join('pp_seeker_resumes','pp_seeker_resumes.seeker_ID = pp_job_seekers.ID');
            
        }
        if(isset($filled_seekers_ids))
        {
            $this->db->where_not_in('pp_job_seekers.ID',$filled_seekers_ids);
        }


        if($minyears>0)
        {
            if($minyears > 0 && $minyears <1)
            {
                $this->db->where('(experience_months)>',0);
                $this->db->where('(experience_years)',0);

            }
            else
            {
                $this->db->where('(experience_years)>=',$minyears);
            }
           
        }
        if($maxyears>0)
        {
            if($maxyears > 1 && $minyears < 1)
            {
                $this->db->or_where('(experience_years)>=','1');
            }

            $this->db->where('(experience_years)<',$maxyears);
        }
        if($to_date != '')
        {
            $to_date = date("Y-m-d", strtotime($to_date));
          $this->db->where('DATE(pp_job_seekers.dated)>=',$to_date);
        }
        if($from_date != '')
        {
            $from_date = date("Y-m-d", strtotime($from_date));
           $this->db->where('DATE(pp_job_seekers.dated)<=',$from_date);
        }
        
        if(isset($skills_data))
        {
            $skills_name = array_column($skills_data, 'skill_name');
            $this->db->select('pp_seeker_skills.skill_name');
            $this->db->join('pp_seeker_skills','pp_seeker_skills.seeker_ID=pp_job_seekers.ID','LEFT');
            for($i=0; $i<count($skills_name); $i++)
            {
                
                $this->db->or_like('pp_seeker_skills.skill_name',$skills_name[$i]);
            }
        }
        if(isset($cities_data))
        {
            $city_name = array_column($cities_data, 'city_name');
            for($j=0; $j<count($city_name); $j++)
            {
                $this->db->or_like('pp_job_seekers.city',$city_name[$j]);
            }
        }
        $this->db->group_by('pp_job_seekers.ID');
        $this->db->order_by("pp_job_seekers.ID", "DESC"); 
           
            $Q = $this->db->get();
          // / echo $this->db->last_query(); die();
            if ($Q->num_rows > 0) {
                $return = $Q->result();
            } else {
                $return = 0;
            }
            $Q->free_result();
            //echo $this->db->last_query(); exit;
            return $return;
    }

    public function get_email_chunks($camp_id)
    {
        $this->db->select('*');
        $this->db->where('camp_id',$camp_id);
        $this->db->from('email_chunks_champign');
        $q = $this->db->get();
        return $q->num_rows;

    }

    public function getindustry($industry_keyword)
    {
        $response = array();

        if(isset($industry_keyword) ){
           // Select record
            $this->db->distinct();
          // $this->db->select('*');
           $this->db->where("industry_name like '".$industry_keyword."%' ");

           $records = $this->db->get('pp_job_industries')->result();
     // /echo $this->db->last_query(); die();
           foreach($records as $row ){
              $response[] = array("id"=>$row->ID ,"name"=>$row->industry_name);
           }

         }

         return $response;
    }


    public function get_jobseeker_industries($industries_id)
    {
        $industries_id_list = explode(',', $industries_id);
        $this->db->select('industry_name');
        //$this->db->where('camp_id',$camp_id);
        $this->db->from('pp_job_industries');
        $this->db->where_in('ID',$industries_id_list);
         $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;

      
    }

    public function get_jobseeker_cities($cities_id)
    {
       $cities_id = explode(',', $cities_id);
        $this->db->select('city_name');
        //$this->db->where('camp_id',$camp_id);
        $this->db->from('pp_cities');
        $this->db->where_in('ID',$cities_id);
         $Q = $this->db->get();

        if ($Q->num_rows() > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_jobseeker_prefered_loc($prefered_loc_ids)
    {
        $prefered_loc_id_list = explode(',', $prefered_loc_ids);
        $this->db->select('city_name');
        //$this->db->where('camp_id',$camp_id);
        $this->db->from('pp_cities');
        $this->db->where_in('ID',$prefered_loc_id_list);
         $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }


    public function is_mobile_already_exists($ID,$mobile)
    {
        $this->db->select('ID');
        $this->db->from('pp_job_seekers');
        $this->db->where('ID !=', $ID);
        $this->db->where('mobile', $mobile);
        $this->db->limit(1);
        $Q = $this->db->get();

        if ($Q->num_rows > 0) {
            $return = $Q->row('ID');
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function update_mobile_verification_status($jobseeker_id)
    {
        $this->db->set('is_mobile_verified',0);
        $this->db->where('ID', $jobseeker_id);
        $return=$this->db->update('pp_job_seekers');
       
        return $return;
    }

    public function get_interview_details($job_id,$emp_id,$seeker_id)
    {
        $this->db->select('*');
        $this->db->from('pp_seeker_interview');
        $this->db->where('seeker_id', $seeker_id);
        $this->db->where('job_id', $job_id);
        $this->db->where('emp_id', $emp_id);
        $this->db->order_by('id','DESC');
       
      
        $Q = $this->db->get();

        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_cities()
    {
        $this->db->distinct();
        $this->db->select('city');
        $this->db->from('pp_job_seekers');
        $this->db->order_by("city", "ASC");
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_all_degree()
    {
         $this->db->distinct();
        $this->db->select('degree_title');
        $this->db->from('pp_seeker_academic');
        $this->db->order_by("degree_title", "ASC");
        $Q = $this->db->get();
        if ($Q->num_rows > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }

    public function get_skills_data($skill_contain,$input_skills)
    {
        if(isset($input_skills) && $input_skills!=""){ 
        $input_skills = trim($input_skills,",");  
        if($skill_contain == 'Any')
        {
            
            
            $input_skills = "'".str_replace(",", "','", trim($input_skills))."'"; 
            $skills_q = $this->db->query("SELECT DISTINCT seeker_ID FROM `pp_seeker_skills` WHERE skill_name IN (".$input_skills.")");
        }
        else
        {
         
            $array_skills = explode(',',$input_skills);
            $skills_andq = "SELECT DISTINCT seeker_ID FROM `pp_seeker_skills` WHERE ";
            $x = 1;
            $length = count($array_skills);
            foreach ($array_skills as $key => $value) {

                
                if($x==$length)
                {
                    $skills_andq.= "skill_name='".$value."'";
                }
                else
                {
                    $skills_andq.= "skill_name='".$value."' AND ";
                }
                $x++;
            }

            $skills_q = $this->db->query($skills_andq);
            
           
        }
        
        $skills_data = $skills_q->result_array();
        return $skills_data;
           

        }

    }

    public function get_degree($degree_contain,$degree_val)
    {
        if(isset($degree_val) && $degree_val!=""){ 
        $degree_val = trim($degree_val,",");  
        if($degree_contain == 'Any')
        {
             
            $degree_val = "'".str_replace(",", "','", trim($degree_val))."'"; 
            $degree_q = $this->db->query("SELECT DISTINCT seeker_ID FROM `pp_seeker_academic` WHERE degree_title IN (".$degree_val.")");
        }
        else
        {
         
            $degree_array = explode(',',$degree_val);
            $degree_and = "SELECT DISTINCT seeker_ID FROM `pp_seeker_academic` WHERE ";
            $x = 1;
            $length = count($degree_array);
            foreach ($degree_array as $key => $value) {

                
                if($x==$length)
                {
                    $degree_and.= "degree_title='".$value."'";
                }
                else
                {
                    $degree_and.= "degree_title='".$value."' AND ";
                }
                $x++;
            }

            $degree_q = $this->db->query($degree_and);
            
           
        }
        
        $degree_data = $degree_q->result_array();


        return $degree_data;
           

        }
    }

    public function get_industries_data($industries_contain,$input_industries)
    {
        if(isset($input_industries) && $input_industries!=""){ 
        $input_industries = trim($input_industries,",");  
        if($industries_contain == 'Any')
        {
            
            
            $input_industries = "'".str_replace(",", "','", trim($input_industries))."'"; 
            $industries_q = $this->db->query("SELECT DISTINCT seeker_id FROM `pp_job_industries` JOIN pp_jobseeker_career_profile ON pp_job_industries.ID = pp_jobseeker_career_profile.industries WHERE industry_name IN (".$input_industries.")");


        }
        else
        {
         $input_industries = "'".str_replace(",", "','", trim($input_industries))."'"; 
         $industries_q = $this->db->query("SELECT ID FROM `pp_job_industries` WHERE industry_name IN (".$input_industries.")");
         $industry_ids = $industries_q->result_array();
         $industry_id = array_column($industry_ids, 'ID');

         $industry_multiple_id = implode(',', $industry_id);

         

        $industries_andq = "SELECT DISTINCT seeker_id FROM `pp_job_industries` JOIN pp_jobseeker_career_profile ON pp_job_industries.ID = pp_jobseeker_career_profile.industries WHERE industries = '".$industry_multiple_id."'";
           
           

           $industries_q = $this->db->query($industries_andq);
            
           
        }
        
        $industries_data = $industries_q->result_array();
        return $industries_data;
      
        }

    }

    public function get_company_data($company_contain,$input_companies)
    {
       if(isset($input_companies) && $input_companies!=""){ 

        $input_companies = trim($input_companies,",");  
        $input_companies = explode(',',$input_companies);
        if($company_contain == 'Any')
        {
            $experience_or = "SELECT DISTINCT seeker_ID FROM `pp_seeker_experience` WHERE ";
            $x = 1;
            $length = count($input_companies);
            foreach ($input_companies as $key => $value) {

                
                if($x==$length)
                {
                    $experience_or.= "company_name LIKE '%".$value."%'";
                }
                else
                {
                    $experience_or.= "company_name LIKE '%".$value."%' OR ";
                }
                $x++;
            }

            $get_experience = $this->db->query($experience_or);

        }
        else
        {
            $experience_or = "SELECT DISTINCT seeker_ID FROM `pp_seeker_experience` WHERE ";
            $x = 1;
            $length = count($input_companies);
            foreach ($input_companies as $key => $value) {

                
                if($x==$length)
                {
                    $experience_or.= "company_name LIKE '%".$value."%'";
                }
                else
                {
                    $experience_or.= "company_name LIKE '%".$value."%' AND ";
                }
                $x++;
            }

            $get_experience = $this->db->query($experience_or);
        }

        $company_data = $get_experience->result_array();
        return $company_data;
       }
    }

    public function get_employer_advance_search($skill_contain,$input_skills,$industries_contain,$input_industries,$company_contain,$input_companies,$minyears,$maxyears,$seeker_min_age,$seeker_max_age,$city_contain,$cities_val,$degree_contain,$degree_val)
    {

        $skills_data = $this->get_skills_data($skill_contain,$input_skills);
        $industries_data = $this->get_industries_data($industries_contain,$input_industries);
        $company_data = $this->get_company_data($company_contain,$input_companies);
        $degree_title = $this->get_degree($degree_contain,$degree_val);
     
        
        $sql = "SELECT ID,google_id,first_name,last_name,email,city,dob,gender,mobile,photo,cv_file,experience_years,experience_months,TIMESTAMPDIFF( YEAR, dob, CURDATE()) as age FROM pp_job_seekers WHERE sts = 'active'";
        if(isset($skills_data) && !empty($skills_data))
        {
            $seeker_id = array_column($skills_data, 'seeker_ID');
           
            $sql.=" AND pp_job_seekers.ID IN ('".implode("','",$seeker_id)."')";
        }

        if(isset($degree_title) && !empty($degree_title))
        {
            $seeker_id = array_column($degree_title, 'seeker_ID');
           
            $sql.=" OR pp_job_seekers.ID IN ('".implode("','",$seeker_id)."')";
        }



        if(isset($industries_data) && !empty($industries_data))
        {
            $industries_data = array_column($industries_data, 'seeker_ID');
           
            $sql.=" OR pp_job_seekers.ID IN ('".implode("','",$industries_data)."')";
        }

        if(isset($company_data) && !empty($company_data))
        {
            $company_data = array_column($company_data, 'seeker_ID');
           
            $sql.=" OR pp_job_seekers.ID IN ('".implode("','",$company_data)."')";
        }

        if($minyears>0)
        {
            if($minyears > 0 && $minyears <1)
            {
                $sql.= " AND (experience_months > 0 AND experience_years = 0)";
            
            }
            else
            {
                $sql.= " AND experience_years>=".$minyears;
            }
           
        }
        if($maxyears>0)
        {
            if($maxyears > 1 && $minyears < 1)
            {
                $sql.= "OR ( experience_years >=1 AND experience_years <= ".$maxyears.")";
              
            }
            else{
                $sql.= " AND (experience_years<=".$maxyears.")";


            }
         
        }

        if(isset($cities_val) && !empty($cities_val))
        {
           
            $cities_val = trim($cities_val,",");  
            $cities_val_array = explode(',',$cities_val);
             $sql.= "AND (";
            if($city_contain == 'Any')
            {
                $x = 1;
                $length = count($cities_val_array);
                
                foreach ($cities_val_array as $key => $value) {

                 $value = trim($value);
                
                if($x==$length)
                {
                    $sql.= "city LIKE '%".$value."%'";
                }
                else
                {
                 
                   
                    $sql.= "city LIKE '%".$value."%' OR ";
                    
                    
                }
                $x++;
               }
              
            }
            else
            {
                $x = 1;
                $length = count($cities_val_array);
                foreach ($cities_val_array as $key => $value) {

                $value = trim($value);
                if($x==$length)
                {
                    $sql.= "city LIKE '%".$value."%'";
                }
                else
                {
                    $sql.= "city LIKE '%".$value."%' AND ";
                }
                $x++;
               }
            }
             $sql.= ")";
 
        }

       /* if(isset($seeker_min_age))
        {
            $sql.= " AND age>=".$seeker_min_age;
        }
        if(isset($seeker_max_age))
        {
             $sql.= " AND age<=".$seeker_max_age;
        }
*/
        $Q = $this->db->query($sql);
      
       
            if ($Q->num_rows > 0) {
                $return = $Q->result();
            } else {
                $return = 0;
            }
            $Q->free_result();
            return $return;
   
    }

    public function get_seeker_prefered_cities($seeker_id)
    {
       $this->db->select('*');
       $this->db->from('pp_jobseeker_career_profile');
       $this->db->where('seeker_id',$seeker_id);
       $sql = $this->db->get();
       return $sql->row();
    }

    public function get_cities_id($location)
    {
        $location = explode(',',$location);
        $location = "'" . implode ( "', '", $location ) . "'";

     
        $Q = $this->db->query('SELECT ID FROM (`pp_cities`) WHERE `city_name` IN ('.$location.')');
        return $Q->result_array();
    }
    
    public function user_exists($user_id)
    {
        $this->db->select('*'); 
        $this->db->from('pp_job_seekers');
        $this->db->where('ID', $user_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function get_data_row($tablename, $where = array(), $field = '*', $ord_field)
    {
        $this->db->select($field);
        $this->db->from($tablename);
        $this->db->order_by($ord_field, "desc");
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_multidata_row($tablename, $where = array(), $field = '*', $ord_field)
    {
        $this->db->select($field);
        $this->db->from($tablename);
        $this->db->order_by($ord_field, "desc");
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }



}
?>
