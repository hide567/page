<?php
/**
 * サイドバーテンプレート
 *
 * @package 行政書士試験ブログ
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside class="widget-area">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside>