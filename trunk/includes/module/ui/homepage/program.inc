<?php
drupal_add_css(drupal_get_path('module', 'vals_soc') . '/includes/module/ui/dashboard/css/dashboard.css');
function getTimelineEnabledMessage(){
	
	$period = Timeline::getInstance()->getCurrentPeriod();
	switch ($period) {
		case PROGRAM_NOT_YET_STARTED:
			return 
				'<p style="font-size: 1.2em;">'.
					t("Program starts"). " " . Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a') . 
					'<hr class="small-style">'. 
				'</p>'.
					
				'<p>'.
					t("Organisations and Academic partner admins can login and start to create their profiles from ") .
					Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_ORG_SIGNUP_PERIOD:
			return
				'<p style="font-size: 1.2em;">'.
					t("Program started on"). " " . Timeline::getInstance()->getProgramStartDate()->format('F j, Y, g:i a') .
					'<hr class="small-style">'.
				'</p>'.
					
				'<p>'.
					t("Organisations and Academic partner admins can login and start to create their profile from ") .
					Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case ORG_SIGNUP_PERIOD:
			return
				'<p style="font-size: 1.2em;">'.
					t("Partner signup period starts"). " " . Timeline::getInstance()->getOrgsSignupStartDate()->format('F j, Y, g:i a') .
					'<hr class="small-style">'.
				'</p>'.
						
				'<p>'.
					t("Organisations and Academic partner admins can now login and start to create their profile.") . ' ' 
					.'<br/">'.
					t("You have until the following date to complete your Organisation and Academic partner details.") . ' ' .
					Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_ORGS_ANNOUNCED_PERIOD:
			return
				'<p style="font-size: 1.2em;">'.
					t("Partner signup period ended"). " " . Timeline::getInstance()->getOrgsSignupEndDate()->format('F j, Y, g:i a') .
					'<hr class="small-style">'.
				'</p>'.
						
				'<p>'.
					t("Organisations and Academic partner registration has ended.") . ' ' 
					.'<br/><br/>'.
					t("Partners announced system wide") . ' ' .
					Timeline::getInstance()->getOrgsAnnouncedDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case POST_ORGS_ANNOUNCED_PERIOD:
			
			return
				'<p style="font-size: 1.2em;">'.
					t("Student signup period starts"). " " . Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a') .
					'<hr class="small-style">'.
				'</p>'.
						
				'<p>'.
					t("Organisations and Academic partner registration has ended.") . ' ' 
					.'<br/><br/>'.
					t("Student signup period begins") . ' ' .
					Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case STUDENT_SIGNUP_PERIOD:
			
			return
				'<p style="font-size: 1.2em;">'.
					t("Student signup period"). " " . Timeline::getInstance()->getStudentsSignupStartDate()->format('F j, Y, g:i a') .					
					'<hr class="small-style">'.
				'</p>'.
					
				'<p>'.
					t("Mentors should now invite students into their own student groups by sending them an email containing their groups activation code. ".
					" They can then browse the project ideas.")
					.'<br/>'.
					t("Student signup period ends") . ' ' .
					Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_ORGS_REVIEW_APPLICATIONS_DEADLINE:
			
			return
				'<p style="font-size: 1.2em;">'.
					t("Student signup period ended"). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a') .					
					'<hr class="small-style">'.
				'</p>'.
					
				'<p>'.
					t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Organisations review project applications deadline") . ' ' .
					Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_PROPOSAL_MATCHED_DEADLINE:
			
			return
				'<p style="font-size: 1.2em;">'.
					t("Student signup period ended"). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a') .					
					'<hr class="small-style">'.
				'</p>'.
					
				'<p>'.
					t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Mentors and tutors deadline to match to accepted projects to mentors.") . ' ' .
					Timeline::getInstance()->getOrgsReviewApplicationsDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_STUDENTS_ANNOUNCED_DEADLINE:
			
			return
				'<p style="font-size: 1.2em;">'.
					t("Student signup period ended"). " " . Timeline::getInstance()->getStudentsSignupEndDate()->format('F j, Y, g:i a') .					
					'<hr class="small-style">'.
				'</p>'.
					
				'<p>'.
					t("Students can no longer submit project proposals.").
					'<br/>'.
					t("Community bonding period soon.").
					'<br/>'.
					t("Accepted Students and projects announced on ") . ' ' .
					Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a') .
				'</p>';
			break;
		case PRE_BONDING_PERIOD:
			return
				'<p style="font-size: 1.2em;">'.
					t("Accepted students now announced."). " " . Timeline::getInstance()->getAcceptedStudentsAnnouncedDate()->format('F j, Y, g:i a') .
					'<hr class="small-style">'.
				'</p>'.
				
				'<p>'.
					t("Students can no longer submit project proposals.").
					'<br/>'.
					t("The Bonding period starts on the following date. ") .
					Timeline::getInstance()->getCommunityBondingPeriodStart()->format('F j, Y, g:i a');
				'</p>';
			break;
		case PRE_CODING_PERIOD:
			return
				'<p style="font-size: 1.2em;">'.
					t("Community bonding period.") .
				'</p>'.
					'<hr class="small-style">'.
				'<p>'.
					t("This period is intended to allow all participants to communicate and and ask questions in advance of actually starting the tasks.").
					'<br/>'.
					'<br/>'.
					t(" Coding starts on the following date. ") .
						Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a');
				'</p>';
			break;
		case PRE_SUGGESTED_CODING_END_DATE:
			return
				'<p style="font-size: 1.2em;">'.
					t("Coding period started on") . ' ' . Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a').
				'</p>'.
				'<hr class="small-style">'.
				'<p>'.
					t("The suggested date students finish their work.") . ' ' . Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a').
					'<br/>'.
					'<br/>'.
				 	t("The deadline date for students to finish their work. ") . Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a').
				'</p>';
			break;
		case PRE_CODING_DEADLINE:
			return
				'<p style="font-size: 1.2em;">'.
					t("Coding period started on") . ' ' . Timeline::getInstance()->getCodingStartDate()->format('F j, Y, g:i a').
				'</p>'.
				'<hr class="small-style">'.
				'<p>'.
					t("The suggested date students finish their work has expired.") . ' ' . Timeline::getInstance()->getSuggestedCodingDeadline()->format('F j, Y, g:i a').
					'<br/>'.
					'<br/>'.
				 	t("The deadline date for students to finish their work. ") . Timeline::getInstance()->getCodingEndDate()->format('F j, Y, g:i a').
				'</p>';
			break;
		case OUT_OF_SEASON:
			return
			'<p style="font-size: 1.2em;">'.
				t("The program is currently out of season.") .
			'</p>'
			;
			break;
		case PROGRAM_INACTIVE:
		default:
			return
			'<p style="font-size: 1.2em;">'.
				t("The program is currently inactive.") .
			'</p>'
			;
			break;
	}
}


function renderFrontProgram(){
	echo '
<div class="vals-program block-padding">
	<div class="block-padding-no-border">
		' . getTimelineEnabledMessage() . '
	</div>
</div>
';
}
renderFrontProgram();
