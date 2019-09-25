<?php

/******************************************************************************
 * #                         BookingWizz v5.5
 * #******************************************************************************
 * #      Author:     Convergine (http://www.convergine.com)
 * #      Website:    http://www.convergine.com
 * #      Support:    http://support.convergine.com
 * #      Version:    5.5
 * #
 * #      Copyright:   (c) 2009 - 2014  Convergine.com
 * #
 * #******************************************************************************/

/******************************************************************************
      BOOKING SYSTEM INTERFACE TEXT STRINGS (BOTH FRONT + ADMIN)
 ******************************************************************************/
define("January", "January");
define("February", "February");
define("March", "March");
define("April", "April");
define("May", "May");
define("June", "June");
define("July", "July");
define("August", "August");
define("September", "September");
define("October", "October");
define("November", "November");
define("December", "December");

define("Jan", "January");
define("Feb", "February");
define("Mar", "March");
define("Apr", "April");
define("Jun", "June");
define("Jul", "July");
define("Aug", "August");
define("Sep", "September");
define("Oct", "October");
define("Nov", "November");
define("Dec", "December");

define("Mon", "Mon");
define("Tue", "Tue");
define("Wed", "Wed");
define("Thu", "Thu");
define("Fri", "Fri");
define("Sat", "Sat");
define("Sun", "Sun");

define("Monday", "Monday");
define("Tuesday", "Tuesday");
define("Wednesday", "Wednesday");
define("Thursday", "Thursday");
define("Friday", "Friday");
define("Saturday", "Saturday");
define("Sunday", "Sunday");

#index_small.php
define("SML_PREV", "Prev");
define("SML_NEXT", "Next");
define("SML_D0", "Su");
define("SML_D1", "Mo");
define("SML_D2", "Tu");
define("SML_D3", "We");
define("SML_D4", "Th");
define("SML_D5", "Fr");
define("SML_D6", "Sa");
define("EVENTS_LIST_TITLE", "Events for");

# admin navigation items
define("MENU1", "Schedule");
define("MENU1_1", "Single Day Service");
define("MENU1_2", "Multi Day Service");
define("MENU1_3", "Events");
define("MENU2", "Website Bookings");
define("MENU2_1", "Website Bookings");
define("MENU2_2", "Manual Bookings");
define("MENU2_3", "Add Booking");
define("MENU2_4", "Export Attendees");
define("MENU3", "SERVICES");
define("MENU3_1", "Add Service");
define("MENU4", "Events");
define('MENU4_0', 'List of Events');
define("MENU4_1", "Add Event");
define("MENU5", "Coupons");
define("MENU5_1", "Add Coupons");
define("MENU6", "Reports");
define("MENU6_1", "Events Reports");
define("MENU6_2", "Appointments Reports");
define("MENU6_3", "Multi-Day Bookings Reports");
define("MENU7", "Add Manual Booking");
define("MENU7_1", "Add Manual Day Booking");
define("MENU8", "Bookings List");
define("MENU9", "Services");
define("MENU10", "Services List");
define("MENU11", "Add Service");
define("MENU11_1", "Add Multi-Day Service");
define("MENU14", "My Coupons");


define("REQUIRED_DEPOSIT", "Required Deposit");


# booking-nojs.php
# General
define("APPLICATION_TITLE", "BookingWizz Admin"); //(used multiple times)
define("GENERIC_QUERY_FAIL", "Oopsy,error occurred when trying to execute query.");
define("BOOKING_FRM_CONFIRMED", "Confirmed"); //(used multiple times)
define("BOOKING_FRM_NOTCONFIRMED", "Not Confirmed"); //(used multiple times)
define("BOOKING_FRM_USERCANCELLED", "User Cancelled"); //(used multiple times)
define("BOOKING_FRM_CANCELLED", "Cancelled"); //(used multiple times)
define("BOOKING_FRM_PAID", "Paid"); //(used multiple times)
define("NO_ACCESS", "You don't have access for this page"); //(used multiple times)

# Emails which are sent from admin side
define("EMAIL_SUBJ_CONFIRMED", "Booking Confirmed!");
define("EMAIL_SUBJ_CANCELLED", "Booking Cancelled!");
define("ADM_MSG1", "Booking confirmation email sent to customer!");
define("ADM_MSG2", "Booking cancellation email sent to customer!");

# admin-index.php
define("ADMIN_WELCOME", "Welcome to your BookingWizz back office");
define("ADMIN_WELCOME_TEXT", "Please use menu to confirm/delete/add bookings.<br />You also should go to settings and set default days/times when booking is available.<br />Use &quot;Add Manual Booking&quot; to add manual booking into database or if you want to reserve some time for some date manually.");
define("QUICK_LINKS", "QUICK LINKS");
define("BASIC_STATS", "BASIC STATS");
define("LASTED_BOOKINGS", "LATEST BOOKINGS");
define("BASIC_STATS_DESCR", "These basic stats help you to keep track of received bookings. One quick look at this report tells you your booking trends and how many bookings you’ve got at a certain day.");

# admin.php
define("ADMIN_LOG", "Welcome to Your BookingWizz Back Office");
define("LOGIN_USERNAME", "Username:");
define("LOGIN_PASSWORD", "Password:");
define("LOGIN_FORGOT", "Forgot Password?");
define("LOGIN_ERROR1", "Empty username and/or password.");
define("LOGIN_ERROR2", "Wrong username and/or password");
define("ADM_BTN_LOGIN", "Login");
define("ADM_BTN_FORGOT", "Reset Password");

# bs-bookings-edit.php
define("BOOKING_SUCC", "Booking was successfully updated!");
define("BOOKING_DEL_ITEM", "Booking time item has been deleted!");
define("BOOKING_TIME_UPDATED", "Booking time was updated!");
define("BOOKING_TIME_DELETED", "Booking time was deleted!");
define("TBL_DATE", "Date");
define("TBL_TIME1", "Time From");
define("TBL_TIME2", "Time To");
define("TBL_QTY", "Qty: "); //(used multiple times)
define("BOOKING_EDIT_TITLE", "Edit Website Booking");
define("BOOKING_FRM_DATE", "Date booking was placed:");
define("BOOKING_FRM_SERVICE", "Service"); //(used multiple times)
define("BOOKING_FRM_STATUS", "Status"); //(used multiple times)
define("BOOKING_FRM_SELECT", "Please Select");
define("BOOKING_FRM_QTY", "Qty "); //(used multiple times)
define("BOOKING_FRM_SUBTOTAL", "Subtotal "); //(used multiple times)
define("BOOKING_FRM_TAX", "Tax "); //(used multiple times)
define("BOOKING_FRM_TOTAL", "Total "); //(used multiple times)
define("BOOKING_FRM_NAME", "Service Title"); // (used multiple times)
define("BOOKING_FRM_TYPE", "Service Type"); // (used multiple times)
define("BOOKING_FRM_EMAIL", "E-mail"); //(used multiple times)
define("BOOKING_FRM_PHONE", "Phone"); //(used multiple times)
define("BOOKING_FRM_COMMENTS", "Comments"); //(used multiple times)
define("BOOKINGR_FRM_DATE", "Date to book"); //(used recurring booking)
define("BOOKING_FRM_BOOKEDDATES", "Booked Dates:");
define("BOOKING_FRM_NOTE1", "Please note: to delete 1 period from booked time you need to leave empty both fields &quot;FROM&quot; and &quot;TO&quot;");


# bs-bookings.php
define("ADM_MSG3", "Selected bookings were deleted.");
define("ADM_MSG4", "0 bookings found in database");
define("ADM_BTN_DELETE", "Delete Selected");
define("ADM_BTN_SUBMIT", "Save");
define("PAGE_TITLE1", "Website Bookings");
define("BOOKING_LST_NAME", "Name");
define("BOOKING_LST_EVENT", "Event");
define("BOOKING_LST_EMAIL", "Email");
define("BOOKING_LST_PHONE", "Phone");
define("BOOKING_LST_ON", "Booked On");
define("BOOKING_LST_DATES", "Dates");
define("BOOKING_LST_SPACES", "Spots"); //(used multiple times)
define("BOOKING_LST_STATUS", "Status");
define("BOOKING_LST_COUPON", "Coupon");
define("BOOKING_LST_YOUNG", "Young");
# bs-bookings_event-edit.php
define("PAGE_TITLE_EDIT", "Edit Website Event Booking");
define("PAGE_TITLE_ADD", "Add Website Event Booking");
define("BOOKING_LST_EVENTS", "Events");
define("DATE_BOOK_PLC", "Date booking was placed:");
define("DAYS_LIST", "&laquo; Back To Bookings List");


# bs-bookings_day-edit.php
define("PAGE_TITLE_DAY_BOOK", "Edit Website Multi-Day Booking");
define("DAY_FROM", "Booking Start Date");
define("DAY_TO", "Booking End Date");
define("TOTAL_DAYS", "Total Days");

# bs-events-add.php
define("BS_EVENT_ADD_TXT", "\"Event\" applies to an entity such as show, concerts, conference and so on. \"Event\" must be associated with a \"Service\". Initial \"Service\" setup is required to create an \"Event\" calendar. Add \"Event\", specify its parameters such as location and map, image, start and end dates, price, spaces, requiring options and have it ready for booking.");
define("ADM_MSG5", "Booking was successfully added!");
define("ADM_MSG6", "Some required fields are empty!");
define("EVENT_SUC_MSG", "Event was successfully updated!");
define("EVENT_SUC_UPD", "Event was successfully created!");
define("EVENT_STR_TIME", "Event start time can't equal or be higher than the event end time!");
define("EVENT_END_RECURING", "End date for recurring event not selected or lower than the minimum interval");
define("STAT_UPDT", "Statuses updated!");
define("NTFC_ESENT", "Notification emails were sent.");
define("SEL_ATT_DEL", "Selected attendee was deleted.");
define("NO_FOUND", "0 attendees found in database");
define("EVENT_DISCRP", "Event Description");
define("IMGJPG", "Note: images aren't adjusted during upload. Image must be JPG only");
define("CRNT_EV_IMG", "Current Event Image");
define("DEL_IMG", "Delete Image");
define("EVENT_ST_DATE", "Event Start Date"); //(used multiple times)
define("EVENT_END_RECURR", "Recurring period end date"); //(used multiple times)
define("EVENT_NOTE_OVER", "please note: event will override any existing bookings on that day");
define("EVENT_ENDDATE", "Event End Date");
define("MAX_SPACE", "Maximum Spaces");
define("NUMB_PLZ", "numbers only,eg 28");
define("PAYMT", "Payment");
define("REC", "Required");
define("NOTREC", "Not Required");
define("PAY_METD", "Payment Method"); //(used multiple times)
define("PAYPAL_GTW", "PayPal Gateway");
define("OFFL_INVC", "Offline Invoice");
define("OFFL_INVC_MSG", "If offline invoice selected - reservation will be confirmed automatically,it is your responsibility to collect payment from customer");
define("PRICE", "Price");
define("TCT_QNTT", "Ticket Quantity");
define("MLTP_TCT_CSTM", "Multiple tickets per customer");
define("ONE_TCT_CSTM", "1 ticket per customer");
define("MXM_TCT", "Maximum tickets");
define("MXM_COUPON", "Allow Coupons");
define("TCT_MSG", "If multiple tickets per customer (on the left) option was selected - customers will be able to purchase multiple tickets at a time, up to maximum number of tickets entered in this field.");
define("ADD_MNL_BOOK", "Add manual booking");
define("ALL_BOOKED", "All spaces are booked");
define("DATE_SUBSCR", "Date Subscribed");
define("DATE_TO_BOOK", "Book Date");
define("EVENT_TTL", "Event Title"); //(used multiple times)
define("EVENT_ID", "ID"); //(used multiple times)
define("ALLFIELDSREQ", "All fields are required!"); //(used multiple times)
define("INCORRECT_DEPOSIT", "Deposit value must be more then 0 and less or equal than 100");
define("ADD_EDIT_EVENT", "Add/Edit Event"); //(used multiple times)
define("ADD_EVENT", "Add Event");
define("EDIT_EVENT", "Edit Event");
define("LOCATION", "Location ");
define("MAP_LINK", "Map Link ");

# bs-events.php
define("ZERO_EVENT_DATABASE", "0 events found in database");
define("BOOK_SYS_ADMIN", "BookingWizz Admin ");
define("END_DATE", "End Date");
define("PAYMENT_QUEST", "Payment Required?");
define("MSG_EVDELETED", "Selected events were deleted!");
define("BTN_DELETESEL", "Delete");
define("SYL_AT", "at");
define("SYL_LEFT", "left of");
define("SYL_TOTAL", "total");
define("EVENT_ADD", "Add Event");
define("EVENT_PAY", " - payment required");
define("EVENT_REC", " - recurring event");
define("EVENT_ATT", " - view attendee");
define("EVENT_EVEN", "Events");
define("EVENT_EVEN2", "Table on the left shows all events for SELECTED service. Use dropdown above the table to change the service.");

# bs-events-attendees.php
define("ATTENDEES", "Event Attendees");
define("ZERO_ATTENDEES_DATABASE", "0 Attendees found in database");
define("END_DATE", "End Date");
define("PAYMENT_QUEST", "Payment Required?");
define("MSG_ATDELETED", "Selected Attendee were deleted!");
define("BTN_DELETESEL", "Delete");
define("BACK_TO_LIST", "Back to list");
define("ATTEND_LST_NAME", "Name");
define("ATTEND_LST_EMAIL", "Email");
define("ATTEND_LST_DATES", "Date");
define("ATTEND_LST_SPACES", "Spaces"); //(used multiple times)
define("ATTEND_VIEW_EVENT", "View attendees for the event");
define("ATTEND_CHOISE_EVENT", "Select Event");
define("ATTEND_LST_BTN", "Add Booking");

#bs-reserve-view.php
define("ZERO_MAN_FOUND", "0 manual bookings found in database");
define("MAN_BOOK", "Manual Bookings");
define("NEW_BOOKING", "New Booking");
define("REASON", "Reason");
define("MANUAL_BK_DESC", "Description");
define("RECURRING", "Recurring Event");
define("DATE_FORM_RES", "Date Reserved From");
define("BTN_NEW_BOOKING", "New Booking");
define("DATE_RES_TO", "Date Reserved To");
define("ADD_EDIT_MAN_BOOK", "Add/Edit Manual Booking");
define("ADD_MAN_BOOK", "Add Manual Booking");
define("EDIT_MAN_BOOK", "Edit Manual Booking");
define("ADD_EDIT_MAN_BOOK_DAY", "Add/Edit Manual Day Booking");
define("ADD_MAN_BOOK_DAY", "Add Manual Day Booking");
define("EDIT_MAN_BOOK_DAY", "Edit Manual Day Booking");
define("SHRT_DESCRPTN", "Short Description:");  // same as small description
define("SEL_SERVICE", "Select Service"); //(used multiple times)
define("EVENT_COLOR", "Event Color");
define("RES_DATE_FROM", "Reserved Date From:");
define("RES_DATE_TO", "Reserved Date To:");
define("REP", "Repeat");
define("HOURLY", "hourly");
define("DAILY", "daily");
define("WEEKLY", "weekly");
define("MONTHLY", "monthly");
define("YEARLY", "yearly");
define("EVERY", "Every");
define("MSG_MAN_DEL", "Selected manual bookings were deleted.");

#bs-reserve.php
define("MSG_BACK", "Back to list");
define("MSG_TMBK", "This time booked!");
define("MSG_DATETO1", "Reserved Date To earlier than the minimum interval");
define("MSG_DATETO2", "Reserved Date To can't be less than Reserved Date From");
define("MSG_BKSAVE", "Booking was successfully saved!");
define("TXT_BS_RESERVE_DESCR", "Using form below you can add booking manually, for example, having customer on the phone. Also, you can use manual booking to block periods of days which your services are not available on, for example - holidays, vacations, special events (full day facility booking) etc.");

#bs-reserve-day.php
define("BS_RESERVE_DAY_DESCR", "Using form below you can add multi-day booking for selected multi-day service (such as hotel or cottage)  while having customer on the phone.");

#bs-schedule.php
define("SCHEDL", "Schedule");
define("SEL_DATE", "Select Day:");
define("SCHEDL_SELECT_SERV", "Select Service:");
define("SCHEDL_VIEW", "View schedule for the service on the selected date");
define("SCHEDL_TIME", "Time");
define("SCHEDL_DATE", "Select Date:");
define("SCHEDL_BTN_VIEW", "View...");
define("SCHEDL_FOR", "for");
define("SCHEDL_BTN1", "Add Single Day Service");
define("SCHEDL_BTN2", "Add Multi Day Service");
define("SCHEDL_BTN3", "Add Event");
define("SCHEDL_CUSTOMER_NAME", "Customer Name");
define("SCHEDL_CUSTOMER_PHONE", "Customer Phone");
define("SCHEDL_CUSTOMER_EMAIL", "Customer Email");

#bs-schedule-day.php
define("SCHEDL_DAY", "Schedule for Day Bookings");

#bs-services-add.php
define("BS_SERV_BTN1", "Add Single Day Service");
define("BS_SERV_BTN2", "Add Multi Day Service");
define("ZEO_FOUND_BS", "0 services found in database");
define("BS_SERV_TXT", "\"Service\" is the most important entity of the application. \"Service\" applies to professional services, classes or any other entity that can fit within single day to book, including events. Add \"Service\", specify parameters such as price, duration, min/max bookings per service, time frames and so on to have it ready for booking.");
define("SERVICES", "Services");
define("PRICE_PER_BOOKING", "Price Per Booking");
define("SERVICE_DURATION", "Service Duration");
define("FREE_BOOKING", "Free");
define("ADD_EDIT_SERV", "Add/Edit Service");
define("ADD_SERV", "Add Service");
define("EDIT_SERV", "Edit Service");
define("SERV_TTL", "Service Title");
define("TIME_BKK_SET", "Time Booking Settings");
define("BOOK_TIME_INTRV", "Service Duration");
define("MIN15", "15 minutes");
define("MIN30", "30 minutes");
define("MIN45", "45 minutes");
define("H1", "1 hour");
define("H2", "2 hours");
define("H3", "3 hours");
define("H4", "4 hours");
define("H5", "5 hours");
define("H6", "6 hours");
define("H7", "7 hours");
define("H8", "8 hours");
define("H9", "9 hours");
define("H10", "10 hours");
define("H11", "11 hours");
define("H12", "12 hours");

define("INTERV_MSG", "Duration of 1 booking interval (spot)");
define("PRICE_SPOT", "Price per 1 space"); // price per one SPOT???
define("TIME_BEFORE", "Time before booking");
define("HOURS", 'hours');
define("TIME_MSG", "For 'free' - put 0, otherwise XX.XX format"); // time bookings? - doesn't sound right
define("ALLOW_MULT_SPACES", "Allow multiple bookings per service duration"); // spots vs spaces?
define("SPACES_INTRV", "Spaces per each interval");
define("PAYMENT_MSG", "If offline invoice selected - reservation will be confirmed automatically and spot will be removed from availability, it is your responsibility to collect payment from customer outside BookingWizz.");
define("SHOW_SPAC", "Show spaces left (interval booking)");
define("NO", "No"); //(used multiple times)
define("YES", "Yes"); //(used multiple times)
define("SPOT_MSG", "Minimum bookings per service allowed");
define("UNLM_SPOT", "Unlimited Spots");
define("SPOT_MSG_MAX", "Maximum bookings per service allowed");
define("SPT1", "1 spot");
define("SPT2", "2 spots");
define("SPT3", "3 spots");
define("SPT4", "4 spots");
define("BOOK_MSG_DAY", "Please set time available for booking for each day,or put N/A if not available");
define("PICK_DAY", "Week day"); //weekday? pick a day?
define("CALND_WEEK_STARTS", "Calendar week starts on");
define("SUN", "Sunday");
define("MON", "Monday");
define("CRNT_SRV_IMG", "Current Service image");
define("EVENT_DISP_SETT", "Events Display Settings");
define("SHOW_TTL", "Show event titles (calendar view)");
define("AUTOCONFIRM", "Automatically confirm free bookings");
define("AUTOCONFIRM_MSG", "If price for booking is set to 0 (free) and 'Yes' selected - all bookings will be automatically confirmed and marked as not available on service calendar. Applicable to both - time bookings and event bookings");
define("EMAIL_SETTINGS", "Email notifications settings for this service");
define("EMAIL_FROM_NAME", "Sender's Name");
define("EMAIL_FROM_EMAIL", "Sender's Email");
define("SHOW_IMG", "Show event image (calendar view)");
define("SHOW_SEATS", "Show \"available seats\" for events (calendar view)");
define("MSG_SRVUPD", "Service updated!");
define("MSG_SHEDULE_DEL", "Selected schedule was deleted!");
define("MSG_DEMO1", "Sorry,selected action is forbidden in live demo");
define("MSG_SRVSAVE", "Service was successfully created!");
define("MSG_SRVDEL", "Selected service was deleted.");
define("MSG_SRVDEL_DEFAULT", "Default Service cannot be deleted");
define("MSG_NOTE", "All reservations (time & events) associated with this service will deleted,operation irreversible");
define("ALLOW_DEL_BOOKINGS", "Allow user to cancel \"confirmed\",\"paid\" bookings");
define("ALLOW_DEL_BOOKINGS_NOTES", "Allow user to cancel \"confirmed\",\"paid\" bookings");
define("HIDE_PRICE", "Hide price on calendar");

#bs-settings.php
define("DEMO_PASS_MSG", " Sorry,password cannot be changed in demo");
define("PASS_NOMATCH", "Passwords don't match!");
define("SCRP_SETNG", "Script Settings");
define("BS_SETTINGS_DESCR", "Please setup your own settings below to fine tune your BookingWizz. Pick your language or currency, setup taxes, select appropriate time mode and time zone to make your script working precisely as you need.");
define("ACC_SETNG", "Access Settings");
define("NEWPASS_ADMN", "New Administrator Password");
define("CNFRM_PASS", "Confirm Password");
define("NOTIF__EMAIL", " Notification Email");
define("PYPAL_STNG", "PayPal Settings");
define("TAX_ON", "Enable Tax");
define("TAX", "Tax");
define("PAYPAL_EMAIL", "PayPal Merchant Email");
define("PAYPAL_CURRN", "Payment Currency");
define("PAYPAL_CURRN_SUP", "(PayPal supported currencies only)");
define("DIPL_SETTNG", "Display Settings");
define("TIME_MODE", "Time mode");
define("DATE_FORMT", "Date format");
define("POPUP_MSG_BOOK", "Use PopUp window for booking");
define("LANGUAGE_SWITCHING", "Enable language switching");
define("LANGUAGE_SETTINGS", "Language settings");
define("CURNT_SYMBL", "Currency Symbol");
define("CURNT_POS", "Currency Position");
define("LANG", "Language");
define("PLUGINS", "Installed Plugins");
define("MSG_SETSAVED", "Settings were updated!");
define("MSG_ADMPSCHG", "Administrator password was changed!");
define("MSG_PSDNTMTCH", "Passwords don't match!");
define("BTN_SUBMITCHANGES", "Submit Changes");
define("TIMEZONES", "Timezones");
define("TIMEZONE_SET", "Select Timezone");
define("MSG_BLANK_TAX", "Tax value are required");
define("REMINDER", "Reminders");
define("SEND_EMAIL", "send email booking reminder");
define("HOURS_BEFORE_BOOKING", "hours before customer booking");
define("HOURS_BEFORE_APPOINTMENT", "hours before the appointment");
define("HOURS_BEFORE_EVENT", "hours before event starts");
define("MULTI_DAY_NOTIFICATIONS", "Multi-Day notifications");
define("SINGLE_DAY_NOTIFICATIONS", "Single-Day notifications");
define("EVENT_NOTIFICATIONS", "Event notifications");
define("CRON_FOR_EMAIL_NOTIFICATIONS", "Cron for Email Notifications");
define("USE_CRON_TAB", "use normal cron tab ");
define("USE_ALTERNATIVE_CRON", "use cron alternative");
define("CRON_TAB_DESCRIPTION", "We highly recommend selecting this option. This is standard CRON (or Scheduled Task). You will need to copy/paste the cron command on the left into your CRON TAB management section. To learn more about setting up CRON/Scheduled Tasks - please refer to BookingWizz PDF Manual.");
define("ALTERNATIVE_CRON_DESCRIPTION", "In case you have any issues with setting regular CRON tab task - you can select this option. In this case you will be passing cron execution on to visitors. Everytime a visitor will access your BookingWizz - BookingWizz will check if cron has to be ran, and will run it and send all required reminders. In case there are no visitors on site - no reminders will be checked/sent.");
define("ENABLED", "Enabled");
define("DISABLED", "Disabled");

#event-booking.php
define("MSG_JS_ALLFIELDS", "Please complete all highlighted fields to continue.");

#Event-booking-nojs.php
define("RESERV_MSG", "Thank you. You will receive confirmation email regarding your reservation after administrator will process it.");
define("FIELDS_NEEDED", "Following fields required: Name,Email,Phone,Selected Event. Please double check your input.");
define("CAPTCHA_ERROR", "Captcha error! Please try again!"); //(used multiple times)
define("JAVA_NEEDED", "Please enable JavaScript or upgrade to better"); //to better what?? //(used multiple times)
define("BROWSER", "browser"); //(used multiple times)
define("YNAME", "Your Name"); //(used multiple times)
define("BOOKING_FORM", "Booking Form"); //(used multiple times)

#eventlist.php
define("WELCM_SYSTM", "Welcome to our booking system.");
define("SAMPLE_TEXT", "Please select the room and time for reservation. ");
define("EVENTS_LIST", "Full Events List");
define("VIEW", "View");
define("CALENDAR", "Calendar");
define("EVENT_START", "Event starts at ");
define("FREE", "FREE");
define("NO_EVENT_MONTH", "No events in current month");
define("LINKTO", "Link to");
define("ADMINAREA", "ADMIN AREA");

#Forgot.php
define("WRONG_EMAIL", "Wrong Notification E-mail");
define("WRONG_EMAIL2", "Wrong PayPal Merchant Email");
define("CHANGE_PASS_TO_NEW", "Please change password as soon as you login.");
define("NEW_PASS_SENT", "New password was set and sent to your email.");
define("WRONG_USERNAME", "Wrong username (Notification Email) and/or PayPal Merchant Email");
define("WRONG_USERNAME2", "Empty Notification Email and/or Wrong PayPal Merchant Email.");
define("MSG_BSFORGOT_TITLE", "BookingWizz Forgot Password");

#booking.processing.php
define("BACK_RETURN", "Back To Calendar");

#booking.event.processing.php
define("BEP_5", "Event booking placed!");
define("BEP_6", "(Please collect payment from customer)<br />");
define("BEP_7", "<br />");
define("BEP_8", "New confirmed event reservation");
define("BEP_9", "New un-confirmed event reservation");
define("BEP_10", "Invalid email address. Please check your input."); //used multiple times
define("BEP_11", "Thank you. You will receive a confirmation email regarding your reservation after the administrator will process it.");
define("BEP_12", "Sorry,somebody just booked that seat");
define("BEP_13", "Following fields required: Name,Email,Phone,Selected Event. Please double check your input. ");
define("BEP_14", "Thank you for your reservation!"); //h1 page title - used multiple times
define("BEP_15", "&laquo; Back To Calendar"); //used multiple times
define("BEP_16", "Payment For order "); //payment order
define("SEL_TIME", "Please select desired time."); // used multiple times
define("BEP_161", "Booking Order");
define("BEP_17", "Following fields required: Name,Email,Phone and booked time. Please double check your input. ");
define("BEP_18", "Some time interval exceeds the Number of seats. Please check your input.");
define("BEP_19", "Selected Day range out of availability "); //payment order
define("AVAIL", "Availability");

#booking.days.processing.php
define("BDP_1", "New multi-day reservation");
define("BDP_2", "New confirmed multi-day reservation");
define("BDP_3", "New un-confirmed multi-day reservation");
define("BDP_4", "Sorry,somebody just booked that seat");
define("BDP_5", "Selected Day range out of availability "); //payment order
define("BEP_6", "Following fields required: Name,Email,Phone,From,To. Please double check your input. ");
define("BEP_7", "Invalid email address. Please check your input.");

#manageReservation.php
define("MNG_ATTDEL", "Selected attendee was cancelled.");
define("MNG_0FOUND", "0 attendees found in database");
define("MNG_RESERFOR", "Reservations for ");
define("TBL_NAME", "Name");
define("TBL_QTY", "Qty.");
define("TBL_SERVICE", "Service");
define("TBL_EVENT", "Event");
define("TBL_TIME", "Time");
define("TBL_DATE", "Date Subscribed");
define("TBL_MNG", "Manage");

#paypal.ipn.php
define("PP_SUBJ_RECEIVED", "New Payment Received");
define("PP_CANCEL", "The payment was canceled!");
define("PP_THANK_H1", "Thank you !");
define("PP_THANKYOU", "Thank you for your payment,we will contact you shortly!");

#thank-you.php
define("THNK_H1", "Thank you for your reservation!");
define("THNK_TEXT", "Lorem ipsum dolor sit amet,consectetuer adipiscing elit,sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam,quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat,vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.");

#functions.php
define("ZERO_SPACES", "0 spaces available");
define("ZERO_SPACES2", "0 spaces");
define("SPC_AVAIL", " spaces available");
define("DAY_AVAIL", "Available");
define("SEATS_AVAIL", " available seats");
define("DAY_BOOKED", "Booked");
define("CHECKOUT_AVAILABLE", "Checkout only");
define("EVENTS_SCHEDULED", " event(s) scheduled");
define("BOOK_NOW", "Book Now");
define("TXT_RESERVED", "Reserved by admin with reason: ");
define("SPACES", " spaces");
define("TXT_EVENT", "Event");
define("TXT_PAST", "Not Available");
define("TXT_QTY", "Qty ");
define("TXT_EVENT_START", "Event starts at ");
define("TXT_EVENT_ENDS", "Event ends at ");
define("SPC_LEFT", " spaces left ");
define("TXT_PLSSELECT", "Please select desired event.");
define("ADM_NONWORKING", "This day is marked as not-working day,so 0 bookings found.");
define("TXT_EVENT2", "Event");
define("TXT_AVAIL", "Available for booking ");
define("TXT_MINUTES_MAX", " minutes maximum;");
define("TXT_MAX", " maximum;");
define("TXT_MINUTES", " minutes");
define("TXT_AND", " and ");
define("TXT_HOURSS", " hour(s)");
define("TXT_MINUTES_MIN", " minutes minimum; ");
define("TXT_MIN", " minimum; ");
define("TXT_SPOTS_LEFT", "spots<br>left");
define("TXT_FUNC_QTY", "Qty.");
define("TXT_FUNC_FREE", "FREE");
define("TXT_FUNC_PAYMENT_FOR", "Payment for reservation");
define("TXT_FUNC_PAYMNT_EVENT", "Payment for event");
define("TXT_FUNC_CLICK_HERE_TO_PAY", "Click Here To Pay For Booking");
define("TXT_FUNC_THANK_YOU_MSG", "<p>Thank you. You will receive confirmation email regarding your reservation after administrator will process it.</p>");
define("TXT_FUNC_ALMOST_DONE", "You're almost done. There's just one thing left to do - payment. Please click button below and you will be transferred to PayPal.com for fast and secure payment. Please note that your booking will be confirmed only after");

#new messages in v5.3
define("REPEAT_MSG", "Please make sure your reservation end date equals actual recurring interval end. For example if recurring booking must end in 6 weeks from now,set 'reservation end date' to 'today+6 weeks'");
define("REPEAT_MSG2", "Please make sure your event end date equals to actual recurring period end date. For example if recurring event must end in 6 weeks from now,set 'recurring period end date' AND 'event end date' to 'today+6 weeks'");


#bs-coupons-add.php
define("BS_COUPONS_TXT", "Use this section to create  multiple discount coupons for your services or events. Specify parameters such as coupon code, discount type, expiration dates, link it to the applicable service and have it ready to use.");
define("TXT_COUPONS_STARTS", "Starts");
define("TXT_COUPONS_ENDS", "Ends");
define("TXT_SET_ST", "Set Start and End Dates");


#bs-coupons.php
define("TXT_COUPONS", "My Coupons");
define("TXT_COUPONS_ADD", "Edit Coupon");
define("TXT_COUPONS_NEW", "Add Coupon");
define("TXT_COUPONS_DEL", "Selected coupon was deleted.");
define("TXT_COUPONS_NOT_FOUND", "0 coupons found in database");
define("LABEL_COUPON_TITLE", "Title");
define("LABEL_COUPON_AMOUNT", "Discount");
define("LABEL_COUPON_CODE", "Coupon Code");
define("LABEL_COUPON_VALUE", "Value");
define("LABEL_COUPON_TYPE", "Type");
define("LABEL_COUPON_VALID_FROM", "Valid From");
define("LABEL_COUPON_VALID_TO", "Valid Until");
define("LABEL_COUPON_CALENDARS", "Applicable Services(Calendars)");
define("MSG_COUPON_SAVE", "Coupon was successfully created!");
define("MSG_COUPON_UPD", "Coupon updated!");

#bs-services_days-add
define("TXT_DAY_SRV_ADD_EDIT", "Add/Edit Multi-Day Service");
define("TXT_DAY_SRV_ADD", "Add Multi-Day Service");
define("TXT_DAY_SRV_EDIT", "Edit Multi-Day Service");
define("TXT_DAY_SRV_TITLE", "Service Title");
define("TXT_DAY_SRV_SETTINGS", "Multi-Day Booking Settings");
define("TXT_DAY_MAX_SPACES", "Maximum spaces");
define("TXT_DAY_SRV_MAX_DAYS", "Maximum days");
define("TXT_DAY_SRV_MIN_DAYS", "Minimum days");
define("TXT_DAY_SRV_DAYS_BEFORE", "Days before booking");
define("TXT_DAY_SRV_DESCR", "Description");
define("TXT_DAY_SRV_IMAGE", "Service Image");
define("TXT_DAY_SRV_AVAILABILITY", "Availability & Pricing");
define("TXT_DAY_SRV_FROM", "From");
define("TXT_DAY_SRV_TO", "To");
define("TXT_DAY_SRV_PRICE", "Price");
define("BS_SERV_TXT_MD", "\"Multi-Day Service\" applies to entity that may spread into several days such as hotel room booking or cottage booking. Add \"Multi-Day Service\", specify parameters such as price, maximum days and spaces, dates availability range and so on to have it ready for booking.");

#bs-services.php
define("TXT_SM_R_ABOUT", "by adding and customizing service, you add service's availability calendar.");
define("DEFAULT_SERVICE", "Default Service");
define("MAKE_DAFAULT", "Make as default service");
define("THIS_DEFAULT_SERVICE", "This is default service ");

#js-table
define("JS_TABLE_FIRST", "first");
define("JS_TABLE_PREV", "previous");
define("JS_TABLE_NEXT", "next");
define("JS_TABLE_LAST", "last");
define("MSG_DAY_SRV", "");
define("LABEL_DAY_SRV", "");
define("SERVICE_ID", "ID"); //(used multiple times)

#bs-addons
define("ADDONS_TITLE", "Installed Add Ons");
define("BS_ADDONS_DESCR", "Please find below currently installed add-ons for your  BookingWizz. You can turn add-on on or off with a click of a button.");
define("PLUGIN_SETTINGS", "Plugin settings");

#bs-attendees-export
define("EXPORT_TITLE", "Attendees export");
define("NO_ATTENDEES", "No attendees found");
define("EXPORT_TITLE2", "In this section you can export all attendees (event/time appointments/daily bookings) in to an .ICS file which you can later import into a program of your preference which supports .ICS file imports (Google Calendar, Outlook Express, iCal etc.)");
define("EXPORT_TITLE3", "Select date range for export");
define("EVENT_BOOKINGS", "Event Bookings");
define("BOOKINGS", "Bookings");
define("DAY_BOOKINGS", "Multi-Day Bookings");
define("BOOKINGS_EXPRT", "Daily & Hourly Bookings");

#bs-reports
define("REPORTS_DESCR", "<em style=\"font-weight: normal;\" >You can navigate using mouse scroll to zoom in<b>/</b>out, by dragging grid to focus on specific data, or with the help of arrows in top right corner.</em>");
define("REPORTS_1", "Please select Service");
define("REPORTS_2", "Please Select Date Range");
define("REPORTS_3", "View Report");
define("REPORTS_4", "Event bookings from");
define("REPORTS_5", "Appointments Reports");
define("REPORTS_6", "From");
define("REPORTS_7", "To");
define("REPORTS_8", "Appointment bookings from");
define("REPORTS_9", "for service");
define("REPORT_TITLE2", "Reports allow you to see how many event bookings / hourly appointments / daily bookings been reserved per selected period of time, for selected service(calendar)");

define("NEED_HELP", "Need help?");
define("SUPPORT_FAILED_TO_LOAD", "Unfortunately, this section is not available at the moment. Please check back a bit later. <br /> If you will continue to experience this issue at a later time - please refer to our support forum located at <a href=\"http://support.convergine.com\" target=\"_blank\">http://support.convergine.com</a> for assistance with BookingWizz.");
define("HELP_SIDE1", "You are viewing all attendees for selected event. To change event please user SELECT EVENT dropdown on the right.");
define("EVENT_IMAGE", "Event Image");
define("EVENT_START_END", "Set Start and End Dates");
define("SELECT_SERVICE", "You must select service (=calendar) where this event will show up.");
define("SELECT_SERVICE_RESERVE", "You must select service (=calendar) where this manual booking is applicable.");
define("RECURRING_MDB", "Recurring Booking");
define("MULTI_DAY_NOT_AV", "This day is not available");
define("MULTI_DAY_BK_ADM", "This day booked by admin ");
define("MULTI_DAY_BK_ADM2", "reason: ");
define("MULTI_DAY_TXT1", "You can book");
define("MULTI_DAY_TXT2", "days maximum.");
define("MULTI_DAY_TXT2_1", "days minimum.");
define("MULTI_DAY_TXT3", "The selected date is outside the range of availability. Please adjust your selection.");
define("MULTI_DAY_TXT4", "Available for Booking");
define("MULTI_DAY_TXT5", "Book");
define("MULTI_DAY_TXT6", "Manual Booking ");
define("MULTI_DAY_TXT7", "This is day booked ");
define("MULTI_DAY_TXT8", "Date From");
define("MULTI_DAY_TXT9", "Date To");

#various tooltips
define("TTIP_1", "If enabled - customer will be able to enter coupon code on the booking form.");
define("TTIP_2", "Number of hours entered here will prevent customer from booking any time spot within this period. Example: if right now is 12pm and you have 4 hours in this field, the earliest time spot customers will be able to book is 4pm");
define("TTIP_3", "Your customers will see this NAME as 'FROM' in all notifications associated with this service.");
define("TTIP_4", "Your customers will see this EMAIL as 'FROM' in all notifications associated with this service.");
define("TTIP_5", "Maximum guests in any booking. At the moment for 'display' purpose only.");
define("TTIP_6", "Maximum number of days allowed per any booking.");
define("TTIP_6_1", "Minimum number of days allowed per any booking.");
define("TTIP_7", "Number of days entered here will prevent customer from booking any date within this period from current time. Example: if today is January 1st and you have 4 days in this field, the earliest day customers will be able to book will be January 5th.");

#homescreen popup
define("HSP1", "Don't show this screen on start up");
define("HSP2", "Welcome to Your <br/>BookingWizz Back Office");
define("HSP3", "As a website owner, you can easily setup events, classes, programs and start taking free or paid reservations online. BookingWizz provides you with great management options, flexibitliy and customization For example, you can set a timeframe available for registration for each day (Monday through Sunday), set price, availabiltiy, various restrictions; set notification email, set how many hours can customer reserve per booking.");
define("HSP4", "You also have an option to reserve time manually right here, for example if there is a corporate party and whole facility will be booked for the whole day – you can add such reservation through this back office – so that customer will see on calendar that this day is not available for bookings.");
define("HSP5", "<a href='help/index.html'>read further how to use BookingWizz</a>");
define("HSP6", "BUILDING YOUR INVENTORY");
define("HSP7", "OPTIONAL ADD-ONS");
define("HSP8", "LEARN MORE");
define("HSP9", "Frequently Asked Questions");
define("HSP10", "Case studies & Applications");

define("TXT_COUPON_CODE", "Enter coupon code (optional)");

#order summery text
define("ORDER_BOOKING_INF", "Booking Information");
define("ORDER_EVENT_INF", "Event Information");
define("ORDER_BOOKING_DATE", "Booking Date");
define("ORDER_BOOKING_TIME", "Booking Time");
define("ORDER_BOOKING_QTY", "QTY");
define("ORDER_DATE_FROM", "Booking Date From");
define("ORDER_DATE_TO", "Booking Date To");
define("ORDER_DAYS", "Days");
define("ORDER_SUBTOTAL", "Sub Total");
define("ORDER_TOTAL", "Total");
define("ORDER_DISCOUNT", "Discount");
define("ORDER_TAX", "Tax");
define("ORDER_TO_PAY", "Total to pay");
define("ORDER_SUMMERY", "Order Summary");

define("ERROR_RESERVED_BY_ADMIN", "Selected days are already booked by admin");
define("ERROR_RESERVED_ALREADY", "Selected days are already booked");

#################     Event Tickets plugin     ######################################################################

define("PLUGIN_NAME", "Event Tickets");
define("ADD_TIX", "Add Ticket");
define("TXT_TIX_DEL", "Selected ticket was deleted");
define("TXT_TIX_DEL_ERROR", "Error deleting ticket");
define("TXT_TIX_NOT_FOUND", "0 tickets found in database");
define("LABEL_TIX_TITLE", "Title");
define("LABEL_TIX_CODE", "Ticket Code");
define("MSG_TIX_UPD", "Ticket updated!");
define("MSG_TIX_SAVE", "Ticket was successfully created!");
define("TXT_TIX_ADD", "Edit Ticket");
define("BS_TIX_TXT", "Use this section to create custom ticket for your event. Step 1 is to setup ticket name and size, after saving it you will be able to specify parameters which to show on ticket, colors, fonts etc.");
define("LABEL_TIX_NAME", "Ticket Name");
define("LABEL_TIX_WIDTH", "Ticket Width (px)");
define("LABEL_TIX_HEIGHT", "Ticket Height");
define("LABEL_TIX_BG_TYPE", "Ticket background type");
define("LABEL_SELECT_BACKGROUND_COLOR", "Select background color");
define("LABEL_UPLOAD_BACKGROUND", "Upload background image");
define("LABEL_BG_IMAGE", "Select background image");
define("LABEL_BG_COLOR", "Select background color");
define("LABEL_FONT_COLOR", "Font color");
define("LABEL_FONT_SIZE", "Font size");
define("LABEL_FONT_FAMILY", "Font");
define("LABEL_LOGO", "Logo");
define("LABEL_ENCODE_IN_BARCODE", "Encode in barcode");
define("LABEL_RESERVATION_ID", "Reservation ID");
define("LABEL_CUSTOMER_NAME", "Customer Name");
define("LABEL_EVENT_NAME", "Event Name");
define("LABEL_EVENT_DATE", "Event Date & Time");
define("LABEL_EVENT_LOCATION", "Event Location");
define("LABEL_EVENT_PRICE", "Event Price");
define("LABEL_BARCODE", "Barcode");
define("LABEL_LABEL", "Label");
