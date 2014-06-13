<?php
include('include.php');//Includes the necessary bootstrapping and the ajax functions
module_load_include('php', 'vals_soc', 'includes/classes/Groups');
module_load_include('php', 'vals_soc', 'includes/classes/Proposal');
module_load_include('php', 'vals_soc', 'includes/classes/Users');

$apply_proposals = vals_soc_access_check('dashboard/projects/apply') ? 1 : 0;
$browse_proposals = vals_soc_access_check('dashboard/proposals/browse') ? 1 : 0;
$is_student = (Users::isOfType('student'));

switch ($_GET['action']){
	case 'proposal_page':
		//module_load_include('php', 'vals_soc', 'includes/classes/Organisations');
		module_load_include('php', 'vals_soc', 'includes/functions/proposals');
		initBrowseProposalsLayout();
	break;
	case 'list_proposals':
		try{
			$student=null;
			if(isset($_POST['student']) && $_POST['student']){
				$student = $_POST['student'];
				
				if ($is_student && $student != Users::getMyId()){
					throw new Exception((t('You can only see your own proposals!')));
				}
			} else {
				if ($is_student){
					throw new Exception((t('You can only see your own proposals!')));
				}
			}
			$institute=null;
			if(isset($_POST['institute']) && $_POST['institute']){
				$institute = $_POST['institute'];
			}
			$organisation=null;
			if(isset($_POST['organisation']) && $_POST['organisation']){
				$organisation = $_POST['organisation'];
			}
			//Return result to jTable
			$cnt = Proposal::getInstance()->getProposalsRowCountBySearchCriteria(
					$student, $institute, $organisation);
			$recs = $cnt ? 
						Proposal::getInstance()->getProposalsBySearchCriteria(
							$student, $institute, $organisation, $_GET["jtSorting"], $_GET["jtStartIndex"],
							$_GET["jtPageSize"]) :
						array();
			
			jsonGoodResultJT($recs, $cnt);
		}
		catch(Exception $ex){
			//Return error message
			jsonBadResultJT($ex->getMessage());
		}
	break;
	case 'proposal_detail':
		$proposal_id=null;
		if(isset($_GET['proposal_id']) && $_GET['proposal_id']){
			$proposal_id = $_GET['proposal_id'];
			if (! ($browse_proposals || ($x = Groups::isOwner('proposal', $proposal_id)) )){
				jsonBadResult(t('You can only see your own proposals!'). " want $browse_proposals en $x myid".Users::getMyId());
			} else {
				include(_VALS_SOC_ROOT.'/includes/classes/Organisations.php');
				include(_VALS_SOC_ROOT.'/includes/classes/Institutes.php');
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				jsonGoodResult($proposal);
			}
		} else{
			jsonBadResult(t('No proposal identifier submitted!'));
		}
	break;
	case 'proposal_edit':
		$proposal_id=null;
		if(isset($_POST['proposal_id']) && $_POST['proposal_id']){
			$proposal_id = $_POST['proposal_id'];
			if (! ($browse_proposals|| Groups::isOwner('proposal', $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$target = altSubValue($_POST, 'target');
				include(_VALS_SOC_ROOT.'/includes/classes/Organisations.php');
				include(_VALS_SOC_ROOT.'/includes/classes/Institutes.php');
				$proposal = Proposal::getInstance()->getProposalById($proposal_id, true);
				
				$f = drupal_get_form('vals_soc_proposal_form', $proposal, $target);
				
				if ($f){
					jsonGoodResult(drupal_render($f));
				} else {
					jsonBadResult();
				}
				/* Something like this
				 * $form = drupal_get_form("vals_soc_${type}_form", null, $target);
				$form['#action'] = url('administer/members');
				// Process the submit button which uses ajax
				$form['submit'] = ajax_pre_render_element($form['submit']);
				// Build renderable array
				$build = array(
					'form' => $form,
					'#attached' => $form['submit']['#attached'], // This will attach all needed JS behaviors onto the page
				);
				// Print $form
				print drupal_render($build);
				// Print JS
				print drupal_get_js();
				 * 
				 */
			}
		} else{
			jsonBadResult(t('No proposal identifier submitted!'));
		}
	break;
	case 'proposal_delete':
		$is_admin = !$is_student && (Users::isOfType('admin'));
		$proposal_id=null;
		if(isset($_POST['proposal_id']) && $_POST['proposal_id']){
			$proposal_id = $_POST['proposal_id'];
			if (! ($is_admin || Groups::isOwner('proposal', $proposal_id) )){
				jsonBadResult(t('You can only see your own proposals!'));
			} else {
				$num_deleted = db_delete(tableName('proposal'))
					->condition(AnstractEntity::keyField('proposal'), $proposal_id)
					->execute();
				if ($num_deleted){
					jsonGoodResult('OK', t("You have removed this proposal"));
				} else {
					jsonBadResult(t('We could not remove your proposal'));
				}
			}
		} else{
			jsonBadResult(t('No proposal identifier submitted!'));
		}
	break;
	
	default: echo "No such action: ".$_GET['action'];
}