<?php 

ini_set('max_execution_time', 300);

function wp_install_defaults($user_id) {global $wpdb, $wp_rewrite, $current_site, $table_prefix;
$selected_plugins = array (
0 => 'akismet',
1 => 'contact-form-7',
2 => 'wordpress-seo',
3 => 'ultimate-tinymce',
4 => 'facebook',
5 => 'si-captcha-for-wordpress',
6 => 'custom-permalinks',
7 => 'link-juice-keeper',
);

$selected_theme = "360optimalegalcouk";	// Default category

$randomCats = array('General','Posts','Wordpress','Stuff','Uncategorized','Some Posts','My Posts','Many Posts','News','Articles','Blogs','Blog Posts','The Posts','Some Stuff','Press','Release','Ramblings','Opinions','OK');



$cat_name = __($randomCats[rand(0,count($randomCats)-1)]);

/* translators: Default category slug */
$cat_slug = sanitize_title(_x('General', 'Default category slug'));

if ( global_terms_enabled() ) {
	$cat_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );
	if ( $cat_id == null ) {
		$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
		$cat_id = $wpdb->insert_id;
	}
	update_option('default_category', $cat_id);
} else {
	$cat_id = 1;
}

$wpdb->insert( $wpdb->terms, array('term_id' => $cat_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $cat_id, 'taxonomy' => 'category', 'description' => '', 'parent' => 0, 'count' => 1));
$cat_tt_id = $wpdb->insert_id;

// Default link category
$cat_name = __('Blogroll');
/* translators: Default link category slug */
$cat_slug = sanitize_title(_x('Blogroll', 'Default link category slug'));

if ( global_terms_enabled() ) {
	$blogroll_id = $wpdb->get_var( $wpdb->prepare( "SELECT cat_ID FROM {$wpdb->sitecategories} WHERE category_nicename = %s", $cat_slug ) );
	if ( $blogroll_id == null ) {
		$wpdb->insert( $wpdb->sitecategories, array('cat_ID' => 0, 'cat_name' => $cat_name, 'category_nicename' => $cat_slug, 'last_updated' => current_time('mysql', true)) );
		$blogroll_id = $wpdb->insert_id;
	}
	update_option('default_link_category', $blogroll_id);
} else {
	$blogroll_id = 2;
}

$wpdb->insert( $wpdb->terms, array('term_id' => $blogroll_id, 'name' => $cat_name, 'slug' => $cat_slug, 'term_group' => 0) );
$wpdb->insert( $wpdb->term_taxonomy, array('term_id' => $blogroll_id, 'taxonomy' => 'link_category', 'description' => '', 'parent' => 0, 'count' => 7));
$blogroll_tt_id = $wpdb->insert_id;

// Now drop in some default links
$default_links = array();
//$default_links[] = array(	'link_url' => 'http://codex.wordpress.org/',
//'link_name' => 'Documentation',
//'link_rss' => '',
//'link_notes' => '');
//
//$default_links[] = array(	'link_url' => 'http://wordpress.org/news/',
//'link_name' => 'WordPress Blog',
//'link_rss' => 'http://wordpress.org/news/feed/',
//'link_notes' => '');
//
//$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/ideas/',
//'link_name' => 'Suggest Ideas',
//'link_rss' => '',
//'link_notes' =>'');
//
//$default_links[] = array(	'link_url' => 'http://wordpress.org/support/',
//'link_name' => 'Support Forum',
//'link_rss' => '',
//'link_notes' =>'');
//
//$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/plugins/',
//'link_name' => 'Plugins',
//'link_rss' => '',
//'link_notes' =>'');
//
//$default_links[] = array(	'link_url' => 'http://wordpress.org/extend/themes/',
//'link_name' => 'Themes',
//'link_rss' => '',
//'link_notes' =>'');
//
//$default_links[] = array(	'link_url' => 'http://planet.wordpress.org/',
//'link_name' => 'WordPress Planet',
//'link_rss' => '',
//'link_notes' =>'');
//$default_links[] = array(	'link_url' => 'http://www.wpkgr.com/',
//'link_name' => 'WPkgr',
//'link_rss' => '',
//'link_notes' =>'');
//
//$default_links[] = array(	'link_url' => 'http://www.thewebsitecreator.com/',
//'link_name' => 'The Website Creator',
//'link_rss' => '',
//'link_notes' =>'');
//foreach ( $default_links as $link ) {
//	$wpdb->insert( $wpdb->links, $link);
//	$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => $blogroll_tt_id, 'object_id' => $wpdb->insert_id) );
//}



// First post

//change the start ID of the posts
$wpdb->query( "ALTER TABLE wp_posts AUTO_INCREMENT=1001" );


$now = date('Y-m-d H:i:s');
$now_gmt = gmdate('Y-m-d H:i:s');
$first_post_guid = get_option('home') . '/?p=1001';

if ( is_multisite() ) {
	$first_post = get_site_option( 'first_post' );

	if ( empty($first_post) )
	$first_post = stripslashes( __( 'Welcome to <a href="SITE_URL">SITE_NAME</a>. This is your first post. Edit or delete it, then start blogging!' ) );

	$first_post = str_replace( "SITE_URL", esc_url( network_home_url() ), $first_post );
	$first_post = str_replace( "SITE_NAME", $current_site->site_name, $first_post );
} else {

	$first_post = __('Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!');


}

update_option( 'default_comment_status', 'closed' );



$wpdb->insert( $wpdb->posts, array(
'post_author' => $user_id,
'post_date' => $now,
'post_date_gmt' => $now_gmt,
'post_content' => $first_post,
'post_excerpt' => '',
'post_title' => __('A Test Post Title Here'),

'post_name' => sanitize_title( _x('a-test-post-title-here', 'Default post slug') ),
'post_modified' => $now,
'post_modified_gmt' => $now_gmt,
'guid' => $first_post_guid,
'comment_count' => 1,
'to_ping' => '',
'pinged' => '',
'post_content_filtered' => ''
));

$wpdb->insert( $wpdb->posts, array(
'post_author' => $user_id,
'post_date' => $now,
'post_date_gmt' => $now_gmt,
'post_content' => $first_post,
'post_excerpt' => '',
'post_title' => __('A Second Test Post Title Here'),

'post_name' => sanitize_title( _x('a-second-test-post-title-here', 'Default post slug') ),
'post_modified' => $now,
'post_modified_gmt' => $now_gmt,
'guid' => $first_post_guid = get_option('home') . '/?p=1002',
'comment_count' => 1,
'to_ping' => '',
'pinged' => '',
'post_content_filtered' => ''
));


$wpdb->insert( $wpdb->term_relationships, array('term_taxonomy_id' => $cat_tt_id, 'object_id' => 1) );

// Default comment
//$first_comment_author = __('Mr WordPress');
//$first_comment_url = 'http://wordpress.org/';
//$first_comment = __('Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.');
//if ( is_multisite() ) {
//
//	$first_comment_author = get_site_option( 'first_comment_author', $first_comment_author );
//	$first_comment_url = get_site_option( 'first_comment_url', network_home_url() );
//	$first_comment = get_site_option( 'first_comment', $first_comment );
//}

/*
$wpdb->insert( $wpdb->comments, array(
'comment_post_ID' => 1,
'comment_author' => $first_comment_author,
'comment_author_email' => '',
'comment_author_url' => $first_comment_url,
'comment_date' => $now,
'comment_date_gmt' => $now_gmt,
'comment_content' => $first_comment
));
*/




// First Page
$first_page = sprintf( __( "" ), admin_url() );
if ( is_multisite() )
$first_page = get_site_option( 'first_page', $first_page );

$first_post_guid = get_option('home') . '/?page_id=1003';

$pageData = array(
'post_author' => $user_id,
'post_date' => $now,
'post_date_gmt' => $now_gmt,
'post_content' => $first_page,
'post_excerpt' => '',
'post_title' => __( 'Home' ),
/* translators: Default page slug */
'post_name' => __( 'home' ),
'post_modified' => $now,
'post_modified_gmt' => $now_gmt,
'guid' => $first_post_guid,
'post_type' => 'page',
'to_ping' => '',
'pinged' => '',
'post_content_filtered' => '',

);

$wpdb->insert( $wpdb->posts, $pageData);
$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 1003, 'meta_key' => '_wp_page_template', 'meta_value' => 'front-page.php' ) );


$pageData['post_title'] =  __( 'Blog' );
$pageData['post_name'] =  __( 'blog' );
$pageData['guid'] = get_option('home') . '/?page_id=1004';

$wpdb->insert( $wpdb->posts, $pageData);
$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 1004, 'meta_key' => '_wp_page_template', 'meta_value' => 'default' ) );


$emailNames = array('Aeneas','Amadeus','Andreas','Antonius','Apollos','Atticus','Augustus','Aurelius','Caesar','Caius','Cassius','Cato','Cicero','Claudius','Cornelius','Cosmo','Cyrus','Decimus','Demetrius','Felix','Flavius','Gaius','Horatio','Justus','Lazarus','Lucius','Magnus','Marcellus','Marcus','Marius','Maximus','Nero','Octavius','Philo','Primus','Quintus','Remus','Romanus','Romulus','Rufus','Seneca','Septimus','Severus','Stephanus','Tarquin','Theon','Thor','Tiberius','Titus','Urban','Abiel','Abijah','Abimael','Abner','Abraham','Absalom','Adonijah','Alden','Amias','Amiel','Ammiras','Amos','Amzi','Archibald','Asa','Asahel','Azariah','Balthasar','Barnabas','Bartholomew','Bazel','Benajah','Clement','Comfort','Constant','Cotton','Cyrus','Ebenezer','Eleazar','Eli','Eliab','Eliakim','Elias','Elihu','Elijah','Eliphalet','Elisha','Emanuel','Emory','Enoch','Experience','Ezekiel','Gideon','Hannibal','Hezekiah','Hiram','Homer','Horatio','Hosea','Increase','Isaac','Isaiah','Isham','Israel','Jared','Jedidiah','Jehu','Jeremiah','Jethro','Jonas','Josiah','Jothan','Lazarus','Lemuel','Levi','Linus','Micajah','Nehemiah','Obadiah','Philo','Philomon','Phineas','Prosperity','Reason','Rufus','Salmon','Sampson','Seth','Solomon','Thaddeus','Theophilus','Truth','Zaccheus','Zachariah','Zadock','Zebulon','Zephaniah','Zophar','Agatha','Anais','Axel','Boden','Brooks','Carter','Dante','Eliane','Florence','Gus','Hanna','Hardy','Haven','Hudson','Hurley','Iris','Ivy','Jac','Joah','Joe','Juliette','Kai','Kingsley','Kit','Lena','Levi','Lilly','Luca','Lulu','Maggie','Marc','Mimi','Ned','Olive','Paul','Petunia','Pippa','Quincy','Ralph','Serena','Sid','Sisley','Stella','Sven','Vincent','Wes','Zella','Adelaide','Alan','Alice','Anastasia','Angel','Anita','Ariel','Aurora','Bambi','Belle','Bernard','Bianca','Bruno','Brutus','Cara','Carlotta','Cinderella','Cody','Daisy','Donald','Duke','Edgar','Ella','Ellie','Ena','Eric','Esmerelda','Faline','Fantasia','Flora','Flower','Flynt','Gaston','Gus','Hercules','Horace','Ichabod','Jacques','Jane','Jaq','Jasper','Jiminy','Joanna','John','Leah','Lilo','Major','Marie','Maurice','Max','Michael','Mickey','Minnie','Molly','Mowgli','Nala','Oliver','Orville','Peg','Penny','Percy','Peter','Phillip','Phillipe','Pierre','Prudence','Red','Robin','Roger','Ruby','Rufus','Samson','Sarah','Sebastian','Simba','Snow','Stefan','Thomas','Tigerlily','Timothy','Tremaine','Ursula','Wendy','Wilbur','Zazu','Abel','Africa','Apollo','Athena','Beach','Boston','Caesar','Caspar','Cassandra','Cato','Cela','Charity','Christmas','Comfort','Cupid','Daphne','Desdemona','Dido','Dinah','Dolly','Dorinda','Easter','Flavia','Flora','Hagar','Hector','Ishmael','Jamaica','Jemima','Jonah','Juba','Jupiter','Keziah','London','Lucinda','Lysander','March','Minerva','Moses','Nero','Pascal','Phillis','Phoebe','Pompey','Sabina','Sable','Scipio','Spencer','Sukey','Tamar','Temperance','Tyra','Venus','Wesley','York','Amelia','Amidala','Anastasia','Anneliese','Ariel','Arwen','Ashlyn','Aurora','Blair','Briar Rose','Buttercup','Calliope','Celestia','Cinderella','Courtney','Daenerys','Daisy','Danielle','Delia','Diana','Edeline','Elia','Ella','Eowyn','Fallon','Fiona','Genevieve','Giselle','Gwenevere','Hadley','Hazel','Irene','Isabelle','Isla','Ithaca','Janessa','Jasmine','Kathleen','Lacey','Leia','Luna','Marie','Merida','Mignonette','Millie','Moanna','Myrcella','Odette','Ozma','Padme','Peach','Pocahontas','Rapunzel','Rosalina','Snow White','Sofia','Tigerlily','Xena','Yue','Zelda','Alonzo','Althea','Alvah','Angel','Axel','Barby','Barton','Bowie','Brandy','Brigid','Bruno','Calder','Candy','Carmen','Celia','Cicily','Cleve','Cora','Crane','Dell','Della','Dix','Dixon','Eden','Edwina','Effie','Evangelene','Fae','Fritzie','Georgette','Gilda','Gilly','Greta','Guy','Hardy','Hatti','Holiday','Ida','Jojo','Kasper','Kitty','Lash','Leona','Leonora','Leslie','Lilah','Lilith','Lola','Lucia','Maxim','Merl','Meta','Miles','Millie','Mona','Monte','Morgan','Nita','Noel','Noll','Nora','Paulette','Pike','Poppy','Rex','Rica','Shelby','Smith','Stella','Storm','Teena','Torsten','Veda','Velma','Venus','Vera','Verna','Vivian','Waldo','Walter','Ward','Webb','Willa','Wilma','Zeena','Ziggy');

$mailProviders = array('hotmail.com','live.com','msn.com','yahoo.com','gmail.com');

$pageData['post_title'] =  __( 'Contact' );
$pageData['post_name'] =  __( 'contact' );
$pageData['guid'] = get_option('home') . '/?page_id=1005';
$pageData['post_content'] = 'Contact us at '.$emailNames[rand(0,count($emailNames)-1)].rand(1980,2013).'@'.$mailProviders[rand(0,count($mailProviders)-1)];



$wpdb->insert( $wpdb->posts, $pageData);
$wpdb->insert( $wpdb->postmeta, array( 'post_id' => 1005, 'meta_key' => '_wp_page_template', 'meta_value' => 'default' ) );





// Set up default widgets for default theme.
//update_option( 'widget_search', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
//update_option( 'widget_recent-posts', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );
//update_option( 'widget_recent-comments', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );
//update_option( 'widget_archives', array ( 2 => array ( 'title' => '', 'count' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
//update_option( 'widget_categories', array ( 2 => array ( 'title' => '', 'count' => 0, 'hierarchical' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
//update_option( 'widget_meta', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
//update_option( 'sidebars_widgets', array ( 'wp_inactive_widgets' => array ( ), 'primary-widget-area' => array ( 0 => 'search-2', 1 => 'recent-posts-2', 2 => 'recent-comments-2', 3 => 'archives-2', 4 => 'categories-2', 5 => 'meta-2', ), 'secondary-widget-area' => array ( ), 'first-footer-widget-area' => array ( ), 'second-footer-widget-area' => array ( ), 'third-footer-widget-area' => array ( ), 'fourth-footer-widget-area' => array ( ), 'array_version' => 3 ) );

if ( is_multisite() ) {
	// Flush rules to pick up the new page.
	$wp_rewrite->init();
	$wp_rewrite->flush_rules();

	$user = new WP_User($user_id);
	$wpdb->update( $wpdb->options, array('option_value' => $user->user_email), array('option_name' => 'admin_email') );

	// Remove all perms except for the login user.
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'user_level') );
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id != %d AND meta_key = %s", $user_id, $table_prefix.'capabilities') );

	// Delete any caps that snuck into the previously active blog. (Hardcoded to blog 1 for now.) TODO: Get previous_blog_id.
	if ( !is_super_admin( $user_id ) && $user_id != 1 )
	$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $wpdb->base_prefix.'1_capabilities') );
}
//foreach ($selected_plugins as $plugin) {
//
//	$request = new StdClass();
//	$request->slug = stripslashes($plugin);
//	$post_data = array(
//	'action' => 'plugin_information',
//	'request' => serialize($request)
//	);
//
//	$options = array(
//	CURLOPT_URL => 'http://api.wordpress.org/plugins/info/1.0/',
//	CURLOPT_POST => true,
//	CURLOPT_POSTFIELDS => $post_data,
//	CURLOPT_RETURNTRANSFER => true
//	);
//	$handle = curl_init();
//	curl_setopt_array($handle, $options);
//	$response = curl_exec($handle);
//	curl_close($handle);
//	$plugin_info = unserialize($response);
//	echo "Downloading and Extracting $plugin_info->name<br />";
//
//	$file = basename($plugin_info->download_link);
//
//	$fp = fopen($file,'w');
//
//	$ch = curl_init();
//	curl_setopt($ch, CURLOPT_USERAGENT, 'WPKGR');
//	curl_setopt($ch, CURLOPT_URL, $plugin_info->download_link);
//	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
//	curl_setopt($ch, CURLOPT_HEADER, 0);
//	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
//	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
//	curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
//	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//	curl_setopt($ch, CURLOPT_FILE, $fp);
//	$b = curl_exec($ch);
//
//	if (!$b) {
//		$message = 'Download error: '. curl_error($ch) .', please try again';
//		curl_close($ch);
//		throw new Exception($message);
//	}
//
//	fclose($fp);
//
//	if (!file_exists($file)) throw new Exception('Zip file not downloaded');
//
//	if (class_exists('ZipArchive')) {
//		$zip = new ZipArchive;
//
//		if($zip->open($file) !== TRUE) throw new Exception('Unable to open Zip file');
//
//		$zip->extractTo(ABSPATH . 'wp-content/plugins/');
//
//		$zip->close();
//	}
//	else {
//		// try unix shell command
//		@shell_exec('unzip -d ../wp-content/plugins/ '. $file);
//	}
//	unlink($file);
//	echo "<strong>Done!</strong><br />";
//}

//if($selected_theme != '') {
//	$request = new StdClass();
//	$request->slug = stripslashes($selected_theme);
//	$post_data = array(
//	'action' => 'theme_information',
//	'request' => serialize($request)
//	);
//	$options = array(
//	CURLOPT_URL => 'http://api.wordpress.org/themes/info/1.0/',
//	CURLOPT_POST => true,
//	CURLOPT_POSTFIELDS => $post_data,
//	CURLOPT_RETURNTRANSFER => true
//	);
//	$handle = curl_init();
//	curl_setopt_array($handle, $options);
//	$response = curl_exec($handle);
//	curl_close($handle);
//	$theme_info = unserialize($response);
//	echo "Downloading and Extracting $theme_info->name<br />";
//
//	$file = basename($theme_info->download_link);
//
//	$fp = fopen($file,'w');
//
//	$ch = curl_init();
//	curl_setopt($ch, CURLOPT_USERAGENT, 'WPKGR');
//	curl_setopt($ch, CURLOPT_URL, $theme_info->download_link);
//	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
//	curl_setopt($ch, CURLOPT_HEADER, 0);
//	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
//	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
//	curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
//	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//	curl_setopt($ch, CURLOPT_FILE, $fp);
//	$b = curl_exec($ch);
//
//	if (!$b) {
//		$message = 'Download error: '. curl_error($ch) .', please try again';
//		curl_close($ch);
//		throw new Exception($message);
//	}
//
//	fclose($fp);
//
//	if (!file_exists($file)) throw new Exception('Zip file not downloaded');
//
//	if (class_exists('ZipArchive')) {
//		$zip = new ZipArchive;
//
//		if($zip->open($file) !== TRUE) throw new Exception('Unable to open Zip file');
//
//		$zip->extractTo(ABSPATH . 'wp-content/themes/');
//
//		$zip->close();
//	}
//	else {
//		// try unix shell command
//		@shell_exec('unzip -d ../wp-content/themes/ '. $file);
//	}
//	unlink($file);
//	echo "<strong>Done!</strong><br />";
//
//}//if theme

function run_activate_plugin( $plugin ) {
	$current = get_option( 'active_plugins' );
	$plugin = plugin_basename( trim( $plugin ) );
	$current[] = $plugin;
	sort( $current );
	do_action( 'activate_plugin', trim( $plugin ) );
	update_option( 'active_plugins', $current );
	do_action( 'activate_' . trim( $plugin ) );
	do_action( 'activated_plugin', trim( $plugin) );
}




foreach ($selected_plugins as $plugin) {
	$request = new StdClass();
	$request->slug = stripslashes($plugin);
	$post_data = array(
	'action' => 'plugin_information',
	'request' => serialize($request)
	);
	$options = array(
	CURLOPT_URL => 'http://api.wordpress.org/plugins/info/1.0/',
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $post_data,
	CURLOPT_RETURNTRANSFER => true
	);
	$handle = curl_init();
	curl_setopt_array($handle, $options);
	$response = curl_exec($handle);
	curl_close($handle);
	$plugin_info = unserialize($response);
	$daplugins = get_plugins( '/' . $plugin_info->slug );
	$paths = array_keys($daplugins);
	$plugin_file = $plugin_info->slug . '/' . $paths[0];
	run_activate_plugin($plugin_file);
}

if($selected_theme != '') {
	//ENABLE THEME
	update_option( 'template', $selected_theme );
	update_option( 'stylesheet', $selected_theme );
}






$res_aioseop_options = array(
"aiosp_can"=>1,
"aiosp_donate"=>0,
"aiosp_home_title"=>null,
"aiosp_home_description"=>'',
"aiosp_home_keywords"=>null,
"aiosp_max_words_excerpt"=>'something',
"aiosp_rewrite_titles"=>1,
"aiosp_post_title_format"=>'%post_title% ',
"aiosp_page_title_format"=>'%page_title%',
"aiosp_category_title_format"=>'%category_title% | %blog_title%',
"aiosp_archive_title_format"=>'%date% | %blog_title%',
"aiosp_tag_title_format"=>'%tag% | %blog_title%',
"aiosp_search_title_format"=>'%search% | %blog_title%',
"aiosp_description_format"=>'%description%',
"aiosp_404_title_format"=>'Nothing found for %request_words%',
"aiosp_paged_format"=>' - Part %page%',
"aiosp_google_analytics_id"=>null,
"aiosp_ga_domain"=>'',
"aiosp_ga_multi_domain"=>0,
"aiosp_ga_track_outbound_links"=>0,
"aiosp_google_publisher"=>'',
"aiosp_use_categories"=>0,
"aiosp_dynamic_postspage_keywords"=>1,
"aiosp_category_noindex"=>0,
"aiosp_archive_noindex"=>0,
"aiosp_tags_noindex"=>0,
"aiosp_cap_cats"=>1,
"aiosp_generate_descriptions"=>1,
"aiosp_debug_info"=>null,
"aiosp_post_meta_tags"=>'',
"aiosp_enablecpost"=>'0',
"aiosp_page_meta_tags"=>'',
"aiosp_home_meta_tags"=>'',
"aiosp_front_meta_tags"=>'',
"aiosp_enabled" =>1,
"aiosp_use_tags_as_keywords" =>1,
"aiosp_seopostcol" => 1,
"aiosp_seocustptcol" => 0,
"aiosp_posttypecolumns" => array('post','page'),
"aiosp_do_log"=>null);


update_option('aioseop_options',$res_aioseop_options);

$permalink_structure = "/%postname%/";

update_option( 'permalink_structure', '/%postname%/' );

$prefix = $blog_prefix = '';
if ( ! empty( $permalink_structure ) ) {

	$permalink_structure = preg_replace( '#/+#', '/', '/' . str_replace( '#', '', $permalink_structure ) );

	if ( $prefix && $blog_prefix )
	$permalink_structure = $prefix . preg_replace( '#^/?index\.php#', '', $permalink_structure );
	else
	$permalink_structure = $blog_prefix . $permalink_structure;
}
$wp_rewrite->set_permalink_structure( $permalink_structure );



//update_option( 'enable_xmlrpc', '1' );


//GET /wp-admin/admin.php?page=wpseo_dashboard&allow_tracking=no&nonce=4fa908b2a5 HTTP/1.1



//POST /wp-admin/admin-ajax.php HTTP/1.1

//action=wpseo_set_ignore&option=tour&_wpnonce=a329817a4e

$options     = get_option( 'wpseo' );

$options['tracking_popup'] = 'done';
$options['yoast_tracking'] = 'off';
$options['ignore_tour'] = 'ignore';
update_option( 'wpseo', $options );

//$options = get_option( 'sidebars_widgets' );
//$options['sidebar-1'][] = 'recent-posts-3';
//$options['sidebar-1'][] = 'archives-3';
//$options['sidebar-2'] = array();
//$options['sidebar-3'] = array();
//
//update_option( 'sidebars_widgets', $options );


// update_option( 'widget_search', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
 update_option( 'widget_recent-posts', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );
// update_option( 'widget_recent-comments', array ( 2 => array ( 'title' => '', 'number' => 5 ), '_multiwidget' => 1 ) );

 update_option( 'widget_archives', array ( 2 => array ( 'title' => '', 'count' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
 
// update_option( 'widget_categories', array ( 2 => array ( 'title' => '', 'count' => 0, 'hierarchical' => 0, 'dropdown' => 0 ), '_multiwidget' => 1 ) );
// 
// update_option( 'widget_meta', array ( 2 => array ( 'title' => '' ), '_multiwidget' => 1 ) );
 
// update_option( 'sidebars_widgets', array ( 'wp_inactive_widgets' => array ( ), 'sidebar-1' => array ( 0 => 'search-2', 1 => 'recent-posts-2', 2 => 'recent-comments-2', 3 => 'archives-2', 4 => 'categories-2', 5 => 'meta-2',), 'sidebar-2' => array ( ), 'sidebar-3' => array ( ), 'sidebar-4' => array ( ), 'sidebar-5' => array ( ), 'array_version' => 3 ) );
 
  update_option( 'sidebars_widgets', array ( 'wp_inactive_widgets' => array ( ), 'sidebar-1' => array (  0 => 'recent-posts-2',1 => 'archives-2'), 'sidebar-2' => array ( ), 'sidebar-3' => array ( ), 'sidebar-4' => array ( ), 'sidebar-5' => array ( ), 'array_version' => 3 ) );
  
  //update default blog page
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', '1003' );
update_option( 'page_for_posts', '1004' );
update_option( 'blogdescription', '' );
update_option( 'posts_per_page', '25' );


wp_cache_flush();

unlink(ABSPATH . 'wp-content/install.php');

}




//overwrite the wordpress htaccess with our wbd one

//$htaccessFileWDB = "../.htaccess_wbd";
//
//$htaccess = "../.htaccess";
//
//unlink($htaccess);
//
//rename($htaccessFileWDB,$htaccess);

//need to create htaccess for this

//$home_path = get_home_path();
//
//if ( ( ! file_exists($home_path . '.htaccess') && is_writable($home_path) ) || is_writable($home_path . '.htaccess') ) {
//
//	$htaccessContent = "# BEGIN WordPress
//<IfModule mod_rewrite.c>
//RewriteEngine On
//RewriteBase /
//RewriteCond %{REQUEST_FILENAME} !-f
//RewriteCond %{REQUEST_FILENAME} !-d
//RewriteRule . /index.php [L]
//
//
//</IfModule>
//
//# END WordPress
//";
//
//	$fp = fopen($home_path . '.htaccess');
//
//	fwrite($fp,$htaccessContent);
//	fclose($fp);
//
//}

?>