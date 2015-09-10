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
  'ajax_save'  => false,
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
      'id'           => 'dum_nasim_upload',
      'type'         => 'notice',
      'class'        => 'success',
      'content'      => '
      <p style="color:#ed6f6f">روش ساختن بسته فارسی ساز:</p>
      <ol>
      <li>فایل راست چین پوسته را با نام rtl.css نام گذاری کنید</li>
      <li>اگر برای مدیریت استایلی وجود دارد آن را با نام admin-rtl.css نام گذاری کنید.</li>
      <li>فایل زبان را با نام fa_IR.mo نام گذاری کنید.</li>
      <li>تمامی فایل ها را انتخاب کرده و راست کلیک کنید و گزینه "Send to > Compressed (zipped) folder" رابزنید .</li>
      <li>فایل zip شده را در این قسمت بارگذاری کنید.</li>
    </ol>',
      'dependency'   => array( 'nasim_sw_upload', '==', 'true' ),
    ),

   array(
    'id'        => 'npm_text_domain',
    'type'      => 'text',
    'title'     => 'نام شاخص',
    'default' =>wp_get_theme()->get( 'TextDomain' ),
    'after'=> '<p class="cs-text-muted">'//.wp_get_theme()->get( 'Name' ).' OR '.wp_get_theme()->get( 'TextDomain' ).
    .nasim_find_textdomain().'</p>',
    'dependency'   => array( 'nasim_sw_upload', '==', 'true' ),
    ),
  
  array(
    'id'    => 'npm_upload_url',
    'type'  => 'upload',
    'title' => 'بارگذاری بسته فارسی ساز',
    'settings'      => array(
    'upload_type'  => 'zip',
    'insert_title' => 'استفاده از این فایل',
    ),
    'dependency'   => array( 'nasim_sw_upload', '==', 'true' ),
  ),
  array(
      'id'      => 'nasim_sw_mail',
      'type'    => 'switcher',
      'title'   => 'انتشار فارسی ساز',
      //'label'   => 'آیا تمایل دارید فارسی ساز شما در نسخه بعدی منتشر شود ؟',
    'default' => true,
    'dependency'   => array( 'nasim_sw_upload', '==', 'true' ),
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

    // Admin Style
    array(
        'id'        => 'npm_admin_syle',
        'type'      => 'fieldset',
        'title'     => 'ظاهر سایت',
        'fields'    => array(
         array(
          'id'      => 'npm_sw_admin_style',
          'type'    => 'switcher',
          'title'   => 'فعال سازی',
          'label'   => 'آیا تمایل دارید تغییرات بر روی قسمت مدیریت اعمال شود ؟',
          'default' => true,
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
              'default'        => 'yekan',
            ),
         ),
      ),

    // Email Options
    array(
        'id'        => 'npm_email_sender',
        'type'      => 'fieldset',
        'title'     => 'تنظیمات ایمیل سایت',
        'fields'    => array(

          array(
            'id'    => 'npm_sw_email_sender',
            'type'  => 'switcher',
            'title' => 'فعال سازی',
          ),
          array(
            'id'    => 'npm_mail_from',
            'type'  => 'text',
            'title' => 'ایمیل ارسال کننده',
          ),
          array(
            'id'    => 'npm_mail_from_name',
            'type'  => 'text',
            'title' => 'نام ارسال کننده',
          ),         

        ),
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

// ------------------------------
// license                      -
// ------------------------------
$options[]   = array(
  'name'     => 'license_section',
  'title'    => 'درباره افزونه',
  'icon'     => 'fa fa-info-circle',
  'fields'   => array(

    array(
      'type'    => 'heading',
      'content' => 'افزونه فارسی ساز نسیم'
    ),
    array(
      'type'    => 'content',
      'content' => 'این افزونه توسط <a href="http://nasimnet.ir" target="_blank">گروه طراحی نسیم نت</a> آماده شده است و به صورت رایگان در اختیار شما قرار گرفته شده است.
      <br>      
      <h3 style="color: #c51616;">ما برای تکمیل بانک فارسی ساز نیازمند حمایت شما هستیم !</h3>
      لطفا جهت حمایت از این حرکت انقلابی ، بسته های فارسی ساز خود را برای ما ارسال نمائید تا بانک فارسی ساز را تکمیل کنیم .

      <h3 style="color: #c51616;">آیا مشکلی در افزونه دیده اید و یا ایده ای جدیدی دارید ؟</h3>
      هرگونه باگ و یا مشکلی در افزونه مشاهده کردید و یا اینکه ایده ای برای بهتر شدن افزونه دارید لطفا به ما اطلاع دهید.<br>
      ایمیل : <a href="mailto:nasim.plugins@gmail.com?Subject=افزونه فارسی ساز نسیم" target="_top">nasim.plugins@gmail.com</a>

      <h3 style="color: #c51616;">از این افزونه خوشتان آمده است ؟!</h3>
      اگر از این افزونه خوشتان آمده است این افزونه را به دوستانتان و یا در وبسایت و یا شبکه های اجتماعی خود معرفی کنید.',
    ),

  )
);
CSFramework::instance( $settings, $options );
