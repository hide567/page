<?php
/**
 * 行政書士の道 - functions.php 完全統合版
 * Astra子テーマ用 - 重複関数エラー解決済み
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

// テーマ有効化時に科目カテゴリーを作成
function gyousei_theme_activation() {
    gyousei_create_subject_categories();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gyousei_theme_activation');

/**
 * 動的データ取得関数（統合版）
 */

// 今月の投稿数を取得（改良版）
function gyousei_get_monthly_posts_count() {
    static $monthly_count = null;
    
    if ($monthly_count === null) {
        $current_month_posts = new WP_Query(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'year'  => date('Y'),
                    'month' => date('n'),
                ),
            ),
            'fields' => 'ids',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        ));
        $monthly_count = $current_month_posts->found_posts;
        wp_reset_postdata();
    }
    
    return $monthly_count;
}

// アクティブな科目数を数える（改良版）
function gyousei_count_active_subjects() {
    static $active_count = null;
    
    if ($active_count === null) {
        $subjects = array('憲法', '行政法', '民法', '商法', '基礎法学', '一般知識');
        $active_count = 0;
        
        foreach ($subjects as $subject) {
            $category = get_category_by_slug(sanitize_title($subject));
            if (!$category) {
                $categories = get_categories(array(
                    'name' => $subject, 
                    'hide_empty' => false,
                    'number' => 1
                ));
                $category = !empty($categories) ? $categories[0] : null;
            }
            
            if ($category && $category->count > 0) {
                $active_count++;
            }
        }
    }
    
    return $active_count;
}

// 学習開始からの経過日数を計算（改良版）
function gyousei_calculate_study_days() {
    static $study_days = null;
    
    if ($study_days === null) {
        $start_date = get_option('gyousei_study_start_date', date('Y-m-d'));
        $days_passed = (strtotime('now') - strtotime($start_date)) / (60 * 60 * 24);
        $study_days = max(1, floor($days_passed));
    }
    
    return $study_days;
}

// 人気記事を取得する関数
function gyousei_get_popular_posts($limit = 3) {
    $popular_posts = new WP_Query(array(
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'meta_key' => 'post_views_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    ));
    
    return $popular_posts;
}

// 最近更新された記事を取得
function gyousei_get_recently_updated_posts($limit = 5) {
    $updated_posts = new WP_Query(array(
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'modified',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => '1 month ago'
            )
        ),
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    ));
    
    return $updated_posts;
}

// 科目別の記事数を取得
function gyousei_get_subject_post_counts() {
    static $subject_counts = null;
    
    if ($subject_counts === null) {
        $subjects = array(
            'constitution' => '憲法',
            'administrative-law' => '行政法', 
            'civil-law' => '民法',
            'commercial-law' => '商法',
            'jurisprudence' => '基礎法学',
            'general-knowledge' => '一般知識'
        );
        
        $subject_counts = array();
        
        foreach ($subjects as $slug => $name) {
            $category = get_category_by_slug($slug);
            if (!$category) {
                $categories = get_categories(array('name' => $name, 'hide_empty' => false));
                $category = !empty($categories) ? $categories[0] : null;
            }
            $subject_counts[$slug] = $category ? $category->count : 0;
        }
    }
    
    return $subject_counts;
}

// 週間学習統計を取得
function gyousei_get_weekly_stats() {
    $weekly_stats = array();
    
    // 今週の投稿数
    $weekly_posts = new WP_Query(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'date_query' => array(
            array(
                'after' => '1 week ago'
            )
        ),
        'fields' => 'ids',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false
    ));
    $weekly_stats['posts'] = $weekly_posts->found_posts;
    wp_reset_postdata();
    
    // 今週のコメント数
    $weekly_comments = get_comments(array(
        'status' => 'approve',
        'date_query' => array(
            array(
                'after' => '1 week ago'
            )
        ),
        'count' => true
    ));
    $weekly_stats['comments'] = $weekly_comments;
    
    return $weekly_stats;
}

// リアルタイム統計データを取得（キャッシュ付き）
function gyousei_get_realtime_stats() {
    $cache_key = 'gyousei_realtime_stats';
    $cached_stats = wp_cache_get($cache_key);
    
    if ($cached_stats !== false) {
        return $cached_stats;
    }
    
    $stats = array(
        'total_posts' => wp_count_posts()->publish,
        'monthly_posts' => gyousei_get_monthly_posts_count(),
        'active_subjects' => gyousei_count_active_subjects(),
        'study_days' => gyousei_calculate_study_days(),
        'total_comments' => wp_count_comments()->approved,
        'weekly_stats' => gyousei_get_weekly_stats(),
        'last_updated' => current_time('Y-m-d H:i:s')
    );
    
    // 5分間キャッシュ
    wp_cache_set($cache_key, $stats, '', 300);
    
    return $stats;
}

// パフォーマンス最適化：トランジェントAPIを使用した長期キャッシュ
function gyousei_get_cached_subject_data() {
    $cache_key = 'gyousei_subject_data_v2';
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $subjects_data = array();
    $subjects = array(
        array(
            'name' => '憲法',
            'icon' => '📜',
            'slug' => 'constitution',
            'description' => '基本的人権、統治機構、憲法の基本原理について学習します'
        ),
        array(
            'name' => '行政法',
            'icon' => '⚖️',
            'slug' => 'administrative-law',
            'description' => '行政行為、行政手続、行政救済について学習します'
        ),
        array(
            'name' => '民法',
            'icon' => '📋',
            'slug' => 'civil-law',
            'description' => '総則、物権、債権、親族、相続について学習します'
        ),
        array(
            'name' => '商法',
            'icon' => '🏢',
            'slug' => 'commercial-law',
            'description' => '会社法、商取引法、有価証券法について学習します'
        ),
        array(
            'name' => '基礎法学',
            'icon' => '🔍',
            'slug' => 'jurisprudence',
            'description' => '法理論、法制史、外国法について学習します'
        ),
        array(
            'name' => '一般知識',
            'icon' => '📝',
            'slug' => 'general-knowledge',
            'description' => '政治、経済、社会、情報通信・個人情報保護について学習します'
        )
    );
    
    foreach ($subjects as $subject) {
        $category = get_category_by_slug($subject['slug']);
        if (!$category) {
            $categories = get_categories(array('name' => $subject['name'], 'hide_empty' => false));
            $category = !empty($categories) ? $categories[0] : null;
        }
        
        $subject['post_count'] = $category ? $category->count : 0;
        $subject['category_url'] = $category ? get_category_link($category->term_id) : '#';
        $subject['category_id'] = $category ? $category->term_id : 0;
        
        $subjects_data[] = $subject;
    }
    
    // 1時間キャッシュ
    set_transient($cache_key, $subjects_data, HOUR_IN_SECONDS);
    
    return $subjects_data;
}

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
    
    // メンテナンスモード設定
    add_settings_field(
        'gyousei_maintenance_mode',
        'メンテナンスモード',
        'gyousei_maintenance_mode_callback',
        'general',
        'gyousei_study_settings'
    );
    
    register_setting('general', 'gyousei_study_start_date');
    register_setting('general', 'gyousei_completion_rate');
    register_setting('general', 'gyousei_study_streak');
    register_setting('general', 'gyousei_total_hours');
    register_setting('general', 'gyousei_goals_achieved');
    register_setting('general', 'gyousei_maintenance_mode');
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

function gyousei_maintenance_mode_callback() {
    $maintenance_mode = get_option('gyousei_maintenance_mode', false);
    echo '<label>';
    echo '<input type="checkbox" name="gyousei_maintenance_mode" value="1" ' . checked(1, $maintenance_mode, false) . ' />';
    echo ' メンテナンスモードを有効にする';
    echo '</label>';
    echo '<p class="description">チェックすると、管理者以外はサイトにアクセスできなくなります。</p>';
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

/**
 * Ajax処理とリアルタイム機能
 */

// Ajax用の統計データ更新ハンドラー
function gyousei_ajax_update_stats() {
    // セキュリティチェック
    if (!wp_verify_nonce($_POST['nonce'], 'gyousei_front_page_nonce')) {
        wp_send_json_error('セキュリティチェックに失敗しました。');
    }
    
    // キャッシュをクリア
    wp_cache_delete('gyousei_realtime_stats');
    
    // 最新統計を取得
    $stats = gyousei_get_realtime_stats();
    
    wp_send_json_success($stats);
}
add_action('wp_ajax_gyousei_update_stats', 'gyousei_ajax_update_stats');
add_action('wp_ajax_nopriv_gyousei_update_stats', 'gyousei_ajax_update_stats');

// フロントページ用の動的JavaScript
function gyousei_enqueue_dynamic_front_page_assets() {
    if (is_front_page()) {
        // 動的統計更新用JavaScript
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // 統計の自動更新（5分ごと）
                function updateStats() {
                    $.ajax({
                        url: "' . admin_url('admin-ajax.php') . '",
                        type: "POST",
                        data: {
                            action: "gyousei_update_stats",
                            nonce: "' . wp_create_nonce('gyousei_front_page_nonce') . '"
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                // 統計数値を更新
                                $(".stat-item").eq(0).find(".stat-number").text(response.data.total_posts);
                                $(".stat-item").eq(1).find(".stat-number").text(response.data.monthly_posts);
                                $(".stat-item").eq(2).find(".stat-number").text(response.data.active_subjects);
                                $(".stat-item").eq(3).find(".stat-number").text(response.data.study_days);
                                
                                // アニメーション効果
                                $(".stat-number").addClass("updated");
                                setTimeout(function() {
                                    $(".stat-number").removeClass("updated");
                                }, 1000);
                            }
                        }
                    });
                }
                
                // 5分ごとに統計を更新
                setInterval(updateStats, 300000);
                
                // ページ表示から30秒後に初回更新
                setTimeout(updateStats, 30000);
            });
        ');
        
        // 動的要素用のCSS
        wp_add_inline_style('astra-child-style', '
            .stat-number.updated {
                animation: pulse 0.5s ease-in-out;
                color: #28a745 !important;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            
            .loading-stats {
                opacity: 0.7;
                pointer-events: none;
            }
            
            .stats-last-updated {
                text-align: center;
                font-size: 0.8rem;
                color: #666;
                margin-top: 10px;
                font-style: italic;
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'gyousei_enqueue_dynamic_front_page_assets', 20);

// 記事投稿/更新時にキャッシュをクリア
function gyousei_clear_stats_cache($post_id) {
    if (get_post_type($post_id) === 'post' && get_post_status($post_id) === 'publish') {
        wp_cache_delete('gyousei_realtime_stats');
        delete_transient('gyousei_subject_data_v2');
    }
}
add_action('save_post', 'gyousei_clear_stats_cache');
add_action('delete_post', 'gyousei_clear_stats_cache');

// カテゴリー変更時にもキャッシュをクリア
function gyousei_clear_stats_cache_on_category_change($post_id) {
    wp_cache_delete('gyousei_realtime_stats');
    delete_transient('gyousei_subject_data_v2');
}
add_action('set_object_terms', 'gyousei_clear_stats_cache_on_category_change');

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
    
    add_management_page(
        '学習データエクスポート',
        '学習データエクスポート',
        'manage_options',
        'gyousei-export',
        'gyousei_export_page'
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

// エクスポート機能
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
    $realtime_stats = gyousei_get_realtime_stats();
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
            <div class="dashboard-stat-number"><?php echo esc_html($realtime_stats['total_posts']); ?></div>
            <div class="dashboard-stat-label">総記事数</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($realtime_stats['monthly_posts']); ?></div>
            <div class="dashboard-stat-label">今月の記事数</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['study_streak']); ?></div>
            <div class="dashboard-stat-label">連続学習日数</div>
        </div>
        
        <div class="dashboard-stat">
            <div class="dashboard-stat-number"><?php echo esc_html($stats['completion_rate']); ?>%</div>
            <div class="dashboard-stat-label">学習完了率</div>
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
 * ショートコード機能
 */

// 学習統計表示ショートコード
function gyousei_study_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'all', // all, completion, streak, hours, goals
        'style' => 'default' // default, minimal, detailed
    ), $atts, 'gyousei_stats');
    
    $stats = gyousei_get_study_progress_data();
    $realtime_stats = gyousei_get_realtime_stats();
    
    $output = '<div class="gyousei-stats-shortcode style-' . esc_attr($atts['style']) . '">';
    
    if ($atts['type'] === 'all' || $atts['type'] === 'posts') {
        $output .= '<div class="stat-item">総記事数: ' . $realtime_stats['total_posts'] . '</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'monthly') {
        $output .= '<div class="stat-item">今月の記事数: ' . $realtime_stats['monthly_posts'] . '</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'subjects') {
        $output .= '<div class="stat-item">対応科目数: ' . $realtime_stats['active_subjects'] . '</div>';
    }
    if ($atts['type'] === 'all' || $atts['type'] === 'days') {
        $output .= '<div class="stat-item">学習日数: ' . $realtime_stats['study_days'] . '日</div>';
    }
    
    if ($atts['style'] === 'detailed') {
        $output .= '<div class="stat-meta">最終更新: ' . $realtime_stats['last_updated'] . '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('gyousei_stats', 'gyousei_study_stats_shortcode');

// 最新記事表示ショートコード
function gyousei_latest_posts_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 3,
        'category' => '',
        'show_excerpt' => 'true',
        'show_date' => 'true',
        'show_author' => 'false'
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
        
        if ($atts['show_date'] === 'true') {
            $output .= '<p class="post-date">' . get_the_date('Y.m.d', $post->ID) . '</p>';
        }
        
        if ($atts['show_author'] === 'true') {
            $output .= '<p class="post-author">投稿者: ' . get_the_author_meta('display_name', $post->post_author) . '</p>';
        }
        
        if ($atts['show_excerpt'] === 'true') {
            $excerpt = get_the_excerpt($post->ID);
            if (empty($excerpt)) {
                $excerpt = wp_trim_words(get_the_content('', false, $post->ID), 20);
            }
            $output .= '<p class="post-excerpt">' . esc_html($excerpt) . '</p>';
        }
        
        $output .= '</div>';
    }
    
    $output .= '</div>';
    wp_reset_postdata();
    
    return $output;
}
add_shortcode('gyousei_latest', 'gyousei_latest_posts_shortcode');

// 科目別統計ショートコード
function gyousei_subject_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'subject' => '',
        'display' => 'all' // all, count, recent
    ), $atts);
    
    if (empty($atts['subject'])) {
        return '';
    }
    
    $subjects_data = gyousei_get_cached_subject_data();
    $subject_data = null;
    
    foreach ($subjects_data as $subject) {
        if ($subject['slug'] === $atts['subject'] || $subject['name'] === $atts['subject']) {
            $subject_data = $subject;
            break;
        }
    }
    
    if (!$subject_data) {
        return '';
    }
    
    $output = '<div class="gyousei-subject-stats">';
    
    if ($atts['display'] === 'all' || $atts['display'] === 'count') {
        $output .= '<p class="subject-post-count">';
        $output .= '<strong>' . esc_html($subject_data['name']) . '</strong>: ';
        $output .= $subject_data['post_count'] . '記事';
        $output .= '</p>';
    }
    
    if ($atts['display'] === 'all' || $atts['display'] === 'recent') {
        if ($subject_data['category_id'] > 0) {
            $recent_posts = get_posts(array(
                'numberposts' => 3,
                'category' => $subject_data['category_id'],
                'post_status' => 'publish'
            ));
            
            if (!empty($recent_posts)) {
                $output .= '<div class="recent-subject-posts">';
                $output .= '<h4>最新記事</h4>';
                $output .= '<ul>';
                foreach ($recent_posts as $post) {
                    $output .= '<li><a href="' . get_permalink($post->ID) . '">';
                    $output .= esc_html($post->post_title);
                    $output .= '</a> <span class="post-date">(' . get_the_date('m/d', $post->ID) . ')</span></li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
            }
        }
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('gyousei_subject_stats', 'gyousei_subject_stats_shortcode');

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
 * デバッグ・統計・メンテナンス機能
 */

// デバッグ用：統計情報を管理画面に表示
function gyousei_add_stats_admin_notice() {
    if (current_user_can('manage_options') && isset($_GET['gyousei_debug_stats'])) {
        $stats = gyousei_get_realtime_stats();
        ?>
        <div class="notice notice-info">
            <h3>行政書士の道 - リアルタイム統計</h3>
            <ul>
                <li>総記事数: <?php echo $stats['total_posts']; ?></li>
                <li>今月の記事数: <?php echo $stats['monthly_posts']; ?></li>
                <li>アクティブ科目数: <?php echo $stats['active_subjects']; ?></li>
                <li>学習日数: <?php echo $stats['study_days']; ?></li>
                <li>総コメント数: <?php echo $stats['total_comments']; ?></li>
                <li>今週の投稿数: <?php echo $stats['weekly_stats']['posts']; ?></li>
                <li>今週のコメント数: <?php echo $stats['weekly_stats']['comments']; ?></li>
                <li>最終更新: <?php echo $stats['last_updated']; ?></li>
            </ul>
            <p>
                <a href="<?php echo admin_url('?gyousei_clear_cache=1'); ?>" class="button">キャッシュをクリア</a>
                <?php if (isset($_GET['cache_cleared'])): ?>
                    <span style="color: green; margin-left: 10px;">✅ キャッシュをクリアしました</span>
                <?php endif; ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'gyousei_add_stats_admin_notice');

// キャッシュクリア用
function gyousei_handle_cache_clear() {
    if (current_user_can('manage_options') && isset($_GET['gyousei_clear_cache'])) {
        wp_cache_delete('gyousei_realtime_stats');
        delete_transient('gyousei_subject_data_v2');
        
        wp_redirect(admin_url('?gyousei_debug_stats=1&cache_cleared=1'));
        exit;
    }
}
add_action('admin_init', 'gyousei_handle_cache_clear');

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

/**
 * 自動化・スケジュール機能
 */

// 統計データの定期的な更新（WP-Cronを使用）
function gyousei_schedule_stats_update() {
    if (!wp_next_scheduled('gyousei_hourly_stats_update')) {
        wp_schedule_event(time(), 'hourly', 'gyousei_hourly_stats_update');
    }
}
add_action('wp', 'gyousei_schedule_stats_update');

function gyousei_hourly_stats_refresh() {
    // キャッシュをクリアして新しいデータを取得
    wp_cache_delete('gyousei_realtime_stats');
    delete_transient('gyousei_subject_data_v2');
    
    // 新しい統計データを事前に生成
    gyousei_get_realtime_stats();
    gyousei_get_cached_subject_data();
}
add_action('gyousei_hourly_stats_update', 'gyousei_hourly_stats_refresh');

// 自動保存とリビジョンの制限
function gyousei_limit_revisions() {
    if (!defined('WP_POST_REVISIONS')) {
        define('WP_POST_REVISIONS', 3);
    }
    
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
 * REST API エンドポイント（将来のモバイルアプリ対応用）
 */

function gyousei_register_rest_endpoints() {
    register_rest_route('gyousei/v1', '/stats', array(
        'methods' => 'GET',
        'callback' => 'gyousei_rest_get_stats',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'gyousei_register_rest_endpoints');

function gyousei_rest_get_stats($request) {
    $stats = gyousei_get_realtime_stats();
    return new WP_REST_Response($stats, 200);
}

/**
 * パフォーマンス監視・デバッグ機能
 */

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

/**
 * 最終処理とクリーンアップ
 */

// テーマの無効化時の処理
function gyousei_theme_deactivation() {
    // スケジュールされたイベントをクリア
    wp_clear_scheduled_hook('gyousei_weekly_cleanup');
    wp_clear_scheduled_hook('gyousei_hourly_stats_update');
    
    // 一時的なオプションをクリーンアップ
    delete_option('gyousei_temp_data');
    
    // デバッグログ
    gyousei_debug_log('テーマが無効化されました');
}
add_action('switch_theme', 'gyousei_theme_deactivation');

// プラグイン/テーマ無効化時のクリーンアップ
function gyousei_cleanup_scheduled_events() {
    wp_clear_scheduled_hook('gyousei_hourly_stats_update');
    wp_clear_scheduled_hook('gyousei_weekly_cleanup');
}
register_deactivation_hook(__FILE__, 'gyousei_cleanup_scheduled_events');

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
 * 高度な機能（将来の拡張用）
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
 * Ajax処理（サンプル・今後拡張用）
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
 * 使用方法とコメント
 * 
 * このfunctions.phpファイルは以下の機能を提供します：
 * 
 * 1. 基本テーマ機能
 *    - Astra子テーマの基本設定
 *    - フロントページ用アセットの読み込み
 * 
 * 2. 動的データ取得
 *    - リアルタイム統計の取得と表示
 *    - キャッシュ機能による高速化
 * 
 * 3. 学習進捗管理
 *    - 学習統計の管理と表示
 *    - 管理画面での設定機能
 * 
 * 4. Ajax処理
 *    - 統計データのリアルタイム更新
 *    - セキュリティ対応済み
 * 
 * 5. 管理画面カスタマイズ
 *    - カスタムダッシュボードウィジェット
 *    - 科目カテゴリー管理
 *    - 学習データエクスポート機能
 * 
 * 6. ショートコード機能
 *    - 学習統計表示
 *    - 最新記事表示
 *    - 科目別統計表示
 * 
 * 7. SEO対策
 *    - OGPタグ自動生成
 *    - 構造化データの追加
 * 
 * 8. セキュリティ強化
 *    - ファイルアップロード制限
 *    - REST API制限
 * 
 * 9. パフォーマンス最適化
 *    - 不要スクリプトの削除
 *    - リビジョン制限
 *    - キャッシュ機能
 * 
 * 10. メンテナンス機能
 *     - 緊急メンテナンスモード
 *     - 自動クリーンアップ
 *     - デバッグ機能
 * 
 * 使用方法：
 * 1. このファイルをAstra子テーマのfunctions.phpとして保存
 * 2. WordPress管理画面の「設定」→「一般」で学習データを設定
 * 3. 「ツール」→「科目カテゴリー作成」でカテゴリーを作成
 * 4. 管理画面URL/?gyousei_debug_stats=1 でデバッグ情報を確認
 * 5. 必要に応じてメンテナンスモードや他の機能を有効化
 * 
 * ショートコード：
 * - [gyousei_stats] - 学習統計表示
 * - [gyousei_latest count="3"] - 最新記事表示  
 * - [gyousei_subject_stats subject="憲法"] - 科目別統計
 * 
 * デバッグ機能：
 * - 管理画面URL/?gyousei_debug_stats=1 - 統計情報表示
 * - 管理画面URL/?gyousei_clear_cache=1 - キャッシュクリア
 * 
 * 注意事項：
 * - 重複する関数定義エラーを避けるため、既存のfunctions.phpと統合済み
 * - パフォーマンスを考慮したキャッシュ機能を実装
 * - セキュリティ対策を各所に実装
 * - 将来の拡張性を考慮した設計
 */

?>