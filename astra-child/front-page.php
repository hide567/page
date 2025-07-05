<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>行政書士の道 - わかりやすい解説の集積地</title>
    <style>
        /* 基本設定 */
        :root {
            --primary-color: #3f51b5;
            --secondary-color: #303f9f;
            --accent-color: #ffc107;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --border-color: #e0e0e0;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --border-radius: 8px;
            --success-color: #28a745;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hiragino Kaku Gothic Pro', 'Meiryo', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ヒーローセクション */
        .gyousei-hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .gyousei-hero-section::before {
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
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 20px;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .hero-description {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.8;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .hero-cta {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .btn-primary, .btn-secondary {
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 統計情報セクション */
        .gyousei-stats-section {
            background: var(--light-bg);
            padding: 40px 0;
            margin-bottom: 40px;
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
            animation: countUp 2s ease;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-color);
            font-weight: 500;
        }

        /* 機能紹介セクション */
        .features-section {
            padding: 60px 0;
            background: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: var(--border-radius);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        /* 最新記事セクション */
        .gyousei-latest-posts {
            padding: 60px 0;
            background: var(--light-bg);
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
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .post-card-inner {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .post-thumbnail {
            position: relative;
            overflow: hidden;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
            flex: 1;
            display: flex;
            flex-direction: column;
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
            flex: 1;
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
            margin-top: auto;
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

        /* 科目別コンテンツセクション */
        .gyousei-subjects-section {
            background: white;
            padding: 60px 0;
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
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

        /* セクション共通スタイル */
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

        .section-footer {
            text-align: center;
        }

        .btn-more-posts {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-more-posts:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* 学習進捗セクション */
        .study-progress-section {
            background: var(--light-bg);
            padding: 60px 0;
        }

        .subsection-title {
            font-size: 1.3rem;
            color: var(--text-color);
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-section,
        .checklist-section,
        .calendar-section,
        .study-stats-section {
            margin-bottom: 50px;
        }

        .progress-section:last-child,
        .checklist-section:last-child,
        .calendar-section:last-child,
        .study-stats-section:last-child {
            margin-bottom: 0;
        }

        .shortcode-area {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            min-height: 200px;
            position: relative;
        }

        .shortcode-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 150px;
            text-align: center;
            color: #666;
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            background: #fafafa;
        }

        .shortcode-placeholder p {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .shortcode-placeholder code {
            background: var(--primary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        /* 学習統計グリッド */
        .study-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .study-stat-item {
            background: white;
            padding: 25px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .study-stat-item:hover {
            transform: translateY(-3px);
        }

        .study-stat-item:nth-child(2) {
            border-left-color: var(--success-color);
        }

        .study-stat-item:nth-child(3) {
            border-left-color: var(--info-color);
        }

        .study-stat-item:nth-child(4) {
            border-left-color: var(--accent-color);
        }

        .stat-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .study-stat-item:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, var(--success-color), #20c997);
        }

        .study-stat-item:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, var(--info-color), #6f42c1);
        }

        .study-stat-item:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, var(--accent-color), #fd7e14);
        }

        .stat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stat-content .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 5px;
            display: flex;
            align-items: baseline;
            gap: 5px;
        }

        .stat-content .stat-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .stat-unit {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* 特別なショートコードエリアのスタイル */
        .progress-bars-area {
            background: linear-gradient(135deg, #f8f9ff, #fff);
            border: 1px solid #e3f2fd;
        }

        .checklist-area {
            background: linear-gradient(135deg, #f3e5f5, #fff);
            border: 1px solid #f8bbd9;
        }

        .calendar-area {
            background: linear-gradient(135deg, #e8f5e8, #fff);
            border: 1px solid #c8e6c9;
        }

        /* レスポンシブ対応 */
        @media (max-width: 768px) {
            .study-stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .study-stat-item {
                padding: 20px 15px;
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .stat-content .stat-number {
                font-size: 1.8rem;
                justify-content: center;
            }

            .shortcode-area {
                padding: 20px;
                min-height: 150px;
            }

            .shortcode-placeholder {
                height: 120px;
            }

            .subsection-title {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .study-stats-grid {
                grid-template-columns: 1fr;
            }

            .study-stat-item {
                flex-direction: row;
                text-align: left;
            }
        }

        /* フッター前セクション */
        .pre-footer-section {
            background: var(--light-bg);
            padding: 40px 0;
            text-align: center;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .social-link {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }

        /* レスポンシブデザイン */
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

            .newsletter-form {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .gyousei-hero-section {
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

        /* アニメーション */
        .fade-in-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ローディングアニメーション */
        .loading-animation {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        /* ツールチップ */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.9rem;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- ヒーローセクション -->
    <section class="gyousei-hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">行政書士の道</h1>
                <p class="hero-subtitle">わかりやすい解説の集積地</p>
                <p class="hero-description">行政書士試験合格を目指す個人の学習記録・解説サイト<br>法律初心者でも理解できる丁寧な解説を心がけています</p>
                
                <div class="hero-cta">
                    <a href="#latest-posts" class="btn-primary">最新記事を見る</a>
                    <a href="#subjects" class="btn-secondary">科目別に学習する</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 統計情報セクション -->
    <section class="gyousei-stats-section fade-in-on-scroll">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">156</div>
                    <div class="stat-label">解説記事</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">23</div>
                    <div class="stat-label">今月の記事</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">対応科目</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">412</div>
                    <div class="stat-label">学習日数</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 機能紹介セクション -->
    <section class="features-section fade-in-on-scroll">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">このサイトの特徴</h2>
                <p class="section-subtitle">行政書士試験合格に向けた効率的な学習をサポート</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h3 class="feature-title">わかりやすい解説</h3>
                    <p class="feature-description">法律初心者でも理解できるよう、難しい用語や概念を丁寧に説明しています。図解や具体例を豊富に使用。</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">科目別体系学習</h3>
                    <p class="feature-description">憲法、行政法、民法など、科目ごとに体系的に整理された学習コンテンツで効率的に勉強できます。</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">実践的な解説</h3>
                    <p class="feature-description">過去問の解法テクニックや、実際の業務で活かせる知識まで、実践的な内容を重視しています。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 最新記事セクション -->
    <section class="gyousei-latest-posts fade-in-on-scroll" id="latest-posts">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">最新の学習記録</h2>
                <p class="section-subtitle">日々の勉強内容と理解のまとめ</p>
            </div>

            <div class="posts-grid">
                <!-- 記事カード1 -->
                <article class="post-card">
                    <div class="post-card-inner">
                        <div class="post-thumbnail">
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 3rem;">📜</div>
                        </div>
                        
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-date">2025.01.05</span>
                                <span class="post-category">憲法</span>
                            </div>
                            
                            <h3 class="post-title">
                                <a href="#">基本的人権の分類と特徴を整理しよう</a>
                            </h3>
                            
                            <div class="post-excerpt">
                                基本的人権は自由権、社会権、参政権、請求権の4つに分類されます。それぞれの特徴と代表的な権利について詳しく解説します...
                            </div>
                            
                            <div class="post-footer">
                                <div class="post-author">
                                    <img src="https://via.placeholder.com/24" alt="投稿者" width="24" height="24">
                                    <span>管理人</span>
                                </div>
                                
                                <div class="read-time">
                                    ⏱️ 5分
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- 記事カード2 -->
                <article class="post-card">
                    <div class="post-card-inner">
                        <div class="post-thumbnail">
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 3rem;">⚖️</div>
                        </div>
                        
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-date">2025.01.04</span>
                                <span class="post-category">行政法</span>
                            </div>
                            
                            <h3 class="post-title">
                                <a href="#">行政処分の種類と効力について</a>
                            </h3>
                            
                            <div class="post-excerpt">
                                行政処分には様々な種類があり、それぞれ異なる効力を持ちます。許可、認可、特許の違いを具体例とともに説明します...
                            </div>
                            
                            <div class="post-footer">
                                <div class="post-author">
                                    <img src="https://via.placeholder.com/24" alt="投稿者" width="24" height="24">
                                    <span>管理人</span>
                                </div>
                                
                                <div class="read-time">
                                    ⏱️ 8分
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- 記事カード3 -->
                <article class="post-card">
                    <div class="post-card-inner">
                        <div class="post-thumbnail">
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 3rem;">📋</div>
                        </div>
                        
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-date">2025.01.03</span>
                                <span class="post-category">民法</span>
                            </div>
                            
                            <h3 class="post-title">
                                <a href="#">物権的請求権の基本的な考え方</a>
                            </h3>
                            
                            <div class="post-excerpt">
                                物権的請求権は物権の内容を実現するための重要な制度です。返還請求権、妨害排除請求権、妨害予防請求権について...
                            </div>
                            
                            <div class="post-footer">
                                <div class="post-author">
                                    <img src="https://via.placeholder.com/24" alt="投稿者" width="24" height="24">
                                    <span>管理人</span>
                                </div>
                                
                                <div class="read-time">
                                    ⏱️ 6分
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="section-footer">
                <a href="#" class="btn-more-posts">すべての記事を見る</a>
            </div>
        </div>
    </section>

    <!-- 科目別コンテンツセクション -->
    <section class="gyousei-subjects-section fade-in-on-scroll" id="subjects">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">科目別学習コンテンツ</h2>
                <p class="section-subtitle">体系的に学べる科目別解説</p>
            </div>

            <div class="subjects-grid">
                <div class="subject-card">
                    <div class="subject-icon">📜</div>
                    <h3 class="subject-title">憲法</h3>
                    <p class="subject-count">24記事</p>
                    <p class="subject-description">基本的人権、統治機構、憲法の基本原理について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
                
                <div class="subject-card">
                    <div class="subject-icon">⚖️</div>
                    <h3 class="subject-title">行政法</h3>
                    <p class="subject-count">32記事</p>
                    <p class="subject-description">行政行為、行政手続、行政救済について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
                
                <div class="subject-card">
                    <div class="subject-icon">📚</div>
                    <h3 class="subject-title">民法</h3>
                    <p class="subject-count">45記事</p>
                    <p class="subject-description">総則、物権、債権、親族、相続について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
                
                <div class="subject-card">
                    <div class="subject-icon">🏢</div>
                    <h3 class="subject-title">商法</h3>
                    <p class="subject-count">18記事</p>
                    <p class="subject-description">会社法、商取引法、有価証券法について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
                
                <div class="subject-card">
                    <div class="subject-icon">🔍</div>
                    <h3 class="subject-title">基礎法学</h3>
                    <p class="subject-count">12記事</p>
                    <p class="subject-description">法理論、法制史、外国法について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
                
                <div class="subject-card">
                    <div class="subject-icon">📝</div>
                    <h3 class="subject-title">一般知識</h3>
                    <p class="subject-count">25記事</p>
                    <p class="subject-description">政治、経済、社会、情報通信・個人情報保護について学習します</p>
                    <a href="#" class="subject-link">学習する</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 学習進捗セクション -->
    <section class="study-progress-section fade-in-on-scroll">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">学習進捗状況</h2>
                <p class="section-subtitle">現在の学習状況を可視化して、効率的に勉強を進めましょう</p>
            </div>

            <!-- プログレスバーエリア -->
            <div class="progress-section">
                <h3 class="subsection-title">📊 科目別進捗状況</h3>
                <div class="shortcode-area progress-bars-area">
                    <!-- WordPress ショートコード貼り付けエリア -->
                    <!-- 例: [study_progress_bars] -->
                    <div class="shortcode-placeholder">
                        <p>💡 ここにプログレスバーのショートコードを貼り付けてください</p>
                        <code>[study_progress_bars]</code>
                    </div>
                </div>
            </div>

            <!-- チェックリストエリア -->
            <div class="checklist-section">
                <h3 class="subsection-title">✅ 学習チェックリスト</h3>
                <div class="shortcode-area checklist-area">
                    <!-- WordPress ショートコード貼り付けエリア -->
                    <!-- 例: [study_checklist] -->
                    <div class="shortcode-placeholder">
                        <p>💡 ここにチェックリストのショートコードを貼り付けてください</p>
                        <code>[study_checklist]</code>
                    </div>
                </div>
            </div>

            <!-- カレンダーエリア -->
            <div class="calendar-section">
                <h3 class="subsection-title">📅 学習カレンダー</h3>
                <div class="shortcode-area calendar-area">
                    <!-- WordPress ショートコード貼り付けエリア -->
                    <!-- 例: [study_calendar] -->
                    <div class="shortcode-placeholder">
                        <p>💡 ここにカレンダーのショートコードを貼り付けてください</p>
                        <code>[study_calendar]</code>
                    </div>
                </div>
            </div>

            <!-- 総合統計エリア -->
            <div class="study-stats-section">
                <h3 class="subsection-title">📈 学習統計</h3>
                <div class="study-stats-grid">
                    <div class="study-stat-item">
                        <div class="stat-icon">📚</div>
                        <div class="stat-content">
                            <div class="stat-number" data-target="85">0</div>
                            <div class="stat-label">学習完了率</div>
                            <div class="stat-unit">%</div>
                        </div>
                    </div>
                    
                    <div class="study-stat-item">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-content">
                            <div class="stat-number" data-target="127">0</div>
                            <div class="stat-label">連続学習日数</div>
                            <div class="stat-unit">日</div>
                        </div>
                    </div>
                    
                    <div class="study-stat-item">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-content">
                            <div class="stat-number" data-target="256">0</div>
                            <div class="stat-label">総学習時間</div>
                            <div class="stat-unit">時間</div>
                        </div>
                    </div>
                    
                    <div class="study-stat-item">
                        <div class="stat-icon">🏆</div>
                        <div class="stat-content">
                            <div class="stat-number" data-target="42">0</div>
                            <div class="stat-label">達成済み目標</div>
                            <div class="stat-unit">個</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- フッター前セクション -->
    <section class="pre-footer-section fade-in-on-scroll">
        <div class="container">
            <h3 class="section-title" style="font-size: 1.5rem;">一緒に行政書士を目指しましょう</h3>
            <p>このサイトは行政書士試験合格を目指す個人の学習記録です。<br>
            法律の学習は決して簡単ではありませんが、一歩ずつ着実に進んでいけば必ず理解できます。</p>
            
            <div class="social-links">
                <a href="#" class="social-link" title="Twitter">📱</a>
                <a href="#" class="social-link" title="note">📖</a>
                <a href="#" class="social-link" title="YouTube">📺</a>
                <a href="#" class="social-link" title="お問い合わせ">✉️</a>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script>
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

        // スクロール時のフェードイン効果
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
            observer.observe(el);
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
        const statsSection = document.querySelector('.gyousei-stats-section');
        if (statsSection) {
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

        // 学習統計のカウントアップアニメーション
        function animateStudyStats() {
            const counters = document.querySelectorAll('.study-stat-item .stat-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target) || parseInt(counter.textContent);
                const duration = 2500;
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

        // 学習進捗セクションが表示されたらカウントアップ開始
        const progressSection = document.querySelector('.study-progress-section');
        if (progressSection) {
            const progressObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateStudyStats();
                        progressObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });
            
            progressObserver.observe(progressSection);
        }

        // ショートコードエリアのプレースホルダーを非表示にする関数
        function hideShortcodePlaceholders() {
            document.querySelectorAll('.shortcode-area').forEach(area => {
                const placeholder = area.querySelector('.shortcode-placeholder');
                const hasRealContent = area.children.length > 1 || 
                    (area.children.length === 1 && !placeholder);
                
                if (hasRealContent && placeholder) {
                    placeholder.style.display = 'none';
                }
            });
        }

        // ページ読み込み後にプレースホルダーをチェック
        setTimeout(hideShortcodePlaceholders, 1000);

        // パフォーマンス最適化：画像の遅延読み込み
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // 動的なページ読み込み時間の表示
        window.addEventListener('load', function() {
            const loadTime = performance.now();
            console.log(`ページ読み込み時間: ${Math.round(loadTime)}ms`);
            
            // 開発時のデバッグ情報
            if (window.location.hostname === 'localhost') {
                console.log('開発モードで実行中');
                console.log('利用可能な機能:', {
                    intersectionObserver: 'IntersectionObserver' in window,
                    smoothScroll: 'scrollBehavior' in document.documentElement.style,
                    webP: document.createElement('canvas').toDataURL('image/webp').indexOf('webp') === 5
                });
            }
        });

        // エラーハンドリング
        window.addEventListener('error', function(e) {
            console.error('JavaScript エラー:', e.error);
            // 本番環境では適切なエラー報告システムに送信
        });

        // サービスワーカーの登録（PWA対応の準備）
        if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>

    <!-- 構造化データ（SEO対策） -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "行政書士の道",
        "description": "行政書士試験合格を目指す個人の学習記録・解説サイト",
        "url": "https://example.com",
        "author": {
            "@type": "Person",
            "name": "管理人"
        },
        "educationalLevel": "大学院・大学・短大・専門学校",
        "about": {
            "@type": "Thing",
            "name": "行政書士試験"
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://example.com/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Google Analytics（実際のトラッキングIDに置き換え） -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_TRACKING_ID');
    </script>
</body>
</html>