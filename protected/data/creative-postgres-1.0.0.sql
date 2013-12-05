----------
--Master--
----------
CREATE TABLE m_group(
	group_id			INTEGER		NOT NULL PRIMARY KEY,
	text				VARCHAR(32)	NOT NULL,
	remarks				TEXT
);

CREATE TABLE m_code(
	group_id			INTEGER		NOT NULL,
	code				VARCHAR(16)	NOT NULL,
	key					VARCHAR(16)	NOT NULL,
	text				VARCHAR(256),
	type				VARCHAR(16),
	PRIMARY KEY (group_id, code, key),
	FOREIGN KEY (group_id) REFERENCES m_group (group_id)
);

CREATE OR REPLACE VIEW m_cd AS
SELECT m_group.text AS category, m_code.text, m_group.group_id, code, key, remarks
FROM m_code
LEFT JOIN m_group ON m_code.group_id = m_group.group_id
WHERE m_code.text IS NOT NULL
ORDER BY m_group.group_id, code;

CREATE OR REPLACE VIEW m_area_cd AS
SELECT a.category, a.text AS "area", b.text AS "city", a.group_id AS "area_group_id", a.code AS "area_code", b.group_id AS "city_group_id", b.code AS "city_code" FROM m_cd a
LEFT JOIN m_cd b ON b.group_id = a.code::int * 1000
WHERE a.category LIKE '%AREA'
ORDER BY a.category, a.text, b.text;

UPDATE m_group SET remarks = 'Passback Method' WHERE group_id = 9;

CREATE ROLE readonly;
GRANT SELECT ON m_cd TO readonly;
GRANT SELECT ON m_area_cd TO readonly;
CREATE ROLE "master" WITH LOGIN ENCRYPTED PASSWORD 'master' IN ROLE readonly;

--------------------
--Target Condition--
--------------------
INSERT INTO m_target_condition (category, display_text) VALUES ('SGAREA', 'Prefecture/Region');
INSERT INTO m_target_condition (category, display_text) VALUES ('GENDER', 'Gender');
INSERT INTO m_target_condition (category, display_text) VALUES ('MARRIAGE', 'Marriage Status');
INSERT INTO m_target_condition (category, display_text) VALUES ('AGE_MIN', 'Age');
INSERT INTO m_target_condition (category, display_text) VALUES ('AGE_MAX', 'Age');
INSERT INTO m_target_condition (category, display_text) VALUES ('TARGET_SIZE', 'Target Sample Size');

---------
--Table--
---------
CREATE TABLE m_creative_category(
	category_id			SERIAL		NOT NULL PRIMARY KEY,
	category_name		VARCHAR(64)	NOT NULL
);

CREATE TABLE m_creative_template(
	template_id			SERIAL		NOT NULL PRIMARY KEY,
	template_name		VARCHAR(64)	NOT NULL
);

CREATE TABLE m_category_attribute(
	attribute_id		SERIAL		NOT NULL,
	language_id			INTEGER		NOT NULL,
	category_id			INTEGER		NOT NULL,
	attribute_text		VARCHAR(32),
	display_flag		BOOLEAN		NOT NULL,
	PRIMARY KEY (attribute_id, language_id, category_id),
	FOREIGN KEY (category_id) REFERENCES m_creative_category (category_id)
);

CREATE TABLE m_creative_user(
	creative_user_id	SERIAL		NOT NULL PRIMARY KEY,
	email				VARCHAR(64)	NOT NULL,
	password			VARCHAR(32)	NOT NULL,
	first_name			VARCHAR(32)	NOT NULL,
	last_name			VARCHAR(32)	NOT NULL,
	job_title			VARCHAR(32),
	user_country		CHAR(2)		NOT NULL,
	company_name		VARCHAR(64),
	company_address		VARCHAR(256),
	contact_number		VARCHAR(24)	NOT NULL,
	industry			CHAR(3)		NOT NULL,
	account_status		CHAR(2)		NOT NULL,
	account_create_date	TIMESTAMP	NOT NULL DEFAULT current_timestamp,
	account_delete_date	TIMESTAMP,
	last_login_date		TIMESTAMP,
	FOREIGN KEY (user_country) REFERENCES m_country (country_id)
);

CREATE TABLE creative_project(
	creative_project_id	SERIAL		NOT NULL PRIMARY KEY,
	creative_user_id	INTEGER		NOT NULL,
	category_id			INTEGER		NOT NULL,
	template_id			INTEGER		NOT NULL,
	project_status		INTEGER		NOT NULL,
	launch_date			TIMESTAMP	NOT NULL,
	close_date			TIMESTAMP,
	create_date			TIMESTAMP	NOT NULL DEFAULT current_timestamp,
	update_date			TIMESTAMP	NOT NULL,
	FOREIGN KEY (creative_user_id) REFERENCES m_creative_user (creative_user_id),
	FOREIGN KEY (category_id) REFERENCES m_creative_category (category_id),
	FOREIGN KEY (template_id) REFERENCES m_creative_template (template_id)
);

CREATE TABLE creative_project_country_language(
	creative_project_id	INTEGER		NOT NULL,
	survey_country_id	VARCHAR(2)	NOT NULL,
	survey_language_id	INTEGER		NOT NULL,
	update_date			TIMESTAMP	NOT NULL,
	PRIMARY KEY (creative_project_id, survey_country_id, survey_language_id),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id),
	FOREIGN KEY (survey_country_id) REFERENCES m_country (country_id),
	FOREIGN KEY (survey_language_id) REFERENCES m_language (language_id)
);

CREATE TABLE m_target_condition(
	category		VARCHAR(32)		NOT NULL,
	display_text	VARCHAR(128)	NOT NULL,
	PRIMARY KEY (category)
);

CREATE TABLE creative_project_target_condition(
	creative_project_id	INTEGER		NOT NULL,
	category			VARCHAR(32)	NOT NULL,
	value				VARCHAR(128)NOT NULL,
	update_date			TIMESTAMP	NOT NULL,
	PRIMARY KEY (creative_project_id, category, value),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id),
	FOREIGN KEY (category) REFERENCES m_target_condition (category)
);

CREATE TABLE creative_project_attribute_question(
	creative_project_id	INTEGER		NOT NULL,
	question_id			SERIAL		NOT NULL,
	media_file			TEXT,
	PRIMARY KEY (creative_project_id, question_id),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id)
);

CREATE TABLE creative_project_image_question(
	creative_project_id	INTEGER		NOT NULL,
	question_id			SERIAL		NOT NULL,
	media_layout		CHAR(1),
	rating_type			CHAR(1),
	min_value			INTEGER,
	max_value			INTEGER,
	measurement_unit	VARCHAR(8),
	media_file_1		TEXT,
	media_file_2		TEXT,
	media_file_3		TEXT,
	media_file_4		TEXT,
	media_file_5		TEXT,
	update_date			TIMESTAMP	NOT NULL,
	PRIMARY KEY (creative_project_id, question_id),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id)
);

CREATE TABLE creative_project_attribute_answer(
	creative_project_id	INTEGER		NOT NULL,
	question_id			INTEGER		NOT NULL,
	uid					VARCHAR(16)	NOT NULL,
	attribute_id		INTEGER		NOT NULL,
	update_date			TIMESTAMP	NOT NULL,
	PRIMARY KEY (creative_project_id, question_id, uid, attribute_id),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id),
	FOREIGN KEY (creative_project_id, question_id) REFERENCES creative_project_attribute_question (creative_project_id, question_id)
);

CREATE TABLE creative_project_image_answer(
	creative_project_id	INTEGER		NOT NULL,
	question_id			INTEGER		NOT NULL,
	uid					VARCHAR(16)	NOT NULL,
	media_file_rate_1	TEXT		NOT NULL,
	media_file_rate_2	TEXT		NOT NULL,
	media_file_rate_3	TEXT		NOT NULL,
	media_file_rate_4	TEXT		NOT NULL,
	media_file_rate_5	TEXT		NOT NULL,
	update_date			TIMESTAMP	NOT NULL,
	PRIMARY KEY (creative_project_id, question_id, uid),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id),
	FOREIGN KEY (creative_project_id, question_id) REFERENCES creative_project_attribute_question (creative_project_id, question_id)
);

CREATE TABLE creative_project_question_by_language(
	creative_project_id	INTEGER		NOT NULL,
	question_id			INTEGER		NOT NULL,
	language_id			INTEGER		NOT NULL,
	question_text		TEXT		NOT NULL,
	PRIMARY KEY (creative_project_id, question_id, language_id),
	FOREIGN KEY (creative_project_id) REFERENCES creative_project (creative_project_id),
	FOREIGN KEY (language_id) REFERENCES m_language (language_id)
);

CREATE TABLE source_message(
    id					SERIAL		NOT NULL,
    category			VARCHAR(32)	NOT NULL,
    message				TEXT		NOT NULL,
	PRIMARY KEY (id, category)
);

CREATE TABLE message(
    id					SERIAL		NOT NULL,
	category			VARCHAR(32)	NOT NULL,
    language			VARCHAR(2)	NOT NULL,
    translation			TEXT		NOT NULL,
    PRIMARY KEY (id, category, language),
    FOREIGN KEY (id, category) REFERENCES source_message (id, category) ON DELETE CASCADE ON UPDATE RESTRICT
);

---------------
--Audit Table--
---------------
CREATE TABLE a_creative_project(
	audit_creative_project_id	SERIAL		NOT NULL PRIMARY KEY,
	creative_project_id			INTEGER		NOT NULL,
	creative_user_id			INTEGER		NOT NULL,
	category_id					INTEGER		NOT NULL,
	template_id					INTEGER		NOT NULL,
	project_status				INTEGER		NOT NULL,
	launch_date					TIMESTAMP	NOT NULL,
	close_date					TIMESTAMP,
	create_date					TIMESTAMP	NOT NULL,
	update_date					TIMESTAMP	NOT NULL,
	audit_action				VARCHAR(6)	NOT NULL,
	audit_create_date			TIMESTAMP	NOT NULL DEFAULT current_timestamp,
	FOREIGN KEY (creative_user_id) REFERENCES m_creative_user (creative_user_id),
	FOREIGN KEY (category_id) REFERENCES m_creative_category (category_id),
	FOREIGN KEY (template_id) REFERENCES m_creative_template (template_id)
);

--------------
--Privileges--
--------------
GRANT ALL PRIVILEGES ON m_creative_category TO creative;
GRANT ALL PRIVILEGES ON m_creative_template TO creative;
GRANT ALL PRIVILEGES ON m_creative_user TO creative;
GRANT ALL PRIVILEGES ON creative_project TO creative;
GRANT ALL PRIVILEGES ON a_creative_project TO creative;

GRANT ALL PRIVILEGES ON m_creative_category TO creative;
GRANT ALL PRIVILEGES ON m_creative_template TO creative;
GRANT ALL PRIVILEGES ON m_target_condition TO creative;
GRANT ALL PRIVILEGES ON m_creative_user TO creative;
GRANT ALL PRIVILEGES ON creative_project TO creative;
GRANT ALL PRIVILEGES ON creative_project_attribute_question TO creative;
GRANT ALL PRIVILEGES ON creative_project_image_question TO creative;
GRANT ALL PRIVILEGES ON m_category_attribute TO creative;
GRANT ALL PRIVILEGES ON creative_project_question_by_language TO creative;
GRANT ALL PRIVILEGES ON creative_project_target_condition TO creative;
GRANT ALL PRIVILEGES ON creative_project_attribute_answer TO creative;
GRANT ALL PRIVILEGES ON creative_project_country_language TO creative;
GRANT ALL PRIVILEGES ON creative_project_image_answer TO creative;
GRANT ALL PRIVILEGES ON a_creative_project TO creative;

GRANT ALL PRIVILEGES ON m_group TO creative;
GRANT ALL PRIVILEGES ON m_code TO creative;