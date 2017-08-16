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
class ServicesController extends AppController
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
		 $name=$this->request->data["name"];
		 $email=$this->request->data["emailid"];
		 $password=$this->request->data["password"];
		 $device_token=$this->request->data["device_token"];
		 
    
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
		 if(empty($password)){
	      $response["message"]="Password can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 if(empty($device_token)){
	      $response["message"]="Device Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
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
		 $email=$this->request->data["emailid"];
		 $password=$this->request->data["password"];
		 $device_token=$this->request->data["device_token"];
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
		 if(empty($device_token)){
	      $response["message"]="Device Token can not be empty.";
	      $response["status"]="failure";
	      echo json_encode($response);
          exit;		  
		 }
		 $usertable=TableRegistry::get("Users");
		 $password=Security::hash($password,'md5',true);
		 $query=$usertable->find('all',['conditions'=>['Users.email'=>$email,'Users.password'=>$password]]);
		 $checkuserexists = $query->toArray();
		 if(empty($checkuserexists)){
	      $response["message"]="Email and password does not match.";
	      $response["status"]="failure";
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
			$response["Userinfo"]["device_token"]=$device_token;
		    if(!empty($response)){
	      $response["message"]="User Login successfully.";
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
		 $address=$this->request->data["address"];
		 $mobile=$this->request->data["mobile"];
		 $city=$this->request->data["city"];
		 $token=$this->request->data["token"];
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
		 }
		
		
		
		$usertable=TableRegistry::get("Users");
		$user = $usertable->get($userid);
		
		$user->mobile=$mobile;
		$user->email=$email;
		$user->address=$address;
		$user->createdOn=date("Y-m-d H:i:s");
		$user->updatedOn=date("Y-m-d H:i:s");
		
		if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/meeat/webroot/upload/".$files)){
		}
		}
		
		$response["Userinfo"]=$user;
		$result=$usertable->save($user);
		$query=$usertable->find('all',['conditions'=>['Users.id'=>$userid]]);
		$checkuserdata = $query->toArray();
		$response["Userinfo"]["image"]=!empty($files)?BASE_URL."/webroot/upload/".$files:(!empty($checkuserdata[0]->image)?BASE_URL."/webroot/upload/".$checkuserdata[0]->image:"");
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
	      $response["status"]="success";
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
			 $email->from(["meeat2017@gmail.com" => "Sender"])
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
		 $eventtable=TableRegistry::get("Events");
		 $event=$eventtable->newEntity();
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
		 $event->createdOn=date("Y-m-d H:i:s");
		 $event->updatedOn=date("Y-m-d H:i:s");
		if(!empty($_FILES["image"]["tmp_name"])){
		$files=date("his").$_FILES["image"]["name"];
		$event->image=$files;
		if(move_uploaded_file($_FILES["image"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/meeat/webroot/upload/".$files)){
		}
		}
		$result=$eventtable->save($event);
		$id=$result->id;
		$query=$eventtable->find('all',['conditions'=>['Events.id'=>$id]]);
		$eventdata = $query->toArray();
		$response["Eventinfo"]=$event;
		$response["Eventinfo"]["image"]=!empty($files)?BASE_URL."/webroot/upload/".$files:(!empty($eventdata[0]->image)?BASE_URL."/webroot/upload/".$eventdata[0]->image:"");
		
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
		 @$search=$this->request->data["search"];
		
		 $latitude=$this->request->data["latitude"];
		 $logitude=$this->request->data["logitude"];
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
			$dist = 10; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
			if(!empty($search)){
			$query = "SELECT *, latitude, logitude, 3956 * 2 * 
			ASIN(SQRT( POWER(SIN(($origLat - abs(latitude))*pi()/180/2),2)
			+COS($origLat*pi()/180 )*COS(abs(latitude)*pi()/180)
			*POWER(SIN(($origLon-logitude)*pi()/180/2),2))) 
			as distance FROM $tableName WHERE 
			logitude between ($origLon-$dist/abs(cos(radians($origLat))*69)) 
			and ($origLon+$dist/abs(cos(radians($origLat))*69)) 
			and latitude between ($origLat-($dist/69)) 
			and ($origLat+($dist/69)) 
			having distance < $dist and (name Like '%$search%' or description Like '%$search%' or address Like '%$search%' ) ORDER BY distance limit 100;";	
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
			having distance < $dist ORDER BY distance limit 100;";
			}			
			$stmt = $conn->execute($query);
			$results = $stmt ->fetchAll('assoc');
			if(!empty($results)){
			foreach($results as $resultsn){
			$results[$i]["image"]=!empty($resultsn["image"])?BASE_URL."/webroot/upload/".$resultsn["image"]:"";
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
			$response["status"]="failure";
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
}
