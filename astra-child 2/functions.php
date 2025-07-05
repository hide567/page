<?php
/**
 * 行政書士の道 - functions.php 完全整理版
 * Astra子テーマ用
 */

if (!defined('ABSPATH')) {
    exit;
}

// Astra子テーマの基本スタイル読み込み
function astra_child_enqueue_styles() {
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style'));
}
add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');

/**
 * 行政書士の道 - カスタム機能
 */

// カスタムフロントページのスタイル読み込み
function gyousei_enqueue_front_page_assets() {
    if (is_front_page()) {
        // カスタムCSS
        if (file_exists(get_stylesheet_directory() . '/css/front-page.css')) {
            wp_enqueue_style(
                'gyousei-front-page',
                get_stylesheet_directory_uri() . '/css/front-page.css',
                array(),
                filemtime(get_stylesheet_directory() . '/css/front-page.css')
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'gyousei_enqueue_front_page_assets');

// テーマサポート機能
function gyousei_theme_setup() {
    // アイキャッチ画像サポート
    add_theme_support('post-thumbnails');
    
    // カスタム画像サイズ
    add_image_size('gyousei-card-thumb', 400, 250, true);
    add_image_size('gyousei-hero-thumb', 800, 400, true);
}
add_action('after_setup_theme', 'gyousei_theme_setup');

// 抜粋の長さをカスタマイズ
function gyousei_custom_excerpt_length($length) {
    if (is_front_page()) {
        return 30;
    }
    return $length;
}
add_filter('excerpt_length', 'gyousei_custom_excerpt_length', 999);

// 抜粋の「[...]」をカスタマイズ
function gyousei_custom_excerpt_more($more) {
    if (is_front_page()) {
        return '...';
    }
    return $more;
}
add_filter('excerpt_more', 'gyousei_custom_excerpt_more');

// 行政書士科目別カテゴリーの自動作成
function gyousei_create_subject_categories() {
    $subjects = array(
        array(
            'name' => '憲法',
            'slug' => 'constitution',
            'description' => '基本的人権、統治機構、憲法の基本原理について学習します'
        ),
        array(
            'name' => '行政法',
            'slug' => 'administrative-law', 
            'description' => '行政行為、行政手続、行政救済について学習します'
        ),
        array(
            'name' => '民法',
            'slug' => 'civil-law',
            'description' => '総則、物権、債権、親族、相続について学習します'
        ),
        array(
            'name' => '商法',
            'slug' => 'commercial-law',
            'description' => '会社法、商取引法、有価証券法について学習します'
        ),
        array(
            'name' => '基礎法学',
            'slug' => 'jurisprudence',
            'description' => '法理論、法制史、外国法について学習します'
        ),
        array(
            'name' => '一般知識',
            'slug' => 'general-knowledge',
            'description' => '政治、経済、社会、情報通信・個人情報保護について学習します'
        )
    );

    foreach ($subjects as $subject) {
        $term = term_exists($subject['name'], 'category');
        
        if (!$term) {
            $term = wp_insert_term(
                $subject['name'],
                'category',
                array(
                    'slug' => $subject['slug'],
                    'description' => $subject['description']
                )
            );
            
            if (!is_wp_error($term) && defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Created category: ' . $subject['name']);
            }
        }
    }
}

// テーマ有効化時に科目カテゴリーを作成
function gyousei_theme_activation() {
    gyousei_create_subject_categories();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gyousei_theme_activation');

// 学習開始日の設定機能
function gyousei_add_study_settings() {
    add_settings_section(
        'gyousei_study_settings',
        '学習記録設定',
        null,
        'general'
    );
    
    add_settings_field(
        'gyousei_study_start_date',
        '学習開始日',
        'gyousei_study_start_date_callback',
        'general',
        'gyousei_study_settings'
    );
    
    register_setting('general', 'gyousei_study_start_date');
}
add_action('admin_init', 'gyousei_add_study_settings');

function gyousei_study_start_date_callback() {
    $start_date = get_option('gyousei_study_start_date', date('Y-m-d'));
    echo '<input type="date" name="gyousei_study_start_date" value="' . esc_attr($start_date) . '" />';
    echo '<p class="description">行政書士試験の学習を開始した日付を設定してください。</p>';
}

// 管理画面にカテゴリー作成ボタンを追加
function gyousei_add_admin_menu() {
    add_management_page(
        '科目カテゴリー作成',
        '科目カテゴリー作成', 
        'manage_options',
        'gyousei-create-categories',
        'gyousei_create_categories_page'
    );
}
add_action('admin_menu', 'gyousei_add_admin_menu');

function gyousei_create_categories_page() {
    if (isset($_POST['create_categories']) && wp_verify_nonce($_POST['create_categories_nonce'], 'create_categories')) {
        gyousei_create_subject_categories();
        echo '<div class="notice notice-success"><p>科目カテゴリーを作成しました！</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>行政書士科目カテゴリー作成</h1>
        <p>行政書士試験の科目別カテゴリーを自動作成します。</p>
        
        <form method="post">
            <?php wp_nonce_field('create_categories', 'create_categories_nonce'); ?>
            <input type="submit" name="create_categories" class="button button-primary" value="科目カテゴリーを作成" />
        </form>
        
        <h2>作成されるカテゴリー</h2>
        <ul>
            <li>憲法 (constitution)</li>
            <li>行政法 (administrative-law)</li>
            <li>民法 (civil-law)</li>
            <li>商法 (commercial-law)</li>
            <li>基礎法学 (jurisprudence)</li>
            <li>一般知識 (general-knowledge)</li>
        </ul>
    </div>
    <?php
}

// セキュリティ強化：ファイルアップロード制限
function gyousei_restrict_file_uploads($file) {
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx');
    $file_info = pathinfo($file['name']);
    $file_extension = strtolower($file_info['extension']);
    
    if (!in_array($file_extension, $allowed_types)) {
        $file['error'] = 'このファイル形式はアップロードできません。';
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'gyousei_restrict_file_uploads');

// パフォーマンス最適化：不要なスクリプトの削除
function gyousei_optimize_scripts() {
    if (!is_admin()) {
        // 絵文字スクリプトを削除
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        
        // WordPress.comのDevice Pixel Ratioスクリプトを削除
        remove_action('wp_head', 'wp_generator');
    }
}
add_action('init', 'gyousei_optimize_scripts');

// ソーシャルメタタグの追加（OGP対応）
function gyousei_add_social_meta_tags() {
    if (is_front_page()) {
        echo '<meta property="og:title" content="行政書士の道 - わかりやすい解説の集積地" />' . "\n";
        echo '<meta property="og:description" content="行政書士試験合格を目指す個人の学習記録・解説サイト。日々の勉強内容と理解をまとめています。" />' . "\n";
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:url" content="' . home_url() . '" />' . "\n";
        echo '<meta property="og:site_name" content="行政書士の道" />' . "\n";
        
        // Twitterカード
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="行政書士の道 - わかりやすい解説の集積地" />' . "\n";
        echo '<meta name="twitter:description" content="行政書士試験合格を目指す個人の学習記録・解説サイト" />' . "\n";
    }
}
add_action('wp_head', 'gyousei_add_social_meta_tags');

// カスタムログイン画面（ブランディング）
function gyousei_custom_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a {
            background-image: none;
            text-indent: 0;
            width: auto;
            height: auto;
            font-size: 24px;
            font-weight: bold;
            color: #3f51b5;
            text-decoration: none;
        }
        #login h1 a:before {
            content: "行政書士の道";
        }
        .login form {
            border-radius: 8px;
        }
        .login #nav a,
        .login #backtoblog a {
            color: #3f51b5 !important;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'gyousei_custom_login_logo');

// ログイン画面のロゴリンクを変更
function gyousei_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'gyousei_login_logo_url');

function gyousei_login_logo_url_title() {
    return '行政書士の道 - ホームページ';
}
add_filter('login_headertitle', 'gyousei_login_logo_url_title');

?>