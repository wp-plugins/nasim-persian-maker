<?php 
/**
* Plugin Name: فارسی ساز نسیم
* Plugin URI: http://nasimnet.ir
* Description: با این افزونه به راحتی می تواید پوسته خود را فارسی و راست چین کنید.
* Version: 2.0
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
        if(!is_dir($destination.'/../../../nasim-persian-maker')){
            mkdir($destination.'/../../../nasim-persian-maker', 0700);
        }
        $pathdir = $destination.'/../../../nasim-persian-maker';
        $theme_name=strtolower(cs_get_option( 'npm_text_domain' ));
        $attachment_id= attachment_url_to_postid(cs_get_option('npm_upload_url'));
        if ( is_wp_error( $attachment_id )||get_attached_file( $attachment_id )==false ) {
            //echo "There was an error uploading the zip file.";
            if(is_dir($pathdir.'/'.$theme_name)){
                $this->nasim_load_upload_mo();
                add_action( 'wp_footer', array( $this,'nasim_theme_rtlcss_upload') );
                add_action( 'admin_init', array($this, 'nasim_admin_rtlcss_upload') );
            }
        }else {
            //echo "Zip file was uploaded successfully!";
            $zip = new ZipArchive;
            if ($zip->open(get_attached_file( $attachment_id ))  === TRUE) {
                $zip->extractTo($pathdir.'/'.$theme_name);
            }
            $zip->close();
            //echo 'ok </br>';
            wp_delete_attachment( $attachment_id );
            //echo 'zip file deleted';
            $this->nasim_load_upload_mo();
            add_action( 'wp_footer', array( $this,'nasim_theme_rtlcss_upload') );
            add_action( 'admin_init', array($this, 'nasim_admin_rtlcss_upload') );
        }
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
    $nasim_sw_admin_style = cs_get_option('npm_sw_admin_style');
    if ($nasim_sw_admin_style == '1') {
        $nasim_admin_style = cs_get_option('npm_select_style_admin');
        wp_enqueue_style('my-admin-theme', plugins_url('admin/css/'.$nasim_admin_style.'/nasim-admin.css', __FILE__));
       
    }
}

add_filter('admin_footer_text', 'nasim_footer_admin');
function nasim_footer_admin () {
    echo 'سپاسگذاریم از اینکه از <a href="http://nasimnet.ir" target="_blank">افزونه فارسی ساز نسیم</a> استفاده می کنید.</p>';
}

