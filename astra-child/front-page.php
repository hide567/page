<?php
/**
 * 行政書士の道 - Astra互換動的フロントページ
 * Astraテーマのヘッダー・フッターを使用し、競合を避ける設計
 */

get_header(); ?>

<!-- カスタムフロントページスタイル -->
<style>
:root {
    --primary-color: #3f51b5;
    --secondary-color: #303f9f;
    --accent-color: #ffc107;
    --text-color: #333;
    --light-bg: #f8f9fa;
    --border-color: #e0e0e0;
    --shadow: 0 2px 10px rgba(0,0,0,0.1);
    --border-radius: 8px;
}

.gyousei-front-page {
    width: 100%;
    margin: 0;
    padding: 0;
}

.gyousei-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ヒーローセクション */
.gyousei-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 60px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.gyousei-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23pattern)"/></svg>');
    animation: float 20s infinite linear;
}

@keyframes float {
    0% { transform: translateX(0); }
    100% { transform: translateX(-20px); }
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.5rem;
    margin-bottom: 20px;
    opacity: 0.9;
}

.hero-description {
    font-size: 1.1rem;
    margin-bottom: 30px;
    opacity: 0.8;
}

.hero-cta {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-hero {
    display: inline-block;
    padding: 12px 30px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: white;
    color: var(--primary-color);
}

.btn-primary:hover {
    background: var(--light-bg);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-secondary:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-2px);
}

/* 統計セクション */
.gyousei-stats {
    background: var(--light-bg);
    padding: 40px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-item {
    background: white;
    padding: 30px 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: transform 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
}

.stat-item:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1rem;
    color: var(--text-color);
    font-weight: 500;
}

/* 最新記事セクション */
.gyousei-latest-posts {
    padding: 60px 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 40px;
}

.section-title {
    font-size: 2.2rem;
    color: var(--text-color);
    margin-bottom: 10px;
    font-weight: bold;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 0;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.post-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
    height: 200px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.post-card:hover .post-thumbnail img {
    transform: scale(1.05);
}

.post-content {
    padding: 20px;
}

.post-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.post-date {
    color: #666;
}

.post-category {
    background: var(--primary-color);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.post-title {
    margin-bottom: 15px;
}

.post-title a {
    color: var(--text-color);
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: 600;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-title a:hover {
    color: var(--primary-color);
}

.post-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.post-author {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #666;
}

.post-author img {
    border-radius: 50%;
}

.read-time {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.9rem;
    color: #666;
}

/* 科目別セクション */
.gyousei-subjects {
    background: var(--light-bg);
    padding: 60px 0;
}

.subjects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.subject-card {
    background: white;
    padding: 30px 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.subject-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-color);
}

.subject-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 1.5rem;
}

.subject-title {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: var(--text-color);
}

.subject-count {
    color: #666;
    margin-bottom: 10px;
    font-weight: 500;
}

.subject-description {
    color: #999;
    font-size: 0.9rem;
    margin-bottom: 20px;
    line-height: 1.4;
}

.subject-link {
    display: inline-block;
    background: var(--accent-color);
    color: var(--text-color);
    padding: 8px 20px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.subject-link:hover {
    background: #ffb300;
    transform: scale(1.05);
}

.section-footer {
    text-align: center;
}

.btn-more {
    display: inline-block;
    background: var(--primary-color);
    color: white;
    padding: 12px 30px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-more:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    color: white;
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-cta {
        flex-direction: column;
        align-items: center;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .subjects-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .gyousei-hero {
        padding: 40px 0;
    }
    
    .hero-title {
        font-size: 1.8rem;
    }
    
    .stats-grid,
    .subjects-grid {
        grid-template-columns: 1fr;
    }
    
    .post-footer {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>

<div class="gyousei-front-page">
    <!-- ヒーローセクション -->
    <section class="gyousei-hero">
        <div class="gyousei-container">
            <div class="hero-content">
                <h1 class="hero-title">行政書士の道</h1>
                <p class="hero-subtitle">わかりやすい解説の集積地</p>
                <p class="hero-description">
                    行政書士試験合格を目指す個人の学習記録・解説サイト<br>
                    法律初心者でも理解できる丁寧な解説を心がけています
                </p>
                
                <div class="hero-cta">
                    <a href="#latest-posts" class="btn-hero btn-primary">最新記事を見る</a>
                    <a href="#subjects" class="btn-hero btn-secondary">科目別に学習する</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 統計情報セクション -->
    <section class="gyousei-stats">
        <div class="gyousei-container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php 
                        $total_posts = wp_count_posts();
                        echo $total_posts->publish;
                    ?></div>
                    <div class="stat-label">解説記事</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php 
                        echo gyousei_get_monthly_posts_count();
                    ?></div>
                    <div class="stat-label">今月の記事</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php 
                        echo gyousei_count_active_subjects();
                    ?></div>
                    <div class="stat-label">対応科目</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php 
                        echo gyousei_calculate_study_days();
                    ?></div>
                    <div class="stat-label">学習日数</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 最新記事セクション -->
    <section class="gyousei-latest-posts" id="latest-posts">
        <div class="gyousei-container">
            <div class="section-header">
                <h2 class="section-title">最新の学習記録</h2>
                <p class="section-subtitle">日々の勉強内容と理解のまとめ</p>
            </div>

            <div class="posts-grid">
                <?php
                $latest_posts = new WP_Query(array(
                    'posts_per_page' => 6,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => '_thumbnail_id',
                            'compare' => 'EXISTS'
                        ),
                        array(
                            'key' => '_thumbnail_id',
                            'compare' => 'NOT EXISTS'
                        )
                    )
                ));

                if ($latest_posts->have_posts()) :
                    while ($latest_posts->have_posts()) : $latest_posts->the_post();
                        $categories = get_the_category();
                        $category_name = !empty($categories) ? $categories[0]->name : '未分類';
                        $category_icons = array(
                            '憲法' => '📜',
                            '行政法' => '⚖️',
                            '民法' => '📋',
                            '商法' => '🏢',
                            '基礎法学' => '🔍',
                            '一般知識' => '📝'
                        );
                        $icon = isset($category_icons[$category_name]) ? $category_icons[$category_name] : '📚';
                ?>
                <article class="post-card">
                    <div class="post-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                        <?php else : ?>
                            <div style="font-size: 3rem;"><?php echo $icon; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-date"><?php echo get_the_date('Y.m.d'); ?></span>
                            <span class="post-category"><?php echo esc_html($category_name); ?></span>
                        </div>
                        
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="post-excerpt">
                            <?php 
                            if (has_excerpt()) {
                                echo wp_trim_words(get_the_excerpt(), 30, '...');
                            } else {
                                echo wp_trim_words(get_the_content(), 30, '...');
                            }
                            ?>
                        </div>
                        
                        <div class="post-footer">
                            <div class="post-author">
                                <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                <span><?php the_author(); ?></span>
                            </div>
                            
                            <div class="read-time">
                                ⏱️ <?php 
                                $content = get_post_field('post_content', get_the_ID());
                                $word_count = str_word_count(strip_tags($content));
                                $reading_time = max(1, ceil($word_count / 200));
                                echo $reading_time;
                                ?>分
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
                <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="btn-more">すべての記事を見る</a>
            </div>
        </div>
    </section>

    <!-- 科目別コンテンツセクション -->
    <section class="gyousei-subjects" id="subjects">
        <div class="gyousei-container">
            <div class="section-header">
                <h2 class="section-title">科目別学習コンテンツ</h2>
                <p class="section-subtitle">体系的に学べる科目別解説</p>
            </div>

            <div class="subjects-grid">
                <?php
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

                foreach ($subjects as $subject) :
                    // カテゴリーの投稿数を取得
                    $category = get_category_by_slug($subject['slug']);
                    if (!$category) {
                        $categories = get_categories(array('name' => $subject['name'], 'hide_empty' => false));
                        $category = !empty($categories) ? $categories[0] : null;
                    }
                    $post_count = $category ? $category->count : 0;
                    $category_url = $category ? get_category_link($category->term_id) : '#';
                ?>
                <div class="subject-card">
                    <div class="subject-icon"><?php echo $subject['icon']; ?></div>
                    <h3 class="subject-title"><?php echo esc_html($subject['name']); ?></h3>
                    <p class="subject-count"><?php echo $post_count; ?>記事</p>
                    <p class="subject-description"><?php echo esc_html($subject['description']); ?></p>
                    <a href="<?php echo esc_url($category_url); ?>" class="subject-link">学習する</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<!-- JavaScript for dynamic features -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // スムーススクロール
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // 統計数字のカウントアップアニメーション
    function animateCountUp() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    }

    // 統計セクションが表示されたらカウントアップ開始
    const statsSection = document.querySelector('.gyousei-stats');
    if (statsSection && 'IntersectionObserver' in window) {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCountUp();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        statsObserver.observe(statsSection);
    }

    // フェードイン効果
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // 各セクションにフェードイン効果を適用
    document.querySelectorAll('.gyousei-stats, .gyousei-latest-posts, .gyousei-subjects').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
});
</script>

<?php get_footer(); ?>