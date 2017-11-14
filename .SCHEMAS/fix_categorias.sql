UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687817011' WHERE `term_id`='68';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687814011' WHERE `term_id`='81';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687857011' WHERE `term_id`='40';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687804011' WHERE `term_id`='43';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687460011' WHERE `term_id`='50';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='16354823011' WHERE `term_id`='29';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='10189676011' WHERE `term_id`='46';
UPDATE `teccheck_wordpress`.`wp_pwgb_terms` SET `browse_node_amazon`='9687950011' WHERE `term_id`='9';

SET SQL_SAFE_UPDATES = 0;
DELETE FROM wp_pwgb_terms WHERE term_id in (334,311,309,387);

DELETE FROM wp_pwgb_term_taxonomy WHERE  term_id in (334,311,309,387);

