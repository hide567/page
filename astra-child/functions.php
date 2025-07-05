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
        
        // カスタムJS
        if (file_exists(get_stylesheet_directory() . '/js/front-page.js')) {
            wp_enqueue_script(
                'gyousei-front-page-js',
                get_stylesheet_directory_uri() . '/js/front-page.js',
                array('jquery'),
                filemtime(get_stylesheet_directory() . '/js/front-page.js'),
                true
            );
            
            // Ajax用のパラメータ
            wp_localize_script('gyousei-front-page-js', 'gyousei_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gyousei_front_page_nonce')
            ));
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
    
    // HTML5サポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // カスタムロゴサポート
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
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

/**
 * 行政書士科目別カテゴリー管理
 */

// 行政書士科目カテゴリーの自動作成
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

// アクティブな科目数を数える関数
function gyousei_count_active_subjects() {
    $subjects = array('憲法', '行政法', '民法', '商法', '基礎法学', '一般知識');
    $active_count = 0;
    
    foreach ($subjects as $subject) {
        $category = get_category_by_slug(sanitize_title($subject));
        if (!$category) {
            $categories = get_categories(array('name' => $subject, 'hide_empty' => false));
            $category = !empty($categories) ? $categories[0] : null;
        }
        
        if ($category && $category->count > 0) {
            $active_count++;
        }
    }
    
    return $active_count;
}

// テーマ有効化時に科目カテゴリーを作成
function gyousei_theme_activation() {
    gyousei_create_subject_categories();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gyousei_theme_activation');

/**
 * 学習進捗管理機能
 */

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
    
    add_settings_field(
        'gyousei_completion_rate',
        '学習完了率（%）',
        'gyousei_completion_rate_callback',
        'general',
        'gyousei_study_settings'
    );
    
    add_settings_field(
        'gyousei_study_streak',
        '連続学習日数',
        'gyousei_study_streak_callback',
        'general',
        'gyousei_study_settings'
    );
    
    add_settings_field(
        'gyousei_total_hours',
        '総学習時間（時間）',
        'gyousei_total_hours_callback',
        'general',
        'gyousei_study_settings'
    );
    
    add_settings_field(
        'gyousei_goals_achieved',
        '達成済み目標数',
        'gyousei_goals_achieved_callback',
        'general',
        'gyousei_study_settings'
    );
    
    register_setting('general', 'gyousei_study_start_date');
    register_setting('general', 'gyousei_completion_rate');
    register_setting('general', 'gyousei_study_streak');
    register_setting('general', 'gyousei_total_hours');
    register_setting('general', 'gyousei_goals_achieved');
}
add_action('admin_init', 'gyousei_add_study_settings');

function gyousei_study_start_date_callback() {
    $start_date = get_option('gyousei_study_start_date', date('Y-m-d'));
    echo '<input type="date" name="gyousei_study_start_date" value="' . esc_attr($start_date) . '" />';
    echo '<p class="description">行政書士試験の学習を開始した日付を設定してください。</p>';
}

function gyousei_completion_rate_callback() {
    $rate = get_option('gyousei_completion_rate', 85);
    echo '<input type="number" name="gyousei_completion_rate" value="' . esc_attr($rate) . '" min="0" max="100" />';
    echo '<p class="description">現在の学習完了率をパーセントで入力してください。</p>';
}

function gyousei_study_streak_callback() {
    $streak = get_option('gyousei_study_streak', 127);
    echo '<input type="number" name="gyousei_study_streak" value="' . esc_attr($streak) . '" min="0" />';
    echo '<p class="description">連続で学習している日数を入力してください。</p>';
}

function gyousei_total_hours_callback() {
    $hours = get_option('gyousei_total_hours', 256);
    echo '<input type="number" name="gyousei_total_hours" value="' . esc_attr($hours) . '" min="0" />';
    echo '<p class="description">これまでの総学習時間を入力してください。</p>';
}

function gyousei_goals_achieved_callback() {
    $goals = get_option('gyousei_goals_achieved', 42);
    echo '<input type="number" name="gyousei_goals_achieved" value="' . esc_attr($goals) . '" min="0" />';
    echo '<p class="description">達成済みの学習目標数を入力してください。</p>';
}

// 学習進捗データを取得する関数
function gyousei_get_study_progress_data() {
    return array(
        'completion_rate' => get_option('gyousei_completion_rate', 85),
        'study_streak' => get_option('gyousei_study_streak', 127), 
        'total_hours' => get_option('gyousei_total_hours', 256),
        'goals_achieved' => get_option('gyousei_goals_achieved', 42),
        'start_date' => get_option('gyousei_study_start_date', date('Y-m-d')),
        'study_days' => gyousei_calculate_study_days()
    );
}

// 学習開始からの経過日数を計算
function gyousei_calculate_study_days() {
    $start_date = get_option('gyousei_study_start_date', date('Y-m-d'));
    $days_passed = (strtotime('now') - strtotime($start_date)) / (60 * 60 * 24);
    return max(0, floor($days_passed));
}

// 今月の投稿数を取得
function gyousei_get_monthly_posts_count() {
    $current_month_posts = new WP_Query(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'date_query' => array(
            array(
                'year'  => date('Y'),
                'month' => date('n'),
            ),
        ),
        'fields' => 'ids'
    ));
    $count = $current_month_posts->found_posts;
    wp_reset_postdata();
    return $count;
}

/**
 * 管理画面カスタマイズ
 */

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
        
        <h2>現在のカテゴリー状況</h2>
        <div class="categories-status">
            <?php
            $subjects = array('憲法', '行政法', '民法', '商法', '基礎法学', '一般知識');
            foreach ($subjects as $subject) {
                $category = get_category_by_slug(sanitize_title($subject));
                $posts_count = 0;
                if ($category) {
                    $posts_count = $category->count;
                    echo '<p>✅ ' . esc_html($subject) . ' - ' . $posts_count . '記事</p>';
                } else {
                    echo '<p>❌ ' . esc_html($subject) . ' - 未作成</p>';
                }
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * セキュリティとパフォーマンス最適化
 */

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
        
        // RSDリンクを削除
        remove_action('wp_head', 'rsd_link');
        
        // Windows Live Writerサポートを削除
        remove_action('wp_head', 'wlwmanifest_link');
    }
}
add_action('init', 'gyousei_optimize_scripts');

// 不要なREST APIエンドポイントを無効化（セキュリティ向上）
function gyousei_disable_rest_api_for_non_logged_users($access) {
    if (!is_user_logged_in() && !is_admin()) {
        return new WP_Error('rest_forbidden', 'REST APIへのアクセスが禁止されています。', array('status' => 401));
    }
    return $access;
}
add_filter('rest_authentication_errors', 'gyousei_disable_rest_api_for_non_logged_users');

/**
 * SEO対策とソーシャルメタタグ
 */

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
    } elseif (is_single()) {
        global $post;
        $title = get_the_title();
        $description = get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);
        $url = get_permalink();
        $image = has_post_thumbnail() ? get_the_post_thumbnail_url($post->ID, 'large') : '';
        
        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
        }
        
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'gyousei_add_social_meta_tags');

// 構造化データの追加（JSON-LD形式）
function gyousei_add_structured_data() {
    if (is_front_page()) {
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => '行政書士の道',
            'description' => '行政書士試験合格を目指す個人の学習記録・解説サイト',
            'url' => home_url(),
            'author' => array(
                '@type' => 'Person',
                'name' => get_bloginfo('name')
            ),
            'educationalLevel' => '大学院・大学・短大・専門学校',
            'about' => array(
                '@type' => 'Thing',
                'name' => '行政書士試験'
            ),
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => home_url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            )
        );
        
        echo '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    } elseif (is_single()) {
        global $post;
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => get_the_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20),
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            )
        );
        
        if (has_post_thumbnail()) {
            $structured_data['image'] = get_the_post_thumbnail_url($post->ID, 'large');
        }
        
        echo '<script type="application/ld+json">' . json_encode($structured_data, JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'gyousei_add_structured_data');

/**
 * カスタムログイン画面
 */

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
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .login #nav a,
        .login #backtoblog a {
            color: #3f51b5 !important;
        }
        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: #303f9f !important;
        }
        body.login {
            background: linear-gradient(135deg, #f8f9fa 0%, #e8eaf6 100%);
        }
        .login .button-primary {
            background: #3f51b5;
            border-color: #3f51b5;
            text-shadow: none;
            box-shadow: none;
        }
        .login .button-primary:hover {
            background: #303f9f;
            border-color: #303f9f;
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

/**
 * Ajax処理（今後のコミュニティ機能用）
 */

// Ajax用のnonce検証関数
function gyousei_verify_ajax_nonce() {
    if (!wp_verify_nonce($_POST['nonce'], 'gyousei_front_page_nonce')) {
        wp_send_json_error('セキュリティチェックに失敗しました。');
        wp_die();
    }
}

// サンプルAjax処理（今後拡張用）
function gyousei_sample_ajax_handler() {
    gyousei_verify_ajax_nonce();
    
    // 処理内容をここに記述
    $response_data = array(
        'message' => 'Ajax処理が成功しました！'
    );
    
    wp_send_json_success($response_data);
}
add_action('wp_ajax_gyousei_sample', 'gyousei_sample_ajax_handler');
add_action('wp_ajax_nopriv_gyousei_sample', 'gyousei_sample_ajax_handler');

/**
 * ユーティリティ関数
 */

// 安全なHTMLエスケープ関数
function gyousei_esc_html($text) {
    return esc_html($text);
}

// 安全なURL生成関数
function gyousei_get_category_url($category_slug) {
    $category = get_category_by_slug($category_slug);
    if ($category) {
        return get_category_link($category->term_id);
    }
    return '#';
}

// 読了時間計算関数
function gyousei_calculate_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = max(1, ceil($word_count / 200)); // 1分間に200語として計算
    return $reading_time;
}

// 投稿の相対時間表示
function gyousei_time_ago($post_date) {
    $time_ago = human_time_diff(strtotime($post_date), current_time('timestamp'));
    return sprintf('%s前', $time_ago);
}

/**
 * ショートコード（今後の拡張用）
 */

// 学習統計表示ショートコード
function gyousei_study_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'all' // all, completion, streak, hours, goals
    ), $atts, 'gyousei_stats');
    
    $stats = gyousei_get_study_progress_data();
    
    $output = '<div class="gyousei-stats-shortcode">';
    
    if ($atts['type'] === 'all' || $atts['type'] === 'completion') {
        $output .= '<div class="stat-item">学習完了率: ' . $stats['completion_rate'] . '%</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'streak') {
        $output .= '<div class="stat-item">連続学習日数: ' . $stats['study_streak'] . '日</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'hours') {
        $output .= '<div class="stat-item">総学習時間: ' . $stats['total_hours'] . '時間</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'goals') {
        $output .= '<div class="stat-item">達成済み目標: ' . $stats['goals_achieved'] . '個</div>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('gyousei_stats', 'gyousei_study_stats_shortcode');

// 最新記事表示ショートコード
function gyousei_latest_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 3,
        'category' => ''
    ), $atts, 'gyousei_latest');
    
    $args = array(
        'posts_per_page' => intval($atts['count']),
        'post_status' => 'publish'
    );
    
    if (!empty($atts['category'])) {
        $args['category_name'] = sanitize_text_field($atts['category']);
    }
    
    $posts = get_posts($args);
    $output = '<div class="gyousei-latest-posts-shortcode">';
    
    foreach ($posts as $post) {
        setup_postdata($post);
        $output .= '<div class="post-item">';
        $output .= '<h4><a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></h4>';
        $output .= '<p class="post-date">' . get_the_date('Y.m.d', $post->ID) . '</p>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('gyousei_latest', 'gyousei_latest_posts_shortcode');

/**
 * WordPressダッシュボードカスタマイズ
 */

// カスタムダッシュボードウィジェット
function gyousei_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'gyousei_study_progress_widget',
        '学習進捗状況',
        'gyousei_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'gyousei_add_dashboard_widget');

function gyousei_dashboard_widget_content() {
    $stats = gyousei_get_study_progress_data();
    ?>
    <div class="gyousei-dashboard-widget">
        <style>
            .gyousei-dashboard-widget {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 15px;
                margin-top: 10px;
            }
            .dashboard-stat {
                text-align: center;
                padding: 15px;
                background: #f1f1f1;
                border-radius: 5px;
                border-left: 4px solid #3f51b5;
            }
            .dashboard-stat-number {
                font-size: 24px;
                font-weight: bold;
                color: #3f51b5;
                margin-bottom: 5px;
            }
            .dashboard-stat-label {
                font-size: 12px;
                color: #666;
            }
        </style>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['completion_rate']); ?>%</div>
            <div class="dashboard-stat-label">学習完了率</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['study_streak']); ?></div>
            <div class="dashboard-stat-label">連続学習日数</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['total_hours']); ?></div>
            <div class="dashboard-stat-label">総学習時間</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['goals_achieved']); ?></div>
            <div class="dashboard-stat-label">達成済み目標</div>
        </div>
        
        <div style="grid-column: 1 / -1; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
            <p><strong>学習開始日:</strong> <?php echo esc_html($stats['start_date']); ?></p>
            <p><strong>学習経過日数:</strong> <?php echo esc_html($stats['study_days']); ?>日</p>
        </div>
        
        <div style="grid-column: 1 / -1; margin-top: 10px;">
            <a href="<?php echo admin_url('options-general.php'); ?>" class="button button-secondary">設定を更新</a>
            <a href="<?php echo home_url(); ?>" class="button button-primary">サイトを表示</a>
        </div>
    </div>
    <?php
}

/**
 * メンテナンスとクリーンアップ
 */

// 自動保存とリビジョンの制限
function gyousei_limit_revisions() {
    if (!defined('WP_POST_REVISIONS')) {
        define('WP_POST_REVISIONS', 3);
    }
    
    // 自動保存間隔を60秒に設定
    if (!defined('AUTOSAVE_INTERVAL')) {
        define('AUTOSAVE_INTERVAL', 60);
    }
}
add_action('init', 'gyousei_limit_revisions');

// 古いリビジョンの定期的な削除
function gyousei_cleanup_old_revisions() {
    global $wpdb;
    
    // 30日以上前のリビジョンを削除
    $wpdb->query("
        DELETE FROM {$wpdb->posts} 
        WHERE post_type = 'revision' 
        AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    
    // 孤立したメタデータを削除
    $wpdb->query("
        DELETE pm FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.ID IS NULL
    ");
}

// 週次クリーンアップをスケジュール
if (!wp_next_scheduled('gyousei_weekly_cleanup')) {
    wp_schedule_event(time(), 'weekly', 'gyousei_weekly_cleanup');
}
add_action('gyousei_weekly_cleanup', 'gyousei_cleanup_old_revisions');

/**
 * 高度な機能（今後の拡張用）
 */

// サイトマップ生成機能（基本版）
function gyousei_generate_sitemap() {
    $posts = get_posts(array(
        'numberposts' => -1,
        'post_type' => 'post',
        'post_status' => 'publish'
    ));
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // ホームページ
    $sitemap .= '<url>' . "\n";
    $sitemap .= '<loc>' . home_url() . '</loc>' . "\n";
    $sitemap .= '<lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    $sitemap .= '<changefreq>daily</changefreq>' . "\n";
    $sitemap .= '<priority>1.0</priority>' . "\n";
    $sitemap .= '</url>' . "\n";
    
    // 投稿ページ
    foreach ($posts as $post) {
        $sitemap .= '<url>' . "\n";
        $sitemap .= '<loc>' . get_permalink($post->ID) . '</loc>' . "\n";
        $sitemap .= '<lastmod>' . date('Y-m-d', strtotime($post->post_modified)) . '</lastmod>' . "\n";
        $sitemap .= '<changefreq>weekly</changefreq>' . "\n";
        $sitemap .= '<priority>0.8</priority>' . "\n";
        $sitemap .= '</url>' . "\n";
    }
    
    $sitemap .= '</urlset>';
    
    return $sitemap;
}

// RSS Feed カスタマイズ
function gyousei_custom_rss_feed() {
    add_filter('the_excerpt_rss', 'gyousei_rss_excerpt');
    add_filter('the_content_feed', 'gyousei_rss_content');
}
add_action('init', 'gyousei_custom_rss_feed');

function gyousei_rss_excerpt($excerpt) {
    return wp_trim_words($excerpt, 50) . '...';
}

function gyousei_rss_content($content) {
    $footer = "\n\n---\n続きを読む: " . get_permalink() . "\n";
    $footer .= "行政書士の道 - " . home_url();
    return $content . $footer;
}

/**
 * バックアップとエクスポート機能
 */

// 学習データのエクスポート機能
function gyousei_export_study_data() {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    
    $study_data = array(
        'settings' => gyousei_get_study_progress_data(),
        'posts' => get_posts(array(
            'numberposts' => -1,
            'post_status' => 'publish'
        )),
        'categories' => get_categories(array('hide_empty' => false)),
        'export_date' => current_time('mysql')
    );
    
    $filename = 'gyousei_study_data_' . date('Y-m-d') . '.json';
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo json_encode($study_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// エクスポート用の管理画面ページ
function gyousei_add_export_menu() {
    add_management_page(
        '学習データエクスポート',
        '学習データエクスポート',
        'manage_options',
        'gyousei-export',
        'gyousei_export_page'
    );
}
add_action('admin_menu', 'gyousei_add_export_menu');

function gyousei_export_page() {
    if (isset($_POST['export_data']) && wp_verify_nonce($_POST['export_nonce'], 'gyousei_export')) {
        gyousei_export_study_data();
    }
    ?>
    <div class="wrap">
        <h1>学習データエクスポート</h1>
        <p>学習設定、投稿データ、カテゴリーをJSONファイルとしてエクスポートできます。</p>
        
        <form method="post">
            <?php wp_nonce_field('gyousei_export', 'export_nonce'); ?>
            <input type="submit" name="export_data" class="button button-primary" value="データをエクスポート" />
        </form>
        
        <h2>エクスポートされるデータ</h2>
        <ul>
            <li>学習進捗設定</li>
            <li>公開済み投稿データ</li>
            <li>カテゴリー情報</li>
            <li>エクスポート日時</li>
        </ul>
        
        <div class="notice notice-info">
            <p><strong>注意:</strong> このエクスポート機能は基本的なデータのみを対象としています。画像ファイルやテーマ設定は含まれません。</p>
        </div>
    </div>
    <?php
}

/**
 * 管理画面フッターカスタマイズ
 */

// 管理画面フッターのカスタマイズ
function gyousei_admin_footer_text($footer_text) {
    return '「行政書士の道」で学習記録を管理中 | <a href="' . home_url() . '" target="_blank">サイトを表示</a>';
}
add_filter('admin_footer_text', 'gyousei_admin_footer_text');

function gyousei_admin_footer_version($version) {
    $theme = wp_get_theme();
    return 'テーマバージョン: ' . $theme->get('Version');
}
add_filter('update_footer', 'gyousei_admin_footer_version', 11);

/**
 * カスタム投稿タイプ（将来の拡張用）
 */

// 学習ノート用カスタム投稿タイプ（今後使用予定）
function gyousei_register_study_notes_post_type() {
    register_post_type('study_notes', array(
        'labels' => array(
            'name' => '学習ノート',
            'singular_name' => '学習ノート',
            'add_new_item' => '新しい学習ノートを追加',
            'edit_item' => '学習ノートを編集',
            'view_item' => '学習ノートを表示'
        ),
        'public' => false, // 将来的に有効化予定
        'show_ui' => false, // 将来的に有効化予定
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'study-notes'),
        'menu_icon' => 'dashicons-edit-page'
    ));
}
// add_action('init', 'gyousei_register_study_notes_post_type'); // 現在はコメントアウト

/**
 * デバッグとログ機能
 */

// デバッグ情報をログに記録
function gyousei_debug_log($message, $data = null) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $log_message = '[行政書士の道] ' . $message;
        if ($data !== null) {
            $log_message .= ' | データ: ' . print_r($data, true);
        }
        error_log($log_message);
    }
}

// パフォーマンス監視（デバッグ用）
function gyousei_performance_monitor() {
    if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
        add_action('wp_footer', function() {
            $memory_usage = memory_get_peak_usage(true) / 1024 / 1024;
            $execution_time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            echo "<!-- パフォーマンス情報: メモリ使用量 {$memory_usage}MB, 実行時間 {$execution_time}秒 -->";
        });
    }
}
add_action('init', 'gyousei_performance_monitor');

/**
 * 緊急メンテナンス機能
 */

// 緊急時のメンテナンスモード
function gyousei_emergency_maintenance() {
    $maintenance_mode = get_option('gyousei_maintenance_mode', false);
    
    if ($maintenance_mode && !current_user_can('manage_options')) {
        wp_die(
            '<h1>メンテナンス中</h1><p>現在サイトのメンテナンスを行っております。しばらくお待ちください。</p>',
            'メンテナンス中 - 行政書士の道',
            array('response' => 503)
        );
    }
}
add_action('init', 'gyousei_emergency_maintenance');

// メンテナンスモード設定のための管理画面
function gyousei_add_maintenance_settings() {
    add_settings_field(
        'gyousei_maintenance_mode',
        'メンテナンスモード',
        'gyousei_maintenance_mode_callback',
        'general',
        'gyousei_study_settings'
    );
    
    register_setting('general', 'gyousei_maintenance_mode');
}
add_action('admin_init', 'gyousei_add_maintenance_settings');

function gyousei_maintenance_mode_callback() {
    $maintenance_mode = get_option('gyousei_maintenance_mode', false);
    echo '<label>';
    echo '<input type="checkbox" name="gyousei_maintenance_mode" value="1" ' . checked(1, $maintenance_mode, false) . ' />';
    echo ' メンテナンスモードを有効にする';
    echo '</label>';
    echo '<p class="description">チェックすると、管理者以外はサイトにアクセスできなくなります。</p>';
}

/**
 * 最終処理とクリーンアップ
 */

// テーマの無効化時の処理
function gyousei_theme_deactivation() {
    // スケジュールされたイベントをクリア
    wp_clear_scheduled_hook('gyousei_weekly_cleanup');
    
    // 一時的なオプションをクリーンアップ
    delete_option('gyousei_temp_data');
    
    // デバッグログ
    gyousei_debug_log('テーマが無効化されました');
}
add_action('switch_theme', 'gyousei_theme_deactivation');

// 最終的なセットアップ完了の確認
function gyousei_setup_complete() {
    if (!get_option('gyousei_setup_complete')) {
        // 初回セットアップ処理
        gyousei_create_subject_categories();
        
        // デフォルト設定の保存
        if (!get_option('gyousei_study_start_date')) {
            update_option('gyousei_study_start_date', date('Y-m-d'));
        }
        
        // セットアップ完了フラグ
        update_option('gyousei_setup_complete', true);
        
        gyousei_debug_log('初回セットアップが完了しました');
    }
}
add_action('after_setup_theme', 'gyousei_setup_complete');

/**
 * 使用方法とコメント
 * 
 * このfunctions.phpファイルは以下の機能を提供します：
 * 
 * 1. 基本テーマ機能
 *    - Astra子テーマの基本設定
 *    - フロントページ用アセットの読み込み
 * 
 * 2. 学習進捗管理
 *    - 学習統計の管理と表示
 *    - 管理画面での設定機能
 * 
 * 3. SEO対策
 *    - OGPタグ自動生成
 *    - 構造化データの追加
 * 
 * 4. セキュリティ強化
 *    - ファイルアップロード制限
 *    - REST API制限
 * 
 * 5. パフォーマンス最適化
 *    - 不要スクリプトの削除
 *    - リビジョン制限
 * 
 * 6. 管理画面カスタマイズ
 *    - カスタムダッシュボードウィジェット
 *    - 学習データエクスポート機能
 * 
 * 7. 将来の拡張に備えた機能
 *    - Ajax処理の基盤
 *    - ショートコード機能
 *    - カスタム投稿タイプの準備
 * 
 * 使用方法：
 * 1. このファイルをAstra子テーマのfunctions.phpとして保存
 * 2. WordPress管理画面の「設定」→「一般」で学習データを設定
 * 3. 「ツール」→「科目カテゴリー作成」でカテゴリーを作成
 * 4. 必要に応じてメンテナンスモードや他の機能を有効化
 */

?>