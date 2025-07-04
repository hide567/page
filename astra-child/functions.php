<?php
/**
 * Astra子テーマ - 行政書士の道 functions.php
 */

// 直接アクセスを防止
if (!defined('ABSPATH')) {
    exit;
}

// Astraの親テーマスタイルを読み込み（必須）
function astra_child_enqueue_styles() {
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');

/**
 * 学習進捗管理機能
 */

// 学習進捗ウィジェット
class Gyouseishoshi_Progress_Widget extends WP_Widget {
    
    function __construct() {
        parent::__construct(
            'gyouseishoshi_progress',
            __('学習進捗状況', 'gyouseishoshi'),
            array('description' => __('各科目の学習進捗を表示します。', 'gyouseishoshi'))
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        // 進捗データ取得
        $progress_data = get_option('gyouseishoshi_progress_data', array(
            'constitutional' => array('percent' => 75, 'completed' => 11, 'total' => 15, 'name' => '憲法'),
            'administrative' => array('percent' => 60, 'completed' => 9, 'total' => 15, 'name' => '行政法'),
            'civil' => array('percent' => 45, 'completed' => 9, 'total' => 20, 'name' => '民法'),
            'commercial' => array('percent' => 30, 'completed' => 3, 'total' => 10, 'name' => '商法・会社法')
        ));
        
        ?>
        <div class="progress-widget">
            <?php foreach ($progress_data as $key => $data) : ?>
            <div class="progress-item">
                <p class="subject-name"><?php echo esc_html($data['name']); ?></p>
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo esc_attr($data['percent']); ?>%;"></div>
                </div>
                <div class="progress-stats">
                    <span><?php echo esc_html($data['percent']); ?>%</span>
                    <span><?php echo esc_html($data['completed']); ?>/<?php echo esc_html($data['total']); ?>章完了</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('学習進捗状況', 'gyouseishoshi');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('タイトル:', 'gyouseishoshi'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// ウィジェット登録
function gyouseishoshi_register_widgets() {
    register_widget('Gyouseishoshi_Progress_Widget');
}
add_action('widgets_init', 'gyouseishoshi_register_widgets');

/**
 * 試験カウントダウン機能
 */
function gyouseishoshi_exam_countdown() {
    // 試験日を設定（2025年11月9日）
    $exam_date = strtotime('2025-11-09');
    $today = current_time('timestamp');
    
    // 残り日数計算
    $days_left = floor(($exam_date - $today) / (60 * 60 * 24));
    
    if ($days_left > 0) {
        $countdown_html = '<div class="exam-countdown">';
        $countdown_html .= '行政書士試験まであと <span class="countdown-number">' . $days_left . '</span> 日';
        $countdown_html .= '</div>';
        return $countdown_html;
    } else {
        return '<div class="exam-countdown">試験お疲れ様でした！</div>';
    }
}

// カウントダウンショートコード
function gyouseishoshi_countdown_shortcode() {
    return gyouseishoshi_exam_countdown();
}
add_shortcode('exam_countdown', 'gyouseishoshi_countdown_shortcode');

/**
 * 管理画面の設定ページを追加
 */
function gyouseishoshi_add_admin_menu() {
    add_options_page(
        __('学習進捗管理', 'gyouseishoshi'),
        __('学習進捗管理', 'gyouseishoshi'),
        'manage_options',
        'gyouseishoshi-progress',
        'gyouseishoshi_progress_page'
    );
}
add_action('admin_menu', 'gyouseishoshi_add_admin_menu');

// 設定ページの内容
function gyouseishoshi_progress_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // 保存処理
    if (isset($_POST['submit_progress']) && wp_verify_nonce($_POST['_wpnonce'], 'gyouseishoshi_progress_nonce')) {
        $progress_data = array(
            'constitutional' => array(
                'percent' => intval($_POST['constitutional_percent']),
                'completed' => intval($_POST['constitutional_completed']),
                'total' => intval($_POST['constitutional_total']),
                'name' => '憲法'
            ),
            'administrative' => array(
                'percent' => intval($_POST['administrative_percent']),
                'completed' => intval($_POST['administrative_completed']),
                'total' => intval($_POST['administrative_total']),
                'name' => '行政法'
            ),
            'civil' => array(
                'percent' => intval($_POST['civil_percent']),
                'completed' => intval($_POST['civil_completed']),
                'total' => intval($_POST['civil_total']),
                'name' => '民法'
            ),
            'commercial' => array(
                'percent' => intval($_POST['commercial_percent']),
                'completed' => intval($_POST['commercial_completed']),
                'total' => intval($_POST['commercial_total']),
                'name' => '商法・会社法'
            )
        );
        
        update_option('gyouseishoshi_progress_data', $progress_data);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('進捗状況を更新しました。', 'gyouseishoshi') . '</p></div>';
    }
    
    // 現在の進捗データを取得
    $progress_data = get_option('gyouseishoshi_progress_data', array(
        'constitutional' => array('percent' => 75, 'completed' => 11, 'total' => 15, 'name' => '憲法'),
        'administrative' => array('percent' => 60, 'completed' => 9, 'total' => 15, 'name' => '行政法'),
        'civil' => array('percent' => 45, 'completed' => 9, 'total' => 20, 'name' => '民法'),
        'commercial' => array('percent' => 30, 'completed' => 3, 'total' => 10, 'name' => '商法・会社法')
    ));
    
    ?>
    <div class="wrap">
        <h1><?php _e('学習進捗管理', 'gyouseishoshi'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('gyouseishoshi_progress_nonce'); ?>
            <table class="form-table">
                <?php foreach ($progress_data as $key => $data) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($data['name']); ?></th>
                    <td>
                        <label><?php _e('進捗率(%):', 'gyouseishoshi'); ?> 
                            <input type="number" name="<?php echo esc_attr($key); ?>_percent" value="<?php echo esc_attr($data['percent']); ?>" min="0" max="100">
                        </label><br>
                        <label><?php _e('完了章数:', 'gyouseishoshi'); ?> 
                            <input type="number" name="<?php echo esc_attr($key); ?>_completed" value="<?php echo esc_attr($data['completed']); ?>" min="0">
                        </label><br>
                        <label><?php _e('総章数:', 'gyouseishoshi'); ?> 
                            <input type="number" name="<?php echo esc_attr($key); ?>_total" value="<?php echo esc_attr($data['total']); ?>" min="1">
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <p class="submit">
                <input type="submit" name="submit_progress" id="submit" class="button button-primary" value="<?php _e('変更を保存', 'gyouseishoshi'); ?>">
            </p>
        </form>
    </div>
    <?php
}

/**
 * コミュニティ機能のカスタム投稿タイプ
 */
function register_community_post_types() {
    // トピック用のカスタム投稿タイプ
    register_post_type('community_topic', array(
        'labels' => array(
            'name' => 'コミュニティトピック',
            'singular_name' => 'トピック',
            'add_new' => '新規追加',
            'add_new_item' => '新しいトピックを追加',
            'edit_item' => 'トピックを編集',
            'new_item' => '新しいトピック',
            'view_item' => 'トピックを表示',
            'search_items' => 'トピックを検索',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'author', 'comments', 'excerpt'),
        'menu_icon' => 'dashicons-format-chat',
        'rewrite' => array('slug' => 'topics'),
        'show_in_rest' => true,
    ));
    
    // 質問用のカスタム投稿タイプ
    register_post_type('community_question', array(
        'labels' => array(
            'name' => 'コミュニティ質問',
            'singular_name' => '質問',
            'add_new' => '新規追加',
            'add_new_item' => '新しい質問を追加',
            'edit_item' => '質問を編集',
            'new_item' => '新しい質問',
            'view_item' => '質問を表示',
            'search_items' => '質問を検索',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'author', 'comments', 'excerpt'),
        'menu_icon' => 'dashicons-format-status',
        'rewrite' => array('slug' => 'questions'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'register_community_post_types');

// カスタムタクソノミー（カテゴリー）の登録
function register_community_taxonomies() {
    // トピックのカテゴリー
    register_taxonomy('topic_category', 'community_topic', array(
        'labels' => array(
            'name' => 'トピックカテゴリー',
            'singular_name' => 'カテゴリー'
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'topic-category')
    ));
    
    // 質問のカテゴリー
    register_taxonomy('question_category', 'community_question', array(
        'labels' => array(
            'name' => '質問カテゴリー',
            'singular_name' => 'カテゴリー'
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'question-category')
    ));
}
add_action('init', 'register_community_taxonomies');

/**
 * Astraテーマのカスタマイザー追加設定
 */
function gyouseishoshi_customize_register($wp_customize) {
    // 新しいセクションを追加
    $wp_customize->add_section('gyouseishoshi_settings', array(
        'title' => __('行政書士試験設定', 'gyouseishoshi'),
        'priority' => 30,
    ));
    
    // 試験日設定
    $wp_customize->add_setting('exam_date', array(
        'default' => '2025-11-09',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('exam_date', array(
        'label' => __('試験日', 'gyouseishoshi'),
        'section' => 'gyouseishoshi_settings',
        'type' => 'date',
    ));
    
    // プライマリーカラー設定
    $wp_customize->add_setting('primary_color', array(
        'default' => '#4a6fa5',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('プライマリーカラー', 'gyouseishoshi'),
        'section' => 'gyouseishoshi_settings',
    )));
}
add_action('customize_register', 'gyouseishoshi_customize_register');

/**
 * ショートコード登録
 */

// 注釈ボックス
function note_box_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'type' => 'note'
    ), $atts, 'note');
    
    $class = ($atts['type'] === 'important') ? 'important-box' : 'note-box';
    return '<div class="' . esc_attr($class) . '">' . do_shortcode($content) . '</div>';
}
add_shortcode('note', 'note_box_shortcode');

// 重要ボックス
function important_box_shortcode($atts, $content = null) {
    return '<div class="important-box">' . do_shortcode($content) . '</div>';
}
add_shortcode('important', 'important_box_shortcode');

/**
 * フロントエンド用のスクリプトとスタイルを追加
 */
function gyouseishoshi_enqueue_scripts() {
    // カスタムスタイル
    wp_enqueue_style('gyouseishoshi-custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), '1.0.0');
    
    // コミュニティ機能用スクリプト（コミュニティページのみ）
    if (is_page('community') || is_singular(array('community_topic', 'community_question'))) {
        wp_enqueue_script('gyouseishoshi-community', get_stylesheet_directory_uri() . '/js/community.js', array('jquery'), '1.0.0', true);
        wp_localize_script('gyouseishoshi-community', 'gyouseishoshi_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gyouseishoshi_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'gyouseishoshi_enqueue_scripts');

/**
 * Astraのフックを使ってカウントダウンを表示
 */
function add_countdown_to_header() {
    if (is_front_page()) {
        echo gyouseishoshi_exam_countdown();
    }
}
add_action('astra_masthead_content', 'add_countdown_to_header');

/**
 * カスタムCSSをheadに追加
 */
function gyouseishoshi_custom_css() {
    $primary_color = get_theme_mod('primary_color', '#4a6fa5');
    ?>
    <style type="text/css">
        :root {
            --gyouseishoshi-primary: <?php echo esc_html($primary_color); ?>;
            --gyouseishoshi-secondary: #334e68;
        }
        
        .exam-countdown {
            background-color: var(--gyouseishoshi-primary);
            color: white;
            padding: 10px 15px;
            text-align: center;
            font-weight: bold;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .countdown-number {
            font-size: 1.4em;
            color: #f9ca24;
        }
        
        .progress-widget .progress-item {
            margin-bottom: 15px;
        }
        
        .progress-widget .subject-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .progress-widget .progress-bar {
            height: 15px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 5px;
            overflow: hidden;
        }
        
        .progress-widget .progress {
            height: 100%;
            background-color: var(--gyouseishoshi-primary);
            transition: width 0.3s ease;
        }
        
        .progress-widget .progress-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
        }
        
        .note-box {
            background-color: #f8f9fa;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .important-box {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
    <?php
}
add_action('wp_head', 'gyouseishoshi_custom_css');

// 初期データ設定（テーマ有効化時）
function gyouseishoshi_theme_setup() {
    // 必要に応じて初期データを設定
    if (!get_option('gyouseishoshi_progress_data')) {
        $default_progress = array(
            'constitutional' => array('percent' => 0, 'completed' => 0, 'total' => 15, 'name' => '憲法'),
            'administrative' => array('percent' => 0, 'completed' => 0, 'total' => 15, 'name' => '行政法'),
            'civil' => array('percent' => 0, 'completed' => 0, 'total' => 20, 'name' => '民法'),
            'commercial' => array('percent' => 0, 'completed' => 0, 'total' => 10, 'name' => '商法・会社法')
        );
        update_option('gyouseishoshi_progress_data', $default_progress);
    }
}
add_action('after_switch_theme', 'gyouseishoshi_theme_setup');
?>