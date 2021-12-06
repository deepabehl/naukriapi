<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends BD_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->auth();
    }
	
	public function verify()
	{
       
        $theCredential = $this->user_data;
        return $theCredential;
        //return $this->response($theCredential, 200); // OK (200) being the HTTP response code
        
	}

    public function verify_otp_post()
    {
        $user_data = $this->verify();
        $this->form_validation->set_rules('otp', 'otp','required|trim',
        array('required'      => 'Oops ! OTP is required')
        );
        $this->form_validation->set_rules('phone_number', 'phone_number','required|trim',
        array('required'      => 'Oops ! Phone number is required')
        );
      
        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            $response['status']= FALSE;
            if(!empty(form_error('type')))$response['message'] = form_error('type');
            if(!empty(form_error('otp')))$response['message'] = form_error('otp');
            if(!empty(form_error('phone_number')))$response['message'] = form_error('phone_number');
            
            
        }
        else
        {
            extract($_POST);
           
           
                $verify_otp = $this->User_model->get_last_otp($phone_number);
                if(!empty($verify_otp['mobile_verification_code'])){
                    if($verify_otp['mobile_verification_code']==$otp){
                        $update_verify_otp = $this->User_model->verify_otp($verify_otp['ID']);
                        if($update_verify_otp==true){
                          //  $update_users = $this->User_model->update_users($phone_number);
                         /*   $user_data = $this->job_seekers_model->get_job_seeker_by_id('pp_job_seekers',array('mobile'=>$phone_number), $field = '*', 'user_id');*/
                            if($update_verify_otp==true){
                                $response=array(
                                    'status'=>TRUE,
                                    'message'=>'OTP verify successfully',
                                    'user_data' => $verify_otp
                                );
                            }else{
                                $response=array(
                                    'status'=>FALSE,
                                    'message'=>'Please enter correct OTP'
                                );
                            }
                            
                        }else{
                            $response=array(
                                'status'=>FALSE,
                                'message'=>'Please enter correct OTP'
                            );
                        }
                    }else{
                        $response=array(
                            'status'=>FALSE,
                            'message'=>'Please enter correct OTP'
                        );
                    }
                }else{
                    $response=array(
                        'status'=>FALSE,
                        'message'=>'OTP not exist'
                    );
                }
             
        }
        $this->response($response, REST_Controller::HTTP_OK);

    }


   


    public function users_details_get()
    {

        $user_data = $this->verify();


        $user_id  = $user_data->user_id;
        $exists = $this->job_seekers_model->user_exists($user_id);
        if($exists == 1)
        {
        
        $user_details['personal'] = $this->job_seekers_model->get_data_row('pp_job_seekers', array('ID'=>$user_id), 'ID,first_name,email,present_address,city,gender,dob,mobile,experience_years,experience_months,career_objective,marital_status,category,dated as created_date,updated_date as last_update_date', 'ID','ID');

        $resume = $this->job_seekers_model->get_data_row('pp_seeker_resumes', array('seeker_ID'=>$user_id), 'file_name,dated as last_updated_resume_date','seeker_ID');
        $user_details['personal']['resume'] = $resume;

        $user_details['skills'] = $this->job_seekers_model->get_multidata_row('pp_seeker_skills', array('seeker_ID'=>$user_id), 'ID,skill_name','seeker_ID');

        $user_details['education'] = $this->job_seekers_model->get_multidata_row('pp_seeker_academic', array('seeker_ID'=>$user_id), '*','seeker_ID');

         $user_details['career_profile'] = $this->job_seekers_model->get_data_row('pp_jobseeker_career_profile', array('seeker_ID'=>$user_id), '*','id');

         $industry_name = $this->job_seekers_model->get_jobseeker_industries($user_details['career_profile']['industries']);
         $industry_name = array_column($industry_name, 'industry_name');
         $city_name = $this->job_seekers_model->get_jobseeker_cities($user_details['career_profile']['prefered_location']);
         $city_name = array_column($city_name, 'city_name');
        //echo "<pre>"; print_r($industry_name); die();
          $user_details['career_profile']['industries_name'] = $industry_name;
          $user_details['career_profile']['preferef_cities_name'] = $city_name;



            $response = array(
                'status' => TRUE,
                'message' => 'User details get successfully',
                'data' => $user_details
            );
        }
        else{
            $response = array(
                'status' => False,
                'message' => 'User Not Exists'
                
            );

        }

        $this->response($response, REST_Controller::HTTP_OK);
        
       
    }

    public function user_update_post()
    {

        $user_data = $this->verify();

        $user_id  = $user_data->user_id;
        $exists = $this->job_seekers_model->user_exists($user_id);
        if($exists == 1)
        {

           
            $object = $_POST;
            extract($_POST);
        
            $keys = $this->getUserModelKeys();
            foreach ($keys as $key)
            {
                if(key_exists($key, $object)) {
                    $this->db->set($key, $object[$key]);
                
                }
            }
        
         
            $this->db->where("ID", $user_id);
            $update = $this->db->update("pp_job_seekers");

        
            if($update)
            {
                $where = array('ID'=>$user_id);
                $userdata = $this->job_seekers_model->get_data_row('pp_job_seekers', array('ID'=>$user_id), 'ID,first_name,email,present_address,city,gender,dob,mobile,experience_years,experience_months,career_objective,marital_status,category,dated as created_date,updated_date as last_update_date', 'ID','ID');
                
                if (!empty($_FILES['cv_file']['name'])){
                    $resume_array = array();
                    $real_path = realpath(APPPATH . '../public/uploads/candidate/resumes/');
                    $config['upload_path'] = $real_path;
                    $config['allowed_types'] = 'doc|docx|pdf|rtf|jpg|txt';
                    $config['overwrite'] = true;
                    $config['max_size'] = 6000;
                    $config['file_name'] = replace_string(' ','-',strtolower($userdata['first_name'])).'-'.$user_id;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('cv_file')){
                       // $this->job_seekers_model->delete_job_seeker($user_id);
                    
                        $data['msg'] = $this->upload->display_errors();
                         $response = array(
                            'status' => FALSE,
                            'message' => $data['msg'],
                          
                           );
                    }
                    else
                    {
                         $current_date = date('Y-m-d H:i:s');
                        $resume = array('upload_data' => $this->upload->data());    
                        $resume_file_name = $resume['upload_data']['file_name'];
                        $resume_array = array(
                                                'seeker_ID' => $user_id,
                                                'file_name' => $resume_file_name,
                                                'dated' => $current_date,
                                                'is_uploaded_resume' => 'yes'
                                                
                        );

                        $this->resume_model->add($resume_array);
                         $response = array(
                            'status' => TRUE,
                            'message' => 'User details update successfully',
                            'data' => $userdata
                           );
                    }
                    
                   

                }
                else
                {
                    $response = array(
                    'status' => TRUE,
                    'message' => 'User details update successfully',
                    'data' => $userdata
                   );
                }

                
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'User details update failed'
                );
            }
            
            
           
        }
        else
        {
           $response = array(
                    'status' => FALSE,
                    'message' => 'User Not Exists'
                );
        }

         $this->response($response, REST_Controller::HTTP_OK);


    }

    private function getUserModelKeys()
    {
        return [
            "first_name","present_address","permanent_address","city","gender","dob","experience_years","experience_months","career_objective","pincode","device_token","marital_status","passport","category"
            ];
    }

   

}
