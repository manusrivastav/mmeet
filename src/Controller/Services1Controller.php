<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Text;
use Cake\Utility\Security;
use App\Component\FileController;
use Cake\Mailer\Email;
use Cake\Datasource\ConnectionManager;
class Services1Controller  extends AppController
{
 /*
 Controller:ServicesController
 Action:signup
 Method:POST
 parameter:name,emailid,password,device_token
 */
 public function signup()
    {
     $this->autoRender=false;
	 $uuid1='';
	 if($this->request->ispost()){
		 $name=@$this->request->data["name"];
		 $email=$this->request->data["emailid"];
		 $address=$this->request->data["address"];
		 $age=$this->request->data["age"];
		 $mobile=$this->request->data["mobile"];
		 $city=$this->request->data["city"];
		 $gender=$this->request->data["gender"];
		 $password=$this->request->data["password"];
		 $device_token=$this->request->data["device_token"];
		 $type=@$this->request->data["type"];
		 $socialid=@$this->request->data["socialid"];
		 $url=@$this->request->data["url"];
		 
    
		 if(empty($name)){
	      $response["message"]="Name can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($email)){
	      $response["message"]="Emailid can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($address)){
	      $response["message"]="Address can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($age)){
	      $response["message"]="Age can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($mobile)){
	      $response["message"]="Mobile can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($city)){
	      $response["message"]="City Name can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($gender)){
	      $response["message"]="Gender can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($password)){
	      $response["message"]="Password can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($device_token)){
			 $device_token=ran(0,10000);
	      /*$response["message"]="Device Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;	*/	  
		 }
		 $usertable=TableRegistry::get("Users");
		 $tokentable=TableRegistry::get("Tokens");
		 $query=$usertable->find('all',['conditions'=>['Users.email'=>$email]]);
		 $checkuserdata = $query->toArray();
		 if(!empty($checkuserdata)){
	      $response["message"]="Email id already exists.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 
		 $user=$usertable->newEntity();
		 $user->name=$name;
		 $user->email=$email;
		 $user->address=$address;
		 $user->city=$city;
		 $user->mobile=$mobile;
		 $user->gender=$gender;
		 $user->age=$age;
		 
		 if(!empty($type) && $type=='facebook'){
		 $user->facebook_id=!empty($socialid)?$socialid:'';
		 }
		 if(!empty($type) && $type=='instagram'){
		 $user->instagram_id=!empty($socialid)?$socialid:'';
		 }		
		
		
		if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		$user->image=$files;
		@chmod($_SERVER["DOCUMENT_ROOT"]."/webroot/upload/",0777);
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/webroot/upload/".$files)){
		}
		$user->type='puzzle';
		 }else{
			 $user->image=isset($url)?$url:'';
         $user->type=!empty($type)?$type:'';			 
		 }
		 //print_r($user);die;
		 $user->password=Security::hash($password,'md5',true);
		 $user->createdOn=date("Y-m-d H:i:s");
		 $user->updatedOn=date("Y-m-d H:i:s");
		 $response["Userinfo"]=$user;
		 $result=$usertable->save($user);
		 if(!empty($result)){
			$id = $result->id; 
			$response["Userinfo"]["user_id"]=$id;
			$token1=$tokentable->newEntity();
			$uuid1 = Text::uuid();
			$token1->token=$uuid1;
			$token1->user_id=$id;
			$token1->device_token=$device_token;
			$token1->createdOn=date("Y-m-d H:i:s");
			$token1->updatedOn=date("Y-m-d H:i:s");
			
			$tokentable->save($token1);
			$response["Userinfo"]["token"]=$uuid1;
			$response["Userinfo"]["image"]=!empty($files)?BASE_URL."/upload/".$files:(!empty($url)?$url:'');
			$response["Userinfo"]["device_token"]=$device_token;
		 }
		 if(!empty($response)){
	      $response["message"]="User register successfully.";
	      $response["status"]="success";
	      echo json_encode($response);
          exit;		  
		 }
		
	 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
    }
	
/*
 Controller:ServicesController
 Action:login
 Method:POST
 parameter:name,emailid,password,device_token
 */
	public function login(){
	 $this->autoRender=false;
	 if($this->request->ispost()){
		 $email=@$this->request->data["emailid"];
		 $password=@$this->request->data["password"];
		 $device_token=$this->request->data["device_token"];
		 $login_type=@$this->request->data["login_type"];
		 $socilaid=@$this->request->data["socilaid"];
		 if(empty($login_type)){
	      $response["message"]="Login Type can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		  if(!empty($login_type) && ($login_type=="facebook" || $login_type=="instagram")){
			  if(empty($socilaid)){
	      $response["message"]="Social id can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		  }
		 if(!empty($login_type) && $login_type=="puzzle"){
		 if(empty($email)){
	      $response["message"]="Email can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($password)){
	      $response["message"]="Password can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 }
		 if(empty($device_token)){
	      $response["message"]="Device Token can not be empty.";
	      $response["status"]="failure";
	     // echo json_encode($response);
         // exit;		  
		 }
		 $usertable=TableRegistry::get("Users");
		 $password=Security::hash($password,'md5',true);
		  if(!empty($login_type) && ($login_type=="facebook" || $login_type=="instagram")){
			$query=$usertable->find('all',['conditions'=>["OR"=>['Users.facebook_id'=>$socilaid,'Users.instagram_id'=>$socilaid]]]);
            $checkuserexists = $query->toArray();			
		  }else{
		 $query=$usertable->find('all',['conditions'=>['Users.email'=>$email]]);
		 $checkuserexists = $query->toArray();
		 if(empty($checkuserexists)){
		  $response["message"]="Emailid does not exits.";
	      $response["isFirstLogin"]="false";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;	
		 }
		 if(!empty($checkuserexists[0]->password) && $checkuserexists[0]->password!=$password){
		  $response["message"]="Password does not match.";
	      $response["isFirstLogin"]="false";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit; 
		 }
		  }
		 
		 if(empty($checkuserexists)){
	      $response["message"]="Record does not exits.";
	      $response["isFirstLogin"]="true";
	      $response["status"]="success";
	      echo json_encode($response);
          exit;		  
		 }else{
	     $checkuserexists=$checkuserexists[0];
		 }
		 $tokentable=TableRegistry::get("Tokens");
		 $token1=$tokentable->newEntity();
			$uuid1 = Text::uuid();
			$token1->token=$uuid1;
			$token1->user_id=$checkuserexists->id;
			$token1->device_token=$device_token;
			$token1->createdOn=date("Y-m-d H:i:s");
			$token1->updatedOn=date("Y-m-d H:i:s");

			$tokentable->save($token1);
			
			$response["Userinfo"]=$checkuserexists;
			$response["Userinfo"]["token"]=$uuid1;
			$response["Userinfo"]["type"]=$login_type;
			$response["Userinfo"]["device_token"]=$device_token;
		    if(!empty($response)){
	      $response["message"]="User Login successfully.";
		  $response["isFirstLogin"]="false";
	      $response["status"]="success";
	      echo json_encode($response);
          exit;		  
		 } 
		 
		 
		 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
		
	}
	
	/*
 Controller:ServicesController
 Action:UpdateProfile
 Method:POST
 parameter:name,emailid,password,device_token
 */
	public function UpdateProfile(){
	 $this->autoRender=false;
	 if($this->request->ispost()){
		 $email=$this->request->data["emailid"];
		 $age=$this->request->data["age"];
		 $name=$this->request->data["name"];
		 //$city=$this->request->data["city"];
		 $address=$this->request->data["address"];
		 $mobile=$this->request->data["mobile"];
		 $city=$this->request->data["city"];
		 $token=$this->request->data["token"];
		  $gender=$this->request->data["gender"];
		  $isImageUpdated=$this->request->data["isImageUpdated"];
		  $userid=$this->checktoken($token);
		   if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($email)){
	      $response["message"]="Email can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($name)){
	      $response["message"]="Name can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($age)){
	      $response["message"]="Age can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($email)){
	      $response["message"]="Email can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($address)){
	      $response["message"]="Address can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($mobile)){
	      $response["message"]="Mobile can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($city)){
	      $response["message"]="City can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($gender)){
	      $response["message"]="Gender can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		
		
		
		$usertable=TableRegistry::get("Users");
		$user = $usertable->get($userid);
		
		$user->mobile=$mobile;
		$user->email=$email;
		$user->address=$address;
		$user->age=$age;
		$user->city=$city;
		$user->name=$name;
		$user->gender=$gender;
		$user->createdOn=date("Y-m-d H:i:s");
		$user->updatedOn=date("Y-m-d H:i:s");
		if($isImageUpdated==1){
		if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		$user->image=$files;
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/webroot/upload/".$files)){
		}
		}else{
	     $user->image='';
		}
		}
		//print_r($user);die;
		$response["Userinfo"]=$user;
		$result=$usertable->save($user);
		$query=$usertable->find('all',['conditions'=>['Users.id'=>$userid]]);
		$checkuserdata = $query->toArray();
		$response["Userinfo"]["image"]=!empty($files)?BASE_URL."/upload/".$files:(!empty($checkuserdata[0]->image)?BASE_URL."/upload/".$checkuserdata[0]->image:"");
		$response["message"]="Profile update successfully.";
		$response["status"]="success";
		echo json_encode($response);
		exit;
	 
	}else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	}
     public function checktoken($token){
		 
		 $usertable=TableRegistry::get("Tokens");
		 
		 $query=$usertable->find('all',['conditions'=>['Tokens.token'=>$token]]);
		 $checkuserexists = $query->toArray();
		 return !empty($checkuserexists[0]->user_id)?$checkuserexists[0]->user_id:"0";
	 }
	 
	 public function changepassword(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 $oldpassword=$this->request->data["oldpassword"];
		 $newpassword=$this->request->data["newpassword"];
		 $confirmpassword=$this->request->data["confirmpassword"];
		 $token=$this->request->data["token"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($oldpassword)){
	      $response["message"]="Oldpassword can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($newpassword)){
	      $response["message"]="Newpassword can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($confirmpassword)){
	      $response["message"]="Confirmpassword can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $usertable=TableRegistry::get("Users");
		 $oldpassword=Security::hash($oldpassword,'md5',true);
		 $query=$usertable->find('all',['conditions'=>['Users.id'=>$userid,'Users.password'=>$oldpassword]]);
		 $checkuserexists = $query->toArray();
		 
		 if(empty($checkuserexists)){
	      $response["message"]="Old password does not match";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if($newpassword!=$confirmpassword){
	      $response["message"]="Both Password do not match.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $user = $usertable->get($userid);
		 $user->password=Security::hash($newpassword,'md5',true);
		 $result=$usertable->save($user);
		 if($result){
		  $response["message"]="Password changed successfully.";
	      $response["status"]="success";
	      echo json_encode($response);
          exit;
		 }
		 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 
	 public function sociallogin(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 $emailid=$this->request->data["emailid"];
		 $socialid=$this->request->data["socialid"];
		 $type=$this->request->data["type"];
		 $device_token=$this->request->data["device_token"];
		  
		 if(empty($emailid)){
	      $response["message"]="email id can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($socialid)){
	      $response["message"]="Social id can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($type)){
	      $response["message"]="Type can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $usertable=TableRegistry::get("Users");
		 $tokentable=TableRegistry::get("Tokens");
		 $query=$usertable->find('all',['conditions'=>['Users.email'=>$emailid]]);
		 $checkuserexists = $query->toArray();
		 if(!empty($checkuserexists)){
	      $user = $usertable->get($checkuserexists[0]->id);	  
		 }else{
	     $user=$usertable->newEntity();
		 
		 }
		 if($type=="facebook"){
		 $user->facebook_id=$socialid;
		 }else{
	      $user->gmail_id=$socialid;		 
		 }
		 $user->email=$emailid;
		 $user->type=$type;
		 $user->createdOn=date("Y-m-d H:i:s");
			
		 $result=$usertable->save($user);
		  if(!empty($result)){
			if(!empty($checkuserexists->id)){
			$id = $checkuserexists->id; 
			}else{
			$id = $result->id; 
			}
		 $tokentable=TableRegistry::get("Tokens");
			$token1=$tokentable->newEntity();
			$uuid1 = Text::uuid();
			$token1->token=$uuid1;
			$token1->user_id=$id;
			$token1->device_token=$device_token;
			$token1->createdOn=date("Y-m-d H:i:s");
			$token1->updatedOn=date("Y-m-d H:i:s");

			$tokentable->save($token1);
            if(!empty($checkuserexists[0]))
			$response["Userinfo"]=$checkuserexists[0];
		    else
			$response["Userinfo"]=$user;
			$response["Userinfo"]["token"]=$uuid1;
			$response["Userinfo"]["device_token"]=$device_token;
			if(!empty($response)){
			$response["message"]="User Login successfully.";
			$response["status"]="success";
			echo json_encode($response);
			exit;		  
			} 
		  }
		 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
		 
	 }
	 
	 
	 public function forgotpassword(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 $emailid=$this->request->data["emailid"];
		  
		 if(empty($emailid)){
		  $response["message"]="email id can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
		 }
		$email = new Email('default');
		$message="";
		 $usertable=TableRegistry::get("Users");
		 $query=$usertable->find('all',['conditions'=>['Users.email'=>$emailid]]);
		 $checkuserexists = $query->toArray();
		 if(!empty($checkuserexists)){
			 $password=rand(1000,1000000);
			 $userid=$checkuserexists[0]->id;
			 $name=$checkuserexists[0]->name;
			 $message.="Hi $name \r\n";
			 $message.="Your Password credentials here. \r\n";
			 $message.="Email ".$emailid." \r\n";
			 $message.="Password ".$password." \r\n";
			 $message.="Thanks \r\n";
			 $message.="Meeat \r\n";
			 $email->from(["meeat.apptest@gmail.com" => "Sender"])
			->to($emailid)
			->subject("Forgot Password")
			->send(($message));
			
			$usertable=TableRegistry::get("Users");
			$user = $usertable->get($userid);
			$user->password=Security::hash($password,'md5',true);
			$result=$usertable->save($user);
			$response["message"]="You password sent in your email.";
			$response["status"]="success"; 
			echo json_encode($response);
			exit;
		 }else{
		  $response["message"]="Email id does not exists.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit; 
			 
		 }
		 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 
	 public function devicetokenupdate(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 $token=$this->request->data["token"];
		 $device_token=$this->request->data["device_token"];
		 if(empty($token)){
		  $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
		 }
		 if(empty($device_token)){
		  $response["message"]="device token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
		 }
		 $tokentable=TableRegistry::get("Tokens");
		 $query=$tokentable->find('all',['conditions'=>['Tokens.token'=>$token]]);
		 $checkuserexists = $query->toArray();
		 if(!empty($checkuserexists)){
		 $tokenid=$checkuserexists[0]->id;
		 $tokens = $tokentable->get($tokenid);
		
		 $tokens->token=$token;
		 $tokens->device_token=$device_token;
		$result=$tokentable->save($tokens);
		$response["message"]="Your token updated now.";
		$response["status"]="success"; 
		echo json_encode($response);
		exit;
		 }
		 }
		 else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 
	 public function createevent(){
		 
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 $eventname=$this->request->data["eventname"];
		 $eventdescription=$this->request->data["eventdescription"];
		 $startdate=$this->request->data["startdate"];
		 $enddate=$this->request->data["enddate"];
		 $starttime=$this->request->data["starttime"];
		 $endtime=$this->request->data["endtime"];
		 $address=$this->request->data["address"];
		 $price=$this->request->data["price"];
		 $noperson=$this->request->data["noperson"];
		 $latitude=$this->request->data["latitude"];
		 $logitude=$this->request->data["logitude"];
		 $visible=$this->request->data["visible"];
		 $minAge=$this->request->data["minAge"];
		 $MaxAge=$this->request->data["maxAge"];
		 $Category=$this->request->data["Category"];
		 $MaxDistance=$this->request->data["MaxDistance"];
		 $eventid=@$this->request->data["eventid"];
		 $token=$this->request->data["token"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($eventname)){
	      $response["message"]="Please enter event name.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($eventdescription)){
	      $response["message"]="Please enter event description.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($startdate)){
	      $response["message"]="Please enter startdate.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($enddate)){
	      $response["message"]="Please enter enddate.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($starttime)){
	      $response["message"]="Please enter starttime.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($endtime)){
	      $response["message"]="Please enter endtime.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($address)){
	      $response["message"]="Please enter address.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($price)){
	      $response["message"]="Please enter price.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($minAge)){
	      $response["message"]="Minage enter price.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($MaxAge)){
	      $response["message"]="MaxAge enter price.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($noperson)){
	      $response["message"]="Please enter person.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($latitude)){
	      $response["message"]="Please enter latitude.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($logitude)){
	      $response["message"]="Please enter logitude.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($visible)){
	      $response["message"]="Please select visible.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 } if(empty($Category)){
	      $response["message"]="Please select Category.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }if(empty($MaxDistance)){
	      $response["message"]="Please select MaxDistance.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 // $response["Eventinfo"]=array();
		 // $response["Eventinfo"]["userimage"]="";
		 $eventtable=TableRegistry::get("Events");
		 if(!empty($eventid)){
		 $event = $eventtable->get($eventid);
		 }else{
		 $event=$eventtable->newEntity();
		 }
		 $usertable=TableRegistry::get("Users");
	     $user = $usertable->get($userid);
		 
		 $event->name=$eventname;
		 $event->user_id=$userid;
		 $event->description=$eventdescription;
		 $event->start_date=$startdate;
		 $event->end_date=$enddate;
		 $event->start_time=$starttime;
		 $event->end_time=$endtime;
		 $event->address=$address;
		 $event->price	=$price;
		 $event->no_person=$noperson;
		 $event->latitude=$latitude;
		 $event->logitude=$logitude;
		 $event->visible=$visible;
		 $event->minAge=$minAge;
		 $event->maxAge=$MaxAge;
		 $event->Category=$Category;
		 $event->MaxDistance=$MaxDistance;
		 $event->createdOn=date("Y-m-d H:i:s");
		 $event->updatedOn=date("Y-m-d H:i:s");
		if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		$event->image=$files;
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/webroot/upload/".$files)){
		}
		}
		$result=$eventtable->save($event);
		$id=$result->id;
		$query=$eventtable->find('all',['conditions'=>['Events.id'=>$id]]);
		$eventdata = $query->toArray();
		//print_r($eventdata);die;
		$response["Eventinfo"]["id"]=$eventdata[0]->id;
		$response["Eventinfo"]["name"]=$eventdata[0]->name;
		$response["Eventinfo"]["description"]=$eventdata[0]->description;
		$response["Eventinfo"]["user_id"]=$eventdata[0]->user_id;
		$response["Eventinfo"]["start_time"]=$eventdata[0]->start_time;
		$response["Eventinfo"]["end_time"]=$eventdata[0]->end_time;
		$response["Eventinfo"]["start_date"]=$eventdata[0]->start_date;
		$response["Eventinfo"]["end_date"]=$eventdata[0]->end_date;
		$response["Eventinfo"]["address"]=$eventdata[0]->address;
		$response["Eventinfo"]["price"]=$eventdata[0]->price;
		$response["Eventinfo"]["no_person"]=$eventdata[0]->no_person;
		$response["Eventinfo"]["minAge"]=$eventdata[0]->minAge;
		$response["Eventinfo"]["maxAge"]=$eventdata[0]->maxAge;
		$response["Eventinfo"]["latitude"]=$eventdata[0]->latitude;
		$response["Eventinfo"]["logitude"]=$eventdata[0]->logitude;
		$response["Eventinfo"]["visible"]=$eventdata[0]->visible;
		$response["Eventinfo"]["Category"]=$eventdata[0]->Category;
		$response["Eventinfo"]["MaxDistance"]=$eventdata[0]->MaxDistance;
	
		$response["Eventinfo"]["image"]=!empty($files)?BASE_URL."/upload/".$files:(!empty($eventdata[0]->image)?BASE_URL."/upload/".$eventdata[0]->image:"");
		
		if($user->type=="puzzle"){
		  $response["Eventinfo"]["userimage"]=!empty($user->image)?BASE_URL."/upload/".$user->image:"";
		  }else{
		  $response["Eventinfo"]["userimage"]=!empty($user->image)?$user->image:"";
		  }
		 
		$response["message"]="Event created successfully.";
		$response["status"]="success";
		echo json_encode($response);
		exit;
		 }
		 else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 
	 public function eventsearch(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
		 @$distance=$this->request->data["distance"];
		 @$mintime=$this->request->data["mintime"];
		 @$mindate=$this->request->data["mindate"];
		 @$maxdate=$this->request->data["maxdate"];
		 @$maxtime=$this->request->data["maxtime"];
		 @$category=$this->request->data["Category"];
		 @$sex=$this->request->data["sex"];
		 @$minage=$this->request->data["minage"];
		 @$maxage=$this->request->data["maxage"];
		 $offset=$this->request->data["offset"];
		
		 $latitude=$this->request->data["latitude"];
		 $logitude=$this->request->data["logitude"];
		 $token=@$this->request->data["token"];
		  if(empty($token)){
	     /* $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;	*/	  
		 }
		 if(!empty($token)){
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 }
		 if(empty($latitude)){
	      $response["message"]="Please enter latitude.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($logitude)){
	      $response["message"]="Please enter logitude.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }  $i=0;
		    $results=array();
			$conn = ConnectionManager::get('default');
			$origLat = $latitude;
			$origLon = $logitude;
			$tableName="events";
			$where='';
			$dist = 10; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
			if(!empty($distance)|| !empty($category) || !empty($minage) || !empty($mindate)|| !empty($maxdate) || !empty($mintime) || !empty($maxtime) || !empty($sex) || !empty($age)){
				if(!empty($distance)){
				$dist = $distance;	
				}else{
			    $dist = 10;
				}
				if(!empty($mintime) && !empty($maxtime)){
				$where .= " and start_time>='$mintime' and end_time<='$maxtime'";	
				}
				if(!empty($mindate) && !empty($maxdate)){
				$where .= " and start_date>=$mindate and end_date<=$maxdate";	
				}
				if(!empty($sex)){
					if(!empty($where)){
					//$where.=" or ";	
					}
					if($sex!='B'){
				$where .= " and visible='$sex' ";	
					}
				}
				if(!empty($minage)){
				$where .= " and (minAge between $minage and $maxage) or (minAge>=$minage or maxAge<=$maxage)";
				
				}
				if(!empty($category)){
				$where .= " and Category IN ($category)";
				
				}
				
			$query = "SELECT *, latitude, logitude, 3956 * 2 * 
			ASIN(SQRT( POWER(SIN(($origLat - abs(latitude))*pi()/180/2),2)
			+COS($origLat*pi()/180 )*COS(abs(latitude)*pi()/180)
			*POWER(SIN(($origLon-logitude)*pi()/180/2),2))) 
			as distance FROM $tableName WHERE 
			logitude between ($origLon-$dist/abs(cos(radians($origLat))*69)) 
			and ($origLon+$dist/abs(cos(radians($origLat))*69)) 
			and latitude between ($origLat-($dist/69)) 
			and ($origLat+($dist/69)) 
			having distance < $dist $where ORDER BY distance limit 10 offset $offset;";	
			}else{
			$query = "SELECT *, latitude, logitude, 3956 * 2 * 
			ASIN(SQRT( POWER(SIN(($origLat - abs(latitude))*pi()/180/2),2)
			+COS($origLat*pi()/180 )*COS(abs(latitude)*pi()/180)
			*POWER(SIN(($origLon-logitude)*pi()/180/2),2))) 
			as distance FROM $tableName WHERE 
			logitude between ($origLon-$dist/abs(cos(radians($origLat))*69)) 
			and ($origLon+$dist/abs(cos(radians($origLat))*69)) 
			and latitude between ($origLat-($dist/69)) 
			and ($origLat+($dist/69)) 
			having distance < $dist ORDER BY distance limit 10 offset $offset;";
			}	//echo $query;die;	
		//	$stmt = $conn->execute($query);
	//		$results = $stmt ->fetchAll('assoc');
	        
			$connection = ConnectionManager::get('default');
            $results = $connection->execute($query)->fetchAll('assoc');
			//print_r($results);die;
			$imagetable=TableRegistry::get("Images");
			if(!empty($results)){
			foreach($results as $resultsn){
				$jointable=TableRegistry::get("JoinEvent");
		 $query=$jointable->find('all',['conditions'=>['JoinEvent.status'=>'confirm','JoinEvent.event_id'=>$resultsn["id"]]]);
		 $joinlist = $query->toArray();
		 
		 $reciverid="";
		 if(!empty($joinlist)){
			 foreach($joinlist as $joinlists){
				 if($joinlists->sender_id!=$resultsn["user_id"]){
					$reciverid[]= $joinlists->sender_id;
				 }else{
					 $reciverid[]= $joinlists->reciever_id;
				 }
			 }
			 
		 }
		 $userlistcountM= $userlistcountF=0;
		 $usertable=TableRegistry::get("Users");
		 if(!empty($reciverid)){
		 $query=$usertable->find('all',['conditions'=>['Users.id IN'=>$reciverid,'Users.gender'=>'M']]);
		 $userlistcountM = $query->count();
		 $query=$usertable->find('all',['conditions'=>['Users.id IN'=>$reciverid,'Users.gender'=>'F']]);
		 $userlistcountF = $query->count();
		 }
			$results[$i]["id"]=!empty($resultsn["id"])?$resultsn["id"]:"";
			$results[$i]["name"]=!empty($resultsn["name"])?$resultsn["name"]:"";
			$results[$i]["num_male"]=!empty($userlistcountM)?($userlistcountM):"0";
			$results[$i]["num_female"]=!empty($userlistcountF)?($userlistcountF):"0";
			$results[$i]["description"]=!empty($resultsn["description"])?$resultsn["description"]:"";
			$results[$i]["user_id"]=!empty($resultsn["user_id"])?$resultsn["user_id"]:"";
			$results[$i]["start_time"]=!empty($resultsn["start_time"])?$resultsn["start_time"]:"";
			$results[$i]["end_time"]=!empty($resultsn["end_time"])?$resultsn["end_time"]:"";
			$results[$i]["start_date"]=!empty($resultsn["start_date"])?$resultsn["start_date"]:"";
			$results[$i]["end_date"]=!empty($resultsn["end_date"])?$resultsn["end_date"]:"";
			$results[$i]["address"]=!empty($resultsn["address"])?$resultsn["address"]:"";
			$results[$i]["price"]=!empty($resultsn["price"])?$resultsn["price"]:"";
			$results[$i]["no_person"]=!empty($resultsn["no_person"])?$resultsn["no_person"]:"";
			$results[$i]["minAge"]=!empty($resultsn["minAge"])?$resultsn["minAge"]:"";
			$results[$i]["maxAge"]=!empty($resultsn["maxAge"])?$resultsn["maxAge"]:"";
			$results[$i]["latitude"]=!empty($resultsn["latitude"])?$resultsn["latitude"]:"";
			$results[$i]["logitude"]=!empty($resultsn["logitude"])?$resultsn["logitude"]:"";
			$results[$i]["logitude"]=!empty($resultsn["logitude"])?$resultsn["logitude"]:"";
			$results[$i]["visible"]=!empty($resultsn["visible"])?$resultsn["visible"]:"";
			$results[$i]["createdOn"]=!empty($resultsn["createdOn"])?$resultsn["createdOn"]:"";
			 $usertable=TableRegistry::get("Users");
	     $user = $usertable->get($resultsn["user_id"]);
		if($user->type=="puzzle"){
		  $results[$i]["userimage"]=!empty($user->image)?BASE_URL."/upload/".$user->image:"";
		  }else{
		  $results[$i]["userimage"]=!empty($user->image)?$user->image:"";
		  }
		 
			$images=array();
			//$results[$i]["image"]=!empty($resultsn["image"])?BASE_URL."/upload/".$resultsn["image"]:"";
			$query=$imagetable->find('all',['conditions'=>['Images.event_id'=>$resultsn["id"]]]);
		    $imglist = $query->toArray();$j=0;
			if(!empty($imglist)){
			 foreach($imglist as $imglists){
			    $files=$imglists->image;
				$images[$j]["image"]= !empty($files)?BASE_URL."/upload/".$files:"";
				$images[$j]["id"]= $imglists->id;
				$j++;
			 }
		 }
		 $results[$i]["images"]=$images;
			$i++;
			}}
			
			if(!empty($results)){
			$response["message"]="Event search successfully.";
			$response["status"]="success";
			$response["eventInfo"]=$results;
			echo json_encode($response);
			exit;
			}else{
			$response["message"]="No event find.";
			$response["status"]="success";
			$response["eventInfo"]=$results;
			echo json_encode($response);
			exit;	
			}
		  }
		 else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 public function eventimages(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
	
		 $token=$this->request->data["token"];
		 $eventid=$this->request->data["eventid"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($eventid)){
	      $response["message"]="Eventid can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($_FILES["image"]["name"])){
	      $response["message"]="Please select image.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;	
		 }
		 $imagetable=TableRegistry::get("Images");
		 $img=$imagetable->newEntity();
		 if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		$img->image=$files;
		$img->event_id=$eventid;
		$img->createdOn=date("Y-m-d H:i:s");
		$img->updatedOn=date("Y-m-d H:i:s");
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/webroot/upload/".$files)){
		}
		}
				
		if(!empty($img->image)){
		$result=$imagetable->save($img);
		$response["Eventinfo"]=$img;
		
		$response["Eventinfo"]["image"]=!empty($files)?BASE_URL."/upload/".$files:"";
		
		$response["message"]="Event Image upoaded successfully.";
		$response["status"]="success";
		echo json_encode($response);
		exit;
		}
	 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 public function eventinfo(){
		 $this->autoRender=false;
	     if($this->request->ispost()){
	
		 $token=$this->request->data["token"];
		 $eventid=$this->request->data["eventid"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($eventid)){
	      $response["message"]="Eventid can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $evnttable=TableRegistry::get("Events");
		 $query=$evnttable->find('all',['conditions'=>['Events.id'=>$eventid]]);
		 $eventlist = $query->toArray();
		 if(!empty($eventlist)){
		 $imagetable=TableRegistry::get("Images");
		 $query=$imagetable->find('all',['conditions'=>['Images.event_id'=>$eventid]]);
		 $imglist = $query->toArray();
		 $jointable=TableRegistry::get("JoinEvent");
		 $query=$jointable->find('all',['conditions'=>['JoinEvent.status'=>'confirm','JoinEvent.event_id'=>$eventid]]);
		 $joinlist = $query->toArray();
		 
		 
		 if(!empty($joinlist)){
			 foreach($joinlist as $joinlists){
				 if($joinlists->sender_id!=$userid){
					$reciverid[]= $joinlists->sender_id;
				 }else{
					 $reciverid[]= $joinlists->reciever_id;
				 }
			 }
			 
		 }
		 if(!empty($reciverid)){
	    // $reciverid=implode(",",$reciverid);		 
		 
		 $usertable=TableRegistry::get("Users");
		 $query=$usertable->find('all',['conditions'=>['Users.id IN'=>$reciverid]]);
		 $userlist = $query->toArray();
		 }
		 $i=0;
		 $userinfo=array();
		 if(!empty($userlist)){
			 foreach($userlist as $userlists){
				 $userinfo[$i]["id"]=$userlists["id"];
				 $userinfo[$i]["name"]=$userlists["name"];
				 
				 if($userlists["type"]=="puzzle"){
		  $userinfo[$i]["image"] =!empty($userlists["image"])?BASE_URL."/upload/".$userlists["image"]:"";
		  }else{
		  $userinfo[$i]["image"]=!empty($userlists["image"])?$userlists["image"]:"";
		  }
				 $i++;
			 }
		 }
		 $response["Eventinfo"]["id"]=$eventlist[0]->id;
		 $response["Eventinfo"]["name"]=$eventlist[0]->name;
		 $response["Eventinfo"]["description"]=$eventlist[0]->description;
		 $response["Eventinfo"]["start_time"]=$eventlist[0]->start_time;
		 $response["Eventinfo"]["end_time"]=$eventlist[0]->end_time;
		 $response["Eventinfo"]["start_date"]=$eventlist[0]->start_date;
		 $response["Eventinfo"]["end_date"]=$eventlist[0]->end_date;
		 $response["Eventinfo"]["address"]=$eventlist[0]->address;
		 $response["Eventinfo"]["price"]=$eventlist[0]->price;
		 $response["Eventinfo"]["no_person"]=$eventlist[0]->no_person;
		 $response["Eventinfo"]["latitude"]=$eventlist[0]->latitude;
		 $response["Eventinfo"]["logitude"]=$eventlist[0]->logitude;
		 $response["Eventinfo"]["visible"]=$eventlist[0]->visible;
		 $response["Eventinfo"]["joinuser"]=$userinfo;
		 $images=array();
		 $i=0;
		 if(!empty($imglist)){
			 foreach($imglist as $imglists){
				 $files=$imglists->image;
				$images[$i]["image"]= !empty($files)?BASE_URL."/upload/".$files:"";
				$images[$i]["id"]= $imglists->id;
				$i++;
			 }
		 }
		 $response["Eventinfo"]["image"]=$images;
	    $response["message"]="Event List.";
	     $response["status"]="success";
	      echo json_encode($response);
          exit;
		 }
		 else{
		  $response["message"]="Event does not exists";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
		 }
		 else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 public function getprofile(){
		  $this->autoRender=false;
	     if($this->request->ispost()){
	
		 $token=$this->request->data["token"];
		 $id=$this->request->data["id"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($id)){
	      $response["message"]="Userid can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $usertable=TableRegistry::get("Users");
		 $query=$usertable->find('all',['conditions'=>['Users.id'=>$id]]);
		 $userlist = $query->toArray();
		 //print_r($userlist);die;
		 if(!empty($userlist)){
		  $user["Userinfo"]["id"] =$userlist[0]->id;
		  $user["Userinfo"]["name"] =$userlist[0]->name;
		  $user["Userinfo"]["email"] =$userlist[0]->email;
		  $user["Userinfo"]["address"] =$userlist[0]->address;
		  $user["Userinfo"]["gender"] =$userlist[0]->gender;
		  $user["Userinfo"]["age"] =$userlist[0]->age;
		  $user["Userinfo"]["mobile"] =$userlist[0]->mobile;
		  $user["Userinfo"]["city"] =$userlist[0]->city;
		  $user["Userinfo"]["facebook_id"] =$userlist[0]->facebook_id;
		  $user["Userinfo"]["instagram_id"] =$userlist[0]->instagram_id;
		  if($userlist[0]->type=="puzzle"){
		  $user["Userinfo"]["image"] =!empty($userlist[0]->image)?BASE_URL."/upload/".$userlist[0]->image:"";
		  }else{
		  $user["Userinfo"]["image"] =!empty($userlist[0]->image)?$userlist[0]->image:"";
		  }
		  $response["Userinfo"]=$user["Userinfo"];
		  $response["message"]="User info.";
	      $response["status"]="success";
	      echo json_encode($response);
          exit;
		 }else{
		  $response["message"]="User does not exists.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
		 }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
	 
	 public function joinEvent(){
		  $this->autoRender=false;
	     if($this->request->ispost()){
	
		 $token=$this->request->data["token"];
		 $evntid=$this->request->data["evntid"];
		 $notifymessage=$this->request->data["notifymessage"];
		 $no_person=$this->request->data["no_person"];
		  if(empty($token)){
	      $response["message"]="Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($evntid)){
	      $response["message"]="Eventid can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $userid=$this->checktoken($token);
		 if(empty($userid)){
	      $response["message"]="Invalid User.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($notifymessage)){
	    /*  $response["message"]="Please enter message.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		*/  
		 }
		 $joinEventtable=TableRegistry::get("JoinEvent");
		 
		 $eventtable=TableRegistry::get("Events");
		 $getevent = $eventtable->get($evntid);
		 if(empty($getevent)){
	      $response["message"]="Evet does not exist.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 
		 $query=$joinEventtable->find('all',['conditions'=>['JoinEvent.event_id'=>$evntid,"OR"=>['JoinEvent.sender_id'=>$userid,'JoinEvent.reciever_id'=>$userid]]]);
		 $getjoinlist = $query->toArray();
		 
		 if(empty($getjoinlist))
		 $joinEvent=$joinEventtable->newEntity();
	 else{
		 
		 $joinEvent = $joinEventtable->get($getjoinlist[0]->id);
	 }
		 $joinEvent->event_id=$evntid;
		 $joinEvent->message=!empty($notifymessage)?$notifymessage:'';
		 $joinEvent->user_id=$getevent->user_id;
		 $joinEvent->sender_id=$userid;
		 $joinEvent->reciever_id=$getevent->user_id;
		 $joinEvent->no_perso=$no_person;
		 if(!empty($getjoinlist)){
		 $joinEvent->status="confirm";
		 }else{
		 $joinEvent->status="pending";
		 }
		 $joinEvent->createdOn=date("Y-m-d H:i:s");
		 $joinEvent->updatedOn=date("Y-m-d H:i:s");
		
		 $result=$joinEventtable->save($joinEvent);
		 $response["message"]="Invite send successfully.";
	     $response["status"]="success";
	      echo json_encode($response);
          exit;
		  }else{
		  $response["message"]="Something went wrong.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;
	 }
	 }
}
