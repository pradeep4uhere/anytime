<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Config;
use Lang;
use Auth;
use Mail;
use App\NotificationEvent;
use App\NotificationEventTemplateDetail;
use App\MailTemplateMaster;
use App\AdminUser;
use App\User;
use App\NotificationEventTemplate;
use App\NotificationEventLog;
use App\Product;

class EmailHelpers extends Mailable {

      use Queueable, SerializesModels;


      public static function sendMailToUserByCron($data){
         Mail::send(['html' =>'emails.cron.welcome'], $data, function($message) use ($data) {
                 $message->to($data['to_email']);
                 // if(!empty($data['cc'])){
                 //   $message->cc($data['cc'], $name = null);
                 // }

                 // if(!empty($data['bcc'])){
                 //   $message->bcc($data['bcc'], $name = null);
                 // }

                 if(!empty($data['reply'])){
                   $message->replyTo($data['reply'], $name = 'Reply');
                 } 
                 $message->subject($data['subject']);
             });

      }




      public static function MailSend($event_id, $lang_id, $relevantdata, $user) {
       
        /*fetch email template and revent data*/
        //slug in email template table
        //DB::raw(1) is used for fetch email data
        $results = DB::table(with(new NotificationEvent)->getTable().' as m')
                    ->leftjoin(with(new NotificationEventTemplate)->getTable().' as md', [['m.id', '=', 'md.noti_event_id'], ['md.noti_type_id', '=' , DB::raw(1)]])
                    ->leftjoin(with(new NotificationEventTemplateDetail)->getTable().' as ms', [['m.id', '=', 'ms.noti_event_id'],['ms.lang_id', '=', DB::raw($lang_id)]])

                    ->leftjoin(with(new MailTemplateMaster)->getTable().' as lm', [['lm.id', '=', 'ms.master_template_id']])
                    ->where('m.id', $event_id)->first();

                    

        $subject = stripslashes($results->mail_subject);
        $mail_content = stripslashes($results->mail_containt);
        /*Merge both layout and content*///template
        if(!empty($results->template)){
          $layout = stripslashes($results->template); 
          $mail_content = str_replace( '[CONTENT]' , $mail_content, $layout);
        }
        
        $emailReplaceData = [ 'SITE_NAME' => config('app.name'),
                              'SITE_URL'  => url('/') ];

         /*Merge data  comman and relevant data*/              
        $emailReplaceData = array_merge($emailReplaceData, $relevantdata);             
        /*replace value in the subject and email content*/
        foreach($emailReplaceData as $key => $value){
              $replaceKey = '['.$key.']';
              $subject = str_replace( $replaceKey ,$value,$subject);
              $mail_content = str_replace( $replaceKey ,$value, $mail_content);
        }
        //dd($mail_content);
        //$mail_content = addslashes($mail_content);

        /*find out to buyer email*/
          $to_email = [];
          if(!empty($results->to_buyer)){
              $to_email[] =  $user->email; 
          }
          /*find out to seller email*/
          //$seller_email = '';
          if(!empty($results->to_seller)){
             $to_email[] =  $user->email;
             //$to_email[] = array_merge($to_email, $admin_email);          
          }
          /*find out to Admin email*/
          $admin_email = $admin_id = [];
                //echo $results->admin_email;
          if(!empty($results->to_admin)){
                /*send email to admin*/
                $admin_id =  array_filter(explode('-', $results->to_admin));
                $admin_email = AdminUser::select('email')->whereIn('role_id', $admin_id)->pluck('email')->toArray();
                $to_email = array_merge($to_email, $admin_email);
          }

            /*cc */
            $cc = [];
            if(!empty($results->cc)){
               /**/  
               $cc_id =  array_filter(explode('-', $results->cc));
               $cc = AdminUser::select('email')->whereIn('role_id', $cc_id)->pluck('email')->toArray();
            }
            /*bcc*/
            $bcc = [];
            if(!empty($results->bcc)){
                $bcc_id =  array_filter(explode('-', $results->bcc));
                $bcc = AdminUser::select('email')->whereIn('role_id', $bcc_id)->pluck('email')->toArray(); 
            }
           //print_r($to_email);
          /* \Mail::to($to_email)
                 ->cc($cc)
                 ->bcc($bcc)
                 //->from($results->sender, 'Admin')
                 ->send($mail_content);*/
              $data = []; 
              $data['mail_content'] = $mail_content;
              $data['to_email'] = $to_email;
              $data['subject'] = $subject;
             
             /*send email to other admin user */
              $data['cc'] = $cc;
              $data['bcc'] = $bcc;
              $data['reply'] = $results->sender;

              if(Config::get('constants.localmode') == true){
              //  echo '<pre>';print_r($data);die;
              }

             // global $to_email;

             // send mail default function
              //dd($data);
              Mail::send(['html' =>'emails.mail'], $data, function($message) use ($data) {

                   $message->to($data['to_email']);

                   if(!empty($data['cc'])){
                     $message->cc($data['cc'], $name = null);
                   }

                   if(!empty($data['bcc'])){
                     $message->bcc($data['bcc'], $name = null);
                   }

                   if(!empty($data['reply'])){
                     $message->replyTo($data['reply'], $name = 'Reply');
                   } 



                   $message->subject($data['subject']);

               });

  }


  public static function SendSMS($event_id, $lang_id, $relevantdata, $user) {
       
        /*fetch email template and revent data*/
        //slug in email template table
        //DB::raw(1) is used for fetch email data
        $results = DB::table(with(new NotificationEvent)->getTable().' as m')
                    ->leftjoin(with(new NotificationEventTemplate)->getTable().' as md', [['m.id', '=', 'md.noti_event_id'], ['md.noti_type_id', '=' , DB::raw(2)]])
                    ->leftjoin(with(new NotificationEventTemplateDetail)->getTable().' as ms', [['m.id', '=', 'ms.noti_event_id'],['ms.lang_id', '=', DB::raw($lang_id)]])

                    ->leftjoin(with(new MailTemplateMaster)->getTable().' as lm', [['lm.id', '=', 'ms.master_template_id']])
                    ->where('m.id', $event_id)->first();

                    

        $subject = stripslashes($results->mail_subject);
        $mail_content = stripslashes($results->mail_containt);
        /*Merge both layout and content*///template
        if(!empty($results->template)){
          $layout = stripslashes($results->template); 
          $mail_content = str_replace( '[CONTENT]' , $mail_content, $layout);
        }
        
        $emailReplaceData = [ 'SITE_NAME' => config('app.name'),
                              'SITE_URL'  => url('/') ];

         /*Merge data  comman and relevant data*/              
        $emailReplaceData = array_merge($emailReplaceData, $relevantdata);             
        /*replace value in the subject and email content*/
        foreach($emailReplaceData as $key => $value){
              $replaceKey = '['.$key.']';
              $subject = str_replace( $replaceKey ,$value,$subject);
              $mail_content = str_replace( $replaceKey ,$value, $mail_content);
        }
        //dd($mail_content);
        //$mail_content = addslashes($mail_content);

           /*find out to buyer SMS*/
           $sms_to = [];
           //mobile_isd_code isd code
           if(!empty($results->to_buyer)){
              $sms_to[] =  $user->mobile; 
           }
          /*find out to seller email*/
          //$seller_email = '';
            if(!empty($results->to_seller)){
             $sms_to[] =  $user->mobile;
             //$to_email[] = array_merge($to_email, $admin_email);          
           }
          

            
              $data = []; 
              $data['mail_content'] = $mail_content;
              $data['sms_to'] = $sms_to;
              $data['subject'] = $subject;
             
             /*send sms to user */
             

  }



   public static function AddNotificationEventLog($event_id, $receiverData=null, $entity_id=null, $entity_type_id=null, $replaceData=array()) {
         
        /*Add Notification EventLog in the tables*/
          /*login User id*/
         $user_id = Auth::id();
          /*Actor type*/
         $user_type = Auth::user()->user_type;
         /*if(!isset($replaceData)){
            $replaceData = array();   
         }*/
        // $replaceData['USER_ID'] = $user_id;

        
         $replaceData['USER_NAME'] = Auth::user()->name;
         $replaceData['USER_IMAGE'] = Auth::user()->image;

         $replace_data = serialize($replaceData);

         $datasave = [];
        
         if(isset($entity_type_id) && ($entity_type_id == '1')){

             $productData= Product::select('user_id')->where('id', $entity_id)->first()->toArray();

             $user_data = User::where('id', $productData['user_id'])->first();
             $lang_id = $user_data->default_language;
             $receiver_type = $user_data->user_type;
             $receiver_id = $user_data->id;

             $datasave[] = ['event_id'=> $event_id, 'actor_id'=> $user_id, 'actor_type'=> $user_type, 'entity_id'=> $entity_id, 'lang_id'=>$lang_id, 'receiver_id'=> $receiver_id, 'receiver_type'=>$receiver_type,
             'entity_type_id' => $entity_type_id, 'replace_data' => $replace_data,'is_read'=> '0', 'created_at' =>date('Y-m-d H:i:s')

           ];

          if(isset($receiverData) && !empty($receiverData)){
              if(isset($receiverData['receiver_id']) && !empty($receiverData['receiver_id'])){
                  $user_data = User::where('id', $receiverData['receiver_id'])->first();

                  $datasave[] = ['event_id'=> $event_id, 'actor_id'=> $user_id, 
                           'actor_type'=> $user_type, 'entity_id'=> $entity_id, 
                           'lang_id'=>$user_data->default_language, 'receiver_id'=> $user_data->id, 
                           'receiver_type'=>$user_data->user_type,
                           'entity_type_id' => $entity_type_id, 'replace_data' => $replace_data, 'is_read'=> '0', 
                           'created_at' => date('Y-m-d H:i:s')

                  ];
              }
           }



         }else if(isset($entity_type_id) && ($entity_type_id == '3' || $entity_type_id == '4')){

          $user_data = User::where('id', $receiverData['receiver_id'])->first();
          $lang_id = $user_data->default_language;
          $receiver_type = $user_data->user_type;
          $receiver_id = $user_data->id;

          $datasave[] = ['event_id'=> $event_id, 'actor_id'=> $user_id, 'actor_type'=> $user_type,'lang_id'=>$lang_id, 'receiver_id'=> $receiver_id, 'receiver_type'=>$receiver_type,
             'entity_type_id' => $entity_type_id, 'replace_data' => $replace_data,'is_read'=> '0', 'created_at' =>date('Y-m-d H:i:s')

           ];



        }else{

          if(isset($receiverData) && !empty($receiverData)){
              if(isset($receiverData['receiver_id']) && !empty($receiverData['receiver_id'])){
                  $user_data = User::where('id', $receiverData['receiver_id'])->first();

                  $datasave[] = ['event_id'=> $event_id, 'actor_id'=> $user_id, 
                           'actor_type'=> $user_type, 'entity_id'=> $entity_id, 
                           'lang_id'=>$user_data->default_language, 'receiver_id'=> $user_data->id, 
                           'receiver_type'=>$user_data->user_type,
                           'entity_type_id' => $entity_type_id, 
                           'replace_data' => $replace_data,
                           'is_read'=> '0', 
                           'created_at' => date('Y-m-d H:i:s')

                  ];
              }
           }else{


              $datasave[] = ['event_id'=> $event_id, 'actor_id'=> $user_id, 
                           'actor_type'=> $user_type, 'entity_id'=> $entity_id, 
                           'lang_id'=>'', 'receiver_id'=> '', 
                           'receiver_type'=>'',
                           'entity_type_id' => $entity_type_id, 
                           'replace_data' => $replace_data,
                           'is_read'=> '1', 
                           'created_at' => date('Y-m-d H:i:s')
                      ];     


           }

        } 

        
         


         NotificationEventLog::insert($datasave);

     
  }



  public static function sendAllEnableNotification($event_id, $emailData=null, $smsData=null, $notificationData = null){
       $notidatas = NotificationEvent::select('noti_type')->where('id', $event_id)->first();
       $notificationwebpuchetc = 0;
       $notificationTypes = unserialize($notidatas->noti_type);
       /*1=>Email, 2=>SMS, 3=> WEB,  4=> PUSH, 5=>TOASTR    Notification Type*/
       if(!empty($notificationTypes) && count($notificationTypes) > 0){
       foreach($notificationTypes as $id){
         if($id == '1'){
             if(!empty($emailData)){
                 extract($emailData);
                 self::MailSend($event_id, $lang_id, $relevantdata, $user);
              }   

          }else if($id == '2'){

            if(!empty($smsData)){
                 extract($smsData);
                 self::SendSMS($event_id, $lang_id, $relevantdata, $user);
             }
          }else if($id == '3' || $id == '4' || $id == '5'){

               $notificationwebpuchetc = '1';
              //set all other notification
              

          }
      }

      if(!empty($notificationwebpuchetc)){

        if(!empty($notificationData)){
           extract($notificationData);

           if(!isset($replaceData)){
             $replaceData = array();
           }
           
          // $replaceData = array_merge($replaceData, $notificationData[]);

          self::AddNotificationEventLog($event_id, $receiverData, $entity_id, $entity_type_id, $replaceData);
        } 


      }


     }

  }
}