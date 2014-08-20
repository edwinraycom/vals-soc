<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');
function getTimelineEnabledMessage(){
	$return_args = array();
	$period = Timeline::getInstance()->getCurrentPeriod();
	switch ($period) {
		case PROGRAM_NOT_YET_STARTED:
			return 

			$return_args[0] = t("Program starts"). ": " . Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Organisations and Academic partner admins can login and start to create their profiles from: ") .
					Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a');
			return $return_args;

			break;
		case PRE_ORG_SIGNUP_PERIOD:

			$return_args[0] = t("Program started on"). " " . Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a');
			$return_args[1] =t("Organisations and Academic partner admins can login and start to create their profile from: ") .
					Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a');
			return $return_args;

			break;
		case ORG_SIGNUP_PERIOD:

			$return_args[0] = t("Partner signup period starts"). " " . Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Organisations and Academic partner admins can now login and start to create their profile.") . ' ' 
					.'<br/">'.
					t("You have until the following date to complete your Organisation and Academic partner details: ") . ' ' .
					Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a');
			return $return_args;

			break;
		case PRE_ORGS_ANNOUNCED_PERIOD:
			
			$return_args[0] = t("Partner signup ended"). " " . Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Organisations and Academic partner registration has ended.") . ' ' 
					.'<br/><br/>'.
					t("Partners announced system wide: ") . ' ' .
					Timeline::getInstance()->getOrgsAnnouncedDate()->format('F j, Y, g:i a');
			return $return_args;

			break;
		case POST_ORGS_ANNOUNCED_PERIOD:

			$return_args[0] = t("Student signup period starts: "). " " . Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Organisations and Academic partner registration has ended.") . ' ' 
					.'<br/><br/>'.
					t("Student signup period begins: ") . ' ' .
					Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			
			break;
		case STUDENT_SIGNUP_PERIOD:

			$return_args[0] = t("Student signup period"). " " . Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Mentors should now invite students into their own student groups by sending them an email containing their groups activation code. ".
					" They can then browse the project ideas.")
					.'<br/>'.
					t("Student signup period ends: ") . ' ' .
					Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a');

			break;
		case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:

			$return_args[0] = t("Student signup period ended"). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Organisations review project applications deadline: ") . ' ' .
					Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');

			break;
		case PRE_PROPOSAL_MATCHED_DEADLINE:

			$return_args[0] = t("Student signup period ended"). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Mentors and tutors deadline to match accepted projects to mentors: ") . ' ' .
					//Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a');
					Timeline::getInstance()->getStudentsMatchedToMentorsDate()->format('F j, Y, g:i a');

			break;
		case PRE_STUDENTS_ANNOUNCED_DEADLINE:

			$return_args[0] = t("Student signup period ended: "). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Community bonding period soon.").
					'<br/>'.
					t("Accepted Students and projects announced on: ") . ' ' .
					Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');

			break;
		case PRE_BONDING_PERIOD:
			return

			$return_args[0] = t("Accepted students now announced."). " " . Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a');
			$return_args[1] = t("Students can no longer submit project proposals.") .
					'<br/>'.
					t("The Bonding period starts on the following date: ") .
					Timeline::getInstance()->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');

			break;
		case PRE_CODING_PERIOD:

			$return_args[0] = t("Community bonding period.");
			$return_args[1] = t("This period is intended to allow all participants to communicate and and ask questions in advance of actually starting the tasks.").
					'<br/>'.
					'<br/>'.
					t(" Coding starts on the following date: ") .
						Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');

			break;
		case PRE_SUGGESTED_CODING_END_DATE:
			return

			$return_args[0] = t("Coding period started on") . ' ' . Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("The suggested date students finish their work.") . ' ' . Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a').
					'<br/>'.
					'<br/>'.
				 	t("The deadline date for students to finish their work: ") . Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a');

			break;
		case PRE_CODING_DEADLINE:

			$return_args[0] = t("Coding period started on") . ' ' . Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
			$return_args[1] = t("The suggested date students finish their work has expired.") . ' ' . Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a').
					'<br/>'.
					'<br/>'.
				 	t("The deadline date for students to finish their work: ") . Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a');

			break;
		case OUT_OF_SEASON:

			$return_args[0] = t("Out of season");
			$return_args[1] = t("The program is currently out of season.");

			break;
		case PROGRAM_INACTIVE:
		default:

			$return_args[0] = t("Program Inactive");
			$return_args[1] = t("The program is currently inactive.");

			break;
	}
}


function renderFrontProgram(){
	$result = getTimelineEnabledMessage();
	echo '
		<div class="vals-program block-padding">
			<div class="block">
				<h4>'.$result[0].'</h4>
					<div class="block-padding-no-border">
						<p>'.$result[1].'</p>
					</div>
			</div>
		</div>
';
}

renderFrontProgram();