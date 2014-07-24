<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
// module_load_include('php', 'vals_soc', 'includes/classes/AbstractEntity');
// module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Institutes');
switch ($_GET['action']){
	case 'list':
		try{
			$instName=null;
			$inst_id = getRequestVar('instid', null);
			if(isset($_POST['iname'])){
				$instName = $_POST['iname'];
			}

			//Return result to jTable
			$jTableResult = array();
			$jTableResult['Result'] = "OK";
			if ($org_id){
				$institutions = Institutes::getInstance()->getInstituteById($org_id);
				$jTableResult['TotalRecordCount'] = count($institutions);
				$jTableResult['Records'] = $institutions;
				
			} else {
				$jTableResult['TotalRecordCount'] = Institutes::getInstance()->getInstitutesRowCountBySearchCriteria($instName);
				$jTableResult['Records'] = Institutes::getInstance()->getInstitutesBySearchCriteria($instName,
						 $_GET["jtSorting"], $_GET["jtStartIndex"], $_GET["jtPageSize"]);
			}
			print json_encode($jTableResult);
		}
		catch(Exception $ex){
			//Return error message
			$jTableResult = array();
			$jTableResult['Result'] = "ERROR";
			$jTableResult['Message'] = $ex->getMessage();
			print json_encode($jTableResult);
		}
	break;
	case 'institution_detail':
		if(isset($_GET['instid'])){
			try {
				$institutions = Institutes::getInstance()->getInstituteById($_GET['instid']);
				echo ($institutions ? jsonGoodResult($institutions[0]) : jsonBadResult(t('Could not find the institution')));
			} catch (Exception $e){
				echo jsonBadResult($e->getMessage());
			}
		}
		else{
			echo jsonBadResult( t("No institution identifier submitted!"));
		}
	break;
	default: echo "No such action: ".$_GET['action'];
}