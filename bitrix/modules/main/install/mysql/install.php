<?
$DB->Query("create table b_lang 
(
	LID			char(2) 	not null,
	SORT			INT(18) 	not null default '100',
	DEF			char(1) 	not null default 'N',
	ACTIVE		char(1) 	not null default 'Y',
	NAME			varchar(50) not null,
	DIR			varchar(50) not null,
	FORMAT_DATE	varchar(50) not null,
	FORMAT_DATETIME varchar(50) not null,
	CHARSET varchar(255),
	LANGUAGE_ID char(2) NOT NULL,
	DOC_ROOT varchar(255) NULL,
	DOMAIN_LIMITED char(1) NOT NULL default 'N',
	SERVER_NAME varchar(255) NULL,
	SITE_NAME varchar(255) NULL,
	EMAIL varchar(255) NULL,
	primary key (LID)
)");



$DB->Query("create table b_language
(
	LID char(2) NOT NULL,
	SORT int NOT NULL default '100',
	DEF char(1) NOT NULL default 'N',
	ACTIVE char(1) NOT NULL default 'Y',
	NAME varchar(50) NOT NULL,
	FORMAT_DATE varchar(50) NOT NULL,
	FORMAT_DATETIME varchar(50) NOT NULL,
	CHARSET varchar(255) NULL,
	DIRECTION char(1) NOT NULL default 'Y',
	primary key (LID)
)");

$DB->Query("create table b_lang_domain
(
	LID char(2) NOT NULL,
	DOMAIN varchar(255) NOT NULL,
	primary key (LID, DOMAIN)
)");

$DB->Query("create table b_event_type
(
	ID 			INT(18) 		not null auto_increment,
	LID 		char(2) 		not null REFERENCES b_lang(LID),
	EVENT_NAME 	varchar(50) 	not null,
	NAME 		varchar(100),
	DESCRIPTION text,
	SORT 		INT(18) 		not null default '150',
	primary key (ID),
	unique ux_1 (EVENT_NAME, LID)
)");


$DB->Query("create table b_event_message
(
	ID 			INT(18) 		not null auto_increment,
	TIMESTAMP_X timestamp(14),
	EVENT_NAME 	varchar(50) 	not null,
	LID 		char(2) 		not null REFERENCES b_lang(LID),
	ACTIVE 		char(1) 		not null default 'Y',
	EMAIL_FROM 	varchar(255) 	not null default '#EMAIL_FROM#',
	EMAIL_TO 	varchar(255) 	not null default '#EMAIL_TO#',
	SUBJECT 	varchar(255),
	MESSAGE 	text,
	BODY_TYPE 	varchar(4) 		not null default 'text',
	BCC 		text,
	primary key (ID)
)");

$DB->Query("create table b_event
(
	ID 			INT(18) 	not null auto_increment,
	EVENT_NAME 	varchar(50) not null,
        MESSAGE_ID      int(18),
	LID 		char(2) 	not null REFERENCES b_lang(LID),
	C_FIELDS 	longtext,
	DATE_INSERT datetime,
	DATE_EXEC 	datetime,
	SUCCESS_EXEC char(1) 	not null default 'N',
	DUPLICATE char(1) 	not null default 'Y',
	primary key (ID),
	index ix_success (SUCCESS_EXEC)
)");

$DB->Query("create table b_group (
   ID int(18) not null auto_increment,
   TIMESTAMP_X timestamp(14),
   ACTIVE char(1) not null default 'Y',
   C_SORT int(18) not null default '100',
   ANONYMOUS char(1) not null default 'N',
   NAME varchar(50) not null,
   DESCRIPTION varchar(255),
   SECURITY_POLICY text null,
   primary key (ID))");


$DB->Query("create table b_user (
   ID int(18) not null auto_increment,
   TIMESTAMP_X timestamp(14),
   LOGIN varchar(50) not null,
   `PASSWORD` varchar(50) not null,
   CHECKWORD varchar(50),
   ACTIVE char(1) not null default 'Y',
   NAME varchar(50),
   LAST_NAME varchar(50),
   EMAIL varchar(255) not null,
   LAST_LOGIN datetime,
   DATE_REGISTER datetime not null,
   LID char(2) REFERENCES b_lang(LID),
   PERSONAL_PROFESSION varchar(255),
   PERSONAL_WWW varchar(255),
   PERSONAL_ICQ varchar(255),
   PERSONAL_GENDER char(1),
   PERSONAL_BIRTHDATE varchar(50),
   PERSONAL_PHOTO int(18),
   PERSONAL_PHONE varchar(255),
   PERSONAL_FAX varchar(255),
   PERSONAL_MOBILE varchar(255),
   PERSONAL_PAGER varchar(255),
   PERSONAL_STREET text,
   PERSONAL_MAILBOX varchar(255),
   PERSONAL_CITY varchar(255),
   PERSONAL_STATE varchar(255),
   PERSONAL_ZIP varchar(255),
   PERSONAL_COUNTRY varchar(255),
   PERSONAL_NOTES text,
   WORK_COMPANY varchar(255),
   WORK_DEPARTMENT varchar(255),
   WORK_POSITION varchar(255),
   WORK_WWW varchar(255),
   WORK_PHONE varchar(255),
   WORK_FAX varchar(255),
   WORK_PAGER varchar(255),
   WORK_STREET text,
   WORK_MAILBOX varchar(255),
   WORK_CITY varchar(255),
   WORK_STATE varchar(255),
   WORK_ZIP varchar(255),
   WORK_COUNTRY varchar(255),
   WORK_PROFILE text,
   WORK_LOGO int(18),
   WORK_NOTES text,
   ADMIN_NOTES text,
   STORED_HASH varchar(32),
   XML_ID varchar(255),
   PERSONAL_BIRTHDAY date,
   EXTERNAL_AUTH_ID varchar(255),
   CHECKWORD_TIME datetime,
   SECOND_NAME varchar(50),
   primary key (ID),
   unique ix_login (LOGIN, EXTERNAL_AUTH_ID)
)");

$DB->Query("create table b_user_group 
(
	USER_ID 	INT(18) not null REFERENCES b_user(ID),
	GROUP_ID 	INT(18) not null REFERENCES b_group(ID),
	DATE_ACTIVE_FROM datetime null,
	DATE_ACTIVE_TO datetime null,
	unique ix_user_group (USER_ID, GROUP_ID)
)");


$DB->Query("create table b_module
(
	ID	 		VARCHAR(50) NOT NULL,
	DATE_ACTIVE timestamp not null,
	primary key (ID)
)");


$DB->Query("create table b_option
(
	MODULE_ID	VARCHAR(50),
	NAME	 	VARCHAR(50) 	NOT NULL,
	VALUE	 	TEXT,
	DESCRIPTION	VARCHAR(255),
	SITE_ID		CHAR(2) default null,
	UNIQUE ix_option(MODULE_ID, NAME, SITE_ID)
)");


$DB->Query("create table b_module_to_module
(  
	ID int not null auto_increment,
    TIMESTAMP_X    TIMESTAMP	not null,
    SORT         INT(18) 	not null default '100',
    FROM_MODULE_ID VARCHAR(50) NOT NULL,
    MESSAGE_ID     VARCHAR(50) NOT NULL,
    TO_MODULE_ID   VARCHAR(50) NOT NULL,
    TO_PATH        VARCHAR(255),
    TO_CLASS       VARCHAR(50),
    TO_METHOD      VARCHAR(50),
	primary key (ID),
     INDEX ix_module_to_module(FROM_MODULE_ID, MESSAGE_ID, TO_MODULE_ID, TO_CLASS, TO_METHOD)
)");


$DB->Query("create table b_agent
(
	ID 				INT(18) 		not null auto_increment,
	MODULE_ID 		varchar(50)		REFERENCES b_module(ID),
	SORT			INT(18) 	not null default '100',
	NAME 			varchar(255)	not null,
	ACTIVE 			char(1) 		not null default 'Y',
	LAST_EXEC 		datetime,
	NEXT_EXEC 		datetime		not null,
	DATE_CHECK 		datetime,
	AGENT_INTERVAL 	INT(18) 					default '86400',
	IS_PERIOD 		char(1) 					default 'Y',
	USER_ID		INT(18) default null,
	primary key (ID),
	index ix_act_next_exec(ACTIVE, NEXT_EXEC),
	index ix_agent_user_id(USER_ID)
)");


$DB->Query("create table b_file
(
	ID				INT(18)		NOT NULL auto_increment,
	TIMESTAMP_X 	TIMESTAMP	not null,
	MODULE_ID 		varchar(50),
	HEIGHT			INT(18),
	WIDTH			INT(18),
	FILE_SIZE		INT(18)	NOT NULL,
	CONTENT_TYPE	VARCHAR(255)	DEFAULT 'IMAGE',
	SUBDIR			VARCHAR(255),
	FILE_NAME		VARCHAR(255)	NOT NULL,
	ORIGINAL_NAME VARCHAR(255) NULL,
	DESCRIPTION VARCHAR(255) NULL,
	PRIMARY KEY (ID)
)");


$DB->Query("create table b_module_group
(
  ID int(11) not null auto_increment,  
  MODULE_ID varchar(50) not null,  
  GROUP_ID int(11) not null,  
  G_ACCESS varchar(255) not null,  
  primary key (ID),  
  unique UK_GROUP_MODULE(MODULE_ID, GROUP_ID)
)");

$DB->Query("create table b_favorite (
	ID int(18) not null auto_increment,
	TIMESTAMP_X datetime,
	DATE_CREATE datetime,
	C_SORT int(18) not null default '100',
	MODIFIED_BY int(18),
	CREATED_BY int(18),
	MODULE_ID varchar(50) null,
	NAME varchar(255),
	URL text,
	COMMENTS text,
	LANGUAGE_ID char(2) null,  
	USER_ID int null,
	COMMON char(1) not null default 'Y',
	primary key (ID)
)");

$DB->Query("create table b_user_stored_auth
(
	ID int(18) not null auto_increment,
	USER_ID int(18) not null,
	DATE_REG datetime not null,
	LAST_AUTH datetime not null,
	STORED_HASH varchar(32) not null,
	TEMP_HASH char(1) not null default 'N',
	IP_ADDR int(10) unsigned not null,
	primary key (ID),
	index ux_user_hash (USER_ID)
)");

$DB->Query("create table b_site_template
(
	ID int NOT NULL auto_increment,
	SITE_ID char(2) NOT NULL,
	`CONDITION` varchar(255) NULL,
	SORT int NOT NULL default '500',
	TEMPLATE varchar(50) NOT NULL,
	primary key (ID)
)");

$DB->Query("alter table b_site_template ADD UNIQUE INDEX UX_B_SITE_TEMPLATE(SITE_ID, `CONDITION`, TEMPLATE)");


$DB->Query("create table b_event_message_site
(
	EVENT_MESSAGE_ID int NOT NULL,
	SITE_ID char(2) NOT NULL,
	primary key (EVENT_MESSAGE_ID, SITE_ID)
)");

$DB->Query("create table b_user_option
(
	ID int not null auto_increment,
	USER_ID int null,
	CATEGORY varchar(50) not null,
	NAME varchar(255) not null,
	VALUE text null,
	COMMON char(1) not null default 'N',
	primary key (ID),
	index ix_user_option_param(CATEGORY, NAME)
)");

$DB->Query("create table b_captcha
(
	ID varchar(32) not null,
	CODE varchar(20) not null,
	IP varchar(15) not null,
	DATE_CREATE datetime not null,
	UNIQUE UX_B_CAPTCHA(ID)
)");

$DB->Query("create table b_user_field
(
	ID int(11) not null auto_increment,
	ENTITY_ID varchar(20),
	FIELD_NAME varchar(20),
	USER_TYPE_ID varchar(50),
	XML_ID varchar(255),
	SORT int,
	MULTIPLE char(1) not null default 'N',
	MANDATORY char(1) not null default 'N',
	SHOW_FILTER char(1) not null default 'N',
	SHOW_IN_LIST char(1) not null default 'Y',
	EDIT_IN_LIST char(1) not null default 'Y',
	IS_SEARCHABLE char(1) not null default 'N',
	SETTINGS text,
	PRIMARY KEY (ID),
	UNIQUE ux_user_type_entity(ENTITY_ID, FIELD_NAME)
)");

$DB->Query("create table b_user_field_lang (
	USER_FIELD_ID int(11) REFERENCES b_user_field(ID),
	LANGUAGE_ID char(2),
	EDIT_FORM_LABEL varchar(255),
	LIST_COLUMN_LABEL varchar(255),
	LIST_FILTER_LABEL varchar(255),
	ERROR_MESSAGE varchar(255),
	HELP_MESSAGE varchar(255),
	PRIMARY KEY (USER_FIELD_ID, LANGUAGE_ID)
)");

$DB->Query("create table b_user_field_enum
(
	ID int(11) not null auto_increment,
	USER_FIELD_ID int(11) REFERENCES b_user_field(ID),
	VALUE varchar(255) not null,
	DEF char(1) not null default 'N',
	SORT int(11) not null default 500,
	XML_ID varchar(255) not null,
	primary key (ID),
	unique ux_user_field_enum(USER_FIELD_ID, XML_ID)
)");

$DB->Query("create table b_task(
	ID int(18) not null auto_increment,
	NAME varchar(50) not null,
	LETTER char(1),
	MODULE_ID varchar(50) not null,
	SYS char(1) not null,
	DESCRIPTION varchar(255),
	BINDING varchar(50) default 'module',
	primary key (ID)
)");

$DB->Query("create table b_group_task(
	GROUP_ID int(18) not null,
	TASK_ID int(18) not null,
	EXTERNAL_ID varchar(50) DEFAULT '',
	primary key (GROUP_ID,TASK_ID)
)");

$DB->Query("create table b_operation(
	ID int(18) not null auto_increment,
	NAME varchar(50) not null,
	MODULE_ID varchar(50) not null,
	DESCRIPTION varchar(255),
	BINDING varchar(50) default 'module',
	primary key (ID)
)");

$DB->Query("create table b_task_operation(
	TASK_ID int(18) not null,
	OPERATION_ID int(18) not null,
	primary key (TASK_ID,OPERATION_ID)
)");

$DB->Query("create table b_group_subordinate(
	ID int(18) not null,
	AR_SUBGROUP_ID varchar(255) not null,
	primary key (ID)
)");

$DB->Query("alter table b_group ADD COLUMN STRING_ID varchar(255)");


$DB->Query("INSERT INTO b_module (ID) VALUES ('main')");
$DB->Query("INSERT INTO b_module_to_module(SORT, FROM_MODULE_ID, MESSAGE_ID, TO_MODULE_ID, TO_CLASS, TO_METHOD, TO_PATH) VALUES(100, 'iblock', 'OnIBlockPropertyBuildList', 'main', 'CIBlockPropertyUserID', 'GetUserTypeDescription', '/modules/main/tools/prop_userid.php')");
$DB->Query("insert into b_agent (`ID`,`MODULE_ID`,`SORT`,`NAME`,`ACTIVE`,`LAST_EXEC`,`NEXT_EXEC`,`DATE_CHECK`,`AGENT_INTERVAL`,`IS_PERIOD`,`USER_ID`) values(1,null,100,'CEvent::CleanUpAgent();','Y','2007-09-26 09:45:56','2007-09-27 00:00:00',null,86400,'Y',null)");
$DB->Query("insert into b_agent (`ID`,`MODULE_ID`,`SORT`,`NAME`,`ACTIVE`,`LAST_EXEC`,`NEXT_EXEC`,`DATE_CHECK`,`AGENT_INTERVAL`,`IS_PERIOD`,`USER_ID`) values(89,'main',100,'CCaptchaAgent::DeleteOldCaptcha(3600);','Y','2007-09-26 10:46:42','2007-09-26 11:46:42',null,3600,'N',null)");
$DB->Query("insert into b_module_to_module (`ID`,`TIMESTAMP_X`,`SORT`,`FROM_MODULE_ID`,`MESSAGE_ID`,`TO_MODULE_ID`,`TO_PATH`,`TO_CLASS`,`TO_METHOD`) values(93,'20060905173348',100,'main','OnUserDelete','main',null,'CFavorites','OnUserDelete')");
$DB->Query("insert into b_module_to_module (`ID`,`TIMESTAMP_X`,`SORT`,`FROM_MODULE_ID`,`MESSAGE_ID`,`TO_MODULE_ID`,`TO_PATH`,`TO_CLASS`,`TO_METHOD`) values(94,'20060905173348',100,'main','OnLanguageDelete','main',null,'CFavorites','OnLanguageDelete')");
$DB->Query("insert into b_module_to_module (`ID`,`TIMESTAMP_X`,`SORT`,`FROM_MODULE_ID`,`MESSAGE_ID`,`TO_MODULE_ID`,`TO_PATH`,`TO_CLASS`,`TO_METHOD`) values(95,'20060905173804',100,'main','OnUserDelete','main',null,'CUserOptions','OnUserDelete')");
$DB->Query("insert into b_module_to_module (`ID`,`TIMESTAMP_X`,`SORT`,`FROM_MODULE_ID`,`MESSAGE_ID`,`TO_MODULE_ID`,`TO_PATH`,`TO_CLASS`,`TO_METHOD`) values(107,'20070221103403',100,'main','OnChangeFile','main',null,'CMain','OnChangeFileComponent')");
$DB->Query("insert into b_module_to_module (`ID`,`TIMESTAMP_X`,`SORT`,`FROM_MODULE_ID`,`MESSAGE_ID`,`TO_MODULE_ID`,`TO_PATH`,`TO_CLASS`,`TO_METHOD`) values(121,'20070920122034',100,'main','OnUserTypeRightsCheck','main',null,'CUser','UserTypeRightsCheck')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(1,'view_own_profile','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(2,'view_subordinate_users','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(3,'view_all_users','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(4,'view_groups','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(5,'view_tasks','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(6,'view_other_settings','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(7,'edit_own_profile','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(8,'edit_all_users','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(9,'edit_subordinate_users','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(10,'edit_groups','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(11,'edit_tasks','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(12,'edit_other_settings','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(13,'cache_control','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(14,'edit_php','main',null,'module')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(15,'fm_view_permission','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(16,'fm_edit_permission','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(17,'fm_edit_existent_folder','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(18,'fm_create_new_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(19,'fm_edit_existent_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(20,'fm_create_new_folder','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(21,'fm_delete_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(22,'fm_delete_folder','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(23,'fm_view_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(24,'fm_view_listing','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(25,'fm_edit_in_workflow','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(26,'fm_rename_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(27,'fm_rename_folder','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(28,'fm_upload_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(29,'fm_add_to_menu','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(30,'fm_download_file','main',null,'file')");
$DB->Query("insert into b_operation (`ID`,`NAME`,`MODULE_ID`,`DESCRIPTION`,`BINDING`) values(31,'fm_lpa','main',null,'file')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'new_user_registration', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'new_user_registration_def_group', '11')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'store_password', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'all_bcc', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'fill_to_mail', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'email_from', 'admin@ourtestsite.ru')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'site_name', 'Демо-сайт')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'server_name', 'www.ourtestsite.ru')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'cookie_name', 'BITRIX_SM')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'upload_dir', 'upload')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'header_200', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'map_top_menu_type', 'top')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'map_left_menu_type', 'left')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'error_reporting', '85')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'admin_lid', 'ru')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'GROUP_DEFAULT_RIGHT', 'D')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_base_stat', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_file_kernel', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_file_public', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_max_file_size', '1048576')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_max_exec_time', '55')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_file_stepped', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'send_mid', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'use_secure_password_cookies', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'CONVERT_UNIX_NEWLINE_2_WINDOWS', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'PARAM_MAX_SITES', '0')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'use_site_template', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'convert_mail_header', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'auth_comp2', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'auth_multisite', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'save_original_file_name', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'convert_original_file_name', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_base_true', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'INSTALL_STATISTIC_TABLES', '25.01.2006 18:27:32')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'ALLOW_SPREAD_COOKIE', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'captcha_registration', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'captcha_password', 'Qd2tcgTh5L')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'crc_code', 'N1dvandROUJscw')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'new_license_sign', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'stable_versions_only', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_site_proxy_addr', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_site_proxy_port', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_site_proxy_user', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_site_proxy_pass', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'strong_update_check', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_site', 'www.1c-bitrix.ru')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'component_cache_on', 'N')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'dump_base_index', '')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'distributive6', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'new_license6_sign', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'vendor', '1c_bitrix')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'templates_visual_editor', 'Y')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_system_check', '21.09.2007 12:37:12')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'update_system_update', '20.09.2007 18:29:29')");
$DB->Query("INSERT INTO b_option (MODULE_ID, NAME, VALUE) VALUES ('main', 'GROUP_DEFAULT_TASK', '1')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(1,'main_denied','D','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(2,'main_change_profile','P','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(3,'main_view_all_settings','R','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(4,'main_view_all_settings_change_profile','T','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(5,'main_edit_subordinate_users','V','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(6,'main_full_access','W','main','Y',null,'module')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(7,'fm_folder_access_denied','D','main','Y',null,'file')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(8,'fm_folder_access_read','R','main','Y',null,'file')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(9,'fm_folder_access_write','W','main','Y',null,'file')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(10,'fm_folder_access_full','X','main','Y',null,'file')");
$DB->Query("insert into b_task (`ID`,`NAME`,`LETTER`,`MODULE_ID`,`SYS`,`DESCRIPTION`,`BINDING`) values(11,'fm_folder_access_workflow','U','main','Y',null,'file')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('2','1')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('2','7')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('3','1')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('3','3')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('3','4')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('3','5')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('3','6')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','1')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','3')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','4')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','5')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','6')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('4','7')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','1')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','2')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','4')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','5')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','6')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','7')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('5','9')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','1')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','3')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','4')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','5')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','6')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','7')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','8')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','10')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','11')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','12')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('6','13')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('8','23')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('8','24')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','17')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','18')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','19')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','20')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','21')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','22')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','23')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','24')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','25')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','26')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','27')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','28')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','29')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('9','30')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','15')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','16')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','17')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','18')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','19')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','20')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','21')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','22')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','23')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','24')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','25')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','26')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','27')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','28')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','29')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('10','30')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('11','15')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('11','19')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('11','23')");
$DB->Query("INSERT INTO b_task_operation (TASK_ID,OPERATION_ID) VALUES ('11','25')");
$DB->Query("insert into b_event_type(ID, LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values(1, 'ru', 'USER_INFO', 'Информация о пользователе', '
#USER_ID# - ID пользователя
#STATUS# - Статус логина
#MESSAGE# - Сообщение пользователю
#LOGIN# - Логин
#CHECKWORD# - Контрольная строка для смены пароля
#NAME# - Имя
#LAST_NAME# - Фамилия
#EMAIL# - E-Mail пользователя
', '1')");
$DB->Query("insert into b_event_type(ID, LID, EVENT_NAME, NAME, DESCRIPTION, SORT) values(3, 'ru', 'NEW_USER', 'Зарегистрировался новый пользователь', '
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
', '3')");
?>
