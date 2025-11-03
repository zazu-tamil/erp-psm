<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['logout'] = 'login/logout'; 

$route['change-password'] = 'login/change_password';

$route['academic-year-list'] = 'master/academic_year_list'; 
$route['academic-year-list/(:num)'] = 'master/academic_year_list/$1'; 

$route['adm-enquiry-status-list'] = 'master/adm_enquiry_status_list'; 
$route['adm-enquiry-status-list/(:num)'] = 'master/adm_enquiry_status_list/$1'; 

$route['admission-type-list'] = 'master/admission_type_list'; 
$route['admission-type-list/(:num)'] = 'master/admission_type_list/$1'; 

$route['class-category-list'] = 'master/class_category_list'; 
$route['class-category-list/(:num)'] = 'master/class_category_list/$1';

$route['class-list'] = 'master/class_list'; 
$route['class-list/(:num)'] = 'master/class_list/$1'; 

$route['syllabus-board-list'] = 'master/syllabus_board_list'; 
$route['syllabus-board-list/(:num)'] = 'master/syllabus_board_list/$1'; 

$route['syllabus-type-list'] = 'master/syllabus_type_list'; 
$route['syllabus-type-list/(:num)'] = 'master/syllabus_type_list/$1';  

$route['note-type-list'] = 'master/note_type_list'; 
$route['note-type-list/(:num)'] = 'master/note_type_list/$1';   

$route['exam-type-list'] = 'master/exam_type_list'; 
$route['exam-type-list/(:num)'] = 'master/exam_type_list/$1'; 

$route['exam-sub-type-list'] = 'master/exam_sub_type_list'; 
$route['exam-sub-type-list/(:num)'] = 'master/exam_sub_type_list/$1'; 

$route['assignment-type-list'] = 'master/assignment_type_list'; 
$route['assignment-type-list/(:num)'] = 'master/assignment_type_list/$1';  

$route['assessment-type-list'] = 'master/assessment_type_list'; 
$route['assessment-type-list/(:num)'] = 'master/assessment_type_list/$1';  

$route['policy-type-list'] = 'master/policy_type_list'; 
$route['policy-type-list/(:num)'] = 'master/policy_type_list/$1'; 
 
$route['policy-list'] = 'master/privacy_policy_list'; 
$route['policy-list/(:num)'] = 'master/privacy_policy_list/$1';  

$route['request-type-list'] = 'master/request_type_list'; 
$route['request-type-list/(:num)'] = 'master/request_type_list/$1';  

$route['admission-enquiry-list'] = 'enquiry/admission_enquiry_list'; 
$route['admission-enquiry-list/(:num)'] = 'enquiry/admission_enquiry_list/$1'; 

$route['student-attendance'] = 'student/student_attendance'; 
$route['student-attendance/(:num)'] = 'student/student_attendance/$1'; 

$route['group-attendance'] = 'student/group_attendance'; 
$route['group-attendance/(:num)'] = 'student/group_attendance/$1'; 

$route['class-section-list'] = 'master/class_section_list'; 
$route['class-section-list/(:num)'] = 'master/class_section_list/$1'; 

$route['enquiry-source-list'] = 'master/enquiry_source_list'; 
$route['enquiry-source-list/(:num)'] = 'master/enquiry_source_list/$1'; 

$route['school-branch-list'] = 'master/school_branch_list'; 
$route['school-branch-list/(:num)'] = 'master/school_branch_list/$1'; 

$route['blood-group-list'] = 'master/blood_group_list'; 
$route['blood-group-list/(:num)'] = 'master/blood_group_list/$1';

$route['group-type-list'] = 'master/group_type_list'; 
$route['group-type-list/(:num)'] = 'master/group_type_list/$1';


$route['fee-type-list'] = 'master/fee_type_list';
$route['fee-type-list/(:num)'] = 'master/fee_type_list/$1';


$route['gender-list'] = 'master/gender_list'; 
$route['gender-list/(:num)'] = 'master/gender_list/$1';

$route['promotion-status-list'] = 'master/promotion_status_list'; 
$route['promotion-status-list/(:num)'] = 'master/promotion_status_list/$1';


$route['fee-category-list'] = 'master/fee_category_list'; 
$route['fee-category-list/(:num)'] = 'master/fee_category_list/$1';



$route['checklist-info'] = 'master/checklist_info'; 
$route['checklist-info/(:num)'] = 'master/checklist_info/$1';

$route['checklist-type-info'] = 'master/checklist_type_info'; 
$route['checklist-type-info/(:num)'] = 'master/checklist_type_info/$1';

$route['checklist-group-info'] = 'master/checklist_group_info'; 
$route['checklist-group-info/(:num)'] = 'master/checklist_group_info/$1';


$route['group-list'] = 'group/group_list'; 
$route['group-list/(:num)'] = 'group/group_list/$1'; 


$route['group-info/(:num)'] = 'group/group_info/$1'; 

$route['class-subject-list'] = 'syllabus/class_subject_list'; 
$route['class-subject-list/(:num)'] = 'syllabus/class_subject_list/$1'; 
$route['class-subject-info/(:num)'] = 'syllabus/class_subject_info/$1'; 
$route['syllabus-info/(:num)'] = 'syllabus/syllabus_information/$1'; 

$route['mon-class-subject-list'] = 'syllabus/class_subject_list'; 
$route['mon-class-subject-list/(:num)'] = 'syllabus/class_subject_list/$1'; 

$route['syllabus-import'] = 'syllabus/syllabus_import_xls'; 
$route['class-chart'] = 'syllabus/class_chart'; 

$route['lesson-plan-list'] = 'lessonplan/lesson_plan_list';  
$route['lesson-plan-list/(:num)'] = 'lessonplan/lesson_plan_list/$1';  
$route['lesson-plan-info/(:num)'] = 'lessonplan/lesson_plan_info/$1'; 

$route['mon-lesson-plan-list'] = 'lessonplan/lesson_plan_list';  
$route['mon-lesson-plan-list/(:num)'] = 'lessonplan/lesson_plan_list/$1';

$route['lesson-plan-activity-info/(:num)'] = 'lessonplan/lesson_plan_activity_info/$1';


$route['mon-create-daily-log'] = 'teacherlog/daily_log';  

$route['create-daily-log'] = 'teacherlog/daily_log';  
$route['mon-daily-log-list'] = 'teacherlog/daily_log_list';  
$route['daily-log-list'] = 'teacherlog/daily_log_list';  
$route['edit-daily-log/(:num)'] = 'teacherlog/edit_daily_log/$1';  
$route['mon-edit-daily-log/(:num)'] = 'teacherlog/edit_daily_log/$1';  



$route['daily-progress-info/(:num)'] = 'lessonplan/daily_progress_info/$1';


$route['teacher-lesson-evaluation-list'] = 'lessonplan/teacher_lesson_evaluation_list';  
$route['teacher-lesson-evaluation-list/(:num)'] = 'lessonplan/teacher_lesson_pevaluation_list/$1';  
$route['teacher-lesson-evaluation-info/(:num)'] = 'lessonplan/teacher_lesson_evaluation_info/$1'; 

$route['student-lesson-evaluation-list'] = 'lessonplan/student_lesson_evaluation_list';  
$route['student-lesson-evaluation-list/(:num)'] = 'lessonplan/student_lesson_pevaluation_list/$1';  
$route['student-lesson-evaluation-info/(:num)'] = 'lessonplan/student_lesson_evaluation_info/$1'; 

$route['mon-student-lesson-evaluation-list'] = 'lessonplan/student_lesson_evaluation_list';  
$route['mon-student-lesson-evaluation-list/(:num)'] = 'lessonplan/student_lesson_pevaluation_list/$1'; 

$route['lesson-plan-progress'] = 'lessonplan/lesson_plan_progress';  
$route['lesson-plan-progress-info/(:num)'] = 'lessonplan/lesson_plan_progress_info/$1';  
$route['edit-lesson-plan-progress-info/(:num)'] = 'lessonplan/edit_lesson_plan_progress_info/$1';


$route['add-lesson-plan'] = 'Lessonplan/add_lesson_plan'; 
$route['edit-lesson-plan/(:num)'] = 'Lessonplan/edit_lesson_plan/$1'; 

$route['mon-create-lesson-plan'] = 'Lessonplan/add_lesson_plan'; 
$route['mon-edit-lesson-plan/(:num)'] = 'Lessonplan/edit_lesson_plan/$1'; 


$route['student-information/(:num)'] = 'reports/student_information/$1';  

$route['add-student'] = 'student/add_student'; 
$route['add-student/(:num)'] = 'student/add_student/$1'; 
$route['edit-student/(:num)'] = 'student/edit_student/$1'; 

$route['student-list'] = 'student/student_list'; 
$route['student-list/(:num)'] = 'student/student_list/$1'; 

$route['student-identity-card'] = 'student/student_identity_card'; 
$route['student-identity-card/(:num)'] = 'student/student_identity_card/$1';


$route['student-branch-shuffle'] = 'student/student_branch_shuffle';
$route['student-branch-shuffle'] = 'student/student_branch_shuffle/$1';

$route['student-class-shuffle'] = 'student/student_class_shuffle';
$route['student-class-shuffle'] = 'student/student_class_shuffle/$1';

$route['student-section-shuffle'] = 'student/student_section_shuffle';
$route['student-section-shuffle'] = 'student/student_section_shuffle/$1';

$route['student-promotion'] = 'student/student_promotion';
$route['student-promotion'] = 'student/student_promotion/$1';

$route['student-qr-code-generate'] = 'student/generate_admission_no_qr_code'; 

$route['student/send-credentials'] = 'student/send_credentials'; 
$route['send-credentials'] = 'student/send_credentials'; 

$route['student/dash'] = 'student/dashboard'; 

$route['student/weekly-food-menu'] = 'student/student_canteen_menu_weekly'; 

$route['student_reference'] = 'student/student_reference'; 
$route['student-assignment'] = 'student/student_assignment_assessment'; 
$route['student-reference'] = 'student/student_reference'; 
$route['student-resource'] = 'student/student_resource'; 
$route['student-asseessment'] = 'student/student_asseessment'; 

$route['student/student-report-generate'] = 'reports/student_report_generate'; 
$route['student/student-report-generate/(:num)'] = 'reports/student_report_generate/$1'; 





$route['razor-order'] = 'fee/razor_order';

$route['fee-plan-list'] = 'fee/fee_plan_list';
$route['fee-plan-list/(:num)'] = 'fee/fee_plan_list/$1';

$route['fee-plan-stud-info/(:num)'] = 'fee/fee_plan_stud_info/$1';

$route['fee-receipt/(:num)'] = 'fee/fee_receipt/$1';
$route['fee-upi-id-info'] = 'master/fee_upi_id_info';
$route['fee-upi-id-info/(:num)'] = 'master/fee_upi_id_info/$1';

$route['googlepay'] = 'GooglePay/index';
$route['googlepay/processPayment'] = 'GooglePay/processPayment';


$route['fee-payment-info'] = 'fee/fee_payment_info'; 
$route['fee-payment-info/(:num)'] = 'fee/fee_payment_info/$1'; 

$route['payment-transfer-type-info'] = 'master/payment_transfer_type_info'; 
$route['payment-transfer-type-info/(:num)'] = 'master/payment_transfer_type_info/$1'; 

$route['document-type-list'] = 'master/document_type_list'; 
$route['document-type-list/(:num)'] = 'master/document_type_list/$1';


$route['montessori-area-list'] = 'montessori/montessori_area_list'; 
$route['montessori-area-list/(:num)'] = 'montessori/montessori_area_list/$1';

$route['montessori-area-staff-list'] = 'montessori/montessori_area_staff_list'; 
$route['montessori-area-staff-list/(:num)'] = 'montessori/montessori_area_staff_list/$1';

$route['montessori-syllabus-list'] = 'montessori/montessori_syllabus_list'; 
$route['montessori-syllabus-list/(:num)'] = 'montessori/montessori_syllabus_list/$1';

$route['montessori-syllabus-import-xls'] = 'montessori/montessori_syllabus_import_xls';

$route['montessori-syllabus-info/(:num)'] = 'montessori/montessori_syllabus_info/$1';
$route['montessori-syllabus-assign'] = 'montessori/montessori_syllabus_assign';

$route['montessori-syllabus-progress'] = 'montessori/montessori_syllabus_progress';
$route['montessori-lesson-progress'] = 'montessorilesson/montessori_lesson_progress';
$route['montessori-lesson-plan'] = 'montessorilesson/montessori_lesson_plan';
$route['montessori-lesson-box'] = 'montessorilesson/montessori_lesson_box';

$route['montessori-area-section/(:num)'] = 'montessori/montessori_area_section/$1';
$route['montessori-area-section'] = 'montessori/montessori_area_section';

$route['montessori-progress-status/(:num)'] = 'montessori/montessori_progress_status/$1';
$route['montessori-progress-status'] = 'montessori/montessori_progress_status';

$route['montessori-group-syllabus-assign'] = 'montessori/montessori_group_syllabus_assign';
$route['montessori-group-syllabus-progress'] = 'montessori/montessori_group_syllabus_progress';

$route['montessori-class-syllabus-assign'] = 'montessori/montessori_class_syllabus_assign';
$route['montessori-class-syllabus-progress'] = 'montessori/montessori_class_syllabus_progress';

$route['montessori-area-syllabus'] = 'montessori/montessori_lesson_plan_for_groups';


////old////
$route['department-list'] = 'master/department_list';
$route['department-list/(:num)'] = 'master/department_list/$1';   

$route['emp-category-list'] = 'master/emp_category_list';
$route['emp-category-list/(:num)'] = 'master/emp_category_list/$1';   

$route['designation-list'] = 'master/designation_list';
$route['designation-list/(:num)'] = 'master/designation_list/$1';  

$route['dyn-fld-option-list'] = 'master/dyn_fld_option_list';
$route['dyn-fld-option-list/(:num)'] = 'master/dyn_fld_option_list/$1'; 
 
$route['create-employee'] = 'master/add_employee'; 
$route['edit-employee/(:num)'] = 'master/edit_employee/$1'; 
  
$route['employee-list'] = 'master/employee_list';   
$route['employee-list/(:num)'] = 'master/employee_list/$1';   

$route['holiday-list'] = 'master/holiday_list'; 
$route['holiday-list/(:num)'] = 'master/holiday_list/$1'; 

$route['doc-upload-type-list'] = 'master/doc_upload_type_list'; 
$route['doc-upload-type-list/(:num)'] = 'master/doc_upload_type_list/$1'; 

$route['salary-break-up-list'] = 'master/salary_break_up_list'; 
$route['salary-break-up-list/(:num)'] = 'master/salary_break_up_list/$1'; 

$route['esi-pf-list'] = 'master/esi_pf_list'; 
$route['esi-pf-list/(:num)'] = 'master/esi_pf_list/$1'; 



 
$route['staff-attendance-list'] = 'staff/staff_attendance_list';   
//$route['staff-attendance-list/(:num)'] = 'staff/staff_attendance_list/$1';   

$route['staff-attendance-chart'] = 'staff/staff_attendance_chart';   

$route['attendance-import'] = 'staff/attendance_import';   
$route['attendance-import-xls'] = 'staff/attendance_import_xls';   
$route['staff-salary'] = 'staff/staff_salary';   
$route['staff-calender'] = 'staff/staff_calender';   
$route['print-payslip/(:num)'] = 'staff/print_payslip/$1';    

$route['attendance-calender/(:num)/(:any)'] = 'staff/attendance_calender/$1/$2';   
$route['staff-attendance-calender/(:num)/(:any)'] = 'staff/staff_attendance_calender/$1/$2';   

$route['salary-bank-submission'] = 'staff/salary_bank_submission';   
$route['salary-bank-submission-report'] = 'staff/salary_bank_submission_report';  

$route['print-bank-submission/(:num)'] = 'staff/print_bank_submission/$1';  

$route['calendar'] = 'calendar/calender';  
$route['calendar/load_lesson_tree/(:num)'] = 'calendar/load_lesson_tree/$1';

$route['event-category-list'] = 'calendar/event_category_list'; 
$route['event-sub-category-list'] = 'calendar/event_sub_category_list'; 
$route['event-target-audience-list'] = 'calendar/event_target_audience_list'; 
$route['event-venue-list'] = 'calendar/event_venue_list'; 
$route['event-audience-allowed-list'] = 'calendar/event_audience_allowed_list'; 

$route['parents/calendar'] = 'parents/calender'; 

$route['students/calendar'] = 'student/calender'; 

 
$route['class-wise-student-report'] = 'reports/class_wise_student_report';   
$route['class-subject-staff-report'] = 'reports/class_subject_staff_report'; 
$route['class-subject-staff-mapping-summary'] = 'reports/class_subject_mapping_summary'; 
$route['class-wise-student-checklist-report'] = 'reports/class_wise_student_checklist_report'; 

$route['employee-login-report'] = 'reports/employee_login_report'; 
$route['lesson-plan-log-report'] = 'reports/lesson_plan_log'; 

$route['environment-progress'] = 'reports/environment_progress'; 
$route['group-environment-progress'] = 'reports/group_environment_progress'; 
$route['class-environment-progress'] = 'reports/class_environment_progress'; 
$route['daily-log-report'] = 'reports/log_details_list'; 

$route['student-attendance-report'] = 'reports/student_attendance_report'; 



$route['staff-salary-summary'] = 'reports/staff_salary_summary';   
$route['staff-identity-data'] = 'reports/staff_identity_data';   
$route['staff-profile'] = 'reports/staff_profile';   
$route['staff-information/(:num)'] = 'reports/staff_information/$1'; 

$route['exam-marks-report'] = 'reports/exam_marks_report';


$route['notice-board-list'] = 'master/notice_board_list';
$route['notice-board-list/(:num)'] = 'master/notice_board_list/$1';
   


$route['dash'] = 'dashboard'; 
$route['faculty-dash'] = 'dashboard/faculty_dashboard'; 
$route['mentor-dash'] = 'dashboard/mentor_dashboard'; 
$route['coordinator-dash'] = 'dashboard/coordinator_dashboard'; 
$route['module'] = 'dashboard/module'; 
$route['canteen-dash'] = 'dashboard/canteen_dash'; 
$route['manager-dash'] = 'dashboard/manager_dash'; 
$route['fa-exam-timetable'] = 'faculty/exam_timetable'; 
$route['faculty-calendar'] = 'faculty/faculty_calendar'; 

//$route['parent-dash'] = 'parents/dashboard'; 
$route['parent-dash'] = 'parents/profile'; 
$route['parents/dash'] = 'parents/profile'; 
$route['parents/profile'] = 'parents/profile'; 
$route['parents/leave-request'] = 'parents/leave_request';   
$route['parents/send-leave-request'] = 'parents/send_leave_request'; 
$route['parents/leave-request-history'] = 'parents/leave_request_history'; 

$route['parents/general-request'] = 'parents/general_request'; 
$route['parents/parent-marks-report'] = 'parents/parent_dash_mark_report'; 
$route['parents/send-general-request'] = 'parents/send_general_request'; 
$route['parents/general-request-history'] = 'parents/general_request_history'; 

$route['parents/pickup-request'] = 'parents/pickup_request'; 
$route['parents/send-pickup-request'] = 'parents/send_pickup_request'; 
$route['parents/pickup-request-history'] = 'parents/pickup_request_history'; 

$route['parents/assignment'] = 'parents/parent_dash_assignment'; 
$route['parents/activity'] = 'parents/parent_dash_activity'; 
$route['parents/performance'] = 'parents/parent_dash_performance'; 
$route['parents/policy'] = 'parents/parent_dash_policy'; 
$route['parents/meetings'] = 'parents/parent_dash_activity'; 


$route['parents/class-specific-events'] = 'parents/parent_dash_activity'; 
$route['parents/meetings '] = 'parents/parent_dash_activity'; 

$route['parents/curriculum-overview'] = 'parents/parent_dash_activity'; 
$route['parents/overall-assessment-summary'] = 'parents/parent_dash_activity'; 
$route['parents/continuous-assessment-grades'] = 'parents/parent_dash_activity'; 
$route['parents/mentor-comments'] = 'parents/parent_dash_activity'; 
$route['parents/term-end-reflections'] = 'parents/parent_dash_activity'; 
$route['parents/portfolios'] = 'parents/parent_dash_activity'; 

$route['parents/gallery-of-photos'] = 'parents/parent_dash_activity'; 

$route['parents/recent-notes-from-mentor'] = 'parents/parent_dash_activity'; 
$route['parents/highlights-from-class-sessions'] = 'parents/parent_dash_activity'; 

$route['parents/communication'] = 'parents/parent_dash_activity'; 

$route['parents/fees'] = 'parents/fees'; 

$route['parents/canteen'] = 'Parents/parent_dash_food_menu'; 

$route['parents/transport'] = 'parents/parent_dash_activity'; 
$route['parents/meet-the-teachers'] = 'parents/parent_dash_activity'; 
$route['parents/contact-details'] = 'parents/parent_dash_activity'; 

$route['parents/term-observation'] = 'parents/term_observation';

$route['get-data'] = 'general/get_data';
$route['update-data'] = 'general/update_data';
$route['insert-data'] = 'general/insert_data';
$route['delete-record'] = 'general/delete_record';
$route['get-content'] = 'general/get_content';  
$route['get-calender-data'] = 'general/get_calender_data';  

$route['get-notice'] = 'general/get_notice';


$route['hd-category-list'] = 'helpdesk/hd_category_list'; 
$route['hd-category-list/(:num)'] = 'helpdesk/hd_category_list/$1'; 

$route['ticket-list'] = 'helpdesk/ticket_list'; 
$route['ticket-list/(:num)'] = 'helpdesk/ticket_list/$1';

$route['ticket/(:num)'] = 'helpdesk/ticket_info/$1';


$route['canteen/dining-session'] = 'canteen/ctn_dining_session';
$route['canteen/dining-session/(:num)'] = 'canteen/ctn_dining_session/$1';

$route['canteen/menu-item'] = 'canteen/ctn_menu_item';
$route['canteen/menu-item/(:num)'] = 'canteen/ctn_menu_item/$1';

$route['canteen/uom'] = 'canteen/ctn_uom';
$route['canteen/uom/(:num)'] = 'canteen/ctn_uom/$1';
 

$route['canteen/menu-assignment'] = 'canteen/ctn_menu_assignment';
$route['canteen/menu-assignment/(:num)'] = 'canteen/ctn_menu_assignment/$1';

$route['canteen/weekly-menu-assignment'] = 'canteen/weekly_menu_assignment';
// $route['canteen/get-weekly-menu-assignments'] = 'canteen/get_weekly_menu_assignments';


$route['canteen/campus-category'] = 'canteen/ctn_campus_category';
$route['canteen/campus-category/(:num)'] = 'canteen/ctn_campus_category/$1';



$route['team-profile-list'] = 'master/team_profile_list';
$route['team-profile-list/(:num)'] = 'master/team_profile_list/$1';


$route['team-profile-in-parents'] = 'parents/team_profile_list';
$route['team-profile-in-parents/(:num)'] = 'parents/team_profile_list/$1';


$route['class-timetable'] = 'syllabus/class_timetable';
$route['class-timetable/(:num)'] = 'syllabus/class_timetable/$1';  


$route['class-timetable-view'] = 'syllabus/class_timetable_view';
$route['class-timetable-view/(:num)'] = 'syllabus/class_timetable_view/$1';  


$route['faculty-timetable'] = 'teacherlog/class_timetable';
$route['faculty-timetable/(:num)'] = 'teacherlog/class_timetable/$1'; 


$route['class-timetable-report'] = 'reports/class_timetable_report';
$route['observation-report'] = 'reports/observation_report';


$route['class-exam-observation'] = 'reports/class_exam_observation';  

$route['exam-observation-list'] = 'reports/exam_observation_list';


$route['term-exam-observation'] = 'reports/term_exam_observation';

$route['term-exam-observation-list'] = 'reports/term_exam_observation_list';
$route['print-student-observation/(:num)'] = 'reports/print_student_observation/$1';


$route['mentor-class-snippet'] = 'teacherlog/mentor_class_snippet';

$route['term-mentor-class-snippet'] = 'teacherlog/term_mentor_class_snippet'; 


$route['assignment-report'] = 'reports/assignment_report';

$route['assessment-report'] = 'reports/assessment_report';

$route['living-childhood-list'] = 'master/event_registration_list'; 
$route['living-childhood-list/(:any)'] = 'master/event_registration_list/$1'; 
$route['living-childhood-list/print/(:any)'] = 'master/event_registration_list_print/$1';

$route['add-event'] = 'event/add_event';

$route['chat'] = 'chat';
$route['chat/fetch_messages/(:num)'] = 'chat/fetch_messages/$1';
$route['chat/send_message'] = 'chat/send_message';
$route['chat/fetch_group_messages/(:num)'] = 'chat/fetch_group_messages/$1';
$route['chat/send_group_message'] = 'chat/send_group_message';
$route['chat/search_contacts_groups'] = 'chat/search_contacts_groups';