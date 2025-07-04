<?php
/**
 * フロントページテンプレート（Astra子テーマ用）
 * 
 * @package Astra Child
 */

if (!defined('ABSPATH')) {
    exit; // 直接アクセスを禁止
}

get_header(); ?>

<?php if (astra_page_layout() == 'left-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<div id="primary" <?php astra_primary_class(); ?>>

    <?php astra_primary_content_top(); ?>

    <?php astra_content_page_loop(); ?>

    <!-- カスタムコンテンツエリア開始 -->
    <div class="gyouseishoshi-home-content">
        
        <!-- ヒーローセクション -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">行政書士試験合格への道</h1>
                <p class="hero-description">効率的な学習とコミュニティの力で、確実に合格を目指しましょう</p>
                
                <!-- 試験カウントダウン -->
                <?php echo gyouseishoshi_exam_countdown(); ?>
                
                <div class="hero-actions">
                    <a href="#progress" class="btn-hero primary">学習進捗を確認</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('community'))); ?>" class="btn-hero secondary">コミュニティに参加</a>
                </div>
            </div>
        </section>

        <!-- 学習進捗ダッシュボード -->
        <section id="progress" class="progress-dashboard">
            <div class="section-header">
                <h2 class="section-title">学習進捗ダッシュボード</h2>
                <p class="section-description">各科目の学習状況を一目で確認できます</p>
            </div>
            
            <div class="progress-grid">
                <?php
                $progress_data = get_option('gyouseishoshi_progress_data', array(
                    'constitutional' => array('percent' => 75, 'completed' => 11, 'total' => 15, 'name' => '憲法'),
                    'administrative' => array('percent' => 60, 'completed' => 9, 'total' => 15, 'name' => '行政法'),
                    'civil' => array('percent' => 45, 'completed' => 9, 'total' => 20, 'name' => '民法'),
                    'commercial' => array('percent' => 30, 'completed' => 3, 'total' => 10, 'name' => '商法・会社法')
                ));
                
                $colors = array(
                    'constitutional' => '#4a90e2',
                    'administrative' => '#7ed321', 
                    'civil' => '#f5a623',
                    'commercial' => '#d0021b'
                );
                
                foreach ($progress_data as $key => $data) :
                ?>
                <div class="progress-card" data-subject="<?php echo esc_attr($key); ?>">
                    <div class="progress-card-header">
                        <h3 class="subject-name"><?php echo esc_html($data['name']); ?></h3>
                        <span class="progress-percentage"><?php echo esc_html($data['percent']); ?>%</span>
                    </div>
                    
                    <div class="circular-progress" data-percent="<?php echo esc_attr($data['percent']); ?>">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring-background" 
                                    cx="60" cy="60" r="50" 
                                    fill="transparent" 
                                    stroke="#e6e6e6" 
                                    stroke-width="8"/>
                            <circle class="progress-ring-progress" 
                                    cx="60" cy="60" r="50" 
                                    fill="transparent" 
                                    stroke="<?php echo esc_attr($colors[$key]); ?>" 
                                    stroke-width="8"
                                    stroke-dasharray="314.16"
                                    stroke-dashoffset="<?php echo esc_attr(314.16 - (314.16 * $data['percent'] / 100)); ?>"
                                    transform="rotate(-90 60 60)"/>
                        </svg>
                        <div class="progress-text">
                            <span class="completed"><?php echo esc_html($data['completed']); ?></span>
                            <span class="separator">/</span>
                            <span class="total"><?php echo esc_html($data['total']); ?></span>
                        </div>
                    </div>
                    
                    <div class="progress-actions">
                        <a href="#" class="btn-small">詳細を見る</a>
                        <a href="#" class="btn-small outline">問題を解く</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="progress-summary">
                <?php 
                $total_percent = 0;
                $subject_count = count($progress_data);
                foreach ($progress_data as $data) {
                    $total_percent += $data['percent'];
                }
                $average_percent = $subject_count > 0 ? round($total_percent / $subject_count) : 0;
                ?>
                <div class="summary-card">
                    <h3>総合進捗率</h3>
                    <div class="overall-progress">
                        <div class="overall-progress-bar">
                            <div class="overall-progress-fill" style="width: <?php echo esc_attr($average_percent); ?>%;"></div>
                        </div>
                        <span class="overall-percentage"><?php echo esc_html($average_percent); ?>%</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 最新記事セクション -->
        <section class="latest-posts">
            <div class="section-header">
                <h2 class="section-title">最新の学習コンテンツ</h2>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="view-all-link">すべて見る</a>
            </div>
            
            <div class="posts-grid">
                <?php
                $latest_posts = get_posts(array(
                    'numberposts' => 6,
                    'post_status' => 'publish'
                ));
                
                foreach ($latest_posts as $post) :
                    setup_postdata($post);
                ?>
                <article class="post-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <?php 
                            $categories = get_the_category();
                            if (!empty($categories)) :
                            ?>
                                <span class="post-category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>
                            <time class="post-date"><?php echo get_the_date('Y年n月j日'); ?></time>
                        </div>
                        
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">続きを読む →</a>
                    </div>
                </article>
                <?php endforeach; 
                wp_reset_postdata();
                ?>
            </div>
        </section>

        <!-- コミュニティ活動セクション -->
        <section class="community-activity">
            <div class="section-header">
                <h2 class="section-title">コミュニティ活動</h2>
                <p class="section-description">仲間と一緒に学習を進めましょう</p>
            </div>
            
            <div class="community-stats">
                <?php
                $topic_count = wp_count_posts('community_topic')->publish;
                $question_count = wp_count_posts('community_question')->publish;
                $user_count = count_users()['total_users'];
                ?>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html($topic_count); ?></div>
                    <div class="stat-label">ディスカッション</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html($question_count); ?></div>
                    <div class="stat-label">質問・回答</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number"><?php echo esc_html($user_count); ?></div>
                    <div class="stat-label">メンバー</div>
                </div>
            </div>
            
            <!-- 最新のコミュニティ投稿 -->
            <div class="community-latest">
                <h3>最新のディスカッション</h3>
                <?php
                $latest_topics = get_posts(array(
                    'post_type' => 'community_topic',
                    'numberposts' => 3,
                    'post_status' => 'publish'
                ));
                
                if (!empty($latest_topics)) :
                ?>
                <div class="topic-list-compact">
                    <?php foreach ($latest_topics as $topic) : ?>
                    <div class="topic-item-compact">
                        <h4 class="topic-title-compact">
                            <a href="<?php echo get_permalink($topic->ID); ?>"><?php echo esc_html($topic->post_title); ?></a>
                        </h4>
                        <div class="topic-meta-compact">
                            <span>投稿者: <?php echo esc_html(get_the_author_meta('display_name', $topic->post_author)); ?></span>
                            <span>返信: <?php echo get_comments_number($topic->ID); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <p>まだディスカッションはありません。最初のトピックを作成しませんか？</p>
                <?php endif; ?>
                
                <div class="community-actions">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('community'))); ?>" class="btn-community">コミュニティに参加</a>
                </div>
            </div>
        </section>

        <!-- 学習リソースセクション -->
        <section class="learning-resources">
            <div class="section-header">
                <h2 class="section-title">学習リソース</h2>
                <p class="section-description">効率的な学習のためのツールとコンテンツ</p>
            </div>
            
            <div class="resources-grid">
                <div class="resource-card">
                    <div class="resource-icon">📚</div>
                    <h3>過去問解説</h3>
                    <p>詳細な解説付きの過去問で実力をチェック</p>
                    <a href="#" class="resource-link">問題を解く</a>
                </div>
                
                <div class="resource-card">
                    <div class="resource-icon">📝</div>
                    <h3>学習ノート</h3>
                    <p>重要ポイントをまとめた要点整理</p>
                    <a href="#" class="resource-link">ノートを見る</a>
                </div>
                
                <div class="resource-card">
                    <div class="resource-icon">⏰</div>
                    <h3>学習スケジュール</h3>
                    <p>効率的な学習計画を立てる</p>
                    <a href="#" class="resource-link">スケジュール管理</a>
                </div>
                
                <div class="resource-card">
                    <div class="resource-icon">📊</div>
                    <h3>弱点分析</h3>
                    <p>苦手分野を特定して重点的に学習</p>
                    <a href="#" class="resource-link">分析を見る</a>
                </div>
            </div>
        </section>
        
    </div>
    <!-- カスタムコンテンツエリア終了 -->

    <?php astra_primary_content_bottom(); ?>

</div><!-- #primary -->

<?php if (astra_page_layout() == 'right-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<style>
/* フロントページ専用スタイル */
.gyouseishoshi-home-content {
    margin: 2rem 0;
}

/* ヒーローセクション */
.hero-section {
    background: linear-gradient(135deg, var(--gyouseishoshi-primary), var(--gyouseishoshi-secondary));
    color: white;
    padding: 4rem 2rem;
    text-align: center;
    border-radius: 12px;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="white" opacity="0.1"/><circle cx="40" cy="80" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-description {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.btn-hero {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-hero.primary {
    background: white;
    color: var(--gyouseishoshi-primary);
}

.btn-hero.secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-hero:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* セクション共通スタイル */
.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    color: var(--gyouseishoshi-secondary);
    margin-bottom: 0.5rem;
}

.section-description {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
}

/* 進捗ダッシュボード */
.progress-dashboard {
    margin-bottom: 4rem;
}

.progress-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.progress-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.progress-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.progress-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.progress-card .subject-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--gyouseishoshi-secondary);
    margin: 0;
}

.progress-percentage {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--gyouseishoshi-primary);
}

.circular-progress {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-progress {
    transition: stroke-dashoffset 1s ease-in-out;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: bold;
    color: var(--gyouseishoshi-secondary);
}

.progress-text .completed {
    font-size: 1.5rem;
}

.progress-text .separator {
    margin: 0 2px;
    color: #999;
}

.progress-text .total {
    font-size: 1.2rem;
    color: #666;
}

.progress-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-small {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    background: var(--gyouseishoshi-primary);
    color: white;
}

.btn-small.outline {
    background: transparent;
    border: 1px solid var(--gyouseishoshi-primary);
    color: var(--gyouseishoshi-primary);
}

.btn-small:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.progress-summary {
    background: var(--gyouseishoshi-light-bg);
    border-radius: 8px;
    padding: 1.5rem;
}

.summary-card h3 {
    text-align: center;
    margin-bottom: 1rem;
    color: var(--gyouseishoshi-secondary);
}

.overall-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.overall-progress-bar {
    flex: 1;
    height: 12px;
    background: #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
}

.overall-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--gyouseishoshi-primary), #5a7cb5);
    transition: width 1s ease-in-out;
}

.overall-percentage {
    font-weight: bold;
    color: var(--gyouseishoshi-primary);
    font-size: 1.1rem;
}

/* 最新記事セクション */
.latest-posts {
    margin-bottom: 4rem;
}

.latest-posts .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: left;
}

.view-all-link {
    color: var(--gyouseishoshi-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.view-all-link:hover {
    color: var(--gyouseishoshi-secondary);
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.post-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.post-thumbnail {
    height: 200px;
    overflow: hidden;
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
    padding: 1.5rem;
}

.post-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.post-category {
    background: var(--gyouseishoshi-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.post-date {
    color: #666;
}

.post-title {
    margin: 0.5rem 0;
    font-size: 1.1rem;
}

.post-title a {
    color: var(--gyouseishoshi-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--gyouseishoshi-primary);
}

.post-excerpt {
    color: #666;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.read-more {
    color: var(--gyouseishoshi-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--gyouseishoshi-secondary);
}

/* コミュニティ活動セクション */
.community-activity {
    background: var(--gyouseishoshi-light-bg);
    padding: 3rem 2rem;
    border-radius: 12px;
    margin-bottom: 4rem;
}

.community-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    padding: 2rem 1rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--gyouseishoshi-primary);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-weight: 500;
}

.community-latest h3 {
    color: var(--gyouseishoshi-secondary);
    margin-bottom: 1rem;
}

.topic-list-compact {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.topic-item-compact {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.topic-item-compact:last-child {
    border-bottom: none;
}

.topic-title-compact {
    margin: 0 0 0.5rem;
    font-size: 1rem;
}

.topic-title-compact a {
    color: var(--gyouseishoshi-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.topic-title-compact a:hover {
    color: var(--gyouseishoshi-primary);
}

.topic-meta-compact {
    font-size: 0.9rem;
    color: #666;
    display: flex;
    gap: 1rem;
}

.community-actions {
    text-align: center;
}

.btn-community {
    background: linear-gradient(135deg, var(--gyouseishoshi-primary), var(--gyouseishoshi-secondary));
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: inline-block;
}

.btn-community:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    color: white;
    text-decoration: none;
}

/* 学習リソースセクション */
.learning-resources {
    margin-bottom: 4rem;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.resource-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.resource-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.resource-card h3 {
    color: var(--gyouseishoshi-secondary);
    margin-bottom: 1rem;
}

.resource-card p {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.resource-link {
    color: var(--gyouseishoshi-primary);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.resource-link:hover {
    color: var(--gyouseishoshi-secondary);
}

/* レスポンシブデザイン */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-hero {
        width: 100%;
        max-width: 300px;
        text-align: center;
    }
    
    .progress-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .community-stats {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .latest-posts .section-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .community-activity {
        padding: 2rem 1rem;
    }
    
    .topic-meta-compact {
        flex-direction: column;
        gap: 0.25rem;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 3rem 1rem;
    }
    
    .progress-card {
        padding: 1.5rem;
    }
    
    .post-content {
        padding: 1rem;
    }
    
    .resource-card {
        padding: 1.5rem;
    }
    
    .community-activity {
        padding: 1.5rem 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 進捗率のアニメーション
    const progressCards = document.querySelectorAll('.progress-card');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const progressRing = card.querySelector('.progress-ring-progress');
                const percent = parseInt(card.querySelector('.circular-progress').dataset.percent);
                
                // アニメーション開始
                setTimeout(() => {
                    const circumference = 2 * Math.PI * 50; // r=50
                    const offset = circumference - (percent / 100) * circumference;
                    progressRing.style.strokeDashoffset = offset;
                }, 200);
                
                observer.unobserve(card);
            }
        });
    }, observerOptions);
    
    progressCards.forEach(card => {
        observer.observe(card);
    });
    
    // 全体進捗率のアニメーション
    const overallProgressFill = document.querySelector('.overall-progress-fill');
    if (overallProgressFill) {
        const overallObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const width = overallProgressFill.style.width;
                    overallProgressFill.style.width = '0%';
                    setTimeout(() => {
                        overallProgressFill.style.width = width;
                    }, 300);
                    overallObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        overallObserver.observe(overallProgressFill.parentElement);
    }
    
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
});
</script>

<?php get_footer(); ?>