/*
UPDATE wp_pwgb_options SET option_value = replace(option_value, 'http://localhost:8000', 'http://localhost/teccheck') WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE wp_pwgb_posts SET guid = replace(guid, 'http://localhost:8000','http://localhost/teccheck');

UPDATE wp_pwgb_posts SET post_content = replace(post_content, 'http://localhost:8000', 'http://localhost/teccheck');

UPDATE wp_pwgb_postmeta SET meta_value = replace(meta_value,'http://localhost:8000','http://localhost/teccheck');

UPDATE wp_pwgb_options SET option_value = replace(option_value, 'http://tec-check.com.mx', 'http://localhost/teccheck') WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE wp_pwgb_posts SET guid = replace(guid, 'http://tec-check.com.mx','http://localhost/teccheck');

UPDATE wp_pwgb_posts SET post_content = replace(post_content, 'http://tec-check.com.mx', 'http://localhost/teccheck');

UPDATE wp_pwgb_postmeta SET meta_value = replace(meta_value,'http://tec-check.com.mx','http://localhost/teccheck');
*/

UPDATE wp_pwgb_options SET option_value = replace(option_value, 'http://tec-check.com.mx', 'http://localhost:8000') WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE wp_pwgb_posts SET guid = replace(guid, 'http://tec-check.com.mx','http://localhost:8000');

UPDATE wp_pwgb_posts SET post_content = replace(post_content, 'http://tec-check.com.mx', 'http://localhost:8000');

UPDATE wp_pwgb_postmeta SET meta_value = replace(meta_value,'http://tec-check.com.mx','http://localhost:8000');

UPDATE wp_pwgb_options SET option_value = replace(option_value, 'http://localhost/teccheck', 'http://localhost:8000') WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE wp_pwgb_posts SET guid = replace(guid, 'http://localhost/teccheck','http://localhost:8000');

UPDATE wp_pwgb_posts SET post_content = replace(post_content, 'http://localhost/teccheck', 'http://localhost:8000');

UPDATE wp_pwgb_postmeta SET meta_value = replace(meta_value,'http://localhost/teccheck','http://localhost:8000');