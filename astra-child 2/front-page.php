<?php
/**
 * 行政書士の道 - カスタムフロントページテンプレート
 * 完全整理版
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<?php if (astra_page_layout() == 'left-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <?php astra_primary_content_top(); ?>

    <main id="main" class="site-main">
        
        <?php astra_content_top(); ?>

        <!-- ヒーローセクション -->
        <section class="gyousei-hero-section">
            <div class="ast-container">
                <div class="hero-content">
                    <h1 class="hero-title">行政書士の道</h1>
                    <p class="hero-subtitle">わかりやすい解説の集積地</p>
                    <p class="hero-description">行政書士試験合格を目指す個人の学習記録・解説サイト</p>
                    
                    <!-- ショートコード挿入エリア（例：検索機能など） -->
                    <div class="hero-shortcode-area">
                        <?php 
                        // ここにショートコードを挿入可能
                        // 例: echo do_shortcode('[custom_search_form]'); 
                        ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- 統計情報セクション -->
        <section class="gyousei-stats-section">
            <div class="ast-container">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo wp_count_posts()->publish; ?></div>
                        <div class="stat-label">解説記事</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php 
                            // 今月の投稿数を取得
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
                            echo $current_month_posts->found_posts;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <div class="stat-label">今月の記事</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">6</div>
                        <div class="stat-label">対応科目</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php 
                            // 学習開始からの経過日数
                            $start_date = get_option('gyousei_study_start_date', date('Y-m-d'));
                            $days_passed = (strtotime('now') - strtotime($start_date)) / (60 * 60 * 24);
                            echo floor($days_passed);
                            ?>
                        </div>
                        <div class="stat-label">学習日数</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ショートコード挿入エリア1（広告・お知らせなど） -->
        <section class="gyousei-shortcode-section-1">
            <div class="ast-container">
                <?php 
                // ショートコード例：お知らせ、広告、特別コンテンツなど
                // echo do_shortcode('[announcement_banner]');
                // echo do_shortcode('[ad_banner location="top"]');
                ?>
            </div>
        </section>

        <!-- 最新記事一覧セクション -->
        <section class="gyousei-latest-posts">
            <div class="ast-container">
                <div class="section-header">
                    <h2 class="section-title">最新の学習記録</h2>
                    <p class="section-subtitle">日々の勉強内容と理解のまとめ</p>
                </div>

                <div class="posts-grid">
                    <?php
                    // 最新記事を5件取得
                    $latest_posts = new WP_Query(array(
                        'posts_per_page' => 5,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($latest_posts->have_posts()) :
                        while ($latest_posts->have_posts()) : $latest_posts->the_post();
                    ?>
                        <article class="post-card">
                            <div class="post-card-inner">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('class' => 'card-image')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date"><?php echo get_the_date('Y.m.d'); ?></span>
                                        <?php
                                        $categories = get_the_category();
                                        if (!empty($categories)) :
                                        ?>
                                            <span class="post-category"><?php echo esc_html($categories[0]->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h3 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="post-excerpt">
                                        <?php 
                                        $excerpt = get_the_excerpt();
                                        echo wp_trim_words($excerpt, 30, '...');
                                        ?>
                                    </div>
                                    
                                    <div class="post-footer">
                                        <div class="post-author">
                                            <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                            <span><?php the_author(); ?></span>
                                        </div>
                                        
                                        <div class="post-stats">
                                            <span class="read-time">
                                                ⏱️
                                                <?php 
                                                // 簡易読了時間計算
                                                $content = get_the_content();
                                                $word_count = str_word_count(strip_tags($content));
                                                $reading_time = max(1, ceil($word_count / 200));
                                                echo $reading_time;
                                                ?>分
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>

                <div class="section-footer">
                    <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="btn-more-posts">
                        すべての記事を見る
                    </a>
                </div>
            </div>
        </section>

        <!-- ショートコード挿入エリア2（中段広告など） -->
        <section class="gyousei-shortcode-section-2">
            <div class="ast-container">
                <?php 
                // ショートコード例：中段広告、関連サービス紹介など
                // echo do_shortcode('[middle_ad_banner]');
                // echo do_shortcode('[related_services]');
                ?>
            </div>
        </section>

        <!-- 科目別コンテンツセクション -->
        <section class="gyousei-subjects-section">
            <div class="ast-container">
                <div class="section-header">
                    <h2 class="section-title">科目別学習コンテンツ</h2>
                    <p class="section-subtitle">体系的に学べる科目別解説</p>
                </div>

                <div class="subjects-grid">
                    <?php
                    // 科目別カテゴリーを手動定義（アイコンと共に）
                    $subjects = array(
                        array(
                            'name' => '憲法',
                            'slug' => 'constitution',
                            'icon' => '📜',
                            'description' => '基本的人権、統治機構'
                        ),
                        array(
                            'name' => '行政法',
                            'slug' => 'administrative-law',
                            'icon' => '⚖️',
                            'description' => '行政行為、行政手続'
                        ),
                        array(
                            'name' => '民法',
                            'slug' => 'civil-law',
                            'icon' => '📚',
                            'description' => '総則、物権、債権'
                        ),
                        array(
                            'name' => '商法',
                            'slug' => 'commercial-law',
                            'icon' => '🏢',
                            'description' => '会社法、商取引法'
                        ),
                        array(
                            'name' => '基礎法学',
                            'slug' => 'jurisprudence',
                            'icon' => '🔍',
                            'description' => '法理論、法制史'
                        ),
                        array(
                            'name' => '一般知識',
                            'slug' => 'general-knowledge',
                            'icon' => '📝',
                            'description' => '政治、経済、社会'
                        )
                    );

                    foreach ($subjects as $subject) :
                        // スラッグでカテゴリーを取得
                        $category = get_category_by_slug($subject['slug']);
                        
                        // カテゴリーが存在しない場合は「カテゴリー名」で検索
                        if (!$category) {
                            $categories = get_categories(array(
                                'name' => $subject['name'],
                                'hide_empty' => false
                            ));
                            $category = !empty($categories) ? $categories[0] : null;
                        }
                        
                        // 記事数を取得（カテゴリーが存在する場合）
                        $post_count = $category ? $category->count : 0;
                        $category_link = $category ? get_category_link($category->term_id) : '#';
                    ?>
                        <div class="subject-card">
                            <div class="subject-icon">
                                <?php echo $subject['icon']; ?>
                            </div>
                            <h3 class="subject-title"><?php echo esc_html($subject['name']); ?></h3>
                            <p class="subject-count"><?php echo $post_count; ?>記事</p>
                            <p class="subject-description"><?php echo esc_html($subject['description']); ?></p>
                            <a href="<?php echo esc_url($category_link); ?>" class="subject-link">
                                学習する
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ショートコード挿入エリア3（下段コンテンツ） -->
        <section class="gyousei-shortcode-section-3">
            <div class="ast-container">
                <?php 
                // ショートコード例：ニュースレター登録、SNSフォロー、下段広告など
                // echo do_shortcode('[newsletter_signup]');
                // echo do_shortcode('[social_follow_buttons]');
                // echo do_shortcode('[bottom_ad_banner]');
                ?>
            </div>
        </section>

        <?php astra_content_bottom(); ?>

    </main><!-- #main -->

    <?php astra_primary_content_bottom(); ?>

</div><!-- #primary -->

<?php if (astra_page_layout() == 'right-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<?php get_footer(); ?>