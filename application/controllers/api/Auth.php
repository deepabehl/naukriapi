<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use \Firebase\JWT\JWT;

class Auth extends BD_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->load->model('M_main');
    }

    

 

    public function login_new_post()
    {

        
        $this->form_validation->set_rules('phone_number', 'phone_number','required|trim',
            array('required'      => 'Oops ! phone_number is required')
        );
        $this->form_validation->set_rules('is_job_seeker', 'is_job_seeker','required|trim',
            array('required'      => 'Oops ! is_job_seeker is required')
        );
       
        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            $response['status']= FALSE;
            if(!empty(form_error('is_job_seeker')))$response['message'] = form_error('is_job_seeker');
            if(!empty(form_error('phone_number')))$response['message'] = form_error('phone_number');
             
        }
        else
        {
            extract($_POST);
            $created_date = date('Y-m-d H:i:s');
            $check_mobile_already = $this->User_model->check_mobile_already($phone_number,'pp_job_seekers');
            if(count($check_mobile_already) == 1)
            {
                $kunthis = $this->config->item('thekey');
                $mobile_otp_status = $this->sendmobileotp($phone_number,$is_job_seeker);
                $token['user_id'] = $check_mobile_already[0]->ID;  //From here
                $token['user_email'] = $check_mobile_already[0]->email;
                $token['first_name'] = $check_mobile_already[0]->first_name;
                $token['is_job_seeker'] = True;
                $date = new DateTime();
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
                $output = JWT::encode($token,$kunthis ); //This is the output token
                    
                //$otp = $this->sendotp($phone_number);
                
                if($mobile_otp_status > 1)
                    {
                        $response = array(
                            'status' => TRUE,
                            'message' => 'Login OTP sent Successfully',
                            'mobile_otp' => $mobile_otp_status,
                            "token"      => $output
                        );
                    }
                    else
                    {
                        $response = array(
                            'status' => FALSE,
                            'message' => 'SomeThing Went Wrong',
                           
                        );
                    }

                
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'Mobile number not exist',
                );
            }
        }
        $this->response($response, REST_Controller::HTTP_OK);

    }

    public function seeker_registration_post(){
       
        $this->form_validation->set_rules('name', 'name','required|trim',
            array('required'      => 'Oops ! Full Name is required')
        );
        $this->form_validation->set_rules('email', 'email','required|trim',
        array('required'      => 'Oops ! Email is required')
        );
        $this->form_validation->set_rules('phone_number', 'phone_number','required|trim',
        array('required'      => 'Oops ! phone_number is required')
        );
        $this->form_validation->set_rules('password', 'password','required|trim',
        array('required'      => 'Oops ! password is required')
        );
        /*$this->form_validation->set_rules('confirm_password', 'confirm_password','required|trim|matches[password]',
        array('required'      => 'Oops ! confirm_password is required')
        );
        */
        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run()== false)
        {
            $response['status']= FALSE;
            
            //if(!empty(form_error('confirm_password')))$response['message'] = form_error('confirm_password');
            if(!empty(form_error('password')))$response['message'] = form_error('password');
            if(!empty(form_error('phone_number')))$response['message'] = form_error('phone_number');
            if(!empty(form_error('email')))$response['message'] = form_error('email');
            if(!empty(form_error('name')))$response['message'] = form_error('name');
            
        }
        else
        {
            extract($_POST);

            $created_date = date('Y-m-d H:i:s');
            $kunthis = $this->config->item('thekey');
            $check_email_already = $this->User_model->check_identity_already($email,'pp_job_seekers');
            
            $check_mobile_already = $this->User_model->check_mobile_already($phone_number,'pp_job_seekers');
        //    echo $check_email_already; echo $check_mobile_already; die();
            if(($check_email_already == 0) && ($check_mobile_already == 0))
            {
                $current_date = date("Y-m-d H:i:s");
                $data = array(
                    'first_name'=>$name,
                    'email'=>$email,
                    'password' => $password,
                    'mobile' => $phone_number,
                    'device_token'=>!empty($device_token)?$device_token:'',
                   /* 'latitude' => !empty($lat)?$lat:'',
                    'longitude'=> !empty($lng)?$lng:'',*/
                    'referral_code' => $this->input->post('referral_code')?$this->input->post('referral_code'):'',
                    'dated'=>$created_date,
                    
                );

               
                
               $insert = $this->job_seekers_model->Insert_user_data($this->security->xss_clean($data));
                
                if($insert)
                {
                    $user_id = $this->db->insert_id();
                    $is_job_seeker= 'TRUE';
                    //$this->send_welcome_mail();

                    $mobile_otp_status = $this->sendmobileotp($phone_number,$is_job_seeker);
                  //  $email_otp_status = $this->sendemailotp($user_id,$is_job_seeker);
                    $token['user_id'] = $user_id;  //From here
                    $token['user_email'] = $email;
                    $token['first_name'] = $name;
                    $token['is_job_seeker'] = True;
                    $date = new DateTime();
                    $token['iat'] = $date->getTimestamp();
                    $token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
                    $output = JWT::encode($token,$kunthis ); //This is the output token
                    
                   
                    if($mobile_otp_status > 1 )
                    {
                        $response = array(
                            'status' => TRUE,
                            'message' => 'User Registration Successful',
                            'mobile_otp' => $mobile_otp_status,
                            "token"      => $output
                          //  'email_otp'  => $email_otp_status
                        );
                    }else{
                        $response = array(
                            'status' => FALSE,
                            'message' => 'SomeThing Went Wrong',
                           
                        );
                    }
                    
                   
                }
                else
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'User registration failed',
                    );
                }
                
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'Email or Mobile Number Already Registered',
                );
            }
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }




    function sendmobileotp($phone_number,$is_job_seeker){
        

        $otp_code = random_int(100000, 999999);
        $otp_msg = "Please Enter ".$otp_code." as OTP to verify your mobile on ".base_url();
        $otp_msg_new = str_replace(" ","%20",$otp_msg);
        $mobile_status = file_get_contents("http://162.241.114.66/api/send_gsm?api_key=1828614ee52d77172&text=".$otp_msg_new."&mobile=".$phone_number."");
        $response = json_decode($mobile_status);
        if($response->status == 'True')
        {

            $mobile_verification_array =array(
            'mobile_verification_code' => $otp_code,
            'is_mobile_verified'       => 0,
            'send_mobile_otp_date'    => date("Y-m-d H:i:s")
            );


            if($is_job_seeker)
            {
                $this->job_seekers_model->update_job_seeker($phone_number,$mobile_verification_array);
            }
            else
            {
                $this->employers_model->update_employer($phone_number,$mobile_verification_array);
            }
            
            return $otp_code;
        }
        else
        {
            return 0;
        }

        
    }


    function send_welcome_mail()
    {
        $row_email = $this->email_model->get_records_by_id(2);

        $config = array();
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
    
        $this->email->initialize($config);
        $this->email->clear(TRUE);
        $this->email->from($row_email->from_email, $row_email->from_name);
        $this->email->to($this->input->post('email'));
        //
        $this->email->subject($row_email->subject);
        $mail_message = $this->email_drafts_model->jobseeker_signup($row_email->content, $job_seeker_array);
        $this->email->message($mail_message);
        $this->email->send();
    }

    function sendemailotp($user_id,$is_job_seeker) {
       
       
        $email_verification_code = random_int(100000, 999999);
        if($is_job_seeker)
        {
            $data = $this->job_seekers_model->get_job_seeker_by_id($user_id);
            $row_email = $this->email_model->get_records_by_id(9);
            $job_seeker_array['code'] = $email_verification_code;
            $job_seeker_array['first_name'] = $data->first_name;
            
        }
        else
        {
            $data = $this->employers_model->get_employer_by_id($user_id);
            $row_email = $this->email_model->get_records_by_id(10);
            $employer_array['code'] = $email_verification_code;
            $employer_array['first_name'] = $data->first_name;
            
        }

    
        
        
        /* 15-09-2021 Deepa - For Testing Email Verification*/

        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://mail.link8.in',
            'smtp_port' => 465,
            'smtp_user' => 'dev@link8.in',// your mail name
            'smtp_pass' => 'dev@Link8Techzone',
            'mailtype'  => 'html', 
            'charset'   => 'iso-8859-1',
             'wordwrap' => TRUE
            );
        $this->email->initialize($config);
        
        
        $this->email->from($row_email->from_email, $row_email->from_name);
        $this->email->to($data->email);

        $this->email->subject($row_email->subject);
        if($is_job_seeker)
        {
            $mail_message = $this->email_drafts_model->jobseeker_email_verify($row_email->content, $job_seeker_array);
        }
        else
        {
            $mail_message = $this->email_drafts_model->employer_email_verify($row_email->content, $employer_array);

        }
        $this->email->message($mail_message);
        $flag = $this->email->send();

        /* 15-09-2021 Deepa - For Server Email Verification*/
        /*$config = array();

        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
    
        $this->email->initialize($config);
        $this->email->clear(TRUE);
        $this->email->from($row_email->from_email, $row_email->from_name);
        $this->email->to($data->email);
        
        $this->email->subject($row_email->subject);
        if($is_job_seeker)
        {
            $mail_message = $this->email_drafts_model->jobseeker_email_verify($row_email->content, $job_seeker_array);
        }
        else
        {
            $mail_message = $this->email_drafts_model->employer_email_verify($row_email->content, $employer_array);

        }
        

        $this->email->message($mail_message);
        $flag = $this->email->send();*/
        if($flag)
        {
            
            $verification_array =array(
            'email_verification_code' => $email_verification_code,
            'send_email_code_date'    => date("Y-m-d H:i:s")
            );


            if($is_job_seeker)
            {
                $this->job_seekers_model->update_job_seeker($user_id,$verification_array);
            }
            else
            {
                $this->employers_model->update_employer($user_id,$verification_array);
            }
            
            return $email_verification_code;
            
            
        }
        else
        {
            return False;
        }
    }


}
