--------
--View--
--------
DROP VIEW IF EXISTS m_cd;

---------------
--Audit Table--
---------------
DROP TABLE IF EXISTS a_creative_project;

---------
--Table--
---------
DROP TABLE IF EXISTS m_code;
DROP TABLE IF EXISTS m_group;

DROP TABLE IF EXISTS creative_project_target_condition;
DROP TABLE IF EXISTS creative_project_country_language;
DROP TABLE IF EXISTS creative_project;
DROP TABLE IF EXISTS m_creative_user;
DROP TABLE IF EXISTS m_category_attribute;
DROP TABLE IF EXISTS m_creative_category;
DROP TABLE IF EXISTS m_creative_template;
DROP TABLE IF EXISTS m_target_condition;
DROP TABLE IF EXISTS creative_project_question_by_language;
DROP TABLE IF EXISTS creative_project_attribute_answer;
DROP TABLE IF EXISTS creative_project_image_answer;
DROP TABLE IF EXISTS creative_project_attribute_question;
DROP TABLE IF EXISTS creative_project_image_question;

-----------
--Trigger--
-----------
DROP TRIGGER IF EXISTS after_creative_project_insert ON creative_project;
DROP TRIGGER IF EXISTS before_creative_project_update ON creative_project;
DROP TRIGGER IF EXISTS before_creative_project_delete ON creative_project;

------------
--Function--
------------
DROP function IF EXISTS creativeProjectInsertFunction();
DROP function IF EXISTS creativeProjectUpdateFunction();
DROP function IF EXISTS creativeProjectDeleteFunction();