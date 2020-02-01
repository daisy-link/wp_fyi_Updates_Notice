
<?php echo  _e('こんにちは！！', WP_FYI_PG_NAME) . "\n\n"; ?>

<?php echo  _e('ご利用中のWordPress本体・プラグイン等で手動更新が可能な情報をお知らせします。', WP_FYI_PG_NAME) . "\n"; ?>
<?php echo  _e('下記より内容を確認の上、アップデートが必要なものを対応してください。', WP_FYI_PG_NAME) . "\n\n"; ?>

-----

<?php echo  _e('## 本体', WP_FYI_PG_NAME) . "\n\n"; ?>
<?php if (empty($plot['core']['updates'])) : ?>
<?php echo  _e('更新可能な情報はありません。', WP_FYI_PG_NAME) . "\n"; ?>
<?php else : foreach ($plot['core']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## プラグイン', WP_FYI_PG_NAME) . "\n\n"; ?>
<?php if (empty($plot['plugins']['updates'])) : ?>
<?php echo  _e('更新可能なプラグインはありません。', WP_FYI_PG_NAME) . "\n"; ?>
<?php else : foreach ($plot['plugins']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## テーマ', WP_FYI_PG_NAME) . "\n\n"; ?>
<?php if (empty($plot['themes']['updates'])) : ?>
<?php echo  _e('更新可能なテーマはありません。', WP_FYI_PG_NAME) . "\n"; ?>
<?php else : foreach ($plot['themes']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## 翻訳', WP_FYI_PG_NAME) . "\n\n"; ?>
<?php if (empty($plot['translation']['updates'])) : ?>
<?php echo  _e('更新可能な翻訳はありません。', WP_FYI_PG_NAME) . "\n"; ?>
<?php else : foreach ($plot['translation']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
