UPDATE wp_pwgb_options SET option_value = replace(option_value, 'http://www.tec-check.com.mx', 'http://18.216.182.173') WHERE option_name = 'home' OR option_name = 'siteurl';

UPDATE wp_pwgb_posts SET guid = replace(guid, 'http://www.tec-check.com.mx','http://18.216.182.173');

UPDATE wp_pwgb_posts SET post_content = replace(post_content, 'http://www.tec-check.com.mx', 'http://18.216.182.173');

UPDATE wp_pwgb_postmeta SET meta_value = replace(meta_value,'http://www.tec-check.com.mx','http://18.216.182.173');
