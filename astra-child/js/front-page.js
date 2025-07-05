/**
 * コミュニティ機能用 JavaScript（Astra子テーマ用）
 */

(function($) {
    'use strict';
    
    // DOM読み込み完了後に実行
    $(document).ready(function() {
        
        // デバッグ情報
        console.log('Community script loaded for Astra child theme');
        
        // グローバル変数の初期化
        window.gyouseishoshi = window.gyouseishoshi || {};
        
        /**
         * Ajax設定
         */
        const ajaxSettings = {
            url: gyouseishoshi_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                showLoader();
            },
            complete: function() {
                hideLoader();
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', status, error);
                showNotification('通信エラーが発生しました', 'error');
            }
        };
        
        /**
         * ローダー表示/非表示
         */
        function showLoader() {
            if (!$('.community-loader').length) {
                $('body').append('<div class="community-loader"><div class="loader-spinner"></div></div>');
            }
        }
        
        function hideLoader() {
            $('.community-loader').remove();
        }
        
        /**
         * 通知表示
         */
        function showNotification(message, type = 'info') {
            const notification = $(`
                <div class="community-notification notification-${type}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);
            
            $('body').append(notification);
            
            // アニメーション
            setTimeout(() => {
                notification.addClass('show');
            }, 100);
            
            // 自動削除
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
            
            // クローズボタン
            notification.find('.notification-close').on('click', function() {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            });
        }
        
        /**
         * モーダル管理クラス
         */
        class ModalManager {
            constructor(modalId) {
                this.modal = $(modalId);
                this.init();
            }
            
            init() {
                // エスケープキーで閉じる
                $(document).on('keydown', (e) => {
                    if (e.key === 'Escape' && this.modal.is(':visible')) {
                        this.close();
                    }
                });
                
                // オーバーレイクリックで閉じる
                this.modal.on('click', (e) => {
                    if (e.target === this.modal[0]) {
                        this.close();
                    }
                });
                
                // クローズボタン
                this.modal.find('.modal-close, .btn-cancel').on('click', () => {
                    this.close();
                });
            }
            
            open() {
                this.modal.fadeIn(300);
                $('body').addClass('modal-open');
                this.modal.find('input, textarea').first().focus();
            }
            
            close() {
                this.modal.fadeOut(300);
                $('body').removeClass('modal-open');
                this.resetForm();
            }
            
            resetForm() {
                this.modal.find('form')[0]?.reset();
                this.modal.find('.form-error').remove();
            }
        }
        
        /**
         * フォームバリデーション
         */
        class FormValidator {
            static validateTopic(formData) {
                const errors = [];
                
                if (!formData.get('topic_title')?.trim()) {
                    errors.push({ field: 'topic_title', message: 'タイトルは必須です' });
                }
                
                if (!formData.get('topic_content')?.trim()) {
                    errors.push({ field: 'topic_content', message: '内容は必須です' });
                }
                
                const title = formData.get('topic_title')?.trim();
                if (title && title.length > 100) {
                    errors.push({ field: 'topic_title', message: 'タイトルは100文字以内で入力してください' });
                }
                
                return errors;
            }
            
            static validateQuestion(formData) {
                const errors = [];
                
                if (!formData.get('question_title')?.trim()) {
                    errors.push({ field: 'question_title', message: 'タイトルは必須です' });
                }
                
                if (!formData.get('question_content')?.trim()) {
                    errors.push({ field: 'question_content', message: '内容は必須です' });
                }
                
                return errors;
            }
            
            static showErrors(form, errors) {
                // 既存のエラーメッセージを削除
                form.find('.form-error').remove();
                
                errors.forEach(error => {
                    const field = form.find(`[name="${error.field}"]`);
                    const errorElement = $(`<div class="form-error">${error.message}</div>`);
                    field.after(errorElement);
                    field.addClass('error');
                });
            }
            
            static clearErrors(form) {
                form.find('.form-error').remove();
                form.find('.error').removeClass('error');
            }
        }
        
        /**
         * トピック管理
         */
        class TopicManager {
            constructor() {
                this.modal = new ModalManager('#topicModal');
                this.form = $('#topicForm');
                this.init();
            }
            
            init() {
                // 新規トピックボタン
                $(document).on('click', '.new-topic-btn', (e) => {
                    e.preventDefault();
                    this.modal.open();
                });
                
                // フォーム送信
                this.form.on('submit', (e) => {
                    e.preventDefault();
                    this.submitTopic();
                });
                
                // リアルタイムバリデーション
                this.form.find('input, textarea').on('blur', () => {
                    const formData = new FormData(this.form[0]);
                    const errors = FormValidator.validateTopic(formData);
                    FormValidator.showErrors(this.form, errors);
                });
            }
            
            async submitTopic() {
                const formData = new FormData(this.form[0]);
                const errors = FormValidator.validateTopic(formData);
                
                if (errors.length > 0) {
                    FormValidator.showErrors(this.form, errors);
                    return;
                }
                
                FormValidator.clearErrors(this.form);
                
                const data = {
                    action: 'create_topic',
                    title: formData.get('topic_title'),
                    content: formData.get('topic_content'),
                    category: formData.get('topic_category'),
                    nonce: gyouseishoshi_ajax.nonce
                };
                
                try {
                    const response = await $.ajax({
                        ...ajaxSettings,
                        data: data
                    });
                    
                    if (response.success) {
                        showNotification('トピックが作成されました！', 'success');
                        this.modal.close();
                        this.refreshTopicList();
                    } else {
                        showNotification(response.data || 'エラーが発生しました', 'error');
                    }
                } catch (error) {
                    console.error('Topic submission error:', error);
                    showNotification('送信に失敗しました', 'error');
                }
            }
            
            refreshTopicList() {
                // トピックリストの更新
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }
        
        /**
         * 質問管理
         */
        class QuestionManager {
            constructor() {
                this.modal = new ModalManager('#questionModal');
                this.form = $('#questionForm');
                this.init();
            }
            
            init() {
                // 新規質問ボタン
                $(document).on('click', '.new-question-btn', (e) => {
                    e.preventDefault();
                    this.modal.open();
                });
                
                // フォーム送信（従来のPOST形式を維持）
                this.form.on('submit', (e) => {
                    const formData = new FormData(this.form[0]);
                    const errors = FormValidator.validateQuestion(formData);
                    
                    if (errors.length > 0) {
                        e.preventDefault();
                        FormValidator.showErrors(this.form, errors);
                        return false;
                    }
                    
                    FormValidator.clearErrors(this.form);
                    // フォームは通常の送信を行う
                });
                
                // リアルタイムバリデーション
                this.form.find('input, textarea').on('blur', () => {
                    const formData = new FormData(this.form[0]);
                    const errors = FormValidator.validateQuestion(formData);
                    FormValidator.showErrors(this.form, errors);
                });
            }
        }
        
        /**
         * フィルター管理
         */
        class FilterManager {
            constructor() {
                this.categoryFilter = $('#topicCategoryFilter');
                this.sortFilter = $('#topicSortFilter');
                this.topicItems = $('.topic-item');
                this.init();
            }
            
            init() {
                this.categoryFilter.on('change', () => this.applyFilters());
                this.sortFilter.on('change', () => this.applyFilters());
                
                // URLパラメータからフィルターを復元
                this.restoreFiltersFromUrl();
            }
            
            applyFilters() {
                const selectedCategory = this.categoryFilter.val();
                const sortBy = this.sortFilter.val();
                
                // カテゴリーフィルター
                this.topicItems.each(function() {
                    const $item = $(this);
                    const itemCategory = $item.data('category') || '';
                    const showItem = !selectedCategory || itemCategory.toString() === selectedCategory;
                    
                    $item.toggle(showItem);
                });
                
                // ソート（簡易実装）
                if (sortBy === 'replies') {
                    this.sortByReplies();
                } else if (sortBy === 'views') {
                    this.sortByViews();
                } else {
                    this.sortByDate();
                }
                
                // URLにフィルター状態を保存
                this.updateUrl();
            }
            
            sortByReplies() {
                const container = $('.topic-list');
                const items = this.topicItems.toArray();
                
                items.sort((a, b) => {
                    const aReplies = parseInt($(a).find('.topic-stats .stat-number').first().text()) || 0;
                    const bReplies = parseInt($(b).find('.topic-stats .stat-number').first().text()) || 0;
                    return bReplies - aReplies;
                });
                
                container.append(items);
            }
            
            sortByViews() {
                const container = $('.topic-list');
                const items = this.topicItems.toArray();
                
                items.sort((a, b) => {
                    const aViews = parseInt($(a).find('.topic-stats .stat-number').last().text()) || 0;
                    const bViews = parseInt($(b).find('.topic-stats .stat-number').last().text()) || 0;
                    return bViews - aViews;
                });
                
                container.append(items);
            }
            
            sortByDate() {
                const container = $('.topic-list');
                const items = this.topicItems.toArray();
                
                items.sort((a, b) => {
                    const aDate = new Date($(a).find('.topic-date').text());
                    const bDate = new Date($(b).find('.topic-date').text());
                    return bDate - aDate;
                });
                
                container.append(items);
            }
            
            updateUrl() {
                const params = new URLSearchParams(window.location.search);
                
                if (this.categoryFilter.val()) {
                    params.set('category', this.categoryFilter.val());
                } else {
                    params.delete('category');
                }
                
                if (this.sortFilter.val() !== 'date') {
                    params.set('sort', this.sortFilter.val());
                } else {
                    params.delete('sort');
                }
                
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState(null, '', newUrl);
            }
            
            restoreFiltersFromUrl() {
                const params = new URLSearchParams(window.location.search);
                
                if (params.get('category')) {
                    this.categoryFilter.val(params.get('category'));
                }
                
                if (params.get('sort')) {
                    this.sortFilter.val(params.get('sort'));
                }
                
                this.applyFilters();
            }
        }
        
        /**
         * タブ管理
         */
        class TabManager {
            constructor() {
                this.tabs = $('.tab-button');
                this.contents = $('.tab-content');
                this.init();
            }
            
            init() {
                this.tabs.on('click', (e) => {
                    const targetTab = $(e.target).data('tab');
                    this.switchTab(targetTab);
                });
                
                // URLハッシュからタブを復元
                this.restoreTabFromHash();
                
                // ハッシュ変更の監視
                $(window).on('hashchange', () => {
                    this.restoreTabFromHash();
                });
            }
            
            switchTab(targetTab) {
                // アクティブなタブボタンを更新
                this.tabs.removeClass('active');
                $(`.tab-button[data-tab="${targetTab}"]`).addClass('active');
                
                // タブコンテンツを切り替え
                this.contents.removeClass('active');
                $(`#${targetTab}`).addClass('active');
                
                // ハッシュを更新
                window.location.hash = targetTab;
                
                // タブ切り替え時のアニメーション
                $(`#${targetTab}`).hide().fadeIn(300);
            }
            
            restoreTabFromHash() {
                const hash = window.location.hash.substring(1);
                if (hash && $(`#${hash}`).length) {
                    this.switchTab(hash);
                }
            }
        }
        
        /**
         * 検索機能
         */
        class SearchManager {
            constructor() {
                this.searchInput = $('#communitySearch');
                this.searchResults = $('#searchResults');
                this.init();
            }
            
            init() {
                if (!this.searchInput.length) return;
                
                let searchTimeout;
                
                this.searchInput.on('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.performSearch(e.target.value.trim());
                    }, 300);
                });
                
                // 検索結果のクリア
                this.searchInput.on('blur', () => {
                    setTimeout(() => {
                        this.searchResults.fadeOut(200);
                    }, 200);
                });
                
                this.searchInput.on('focus', () => {
                    if (this.searchResults.children().length > 0) {
                        this.searchResults.fadeIn(200);
                    }
                });
            }
            
            async performSearch(query) {
                if (query.length < 2) {
                    this.searchResults.hide();
                    return;
                }
                
                const data = {
                    action: 'community_search',
                    query: query,
                    nonce: gyouseishoshi_ajax.nonce
                };
                
                try {
                    const response = await $.ajax({
                        ...ajaxSettings,
                        data: data
                    });
                    
                    if (response.success) {
                        this.displaySearchResults(response.data);
                    }
                } catch (error) {
                    console.error('Search error:', error);
                }
            }
            
            displaySearchResults(results) {
                this.searchResults.empty();
                
                if (results.length === 0) {
                    this.searchResults.html('<div class="search-no-results">結果が見つかりませんでした</div>');
                } else {
                    results.forEach(result => {
                        const resultItem = $(`
                            <div class="search-result-item">
                                <h4><a href="${result.url}">${result.title}</a></h4>
                                <p class="search-excerpt">${result.excerpt}</p>
                                <span class="search-type">${result.type}</span>
                            </div>
                        `);
                        this.searchResults.append(resultItem);
                    });
                }
                
                this.searchResults.fadeIn(200);
            }
        }
        
        /**
         * 無限スクロール
         */
        class InfiniteScroll {
            constructor(container, loadMoreCallback) {
                this.container = $(container);
                this.callback = loadMoreCallback;
                this.loading = false;
                this.page = 1;
                this.init();
            }
            
            init() {
                $(window).on('scroll', () => {
                    if (this.shouldLoadMore()) {
                        this.loadMore();
                    }
                });
            }
            
            shouldLoadMore() {
                if (this.loading) return false;
                
                const scrollTop = $(window).scrollTop();
                const windowHeight = $(window).height();
                const documentHeight = $(document).height();
                
                return scrollTop + windowHeight > documentHeight - 1000;
            }
            
            async loadMore() {
                this.loading = true;
                this.page++;
                
                try {
                    await this.callback(this.page);
                } catch (error) {
                    console.error('Load more error:', error);
                    this.page--; // ページカウンターを戻す
                } finally {
                    this.loading = false;
                }
            }
        }
        
        // ユーティリティ関数
        window.gyouseishoshi.utils = {
            formatDate: (dateString) => {
                const date = new Date(dateString);
                return date.toLocaleDateString('ja-JP', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },
            
            formatRelativeTime: (dateString) => {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                
                const minutes = Math.floor(diff / (1000 * 60));
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (minutes < 60) return `${minutes}分前`;
                if (hours < 24) return `${hours}時間前`;
                if (days < 7) return `${days}日前`;
                
                return window.gyouseishoshi.utils.formatDate(dateString);
            },
            
            truncateText: (text, length = 100) => {
                if (text.length <= length) return text;
                return text.substring(0, length) + '...';
            },
            
            escapeHtml: (text) => {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };
        
        // 初期化
        const topicManager = new TopicManager();
        const questionManager = new QuestionManager();
        const filterManager = new FilterManager();
        const tabManager = new TabManager();
        const searchManager = new SearchManager();
        
        // 無限スクロール（トピックリスト用）
        const infiniteScroll = new InfiniteScroll('.topic-list', async (page) => {
            const data = {
                action: 'load_more_topics',
                page: page,
                category: $('#topicCategoryFilter').val(),
                sort: $('#topicSortFilter').val(),
                nonce: gyouseishoshi_ajax.nonce
            };
            
            const response = await $.ajax({
                ...ajaxSettings,
                data: data
            });
            
            if (response.success && response.data.length > 0) {
                $('.topic-list').append(response.data);
                return true;
            }
            
            return false;
        });
        
        // ページロード時の状態復元
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        
        if (status === 'success') {
            showNotification('投稿が完了しました！', 'success');
        } else if (status && status.startsWith('error')) {
            showNotification('エラーが発生しました。もう一度お試しください。', 'error');
        }
        
        // グローバルにアクセス可能にする
        window.gyouseishoshi.community = {
            topicManager,
            questionManager,
            filterManager,
            tabManager,
            searchManager,
            showNotification
        };
        
        console.log('Community functionality initialized');
    });
    
})(jQuery);

// CSS スタイルを動的に追加
const communityStyles = `
<style>
.community-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}

.loader-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--gyouseishoshi-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.community-notification {
    position: fixed;
    top: 20px;
    right: -400px;
    min-width: 300px;
    max-width: 400px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 10001;
    transition: right 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.community-notification.show {
    right: 20px;
}

.notification-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.notification-error {
    background: linear-gradient(135deg, #dc3545, #fd7e14);
}

.notification-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
}

.notification-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.notification-close:hover {
    opacity: 1;
}

.form-error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 5px;
    margin-bottom: 10px;
}

.form-control.error {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
}

.search-result-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item h4 {
    margin: 0 0 5px;
    font-size: 1rem;
}

.search-result-item h4 a {
    color: var(--gyouseishoshi-secondary);
    text-decoration: none;
}

.search-result-item h4 a:hover {
    color: var(--gyouseishoshi-primary);
}

.search-excerpt {
    margin: 0 0 5px;
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
}

.search-type {
    background: var(--gyouseishoshi-primary);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.search-no-results {
    padding: 15px;
    text-align: center;
    color: #666;
}

body.modal-open {
    overflow: hidden;
}

@media (max-width: 768px) {
    .community-notification {
        right: -350px;
        min-width: 250px;
        max-width: 300px;
    }
    
    .community-notification.show {
        right: 10px;
    }
}
</style>
`;

// スタイルをheadに追加
if (document.head) {
    document.head.insertAdjacentHTML('beforeend', communityStyles);
}