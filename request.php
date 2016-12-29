
<?php
include_once 'connection.php';

if(isset($_REQUEST['request-for'])){
	$requestHandler = new RequestHandler();
	switch($_REQUEST['request-for']){
		case 'application-detail' :
			return $requestHandler->applicationDetails();
			break;

		case 'application-detail-by-district':
			return $requestHandler->applicationDetailByDistrict();
			break;
                case 'industry-detail-by-district':
			return $requestHandler->industryDetailByDistrict();
			break;
		case 'industry-detail-by-status':
			return $requestHandler->industryDetailByStatus();
			break;	

		case 'download-cert':
			return $requestHandler->downloadConsentCert();
			break;
		
		case 'download-other-cert':
			return $requestHandler->downloadOtherCert();
			break;
		
		case 'document-check':
			return $requestHandler->documentCheck();
			break;

		case 'application-documents':
			return $requestHandler->applicationDocuments();
			break;
  
		case 'feedback':
			return $requestHandler->feedback();
			break;

		case 'login':
			return $requestHandler->login();
			break;
			
		case 'ind-login':
			return $requestHandler->industryLogin();
			break;

		case 'inspection-application':
			return $requestHandler->inspectedApplications();
			break;
			
		case 'ind-applications':
			return $requestHandler->indApplications();
			break;
			
		case 'save-inspection':
			return $requestHandler->saveInspection();
			break;
	
		case 'check-image':
			return $requestHandler->checkImage();
			break;

		case 'download-application-form':
			return $requestHandler->downloadapplicationForm();
			break;
			
		case 'note-history':
			return $requestHandler->noteHistory();
			break;
			
		case 'forward-to-roles':
			return $requestHandler->forwardToRoles();
			break;	
			
		case 'forward':
			return $requestHandler->forward();
			break;
	
		case 'inspection-permission':
			return $requestHandler->inspectionPermission();
			break;

  
		case 'default':
			return 'Invalid request';
	}
}else{

}

class RequestHandler{

	public $response = array('status' => 'ok', 'message' => '');


	public function downloadConsentCert() {

		$connectionClass = new Connection();
		$connection = $connectionClass->getConnection();

		$appId = $_REQUEST['app_id'];

		$stmt = $connection->prepare("SELECT *  FROM application_file_record where ind_application_id = $appId AND type_of_file IN ('Certificate','Certificate1') order by id DESC limit 1");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$row = $stmt->fetch();

		header('Content-Type: application/pdf');
		header('Content-disposition: attachment; filename=Certificate.pdf');
 		echo $content = stream_get_contents($row['data']); 

		die;
 	}
	
	public function downloadOtherCert() {

		$connectionClass = new Connection();
		$connection = $connectionClass->getConnection();

		$appId = $_REQUEST['app_id'];

		$stmt = $connection->prepare("SELECT *  FROM application_file_record where ind_application_id = $appId AND type_of_file IN ('OtherCertificate') order by id DESC limit 1");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$row = $stmt->fetch();

		header('Content-Type: application/pdf');
		header('Content-disposition: attachment; filename=Certificate.pdf');
 		echo $content = stream_get_contents($row['data']); 

		die;
 	}
	
	
	public function downloadapplicationForm() {

		$connectionClass = new Connection();
		$connection = $connectionClass->getConnection();

		$formId = $_REQUEST['id'];
		$typeFile = $_REQUEST['type_of_file'];

		$stmt = $connection->prepare("SELECT *  FROM application_file_record where id = $formId");
		// FormWater FormAir
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		$row = $stmt->fetch();

		header('Content-Type: application/pdf');
		header('Content-disposition: attachment; filename=Certificate.pdf');
 		echo $content = stream_get_contents($row['data']); 

		die;
 	}

	public function industryDetailByStatus(){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();

			$stmt = $connection->prepare("SELECT count(*), application_status as total FROM kerala_mobileapp group by application_status");
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}

			echo "<pre>";print_r($result);exit; 

			$response['data']['all'] = $result;

			
			$stmt = $connection->prepare("SELECT COUNT(*) AS total, to_char(submission_date, 'Mon'), application_status FROM jhk_mobile_app 
							where to_char(submission_date, 'YYYY')  = to_char( now(), 'YYYY' )
							GROUP BY challan_for, to_char(submission_date, 'Mon')");
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}

			echo "<pre>";print_r($result);exit; 

			$response['data']['month'] = $result;

			

			$stmt = $connection->prepare("SELECT COUNT(*) AS total, to_char(submission_date, 'Mon'), application_status FROM jhk_mobile_app 
							where to_char(submission_date, 'YYYY')  = to_char( now(), 'YYYY' )
							GROUP BY challan_for, to_char(submission_date, 'Mon')");
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}

			echo "<pre>";print_r($result);exit; 

			$response['data']['year'] = $result;


			$response['status'] = 'ok';
			$response['message'] = "Request completed successfully";
			//

		
			die(json_encode($response));
	}


	public function applicationDetails(){
		if(isset($_REQUEST['industry_id']) && $_REQUEST['industry_id'] != ""){
			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();

			$stmt = $connection->prepare("SELECT * FROM kerala_mobileapp WHERE application_id = :industry_id ");
			$stmt->bindParam(':industry_id', $_REQUEST['industry_id']);
			$stmt->execute();
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}

			$response['status'] = 'ok';
			$response['message'] = "Request completed successfully";
			$response['data'] = $result;

		} else if(isset($_REQUEST['industry_name']) && $_REQUEST['industry_name'] != ""){
			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();

			$stmt = $connection->prepare("SELECT * FROM kerala_mobileapp WHERE ind_name ilike ?");
			//$stmt->bindParam(1, $_REQUEST['industry_name'], PDO::PARAM_STR);
			$params = array("%".trim($_REQUEST['industry_name'])."%");

			$stmt->execute($params);
			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			$result = array();
			while ($row = $stmt->fetch()) {
				array_push($result, $row);
			}

			$response['status'] = 'ok';
			$response['message'] = "Request completed successfully";
			$response['data'] = $result;

		} else{
			$response['status'] = 'error';
			$response['message'] = "Industry ID is needed";
		}

		die(json_encode($response));
	}

	public function applicationDetailByDistrict(){

		if(isset($_REQUEST['district']) && $_REQUEST['district'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				$query = " SELECT * FROM kerala_mobileapp  WHERE district_name = ? ";
				if(isset($_REQUEST['industry_name']) && $_REQUEST['industry_name'] != ""){
					$query .= " AND ind_name ilike ? ";

				}
				$stmt = $connection->prepare($query);

				//$stmt->bindParam(':DISTRICT', $_REQUEST['district'], PDO::PARAM_STR);
				$params = array($_REQUEST['district']);
				if(isset($_REQUEST['industry_name']) && $_REQUEST['industry_name'] != ""){
					//$stmt->bindParam(':INDUSTRY_NAME', trim($_REQUEST['industry_name']), PDO::PARAM_STR);
					$params = array($_REQUEST['district'], "%".trim($_REQUEST['industry_name'])."%");
				}

				//echo "<pre>";print_r($stmt);exit;

				$stmt->execute($params);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				//echo "<pre>";print_r($stmt);exit;
				$result = array();
				while ($row = $stmt->fetch()) {
					array_push($result, $row);
				}
				$response['status'] = 'ok';
				$response['message'] = "Request completed successfully";
				$response['data'] = $result;

			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}




		}else{
			$response['status'] = 'error';
			$response['message'] = "District is needed";
		}

		die(json_encode($response));
	}
        
        public function industryDetailByDistrict(){

            if(isset($_REQUEST['district']) && $_REQUEST['district'] != ""){
			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				$query = " SELECT application_id, ind_name,ind_address FROM kerala_mobileapp WHERE district_name = ? ";
				
				$stmt = $connection->prepare($query);
                                
				$params = array($_REQUEST['district']);
				$stmt->execute($params);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$result = array();
				while ($row = $stmt->fetch()) {
					array_push($result, $row);
				}
				$response['status'] = 'ok';
				$response['message'] = "Request completed successfully";
				$response['data'] = $result;

			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}




		}else{
			$response['status'] = 'error';
			$response['message'] = "District is needed";
		}

		die(json_encode($response));
        }
		
	public function documentCheck() 
	{
		if(isset($_REQUEST['industry_id']) && $_REQUEST['industry_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				$query = " SELECT application_id, type_of_industry FROM kerala_mobileapp WHERE application_id = ? ";
				
				$stmt = $connection->prepare($query);
                                
				$params = array($_REQUEST['industry_id']);
				

				$stmt->execute($params);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
//				echo "<pre>";print_r($stmt);exit;
				$result = array();
				$row = $stmt->fetch();
//echo "<pre>";print_r($row);exit;

				if (count($row) ) {
				
				$query = " SELECT id, document, document_type FROM document_checklist_master WHERE application_for = ? ";
				
				$stmt = $connection->prepare($query);
                                
				$params1 = array($row['type_of_industry']);
				

				$stmt->execute($params1);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				
				$requiredDocs = array();
				$allDocs = array();

				 
				$chk = '';
				while ($row = $stmt->fetch()) {

					$chk = substr($row['document'], -2); 
					if($chk == '**') {
						array_push($requiredDocs , $row);
					}
					array_push($allDocs , $row);
	
					
				} 



				$query = "select id, version, extension, ind_application_id, level, name, size from application_documents where ind_application_id = ? ";
				
				$stmt = $connection->prepare($query);
                                
				

				$stmt->execute($params);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
//				echo "<pre>";print_r($stmt);exit;
				$result = array();

				while ($row = $stmt->fetch()) {

 					array_push($result , $row);
					
				}


//echo "<pre>";print_r($requiredDocs );exit;


				} else {
					$response['status'] = 'error';
					$response['message'] = "No application found.";
		 			die(json_encode($response));


				}
				$response['status'] = 'ok';
				$response['message'] = "Request completed successfully";
				$response['data'] = array('allDocs' => $allDocs, 'requiredDocs' => $requiredDocs);

			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}

			
		} else {
			$response['status'] = 'error';
			$response['message'] = "Industry id is required";
		}

		die(json_encode($response));
		

	}

	public function applicationDocuments() 
	{  
		if(isset($_REQUEST['industry_id']) && $_REQUEST['industry_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				$query = "select id, version, extension, ind_application_id, level, name, size from application_documents where ind_application_id = ? ";
				
				$stmt = $connection->prepare($query);
                                
				$params = array($_REQUEST['industry_id']);
				

				$stmt->execute($params);

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
//				echo "<pre>";print_r($stmt);exit;
				$result = array();

				while ($row = $stmt->fetch()) {

 					array_push($result , $row);
					
				}

				 
				$response['status'] = 'ok';
				$response['message'] = "Request completed successfully";
				$response['data'] = $result ;

			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}

			
		} else {
			$response['status'] = 'error';
			$response['message'] = "Industry id is required";
		}

		die(json_encode($response));

	}

	
	public function feedback() 
	{
		if(isset($_POST['mobile']) && $_POST['mobile'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {

				$name= stripslashes(trim($_POST['name']));
				$email = stripslashes(trim($_POST['email']));
				$mobile = stripslashes(trim($_POST['mobile']));
				$feedback = stripslashes(trim($_POST['feedback']));

				$stmt =   $connection->prepare(" INSERT into feed_backs ( version, name, email, mobile, feedback) VALUES ( :version, :name, :email, :mobile, :feedback)" );		
				//$stmt = $connection->prepare($query);
                                
				$params = array('version' => 0,'name' => $name, 'email'=>$email, 'mobile' =>  $mobile, 'feedback' => $feedback);

				//echo "<pre>";print_r($stmt);exit;

				$stmt->execute($params);


				$response['status'] = 'ok';
				$response['message'] = "Feedback submitted successfully";


			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}

			
		} else {
			$response['status'] = 'error';
			$response['message'] = "Feedback not submitted successfully";
		}

		die(json_encode($response));
		

	} 

	
	
	public function login() 
	{  
		
		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "" && isset($_REQUEST['password']) && $_REQUEST['password'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				
				$user_id = stripslashes(trim($_POST['user_id']));
				$password = stripslashes(trim($_POST['password']));
				
				//$ins = $_REQUEST['user_id'].'   '.md5($_REQUEST['password']).'   '.$_REQUEST['password'];
				// $ins = $user_id.'   '.md5($password).'   '.$password;
				// $pending_with_id = 123;
				// $escaped1 = 123;
				//pg_query("INSERT INTO inspection_data_2 (application_id, date_created, pending_with_id, role_id, inspection_report) VALUES (123, NOW(), '{$pending_with_id}', $pending_with_id, '{$ins}')");  

				
				
				$query = "select id from user_master where id = ? and password = ?";
				$stmt = $connection->prepare($query);
				//$params = array($_REQUEST['user_id'], md5($_REQUEST['password']));
				$params = array($user_id, md5($password));
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = $stmt->fetch();
 
				if($result) {
					$response['status'] = 'ok';
					$response['message'] = "Login successfully";
					$response['token'] = md5(time());
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid credentials";
				}
 
			}catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}

		} else {
			$response['status'] = 'error';
			$response['message'] = "User id and password is required";
		}

		die(json_encode($response));

	}
	
	public function industryLogin() 
	{  
		
		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "" && isset($_REQUEST['password']) && $_REQUEST['password'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				
				$user_id = stripslashes(trim($_POST['user_id']));
				$password = stripslashes(trim($_POST['password']));
				
				$query = "select industry_reg_master_id from ind_user where id = ? and password = ?";
				$stmt = $connection->prepare($query);
				
				$params = array($user_id, md5($password));
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = $stmt->fetch();
 
				if($result) {
					$response['status'] = 'ok';
					$response['message'] = "Login successfully";
					$response['ind_user_id'] = $result['industry_reg_master_id'];
					$response['token'] = md5(time());
				} else {
					$response['status'] = 'error';
					$response['message'] = "Invalid credentials";
				}
 
			}catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}

		} else {
			$response['status'] = 'error';
			$response['message'] = "User id and password is required";
		}

		die(json_encode($response));

	}
	
	public function inspectedApplications() 
	{  
		
		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				/*$query = "select application_pending_details.id, application_pending_details.application_id, application_status, application_pending_details.pending_with_id, application_pending_details.role_id, ind_application_details.application_date, 
				ind_application_details.application_name,ind_application_details.application_type, application_processing_details.date_created, application_processing_details.inspection_note  
				from application_pending_details
				join ind_application_details on application_pending_details.application_id = ind_application_details.id AND ind_application_details.inspection = TRUE 
				join application_processing_details on application_pending_details.application_id = application_processing_details.application_id AND application_processing_details.inspection = TRUE				
				where application_pending_details.pending_with_id = ? AND application_pending_details.id  NOT IN (
					SELECT app_pending_details_id 
					FROM inspection_data
					WHERE inspection_data.pending_with_id = ? 
				) "; */
				
				
				
				$query = "select application_pending_details.id, application_pending_details.application_id, application_status, application_pending_details.pending_with_id, application_pending_details.role_id, ind_application_details.application_date,ind_application_details.application_name,ind_application_details.application_type, ind_application_details.inspection, industry_reg_master.ind_address,industry_reg_master.ind_status, ind_cat_master.name as category_name, inspection_data.lat_long
				from application_pending_details
				join ind_application_details on application_pending_details.application_id = ind_application_details.id
				join industry_reg_master on industry_reg_master.id = ind_application_details.ind_user_id
				join ind_cat_master on ind_cat_master.id = industry_reg_master.category_id
				left join inspection_data on inspection_data.application_id = ind_application_details.id
				where application_pending_details.pending_with_id = ? AND application_pending_details.application_status = 'pending' ";
				
				$stmt = $connection->prepare($query);
				$params = array($_REQUEST['user_id']);
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = array();

				while ($row = $stmt->fetch()) {
					
					
					$appId = $row['application_id'];
					$queryFile = "SELECT id, type_of_file  FROM application_file_record where ind_application_id = $appId AND type_of_file IN ('Application Form')";
					$stmtFile = $connection->prepare($queryFile);
					$stmtFile->execute();
					$stmtFile->setFetchMode(PDO::FETCH_ASSOC);				 
					//$applicationForms = array();
					$applicationForms = "";
			
					while ($rowFile = $stmtFile->fetch()) {
//						$applicationForms[$rowFile['type_of_file']] = $rowFile;
						$applicationForms['application_form'] = $rowFile;
						//echo '<pre>'; print_r($rowFile); die;
					}
					if(!$applicationForms){
						$applicationForms = new stdClass ();
					}
					
					if(!$row['lat_long']){
						$row['lat_long'] = "" ;
					}
					
					
					$row['application_forms']  =  $applicationForms;
					
 					array_push($result , $row);
					
				} 
				

				$response['status'] = 'ok';
				$response['message'] = "Data fetched successfully";
				$response['result'] = $result;
				 
 
			}catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}

		} else {
			$response['status'] = 'error';
			$response['message'] = "User id is required";
		}

		die(json_encode($response));

	}

	public function indApplications() 
	{  
	
		
		if(isset($_POST['ind_user_id']) && $_POST['ind_user_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				
				$query = "select id as application_id, ind_application_details.application_date,ind_application_details.application_name,ind_application_details.application_type, ind_application_details.inspection, ind_application_details.completion_status
				from ind_application_details
				where ind_application_details.ind_user_id = ? AND ind_application_details.completion_status = 'completed' ";
				
				$stmt = $connection->prepare($query);
				$params = array($_POST['ind_user_id']);
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = array();

				while ($row = $stmt->fetch()) {
					
					//print_r($row); die;
					$appId = $row['application_id'];
					$queryFile = "SELECT id, type_of_file  FROM application_file_record where ind_application_id = $appId AND type_of_file IN ('Application Form')";
					$stmtFile = $connection->prepare($queryFile);
					$stmtFile->execute();
					$stmtFile->setFetchMode(PDO::FETCH_ASSOC);				 
					//$applicationForms = array();
					$applicationForms = "";
			
					while ($rowFile = $stmtFile->fetch()) {
						$applicationForms[$rowFile['type_of_file']] = $rowFile;
						//echo '<pre>'; print_r($rowFile); die;
					}
					if(!$applicationForms){
						$applicationForms = new stdClass ();
					}
					$row['application_forms']  =  $applicationForms;
					
 					array_push($result , $row);
					
				} 
				

				$response['status'] = 'ok';
				$response['message'] = "Data fetched successfully";
				$response['result'] = $result;
				 
 
			}catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}

		} else {
			$response['status'] = 'error';
			$response['message'] = "User id is required";
		}

		die(json_encode($response));

	}

	public function noteHistory() {
		
		if(isset($_POST['application_id']) && $_POST['application_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				$appId = trim($_POST['application_id']);
				
				//$query = "SELECT *  FROM application_processing_details where application_id = ? ";
				$query = "SELECT application_processing_details.*, rm.role_name as note_by, rmf.role_name as forwarded_to  FROM application_processing_details 
left join role_master rm on application_processing_details.role_id = rm.id
left join role_master rmf on application_processing_details.role_fwd_id = rmf.id
where application_id = ? ORDER BY application_processing_details.date_created DESC ";
				$stmt = $connection->prepare($query);
				$params = array($appId);
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = array();
		
				while ($row = $stmt->fetch()) {
					
					$activity_details = array();
					
					$activity_details[] = array('activity' => 'Forward', 'description' => $row['file_note']);
					
					if($row['clarification']) {
						$activity_details[] = array('activity' => 'Clarification', 'description' => $row['clarification_note']);
					}
					if($row['inspection']) {
						$activity_details[] = array('activity' => 'Inspection', 'description' => $row['inspection_note']);
						 
					}
					if($row['inspection_close']) {
						$activity_details[] = array('activity' => 'Inspection Close', 'description' => $row['inspection_close_note']);
						 
					}
					if($row['officer'] == 'SPCB' && $row['officer_fwd'] == 'SPCB'  ) {
						$activity_details[] = array('activity' => 'Clarification Reply', 'description' => $row['clarification_note']);
						
					}
					
					$row['activity_details'] = $activity_details;
					array_push($result , $row);	
					
				 
				}
				
				//echo '<pre>'; print_r($result); die;
				
				$response['status'] = 'ok';
				$response['message'] = 'Data fetched successfully';
				$response['result'] = $result;
				
			} catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = "Error! ".$e->getMessage();
				
			}
		
		} else {
			$response['status'] = 'error';
			$response['message'] = "Application id is required";
		}

		die(json_encode($response));
	}
	
	public function forwardToRoles() {
		
		if(isset($_POST['role_id']) && $_POST['role_id'] != ""){
			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				$role_id = trim($_POST['role_id']);
				$query = "SELECT can_forward_to_id, role_master.role_name  FROM work_flow_master 
							JOIN role_master on role_master.id = can_forward_to_id  where role_id = ? ";
				$stmt = $connection->prepare($query);
				$params = array($role_id);
				
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = array();
		
				while ($row = $stmt->fetch()) {
					array_push($result , $row);	
				}
				
				//echo '<pre>'; print_r($result); die;
				if($result){
					$response['status'] = 'ok';
					$response['message'] = 'Data fetched successfully';
					$response['result'] = $result;	
				}else{
					$response['status'] = 'error';
					$response['message'] = 'No record founds';
				}				
			} catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = "Error! ".$e->getMessage();
				
			}
		
		} else {
			$response['status'] = 'error';
			$response['message'] = "Role id is required";
		}

		die(json_encode($response));
	}
	
	public function inspectionPermission() {
		
		if(isset($_POST['role_id']) && $_POST['role_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				$role_id = trim($_POST['role_id']);

				$stmt = $connection->prepare("select * from role_vs_activity where role_id =  $role_id and activity_id = 8");
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);

				$row = $stmt->fetch();
				//echo '<pre>'; print_r($row); die;
				if(!empty($row['activity_id']) ) {
					
					$response['result'] = '1';
				} else {
					 
					$response['result'] = '0';
				}
				//echo '<pre>'; print_r($result); die;
 
				$response['status'] = 'ok';
				$response['message'] = 'Data fetched successfully';
					
			} catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = "Error! ".$e->getMessage();
				
			}
		
		} else {
			$response['status'] = 'error';
			$response['message'] = "Role id is required";
		}

		die(json_encode($response));
	}
	
	/*
	 * Forwarding the application to another role
	 *
	 * @param  can_forward_to_id, role_id, note, user_id, app_id, role_fwd_name
	 * @return array response
	 */
	
	public function forward() 
	{
		if(isset($_POST['can_forward_to_id']) && $_POST['can_forward_to_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {
				
				$role_fwd_id  = stripslashes(trim($_POST['can_forward_to_id']));
				$role_id  = stripslashes(trim($_POST['role_id']));
				$note = stripslashes(trim($_POST['note']));
				$officer = stripslashes(trim($_POST['user_id']));
				$app_id = stripslashes(trim($_POST['app_id']));
				$role_fwd_name = stripslashes(trim($_POST['role_fwd_name']));
				 
				$appId = $_REQUEST['app_id'];

				$stmt = $connection->prepare("SELECT primary_employee_id  FROM role_profile_assignment where role_id = $role_fwd_id");
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);

				$row = $stmt->fetch();
				if (!count($row)){
					$response['status'] = 'error';
					$response['message'] = "Forwarded user not found";
					die(json_encode($response));
				}
				$officer_fwd = $row['primary_employee_id'];
				
				// Add new row on the application_processing_details table
				$stmt =   $connection->prepare("INSERT INTO application_processing_details (version , application_id, approve, approve_note, clarification, clarification_note, date_created, file_note, inspection, inspection_close, inspection_close_note, inspection_note, last_updated, officer, officer_fwd, reject, reject_note, role_id, role_fwd_id, attached_file, clarification_attached_file, inspection_attached_file, inspection_close_attached_file, clarification_reply_attached_file, return_note, returned, scrutiny_description, scrutiny_status, resubmit_status, approval_status, clarification_days, notice_type,hearing_date) VALUES (0, $app_id, 'f', '', 'f', '', NOW(), '$note', 'f', 'f', '', '', NOW(), '$officer', '$officer_fwd', 'f', '', $role_id, $role_fwd_id, 'f', 'f', 'f', 'f', 'f', '', 'f', '', '', 'f', '', '', '',NOW());");

				// execute the query
				$stmt->execute();
			
				// Updating the application_pending_details
				$update_pending_details = "UPDATE application_pending_details SET pending_with_id = '$officer_fwd', role_id = '$role_fwd_id' WHERE application_id = $app_id";
				$stmtUpdate = $connection->prepare($update_pending_details);

				// execute the query
				$stmtUpdate->execute();
	

				$stmt = $connection->prepare("SELECT employee_first_name, employee_last_name FROM user_profile where id = '$officer_fwd'");
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);

				$row = $stmt->fetch();
				$fwd_officer_name = '';
				if(COUNT($row)) {
					$fwd_officer_name = $row['employee_first_name'].' '.$row['employee_last_name'];
				}
				
				$update_all_summary = "UPDATE all_summary_report SET pending_with = '$officer_fwd', role_id = '$role_fwd_id', role_name = '$role_fwd_name', officer_name = '$fwd_officer_name', pending_since = NOW() WHERE application_id = '$app_id'";
				$stmtUpdate = $connection->prepare($update_all_summary);

				// execute the query
				$stmtUpdate->execute();

				$response['status'] = 'ok';
				$response['message'] = "Application forwarded successfully";


			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}

			
		} else {
			$response['status'] = 'error';
			$response['message'] = "Application not forwarded successfully";
		}

		die(json_encode($response));
		

	} 
	
	/*
	 * Save inspection note
	 *
	 * @param  application_id, inspection_report, pending_with_id, role_id
	 * @return array response
	 */

	public function saveInspection() 
	{
					
		//print_r($_POST);  die;
		$response = array();
		if(isset($_POST['application_id']) && $_POST['application_id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			try {

				$application_id = stripslashes(trim($_POST['application_id']));
				$lat_long = stripslashes(trim($_POST['lat_long']));
				$inspection_report= stripslashes(trim($_POST['inspection_report']));
				$pending_with_id = stripslashes(trim($_POST['pending_with_id']));
				$role_id = stripslashes(trim($_POST['role_id']));
				$app_pending_details_id = stripslashes(trim($_POST['id']));
				//$date_created = stripslashes(trim($_POST['role_id']));

				
				 
				$stmt =   $connection->prepare(" INSERT into inspection_data ( application_id, version, app_pending_details_id, lat_long, inspection_report, pending_with_id, role_id, date_created, image1, image2, image3,image1_url,image2_url,image3_url) VALUES ( :application_id, :version, :app_pending_details_id, :lat_long, :inspection_report, :pending_with_id, :role_id, :date_created, :image1, :image2, :image3,:image1_url,:image2_url,:image3_url)" );		
				//$stmt = $connection->prepare($query);  lo_import('/p ath/to/images/peach.jpg')
                $escaped1 = ''; 
                $escaped2 = ''; 
                $escaped3 = ''; 
                $escaped4 = ''; 
                $image1_url = null; 
                $image2_url = null; 
                $image3_url = null; 
				$inspection_close_attached_file = 'f';
				if (isset($_FILES['image1']) && $_FILES['image1']['size'] > 0) 
				  { 
					  $tmpName  = $_FILES['image1']['tmp_name'];  
					  $inspection_close_attached_file = 't';
					  // upload image
					  $uploaddir = realpath('.').'/uploads/';
					  $image1_url = @date('YdmHis').basename($_FILES['image1']['name']);
					  
					  move_uploaded_file($tmpName, $uploaddir.$image1_url);					

					  $fp = fopen($uploaddir.$image1_url, 'rb'); // read binary
					  //$blob = fopen($filePath, 'rb');
					  $data = file_get_contents( $uploaddir.$image1_url );
				      $escaped1 = pg_escape_bytea($data);
				  } 
       
				if (isset($_FILES['image2']) && $_FILES['image2']['size'] > 0) 
				  { 
						$inspection_close_attached_file = 't';
					  $tmpName  = $_FILES['image2']['tmp_name'];  
					  					  // upload image
					  $uploaddir = realpath('.').'/uploads/';
					  $image2_url = @date('YdmHis').basename($_FILES['image2']['name']);
					  move_uploaded_file($tmpName, $uploaddir.$image2_url);					


					  $fp = fopen($uploaddir.$image2_url, 'rb'); // read binary
					  //$blob = fopen($filePath, 'rb');
					  $data = file_get_contents( $uploaddir.$image2_url );
				      $escaped2 = pg_escape_bytea($data);
				  } 

			     if (isset($_FILES['image3']) && $_FILES['image3']['size'] > 0) 
				  { 
					  $inspection_close_attached_file = 't';
					  $tmpName  = $_FILES['image3']['tmp_name'];  
					  					  // upload image
					  $uploaddir = realpath('.').'/uploads/';
					  $image3_url = @date('YdmHis').basename($_FILES['image3']['name']);
					  move_uploaded_file($tmpName, $uploaddir.$image3_url);					


					  $fp = fopen($uploaddir.$image3_url, 'rb'); // read binary
					  //$blob = fopen($filePath, 'rb');
					  $data = file_get_contents( $uploaddir.$image3_url );
				      $escaped3 = pg_escape_bytea($data);
				  } 
	/*
				   pg_query("INSERT INTO inspection_data (application_id, date_created, pending_with_id, role_id ,image1) VALUES ($application_id, NOW(), '{$pending_with_id}', $role_id, '{$escaped1}')");   die;
				  
				   pg_query("INSERT INTO inspection_data ( application_id, app_pending_details_id, lat_long, inspection_report, pending_with_id, role_id, date_created, image1) VALUES ($application_id, $app_pending_details_id, '{$lat_long}', '{$inspection_report}', '{$pending_with_id}', $role_id, 'NOW()', '{$escaped1}'");  
				   die;
				  */
			     
			    //echo '<pre>';	print_r($escaped); die;	
			  
			  /*
			    $escaped1 = ''; 
                $escaped2 = ''; 
                $escaped3 = '';  */
                $version = 0; 
			  
				$params = array('application_id' => $application_id, 'version' => $version, 'app_pending_details_id' => $app_pending_details_id,'lat_long' => $lat_long, 'inspection_report'=>$inspection_report, 'pending_with_id' =>  $pending_with_id, 'role_id' => $role_id, 'date_created' => 'NOW()', 'image1' => $escaped1, 'image2' => $escaped2, 'image3' => $escaped3,'image1_url'=>$image1_url,'image2_url'=>$image2_url,'image3_url'=>$image3_url);

				$stmt->execute($params);
				/*
				$date = '2016-09-30 16:00:38.007951';
				$img3 = '';
				
				  try
					{
						
						$stmt->bindParam(':application_id', $application_id);
						//$stmt->bindParam(':version', '0');
						$stmt->bindParam(':app_pending_details_id', $app_pending_details_id);
						$stmt->bindParam(':lat_long', $lat_long);
						$stmt->bindParam(':inspection_report', $inspection_report);
						$stmt->bindParam(':pending_with_id', $pending_with_id);
						$stmt->bindParam(':role_id', $role_id);
						$stmt->bindParam(':date_created', $date);
						$stmt->bindParam(':image1', $escaped);
						$stmt->bindParam(':image2', $fp, PDO::PARAM_LOB);
						$stmt->bindParam(':image3', $img3);
						
						//$connection->errorInfo();
						$stmt->execute();
					}
					catch(PDOException $e)
					{
						'Error : ' .$e->getMessage();
					}
					*/
				
				//$stmt->bindParam(':image1', $blob, PDO::PARAM_LOB);
				
				//echo "<pre>";print_r($stmt);exit;
//echo '<pre>';	print_r($fp); die;	
				

				// Updating the application_pending_details
				$update = "UPDATE ind_application_details SET inspection = false WHERE id = $application_id";
				$stmtUpdate = $connection->prepare($update);

				// execute the query
				$stmtUpdate->execute();
				
				$stmt =   $connection->prepare("INSERT INTO application_processing_details (version , application_id, approve, approve_note, clarification, clarification_note, date_created, file_note, inspection, inspection_close, inspection_close_note, inspection_note, last_updated, officer, officer_fwd, reject, reject_note, role_id, role_fwd_id, attached_file, clarification_attached_file, inspection_attached_file, inspection_close_attached_file, clarification_reply_attached_file, return_note, returned, scrutiny_description, scrutiny_status, resubmit_status, approval_status, clarification_days, notice_type,hearing_date) VALUES (0, $application_id, 'f', '', 'f', '', NOW(), '', 'f', 't', '$inspection_report', '', NOW(), '$pending_with_id', '$pending_with_id', 'f', '', $role_id, $role_id, 'f', 'f', 'f', '$inspection_close_attached_file', 'f', '', 'f', '', '', 'f', '', '', '',NOW());");	
				//$stmt =   $connection->prepare($sql);	

				// execute the query
				$stmt->execute();
				
				if(isset($_POST['category_name']))
					$this->saveInspectionReportData($_POST);
				

				$response['status'] = 'ok';
				$response['message'] = "Inspected report submitted successfully";


			}catch (PDOException $e){
				echo "Error: " . $e->getMessage();
			}

			
		} else {
			$response['status'] = 'error';
			$response['message'] = "Inspected report not submitted successfully";
		}

		die(json_encode($response));
		

	}
	
	public function saveInspectionReportData($data) {
		
		$connectionClass = new Connection();
		$connection = $connectionClass->getConnection();
		
				
		$application_id = stripslashes(trim($_POST['application_id']));
		$pending_with_id = stripslashes(trim($_POST['pending_with_id']));
		$role_id = stripslashes(trim($_POST['role_id']));
		$representative_details = stripslashes(trim($_POST['representative_details']));
		$category_name = stripslashes(trim($_POST['category_name']));
		$industry_status = stripslashes(trim($_POST['industry_status']));
		$product_details = stripslashes(trim($_POST['product_details']));
		$row_material = stripslashes(trim($_POST['row_material']));
		$waste_water_eff_quantity = stripslashes(trim($_POST['waste_water_eff_quantity']));
		$source_of_emissions = stripslashes(trim($_POST['source_of_emissions']));
		$pollution_device = stripslashes(trim($_POST['pollution_device']));
		$unauthorised_bypass = stripslashes(trim($_POST['unauthorised_bypass']));
		$point_of_discharge = stripslashes(trim($_POST['point_of_discharge']));
		$sampling_point = stripslashes(trim($_POST['sampling_point']));
		$status_of_effluent = stripslashes(trim($_POST['status_of_effluent']));
		$compliance_of_noc = stripslashes(trim($_POST['compliance_of_noc']));
		$discharge_consent = stripslashes(trim($_POST['discharge_consent']));
		$emission_consent = stripslashes(trim($_POST['emission_consent']));
		$hazardous_waste = stripslashes(trim($_POST['hazardous_waste']));
		$plantation = stripslashes(trim($_POST['plantation']));
		$deficiency_noticed = stripslashes(trim($_POST['deficiency_noticed']));
		$remarks = stripslashes(trim($_POST['remarks']));


		$stmt =   $connection->prepare("INSERT INTO inspection_report_data (application_id, pending_with_id, role_id, representative_details, category_name, industry_status, product_details, row_material, waste_water_eff_quantity, source_of_emissions, pollution_device, unauthorised_bypass, point_of_discharge, sampling_point, status_of_effluent, compliance_of_noc, discharge_consent, emission_consent, hazardous_waste, plantation, deficiency_noticed, remarks, date_created) VALUES ($application_id, '$pending_with_id', $role_id, '$representative_details', '$category_name', '$industry_status', '$product_details', '$row_material', '$waste_water_eff_quantity', '$source_of_emissions', '$pollution_device', '$unauthorised_bypass', '$point_of_discharge', '$sampling_point', '$status_of_effluent', '$compliance_of_noc', '$discharge_consent', '$emission_consent', '$hazardous_waste', '$plantation', '$deficiency_noticed', '$remarks', NOW());");

		// execute the query
		$stmt->execute();
		
	}

	
	public function checkImage() 
	{  
		
		if(isset($_REQUEST['id']) && $_REQUEST['id'] != ""){

			$connectionClass = new Connection();
			$connection = $connectionClass->getConnection();
			
			
			
			$res = pg_query("SELECT encode(image1, 'base64') AS data FROM inspection_data WHERE id=".$_REQUEST['id']);  
			  $raw = pg_fetch_result($res, 'data');
			  //print_r($raw); die;
			  // Convert to binary and send to the browser
			  //echo '<pre>';	print_r($raw); die;	
			  header('Content-type: image/jpeg');
			  echo base64_decode($raw); die;
						

			try {
				
	 
				$query = "SELECT encode(image1, 'base64') AS data FROM inspection_data WHERE id=?";
				
				$stmt = $connection->prepare($query);
				$params = array($_REQUEST['id']);
				$stmt->execute($params);
				$stmt->setFetchMode(PDO::FETCH_ASSOC);				 
				$result = array();

				$row = $stmt->fetch();
//echo '<pre>';	print_r(base64_decode($row['data'])); die;	
 				header('Content-type: image/jpeg');
				echo base64_decode($row['data']);
		 
				 
				 
 
			}catch (PDOException $e){
				$response['status'] = 'error';
				$response['message'] = $e->getMessage();
			}

		} else {
			 
			echo  "Id is required";
		}

	 
	}
	

}

?>
