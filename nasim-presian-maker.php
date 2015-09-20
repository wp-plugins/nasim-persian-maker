<?php 
/**
* Plugin Name: فارسی ساز نسیم
* Plugin URI: http://nasimnet.ir
* Description: با این افزونه به راحتی می تواید پوسته خود را فارسی و راست چین کنید.
* Version: 2.1
* Author: NasimNet
* Author URI: http://nasimnet.ir
* License: GPL2
*/
if ( ! defined( 'ABSPATH' ) ) { die; } 
// ------------------------------------------------------------------------------------------------
include_once dirname( __FILE__ ) .'/cs-framework-path.php';
// ------------------------------------------------------------------------------------------------

if( ! function_exists( 'cs_framework_init' ) && ! class_exists( 'CSFramework' ) ) {
  function cs_framework_init() {

    // active modules
    defined( 'CS_ACTIVE_FRAMEWORK' )  or  define( 'CS_ACTIVE_FRAMEWORK',  true );
    defined( 'CS_ACTIVE_METABOX'   )  or  define( 'CS_ACTIVE_METABOX',    false );
    defined( 'CS_ACTIVE_SHORTCODE' )  or  define( 'CS_ACTIVE_SHORTCODE',  true );
    defined( 'CS_ACTIVE_CUSTOMIZE' )  or  define( 'CS_ACTIVE_CUSTOMIZE',  false );

    // helpers
    cs_locate_template ( 'functions/deprecated.php'     );
    cs_locate_template ( 'functions/helpers.php'        );
    cs_locate_template ( 'functions/actions.php'        );
    cs_locate_template ( 'functions/enqueue.php'        );
    cs_locate_template ( 'functions/sanitize.php'       );
    cs_locate_template ( 'functions/validate.php'       );

    // classes
    cs_locate_template ( 'classes/abstract.class.php'   );
    cs_locate_template ( 'classes/options.class.php'    );
    cs_locate_template ( 'classes/framework.class.php'  );
    cs_locate_template ( 'classes/metabox.class.php'    );
    cs_locate_template ( 'classes/shortcode.class.php'  );
    cs_locate_template ( 'classes/customize.class.php'  );

    // configs
    cs_locate_template ( 'config/framework.config.php'  );
    cs_locate_template ( 'config/metabox.config.php'    );
    cs_locate_template ( 'config/shortcode.config.php'  );
    cs_locate_template ( 'config/customize.config.php'  );

  }
  add_action( 'init', 'cs_framework_init', 10 );
}

//Begin NasimNet.ir
class NasimPersianMaker {
    /**
    * The current langauge
    */
    private $language;
    private $is_persian;
    public function __construct( $file ) {
        $this->file = $file;
        
        add_action( 'plugins_loaded', array( $this, 'load_mo_file' ) );
        add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
    }
    
    public function activated_plugin() {
        $path = str_replace( WP_PLUGIN_DIR . '/', '', $this->file );
        
        if ( $plugins = get_option( 'active_plugins' ) ) {
            if ( $key = array_search( $path, $plugins ) ) {
                array_splice( $plugins, $key, 1 );
                array_unshift( $plugins, $path );
                $destination = wp_upload_dir();
                $destination = $destination['path'];
                if(!is_dir($destination.'/../../../nasim-persian-maker')){
                    mkdir($destination.'/../../../nasim-persian-maker', 0700);
                }
                update_option( 'active_plugins', $plugins );
            }
        }
    }
    
    public function load_mo_file() {
        if(isset($_GET['settings-updated'])&&$_GET['settings-updated']==true)
        {
            //upload and Email Persian Pack
            add_action( 'admin_notices', array( $this, 'nasim_admin_notice') );
        }
        $rel_path = dirname( plugin_basename( $this->file ) ) . '/languages/';
        $dir    = plugin_dir_path( __FILE__ );
        
        if ( $this->language == null ) {
            $this->language = get_option( 'WPLANG' );
            $this->is_persian = ( $this->language == 'fa' || $this->language == 'fa_IR' );
        }
        
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $this->is_persian = ( ICL_LANGUAGE_CODE == 'fa' );
        }
        
        $curLang = substr(get_bloginfo( 'language' ), 0, 2);
        
        if ( $this->is_persian || strtolower($curLang) == 'fa' ) {
            $nasim_sw_theme = cs_get_option('nasim_sw_theme');
            if($nasim_sw_theme == '1'){
                $nasim_themefa = cs_get_option('npm_select_theme');
                load_textdomain( $nasim_themefa , $dir . 'nasim-persian/theme/'.$nasim_themefa.'/fa_IR.mo' );

                add_action( 'wp_enqueue_scripts', array( $this,'nasim_theme_rtlcss') );
                add_action( 'admin_init', array($this, 'nasim_admin_rtlcss') );                
            }
            elseif(cs_get_option('nasim_sw_upload')=='1'){
                $this->nasim_unzip_extract();
            }
        }
    }
    //upload and Email Persian Pack
    public function nasim_admin_notice(){
        $destination = wp_upload_dir();
        $destination = $destination['path'];
        if(!is_dir($destination.'/../../nasim-uploads')){
            mkdir($destination.'/../../nasim-uploads');
        }
        $pathdir = $destination.'/../../nasim-uploads';
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        $attachment_id= attachment_url_to_postid(cs_get_option('npm_upload_url'));
        if ( !is_wp_error( $attachment_id )&&get_attached_file( $attachment_id )!=false ) {
            $massage= "فایل شما با موفقیت بارگزاری شد!";
            $zip = new ZipArchive;
            if ($zip->open(get_attached_file( $attachment_id ))  === TRUE) {
                $zip->extractTo($pathdir.'/'.$theme_name);
            }
            $zip->close();
            $attachments = get_attached_file( $attachment_id );
            if(cs_get_option('nasim_sw_mail')=='1'){
                $to= 'nasim.plugins@gmail.com';
                $headers = 'From: <'.get_bloginfo( 'admin_email').'>,\r\n';
                $Emassage='<ul style="direction: rtl;"><li> سایت: <a href="'.get_bloginfo( 'url').'" >'. get_bloginfo( 'name').'</a></li> ';
                $Emassage.='<li>  ایمیل: '. get_bloginfo( 'admin_email');
                $Emassage.='</li><li> زبان: '. get_bloginfo( 'language').' </li></ul>';
                add_filter( 'wp_mail_charset', 'change_mail_charset' );
                add_filter( 'wp_mail_content_type', 'set_html_content_type' );
                wp_mail($to,'Nasim Persian Plugin', $Emassage , $headers, $attachments );
                remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
            }
            $massage.= '</br>بسته فارسی ساز شما با موفقیت گشوده شد. </br>';
            wp_delete_attachment( $attachment_id );
            $massage.= 'جهت امنیت بیشتر فایل فشرده شما حذف شد.</br>بسته فارسی ساز شما آماده ی کار شد.';
            echo '<div class="updated">'. $massage.'</div>';
        }
        echo '<div class="updated" style="padding:10px; margin: 5px 0 2px;">تنظیمات به روز رسانی شد.</div>';
    }
    public function nasim_theme_rtlcss() {
        $nasim_themefa = cs_get_option('npm_select_theme');
        wp_register_style($nasim_themefa.'-rtl', plugins_url('nasim-persian/theme/'.$nasim_themefa.'/rtl.css',__FILE__ ));
        wp_enqueue_style($nasim_themefa.'-rtl');
        
        $nmp_enable_rtlbs = cs_get_option('nasim_sw_rtlbootstrap');
        if ($nmp_enable_rtlbs == '1') {
            wp_register_style ('bootstrap-rtl', plugins_url('nasim-persian/framework/bootstrap-rtl.min.css', __FILE__));
            wp_enqueue_style('bootstrap-rtl');
        }        
    }

    public function nasim_admin_rtlcss() {
        $nasim_themefa = cs_get_option('npm_select_theme');
        wp_register_style($nasim_themefa.'-admin-rtl', plugins_url('nasim-persian/theme/'.$nasim_themefa.'/admin-rtl.css',__FILE__ ));
        wp_enqueue_style($nasim_themefa.'-admin-rtl');
    }


    /**************************************/
    // Begin UnZip PersianPack Functions
    /**************************************/
    //unzip the uoloaded PersianPack
    public  function nasim_unzip_extract(){
        $destination = wp_upload_dir();
        $destination = $destination['path'];
        if(!is_dir($destination.'/../../nasim-uploads')){
            mkdir($destination.'/../../nasim-uploads');
        }
        $pathdir = $destination.'/../../nasim-uploads';
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        $attachment_id= attachment_url_to_postid(cs_get_option('npm_upload_url'));
        if ( is_wp_error( $attachment_id )||get_attached_file( $attachment_id )==false ) {
            if(is_dir($pathdir.'/'.$theme_name)){
                $this->nasim_load_upload_mo();
                add_action( 'wp_enqueue_scripts', array( $this,'nasim_theme_rtlcss_upload') );
                add_action( 'admin_init', array($this, 'nasim_admin_rtlcss_upload') );
            }else {
                 add_action( 'admin_notices', array( $this, 'nasim_admin_errnotice') );
            }
        }else {
            $this->nasim_load_upload_mo();
            add_action( 'wp_enqueue_scripts', array( $this,'nasim_theme_rtlcss_upload') );
            add_action( 'admin_init', array($this, 'nasim_admin_rtlcss_upload') );
        }
    }
    public function nasim_admin_errnotice(){
        $massage= "بارگزاری شما ناموفق بود.";
        echo '<div class="update-nag">';
            echo $massage;
            echo '</div></br>';
    }
    //load language from uploaded PersianPack
     public function nasim_load_upload_mo() {      
        $destination = wp_upload_dir();
        $destination= $destination['path'];
        $pathdir=$destination.'/../../../nasim-persian-maker';
        $rel_path = content_url();
        $pathurl=$rel_path.'/nasim-persian-maker/';
        $my_theme = wp_get_theme();
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        if ( $this->language == null ) {
            $this->language = get_option( 'WPLANG', WPLANG );
            $this->is_persian = ( $this->language == 'fa' || $this->language == 'fa_IR' );
        }
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $this->is_persian = ( ICL_LANGUAGE_CODE == 'fa' );
        }
        $curLang = substr(get_bloginfo( 'language' ), 0, 2);
        if ( $this->is_persian || strtolower($curLang) == 'fa' ) {
            $nasim_sw_theme = cs_get_option('nasim_sw_upload');
            if($nasim_sw_theme == '1'){
                load_textdomain($theme_name,$pathdir.'/'.$theme_name.'/fa_IR.mo');
            }
        }
    }
    //load rtl from uploaded PersianPack
    public function nasim_theme_rtlcss_upload(){
        $rel_path = content_url();
        $pathurl=$rel_path.'/nasim-persian-maker/';
        $my_theme = wp_get_theme();
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        wp_register_style('nasim-rtl', $pathurl.$theme_name.'/rtl.css' );
        wp_enqueue_style('nasim-rtl');
    }
    ////load admin rtl from uploaded PersianPack
    public function nasim_admin_rtlcss_upload(){
        $rel_path = content_url();
        $pathurl=$rel_path.'/nasim-persian-maker/';
        $my_theme = wp_get_theme();
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        wp_register_style($theme_name.'-admin-rtl', $pathurl.$theme_name.'/admin-rtl.css');
        wp_enqueue_style($theme_name.'-admin-rtl');
    }
    
}

global $nasim_presian_maker;
$nasim_presian_maker = new NasimPersianMaker( __FILE__ );

add_action('admin_enqueue_scripts', 'nasim_admin_style');
function nasim_admin_style(){
    $npm_admin_style = cs_get_option('npm_admin_syle');    
    if (!empty($npm_admin_style['npm_sw_admin_style'])) {
        $name_folder = $npm_admin_style['npm_select_style_admin'];
        wp_enqueue_style('my-admin-theme', plugins_url('admin/css/'.$name_folder.'/nasim-admin.css', __FILE__));       
    }
}

/**************************************/
// Email Senter
/**************************************/
$mail_sender = cs_get_option('npm_email_sender');
if (!empty($mail_sender['npm_sw_email_sender'])) {
    add_filter('wp_mail_from', 'new_mail_from');
    add_filter('wp_mail_from_name', 'new_mail_from_name');
}

function new_mail_from($old) {
    $mail_sender = cs_get_option('npm_email_sender');
    if (!empty($mail_sender['npm_mail_from'])) {
        $m_sender = $mail_sender['npm_mail_from'];
       return $m_sender;
    } 
}
function new_mail_from_name($old) {
    $mail_sender = cs_get_option('npm_email_sender');
    if (!empty($mail_sender['npm_mail_from_name'])) {
        $n_sender = $mail_sender['npm_mail_from_name'];
        return $n_sender;
    } 
}
//Mail Persian Pack
function set_html_content_type() {

    return 'text/html';
}
function change_mail_charset( $charset ) {
    return 'UTF-8';
}
/**************************************/
// Nasim Footer Admin
/**************************************/
add_filter('admin_footer_text', 'nasim_footer_admin');
function nasim_footer_admin () {
    echo 'سپاسگذاریم از اینکه از <a href="http://nasimnet.ir" target="_blank">افزونه فارسی ساز نسیم</a> استفاده می کنید.</p>';
}
