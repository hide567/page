<?php
/**
 * コミュニティページテンプレート（Astra子テーマ用）
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

    <div class="community-page-content">
        
        <!-- ページヘッダー -->
        <header class="community-header">
            <h1 class="community-title">行政書士試験 学習コミュニティ</h1>
            <p class="community-description">仲間と一緒に学習を進め、質問や情報を共有しましょう</p>
            
            <!-- コミュニティ統計 -->
            <div class="community-stats-header">
                <?php
                $topic_count = wp_count_posts('community_topic')->publish;
                $question_count = wp_count_posts('community_question')->publish;
                $user_count = count_users()['total_users'];
                $total_comments = wp_count_comments()->total_comments;
                ?>
                
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($topic_count); ?></span>
                    <span class="stat-label">トピック</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($question_count); ?></span>
                    <span class="stat-label">質問</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($total_comments); ?></span>
                    <span class="stat-label">回答</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($user_count); ?></span>
                    <span class="stat-label">メンバー</span>
                </div>
            </div>
        </header>

        <!-- メインコンテンツエリア -->
        <div class="community-main">
            
            <!-- タブナビゲーション -->
            <div class="community-tabs">
                <button class="tab-button active" data-tab="discussions">ディスカッション</button>
                <button class="tab-button" data-tab="questions">質問・回答</button>
                <button class="tab-button" data-tab="members">メンバー</button>
            </div>

            <!-- ディスカッションタブ -->
            <div id="discussions" class="tab-content active">
                <div class="tab-header">
                    <h2>ディスカッションフォーラム</h2>
                    <button class="new-topic-btn" id="newTopicBtn">新しいトピックを作成</button>
                </div>
                
                <!-- トピックフィルター -->
                <div class="topic-filters">
                    <select id="topicCategoryFilter" class="filter-select">
                        <option value="">すべてのカテゴリー</option>
                        <?php
                        $topic_categories = get_terms(array(
                            'taxonomy' => 'topic_category',
                            'hide_empty' => false
                        ));
                        if (!empty($topic_categories) && !is_wp_error($topic_categories)) :
                            foreach ($topic_categories as $category) :
                        ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                    
                    <select id="topicSortFilter" class="filter-select">
                        <option value="date">投稿日順</option>
                        <option value="replies">返信数順</option>
                        <option value="views">閲覧数順</option>
                    </select>
                </div>
                
                <!-- トピックリスト -->
                <div class="topic-list" id="topicList">
                    <?php
                    $topics = get_posts(array(
                        'post_type' => 'community_topic',
                        'numberposts' => 10,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if (!empty($topics)) :
                        foreach ($topics as $topic) :
                            $terms = get_the_terms($topic->ID, 'topic_category');
                            $comment_count = get_comments_number($topic->ID);
                            $view_count = get_post_meta($topic->ID, 'view_count', true) ?: 0;
                            $last_activity = get_the_modified_time('Y年n月j日 H:i', $topic->ID);
                    ?>
                    <div class="topic-item" data-category="<?php echo $terms && !is_wp_error($terms) ? esc_attr($terms[0]->term_id) : ''; ?>">
                        <div class="topic-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID', $topic->post_author), 50); ?>
                        </div>
                        
                        <div class="topic-content">
                            <div class="topic-header">
                                <h3 class="topic-title">
                                    <a href="<?php echo get_permalink($topic->ID); ?>"><?php echo esc_html($topic->post_title); ?></a>
                                </h3>
                                <?php if ($terms && !is_wp_error($terms)) : ?>
                                    <span class="topic-category-tag"><?php echo esc_html($terms[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="topic-meta">
                                <span class="topic-author">投稿者: <?php echo esc_html(get_the_author_meta('display_name', $topic->post_author)); ?></span>
                                <span class="topic-date"><?php echo get_the_date('Y年n月j日 H:i', $topic->ID); ?></span>
                                <span class="topic-activity">最終更新: <?php echo $last_activity; ?></span>
                            </div>
                            
                            <p class="topic-excerpt"><?php echo wp_trim_words($topic->post_content, 25); ?></p>
                        </div>
                        
                        <div class="topic-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $comment_count; ?></span>
                                <span class="stat-label">返信</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $view_count; ?></span>
                                <span class="stat-label">閲覧</span>
                            </div>
                        </div>
                    </div>
                    <?php
                        endforeach;
                    else :
                    ?>
                    <div class="no-topics">
                        <p>まだトピックはありません。最初のトピックを作成しませんか？</p>
                        <button class="new-topic-btn">新しいトピックを作成</button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- ページネーション -->
                <div class="community-pagination">
                    <a href="#" class="pagination-btn disabled">前へ</a>
                    <span class="pagination-info">1 / 1</span>
                    <a href="#" class="pagination-btn disabled">次へ</a>
                </div>
            </div>

            <!-- 質問・回答タブ -->
            <div id="questions" class="tab-content">
                <div class="tab-header">
                    <h2>質問・回答</h2>
                    <button class="new-question-btn" id="newQuestionBtn">質問を投稿</button>
                </div>
                
                <!-- 質問リスト -->
                <div class="question-list">
                    <?php
                    $questions = get_posts(array(
                        'post_type' => 'community_question',
                        'numberposts' => 10,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if (!empty($questions)) :
                        foreach ($questions as $question) :
                            $terms = get_the_terms($question->ID, 'question_category');
                            $comment_count = get_comments_number($question->ID);
                            $is_answered = $comment_count > 0;
                    ?>
                    <div class="question-item <?php echo $is_answered ? 'answered' : 'unanswered'; ?>">
                        <div class="question-status">
                            <?php if ($is_answered) : ?>
                                <span class="status-badge answered">回答済み</span>
                            <?php else : ?>
                                <span class="status-badge unanswered">未回答</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="question-content">
                            <h3 class="question-title">
                                <a href="<?php echo get_permalink($question->ID); ?>"><?php echo esc_html($question->post_title); ?></a>
                            </h3>
                            
                            <?php if ($terms && !is_wp_error($terms)) : ?>
                                <span class="question-category"><?php echo esc_html($terms[0]->name); ?></span>
                            <?php endif; ?>
                            
                            <p class="question-excerpt"><?php echo wp_trim_words($question->post_content, 30); ?></p>
                            
                            <div class="question-meta">
                                <span>質問者: <?php echo esc_html(get_the_author_meta('display_name', $question->post_author)); ?></span>
                                <span><?php echo get_the_date('Y年n月j日', $question->ID); ?></span>
                                <span>回答数: <?php echo $comment_count; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                        endforeach;
                    else :
                    ?>
                    <div class="no-questions">
                        <p>まだ質問はありません。最初の質問を投稿しませんか？</p>
                        <button class="new-question-btn">質問を投稿</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- メンバータブ -->
            <div id="members" class="tab-content">
                <div class="tab-header">
                    <h2>コミュニティメンバー</h2>
                </div>
                
                <div class="members-grid">
                    <?php
                    $users = get_users(array(
                        'number' => 12,
                        'orderby' => 'registered',
                        'order' => 'DESC'
                    ));
                    
                    if (!empty($users)) :
                        foreach ($users as $user) :
                            $post_count = count_user_posts($user->ID);
                            $comment_count = get_comments(array(
                                'user_id' => $user->ID,
                                'count' => true
                            ));
                    ?>
                    <div class="member-card">
                        <div class="member-avatar">
                            <?php echo get_avatar($user->ID, 80); ?>
                        </div>
                        
                        <div class="member-info">
                            <h4 class="member-name"><?php echo esc_html($user->display_name); ?></h4>
                            <p class="member-role"><?php echo esc_html(ucfirst($user->roles[0])); ?></p>
                            
                            <div class="member-stats">
                                <div class="member-stat">
                                    <span class="stat-number"><?php echo $post_count; ?></span>
                                    <span class="stat-label">投稿</span>
                                </div>
                                <div class="member-stat">
                                    <span class="stat-number"><?php echo $comment_count; ?></span>
                                    <span class="stat-label">回答</span>
                                </div>
                            </div>
                            
                            <p class="member-joined">登録: <?php echo date('Y年n月', strtotime($user->user_registered)); ?></p>
                            
                            <?php if ($user->description) : ?>
                                <p class="member-bio"><?php echo esc_html(wp_trim_words($user->description, 15)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                        endforeach;
                    else :
                    ?>
                    <p>メンバーが見つかりませんでした。</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- トピック作成モーダル -->
    <div id="topicModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">新しいトピックを作成</h3>
                <button class="modal-close" id="closeTopicModal">&times;</button>
            </div>
            
            <form id="topicForm" class="modal-form">
                <div class="form-group">
                    <label for="topicCategory" class="form-label">カテゴリー</label>
                    <select id="topicCategory" name="topic_category" class="form-control">
                        <option value="">カテゴリーを選択</option>
                        <?php
                        if (!empty($topic_categories) && !is_wp_error($topic_categories)) :
                            foreach ($topic_categories as $category) :
                        ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="topicTitle" class="form-label">タイトル</label>
                    <input type="text" id="topicTitle" name="topic_title" class="form-control" placeholder="トピックのタイトルを入力" required>
                </div>
                
                <div class="form-group">
                    <label for="topicContent" class="form-label">内容</label>
                    <textarea id="topicContent" name="topic_content" class="form-control" rows="6" placeholder="トピックの内容を入力" required></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelTopic">キャンセル</button>
                    <button type="submit" class="submit-btn">トピックを作成</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 質問投稿モーダル -->
    <div id="questionModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">質問を投稿</h3>
                <button class="modal-close" id="closeQuestionModal">&times;</button>
            </div>
            
            <form id="questionForm" class="modal-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <div class="form-group">
                    <label for="questionCategory" class="form-label">カテゴリー</label>
                    <select id="questionCategory" name="question_category" class="form-control">
                        <option value="">カテゴリーを選択</option>
                        <?php
                        $question_categories = get_terms(array(
                            'taxonomy' => 'question_category',
                            'hide_empty' => false
                        ));
                        if (!empty($question_categories) && !is_wp_error($question_categories)) :
                            foreach ($question_categories as $category) :
                        ?>
                            <option value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="questionTitle" class="form-label">質問タイトル</label>
                    <input type="text" id="questionTitle" name="question_title" class="form-control" placeholder="質問のタイトルを入力" required>
                </div>
                
                <div class="form-group">
                    <label for="questionContent" class="form-label">質問内容</label>
                    <textarea id="questionContent" name="question_content" class="form-control" rows="6" placeholder="質問の詳細を入力" required></textarea>
                </div>
                
                <input type="hidden" name="action" value="submit_question_form">
                <?php wp_nonce_field('submit_question_nonce', 'question_nonce'); ?>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelQuestion">キャンセル</button>
                    <button type="submit" class="submit-btn">質問を投稿</button>
                </div>
            </form>
        </div>
    </div>

    <?php astra_primary_content_bottom(); ?>

</div><!-- #primary -->

<?php if (astra_page_layout() == 'right-sidebar') : ?>
    <?php get_sidebar(); ?>
<?php endif ?>

<style>
/* コミュニティページ専用スタイル */
.community-page-content {
    margin: 2rem 0;
}

/* コミュニティヘッダー */
.community-header {
    background: linear-gradient(135deg, var(--gyouseishoshi-primary), var(--gyouseishoshi-secondary));
    color: white;
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 2rem;
}

.community-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.community-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.community-stats-header {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 2rem;
    max-width: 600px;
    margin: 0 auto;
}

.community-stats-header .stat-item {
    text-align: center;
}

.community-stats-header .stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: #f9ca24;
}

.community-stats-header .stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* タブナビゲーション */
.community-tabs {
    display: flex;
    background: white;
    border-radius: 8px;
    padding: 4px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.tab-button {
    flex: 1;
    padding: 12px 20px;
    border: none;
    background: transparent;
    color: #666;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-button.active {
    background: var(--gyouseishoshi-primary);
    color: white;
    box-shadow: 0 2px 8px rgba(74, 111, 165, 0.3);
}

.tab-button:hover:not(.active) {
    background: #f8f9fa;
    color: var(--gyouseishoshi-primary);
}

/* タブコンテンツ */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

.tab-header h2 {
    margin: 0;
    color: var(--gyouseishoshi-secondary);
}

/* フィルター */
.topic-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.filter-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
    min-width: 150px;
}

/* トピックリスト */
.topic-list {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.topic-item {
    display: flex;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.topic-item:hover {
    background-color: #f8f9fa;
}

.topic-item:last-child {
    border-bottom: none;
}

.topic-avatar {
    margin-right: 1rem;
    flex-shrink: 0;
}

.topic-avatar img {
    border-radius: 50%;
}

.topic-content {
    flex: 1;
    min-width: 0;
}

.topic-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.topic-title {
    margin: 0;
    font-size: 1.1rem;
    flex: 1;
}

.topic-title a {
    color: var(--gyouseishoshi-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.topic-title a:hover {
    color: var(--gyouseishoshi-primary);
}

.topic-category-tag {
    background: var(--gyouseishoshi-primary);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    white-space: nowrap;
}

.topic-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.topic-excerpt {
    color: #555;
    line-height: 1.5;
    margin: 0;
}

.topic-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-left: 1rem;
    text-align: center;
}

.topic-stats .stat-item {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 6px;
    min-width: 60px;
}

.topic-stats .stat-number {
    display: block;
    font-weight: bold;
    color: var(--gyouseishoshi-primary);
}

.topic-stats .stat-label {
    font-size: 0.8rem;
    color: #666;
}

/* 質問リスト */
.question-list {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.question-item {
    display: flex;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s ease;
}

.question-item:hover {
    background-color: #f8f9fa;
}

.question-item:last-child {
    border-bottom: none;
}

.question-status {
    margin-right: 1rem;
    flex-shrink: 0;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-badge.answered {
    background: #d4edda;
    color: #155724;
}

.status-badge.unanswered {
    background: #f8d7da;
    color: #721c24;
}

.question-content {
    flex: 1;
}

.question-title {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
}

.question-title a {
    color: var(--gyouseishoshi-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.question-title a:hover {
    color: var(--gyouseishoshi-primary);
}

.question-category {
    background: var(--gyouseishoshi-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.question-excerpt {
    color: #555;
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.question-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
    flex-wrap: wrap;
}

/* メンバーグリッド */
.members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

.member-card {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.member-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.member-avatar {
    margin-bottom: 1rem;
}

.member-avatar img {
    border-radius: 50%;
    border: 3px solid #eee;
}

.member-name {
    margin: 0 0 0.5rem;
    color: var(--gyouseishoshi-secondary);
    font-size: 1.2rem;
}

.member-role {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    text-transform: capitalize;
}

.member-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 1rem;
}

.member-stat {
    text-align: center;
}

.member-stat .stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--gyouseishoshi-primary);
}

.member-stat .stat-label {
    font-size: 0.8rem;
    color: #666;
}

.member-joined {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.member-bio {
    font-size: 0.9rem;
    color: #555;
    line-height: 1.4;
    margin: 0;
}

/* モーダル */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 0;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 2rem 1rem;
    border-bottom: 1px solid #eee;
}

.modal-title {
    margin: 0;
    color: var(--gyouseishoshi-secondary);
    font-size: 1.4rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

.modal-close:hover {
    background: #f0f0f0;
    color: #666;
}

.modal-form {
    padding: 1rem 2rem 2rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

/* ボタン */
.new-topic-btn,
.new-question-btn {
    background: linear-gradient(135deg, var(--gyouseishoshi-primary), var(--gyouseishoshi-secondary));
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.new-topic-btn:hover,
.new-question-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    color: white;
    text-decoration: none;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-cancel:hover {
    background: #5a6268;
}

/* 空状態 */
.no-topics,
.no-questions {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.no-topics p,
.no-questions p {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

/* ページネーション */
.community-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.pagination-btn {
    padding: 8px 16px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    color: var(--gyouseishoshi-primary);
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination-btn:hover:not(.disabled) {
    background: var(--gyouseishoshi-primary);
    color: white;
    text-decoration: none;
}

.pagination-btn.disabled {
    color: #ccc;
    cursor: not-allowed;
}

.pagination-info {
    color: #666;
    font-weight: 500;
}

/* レスポンシブデザイン */
@media (max-width: 768px) {
    .community-header {
        padding: 2rem 1rem;
    }
    
    .community-title {
        font-size: 1.8rem;
    }
    
    .community-stats-header {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .tab-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .topic-filters {
        flex-direction: column;
    }
    
    .filter-select {
        min-width: auto;
    }
    
    .topic-item {
        flex-direction: column;
        gap: 1rem;
    }
    
    .topic-avatar {
        margin-right: 0;
        align-self: flex-start;
    }
    
    .topic-stats {
        flex-direction: row;
        margin-left: 0;
        justify-content: flex-start;
    }
    
    .topic-meta {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .question-item {
        flex-direction: column;
        gap: 1rem;
    }
    
    .question-status {
        margin-right: 0;
        align-self: flex-start;
    }
    
    .question-meta {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .members-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .member-stats {
        gap: 1rem;
    }
    
    .modal-content {
        margin: 1rem;
        width: calc(100% - 2rem);
    }
    
    .modal-header {
        padding: 1.5rem 1.5rem 1rem;
    }
    
    .modal-form {
        padding: 1rem 1.5rem 1.5rem;
    }
    
    .modal-footer {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .community-stats-header {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .community-stats-header .stat-number {
        font-size: 1.5rem;
    }
    
    .tab-button {
        padding: 10px 12px;
        font-size: 0.9rem;
    }
    
    .topic-item,
    .question-item {
        padding: 1rem;
    }
    
    .member-card {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // タブ切り替え
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // アクティブなタブボタンを更新
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // タブコンテンツを切り替え
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === targetTab) {
                    content.classList.add('active');
                }
            });
        });
    });
    
    // モーダル制御
    const topicModal = document.getElementById('topicModal');
    const questionModal = document.getElementById('questionModal');
    const newTopicBtns = document.querySelectorAll('.new-topic-btn');
    const newQuestionBtns = document.querySelectorAll('.new-question-btn');
    
    // トピック作成モーダル
    newTopicBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            topicModal.style.display = 'flex';
        });
    });
    
    // 質問投稿モーダル
    newQuestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            questionModal.style.display = 'flex';
        });
    });
    
    // モーダルを閉じる
    document.getElementById('closeTopicModal').addEventListener('click', function() {
        topicModal.style.display = 'none';
    });
    
    document.getElementById('closeQuestionModal').addEventListener('click', function() {
        questionModal.style.display = 'none';
    });
    
    document.getElementById('cancelTopic').addEventListener('click', function() {
        topicModal.style.display = 'none';
    });
    
    document.getElementById('cancelQuestion').addEventListener('click', function() {
        questionModal.style.display = 'none';
    });
    
    // モーダル外クリックで閉じる
    window.addEventListener('click', function(e) {
        if (e.target === topicModal) {
            topicModal.style.display = 'none';
        }
        if (e.target === questionModal) {
            questionModal.style.display = 'none';
        }
    });
    
    // フィルター機能
    const categoryFilter = document.getElementById('topicCategoryFilter');
    const sortFilter = document.getElementById('topicSortFilter');
    const topicItems = document.querySelectorAll('.topic-item');
    
    function filterTopics() {
        const selectedCategory = categoryFilter ? categoryFilter.value : '';
        const sortBy = sortFilter ? sortFilter.value : 'date';
        
        topicItems.forEach(item => {
            const itemCategory = item.dataset.category || '';
            const showItem = !selectedCategory || itemCategory === selectedCategory;
            item.style.display = showItem ? 'flex' : 'none';
        });
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterTopics);
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', filterTopics);
    }
    
    // AJAX でトピック投稿
    const topicForm = document.getElementById('topicForm');
    if (topicForm) {
        topicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                action: 'create_topic',
                title: formData.get('topic_title'),
                content: formData.get('topic_content'),
                category: formData.get('topic_category'),
                nonce: '<?php echo wp_create_nonce("gyouseishoshi_nonce"); ?>'
            };
            
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('トピックが作成されました！');
                    topicModal.style.display = 'none';
                    location.reload();
                } else {
                    alert('エラーが発生しました: ' + (result.data || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('通信エラーが発生しました');
            });
        });
    }
    
    // ステータスメッセージの表示
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    
    if (status === 'success') {
        showNotification('投稿が完了しました！', 'success');
    } else if (status && status.startsWith('error')) {
        showNotification('エラーが発生しました。もう一度お試しください。', 'error');
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
});
</script>

<?php get_footer(); ?>