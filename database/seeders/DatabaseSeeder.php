<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      $this->call([
        // rolesSeeder::class,
        // sm_base_groupsSeeder::class,
        // sm_base_setupsSeeder::class,
        // usersSeeder::class,

        sm_classesSeeder::class,
        sm_sectionsSeeder::class,
        sm_class_sectionsSeeder::class,
        sm_subjectsSeeder::class,
        sm_assign_class_teachersSeeder::class,
        sm_class_teachersSeeder::class, /*it's dependent sm_assign_class_teachersSeeder */
        sm_visitorsSeeder::class,
        continentsSeeder::class,/*check*/
        // countriesSeeder::class,
        // languagesSeeder::class,
        // sm_about_pagesSeeder::class,
        // sm_academic_yearsSeeder::class,
        // sm_date_formatsSeeder::class,

        sm_designationsSeeder::class,
        sm_human_departmentsSeeder::class,
        sm_staffsSeeder::class,
        sm_staff_attendencesSeeder::class,
        sm_hr_payroll_generatesSeeder::class,

        sm_expense_headsSeeder::class,
        // sm_payment_methhodsSeeder::class,
        sm_add_expensesSeeder::class,
        sm_income_headsSeeder::class,
        sm_add_incomesSeeder::class,
        sm_bank_accountsSeeder::class,

        sm_admission_queriesSeeder::class,
        sm_admission_query_followupsSeeder::class,

        sm_assign_subjectsSeeder::class,



        sm_vehiclesSeeder::class,
        sm_routesSeeder::class,
        sm_assign_vehiclesSeeder::class,

        sm_background_settingsSeeder::class,
        sm_book_categoriesSeeder::class,
        sm_booksSeeder::class,
        sm_book_issuesSeeder::class,

        sm_chart_of_accountsSeeder::class,
        sm_class_roomsSeeder::class,
        sm_class_routine_updatesSeeder::class,


        sm_class_routinesSeeder::class,
        sm_class_timesSeeder::class,
        sm_complaintsSeeder::class,
        sm_contact_messagesSeeder::class,



        sm_contact_pagesSeeder::class,
        sm_content_typesSeeder::class,
        sm_countriesSeeder::class,
        sm_coursesSeeder::class,
        sm_currenciesSeeder::class,
        sm_custom_linksSeeder::class,
        sm_dashboard_settingsSeeder::class,
        sm_email_settingsSeeder::class,
        sm_email_sms_logsSeeder::class,

        sm_dormitory_listsSeeder::class,
        sm_room_typesSeeder::class,
        sm_room_listsSeeder::class,


        sm_sessionsSeeder::class,
        sm_student_categoriesSeeder::class,
        sm_studentsSeeder::class,

        sm_exam_typesSeeder::class,
        sm_exam_setupsSeeder::class,
        sm_examsSeeder::class,
        sm_exam_schedulesSeeder::class,
        sm_exam_schedule_subjectsSeeder::class,

        sm_fees_groupsSeeder::class,
        sm_fees_typesSeeder::class,
        sm_fees_mastersSeeder::class,
        sm_fees_discountsSeeder::class,
        sm_fees_assign_discountsSeeder::class,
        sm_fees_assignsSeeder::class,
        sm_fees_paymentsSeeder::class,
        sm_fees_carry_forwardsSeeder::class,
        
        sm_exam_marks_registersSeeder::class,

        sm_frontend_persmissionsSeeder::class,
        sm_holidaysSeeder::class,
        sm_homework_studentsSeeder::class,
        sm_homeworksSeeder::class,
        sm_eventsSeeder::class,
        sm_exam_attendance_childrenSeeder::class,
        sm_exam_attendancesSeeder::class,
        sm_hourly_ratesSeeder::class,

        sm_hr_payroll_earn_deducsSeeder::class,
        sm_hr_salary_templatesSeeder::class,
        sm_instructionsSeeder::class,
        sm_inventory_paymentsSeeder::class,
        
        //*************** End ***********************

        sm_item_categoriesSeeder::class,
        sm_item_issuesSeeder::class,
        sm_item_receive_childrenSeeder::class,
        sm_item_receivesSeeder::class,
        sm_item_sell_childrenSeeder::class,
        sm_item_sellsSeeder::class,
        sm_item_storesSeeder::class,
        sm_itemsSeeder::class,
        sm_language_phrasesSeeder::class,
        sm_languagesSeeder::class,


        sm_leave_typesSeeder::class,
        sm_leave_definesSeeder::class,
        sm_leave_requestsSeeder::class,

        sm_lessonSeeder::class,
        sm_lesson_topicSeeder::class,


        // sm_library_membersSeeder::class,
        sm_mark_storesSeeder::class,
        sm_marks_gradesSeeder::class,
        sm_marks_register_childrenSeeder::class,
        sm_marks_registersSeeder::class,
        sm_marks_send_smsSeeder::class,
        sm_module_linksSeeder::class,
        sm_modulesSeeder::class,
        sm_newsSeeder::class,
        sm_news_categoriesSeeder::class,
        sm_notice_boardsSeeder::class,
        sm_notificationsSeeder::class,

        sm_question_groupsSeeder::class,
        sm_question_levelsSeeder::class,
        sm_question_banksSeeder::class,

        sm_online_examsSeeder::class,
        sm_online_exam_questionsSeeder::class,
        sm_online_exam_question_assignsSeeder::class,
        sm_online_exam_marksSeeder::class,

        sm_parentsSeeder::class,
        sm_payment_gateway_settingsSeeder::class,
        sm_postal_dispatchesSeeder::class,
        sm_postal_receivesSeeder::class,
        sm_product_purchasesSeeder::class,


        sm_result_storesSeeder::class,
        sm_role_permissionsSeeder::class,
        sm_seat_plan_childrenSeeder::class,
        sm_seat_plansSeeder::class,
        sm_send_messagesSeeder::class,
        sm_setup_adminsSeeder::class,
        sm_sms_gatewaysSeeder::class,

        sm_student_attendancesSeeder::class,
        sm_student_certificatesSeeder::class,
        sm_student_documentsSeeder::class,
        sm_student_excel_formatsSeeder::class,
        sm_student_groupsSeeder::class,
        sm_student_homeworksSeeder::class,
        sm_student_id_cardsSeeder::class,
        sm_student_promotionsSeeder::class,
        sm_student_take_online_exam_questionsSeeder::class,
        sm_student_take_online_examsSeeder::class,
        sm_student_take_onln_ex_ques_optionsSeeder::class,
        sm_student_timelinesSeeder::class,
        sm_suppliersSeeder::class,
        sm_system_versionsSeeder::class,
        sm_teacher_upload_contentsSeeder::class,
        sm_temporary_meritlistSeeder::class,
        sm_temporary_meritlistsSeeder::class,
        sm_testimonialsSeeder::class,
        sm_to_dosSeeder::class,
        sm_upload_contentsSeeder::class,
        // sm_user_logsSeeder::class,
        sm_optional_subject_assign::class,
        SmSubjectAttendanceSeeder::class,
        // SmSchoolSeeder::class, 
      ]);
        
    }
}