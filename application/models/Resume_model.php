<?php
class Resume_Model extends CI_Model {
	
	private $table_name = 'pp_seeker_resumes';
	
    public function __construct() {
	   $this->load->database();
    }
    
	public function add($data){
  
            $return = $this->db->insert($this->table_name, $data);
            if ((bool) $return === TRUE) {
                return $this->db->insert_id();
            } else {
                return $return;
            }       
			
	}	
	
	public function update($id, $data){
		$this->db->where('ID', $id);
		$return=$this->db->update($this->table_name, $data);
		return $return;
	}
	
	public function delete($id){
		$this->db->where('ID', $id);
		$this->db->delete($this->table_name);
	}
	
	public function delete_by_id_seeker_id($id, $seeker_id){
		$this->db->where('ID', $id);
		$this->db->where('seeker_ID', $seeker_id);
		$this->db->delete($this->table_name);
	}
	
	public function get_all_records() {
        $this->db->select('*');
        $this->db->from($this->table_name);
		$this->db->order_by("ID", "ASC");
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
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
	
	public function get_records_by_id($id) {
        $this->db->select('*');
        $this->db->from($this->table_name);
		$this->db->where('ID', $id);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $return = $Q->row();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function get_records_by_seeker_id($seeker_id, $per_page='', $page='') {
        $this->db->select('*');
        $this->db->from($this->table_name);
		$this->db->where('seeker_ID', $seeker_id);
		if($per_page!='')
			$this->db->limit($per_page, $page);
        $Q = $this->db->get();
        if ($Q->num_rows() > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
        $Q->free_result();
        return $return;
    }
	
	public function count_records_jobseeker_id($seeker_id) {		
		$this->db->where('seeker_ID', $seeker_id);
		$this->db->from($this->table_name);
		return $this->db->count_all_results();
	}
	
	//Search
	public function get_searched_resume($param, $per_page, $page) {
       $Q = $this->db->query('CALL ft_search_resume("'.$param.'", '.$page.', '.$per_page.')');
        if ($Q->num_rows() > 0) {
            $return = $Q->result();
        } else {
            $return = 0;
        }
		$Q->next_result();
        $Q->free_result();
        return $return;
    }
    

	public function count_searched_resume_records($param) {
		$Q = $this->db->query('CALL count_ft_search_resume("'.$param.'")');	
		 if ($Q->num_rows() > 0) {
            $return = $Q->row('total');
        } else {
            $return = 0;
        }
		$Q->next_result();
        $Q->free_result();
        return $return;
		
    }
    public function get_latest_job_post($employer_id)
    {
      $this->db->select('required_skills,city,experience');
      $this->db->from('pp_post_jobs');
      $this->db->where('employer_ID',$employer_id);
      $this->db->where('sts','active');
      $this->db->limit(1);
      $this->db->order_by('ID','DESC');
      $result = $this->db->get();
      if($result->num_rows()>0){
            return $result->result_array()[0];
        }else{
            return array();
        }
    }

    //searching resume with excat match 
    public function get_matching_resume($latest_job_post_data)
    {
        $skills_array = explode(',',$latest_job_post_data['required_skills']);
        $experience = $latest_job_post_data['experience'];
        $city = $latest_job_post_data['city'];
        $array_count = count($skills_array);
        $like_condition  = '(';
        foreach ($skills_array as $key => $value) {

        
        if($key == ($array_count-1))
        {

          
        $like_condition  .= " pp_seeker_skills.skill_name LIKE '%".trim($value)."%' ";
         }
         else
         {
            $like_condition  .= " pp_seeker_skills.skill_name LIKE '%".trim($value)."%' OR ";
         }
        }
        $like_condition .=  ')';
       
      
       $this->db->select('pp_job_seekers.ID,google_id,first_name,last_name,email,city,gender,dob,phone,photo,cv_file,experience_years,skill_name');
        $this->db->from('pp_job_seekers');

        $this->db->join('pp_seeker_skills','pp_seeker_skills.seeker_ID=pp_job_seekers.ID');
        $this->db->where('sts','active');
        $this->db->like('pp_job_seekers.city',$city);
        $this->db->where('pp_job_seekers.experience_years',$experience);
        $this->db->where($like_condition);
        $this->db->group_by('pp_job_seekers.ID');
        $this->db->order_by('pp_job_seekers.ID','DESC');
        $result = $this->db->get();
       
        if($result->num_rows()>0){
                return $result->result();
            }else{
                return array();
            }
        }


        public function get_atleast_match_resume($latest_job_post_data,$match_seekers_id,$next_count)
        {
           $skills_array = explode(',',$latest_job_post_data['required_skills']);
        $experience = $latest_job_post_data['experience'];
        $city = $latest_job_post_data['city'];
        $array_count = count($skills_array);
        $like_condition  = '(';
        foreach ($skills_array as $key => $value) {

        
        if($key == ($array_count-1))
        {

          
        $like_condition  .= " pp_seeker_skills.skill_name LIKE '%".trim($value)."%' ";
         }
         else
         {
            $like_condition  .= " pp_seeker_skills.skill_name LIKE '%".trim($value)."%' OR ";
         }
        }
        $like_condition .=  ')';
       
      
        $this->db->select('pp_job_seekers.ID,google_id,first_name,last_name,email,city,dob,gender,phone,photo,cv_file,experience_years,skill_name');
        $this->db->from('pp_job_seekers');

        $this->db->join('pp_seeker_skills','pp_seeker_skills.seeker_ID=pp_job_seekers.ID');
       
        $this->db->or_like('pp_job_seekers.city',$city);
        $this->db->or_where('pp_job_seekers.experience_years',$experience);
        $this->db->or_where($like_condition);
        $this->db->where('sts','active');
        if(!empty($match_seekers_id))
        {
            $this->db->where_not_in('pp_job_seekers.ID',$match_seekers_id);
        }
        
        $this->db->group_by('pp_job_seekers.ID');
        $this->db->limit($next_count);
        $this->db->order_by('pp_job_seekers.ID','DESC');
        $result = $this->db->get();
    // echo $this->db->last_query(); die();
        if($result->num_rows()>0){
                return $result->result();
            }else{
                return array();
            }
        }

        public function get_all_resume($all_seeker_ids,$final_count)
        {
        $this->db->select('pp_job_seekers.ID,google_id,first_name,last_name,email,city,gender,dob,phone,photo,cv_file,experience_years,skill_name');
        $this->db->from('pp_job_seekers');

        $this->db->join('pp_seeker_skills','pp_seeker_skills.seeker_ID=pp_job_seekers.ID');
       
        $this->db->where('sts','active');
         if(!empty($all_seeker_ids))
        {
             $this->db->where_not_in('pp_job_seekers.ID',$all_seeker_ids);
        }
       
        $this->db->group_by('pp_job_seekers.ID');
        $this->db->limit($final_count);
        $this->db->order_by('pp_job_seekers.ID','DESC');
        $result = $this->db->get();
        //echo $this->db->last_query(); die();
     
        if($result->num_rows()>0){
                return $result->result();
            }else{
                return array();
            }
        }

        
    

}