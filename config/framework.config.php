<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
 function nasim_find_themename(){
    $get_textdomain = wp_get_theme()->get( 'TextDomain' );
    if (!empty($get_textdomain)) {
        return 'نام پوسته شما <b>'.wp_get_theme()->get( 'Name' ) .'</b> شناسائی شد. در صورت وجود ، آن را به صورت دستی از لیست بالا انتخاب کنید و یا می توانید از قسمت <b>بارگذاری فارسی ساز</b> بسته فارسی ساز خود را بارگذاری نمائید.';
    }
    else {
        return '<b>متاسفانه نام پوسته شما را پیدا نکردیم ! </b>در صورت وجود ، آن را به صورت دستی از لیست بالا انتخاب کنید و یا می توانید از قسمت <b>بارگذاری فارسی ساز</b> بسته فارسی ساز خود را بارگذاری نمائید.';
    }
}
 function nasim_find_textdomain(){
    $get_textdomain = wp_get_theme()->get( 'TextDomain' );
    if (!empty($get_textdomain)) {
        return 'نام شاخص پوسته شما <b>'.wp_get_theme()->get( 'TextDomain' ) .'</b> شناسائی شد. لطفا این نام را در کادر بالا کپی کنید .';
    }
    else {
        return '<b>نام شاخص شما را پیدا نکردیم ! </b>نام شاخص را به صورت دستی وارد نمائید.';
    }
}

// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings      = array(
  'menu_title' => 'فارسی‌ساز نسیم',
  'menu_type'  => 'add_menu_page',
  'menu_slug'  => 'nasim-persian-maker',
  'ajax_save'  => true,
);

// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options        = array();

// ----------------------------------------
// Setting For Auto Persian Maker
// ----------------------------------------
$options[]      = array(
  'name'        => 'nasimpm',
  'title'       => 'فارسی ساز خودکار',
  'icon'        => 'fa fa-star',

  // begin: fields
  'fields'      => array(

    // begin: a field
     array(
      'id'      => 'nasim_sw_theme',
      'type'    => 'switcher',
      'title'   => 'فعال سازی',
      'label'   => 'آیا تمایل دارید تغییرات فارسی ساز اعمال شود ؟',
    ),

    array(
          'id'             => 'npm_select_theme',
          'type'           => 'select',
          'title'          => 'انتخاب پوسته',
          'options'        => array(
            'betheme'      => 'BeTheme',
            'zerif-lite'   => 'Zrerif Lite',
            'brasserie'    => 'Brasserie',
            'appointment'  => 'Appointment',
          ),
          'default_option' => 'انتخاب پوسته',
          'after'=> '<p class="cs-text-muted">'.nasim_find_themename().'</p>',
        ),

    array(
      'id'      => 'nasim_sw_rtlbootstrap',
      'type'    => 'switcher',
      'title'   => 'فعال سازی بوت استراپ',
      'label'   => 'آیا تمایل دارید بوت استراپ راست چین فعال شود ؟',
    ),

  ), // end: fields
);

// ----------------------------------------
// Setting For Upload Pack Persian
// ----------------------------------------
$options[]      = array(
  'name'        => 'nasim_upload',
  'title'       => 'بارگذاری فارسی ساز',
  'icon'        => 'fa fa-star',

  // begin: fields
  'fields'      => array(

    // begin: a field
     array(
      'id'      => 'nasim_sw_upload',
      'type'    => 'switcher',
      'title'   => 'فعال سازی',
      'label'   => 'آیا تمایل دارید فارسی ساز خود را بارگذاری نمائید ؟',
    ),

   array(
    'id'        => 'npm_text_domain',
    'type'      => 'text',
    'title'     => 'نام شاخص',
    'default' =>wp_get_theme()->get( 'TextDomain' ),
    'after'=> '<p class="cs-text-muted">'//.wp_get_theme()->get( 'Name' ).' OR '.wp_get_theme()->get( 'TextDomain' ).
    .nasim_find_textdomain().'</p>',
    ),
  
  array(
    'id'    => 'npm_upload_url',
    'type'  => 'upload',
    'title' => 'بارگذاری بسته فارسی ساز',
    'settings'      => array(
    'upload_type'  => 'zip',    
    ),
    'after' => '<p><span style="color:#ed6f6f">روش ساختن بسته زبان:</span>
    <ol>
      <li>فایل راست چین پوسته را با نام rtl.css نام گذاری کنید</li>
      <li>اگر برای مدیریت استایلی وجود دارد آن را با نام admin-rtl.css نام گذاری کنید.</li>
      <li>فایل زبان را با نام fa_IR.mo نام گذاری کنید.</li>
      <li>تمامی فایل ها را انتخاب کرده و راست کلیک کنید و گزینه "Send to > Compressed (zipped) folder" رابزنید .</li>
      <li>فایل zip شده را در این قسمت بارگذاری کنید.</li>
    </ol>


    </p>',
  ),

  ), // end: fields
);

// ----------------------------------------
// Setting For Dashboard Persian Maker
// ----------------------------------------
$options[]      = array(
  'name'        => 'nasim_dashboard',
  'title'       => 'تنظیمات مدیریت',
  'icon'        => 'fa fa-star',

  // begin: fields
  'fields'      => array(

    // begin: a field
     array(
      'id'      => 'npm_sw_admin_style',
      'type'    => 'switcher',
      'title'   => 'فعال سازی',
      'label'   => 'آیا تمایل دارید تغییرات بر روی قسمت مدیریت اعمال شود ؟',
    ),

    array(
          'id'             => 'npm_select_style_admin',
          'type'           => 'select',
          'title'          => 'فونت مدیریت',
          'options'        => array(
            'yekan'        => 'فونت یکان',
            //'red'          => 'فونت زر',
            //'blue'         => 'فونت نازنین',
            //'yellow'       => 'فونت مجله',
            //'black'        => 'فونت الکی',
          ),
          'default_option' => 'انتخاب فونت',
          'info'           => 'فونت مورد نظر خود را انتخاب نمائید.',
        ),

  ), // end: fields
);

// ------------------------------
// backup                       -
// ------------------------------
$options[]   = array(
  'name'     => 'backup_section',
  'title'    => 'برون ریزی',
  'icon'     => 'fa fa-shield',
  'fields'   => array(

    array(
      'type'    => 'notice',
      'class'   => 'warning',
      'content' => 'شما می توانید از این قسمت کلیه تنظیمات افزونه را برون ریزی کرده و بعدا در صورت نیاز درون ریزی کنید.',
    ),

    array(
      'type'    => 'backup',
    ),

  )
);

CSFramework::instance( $settings, $options );
