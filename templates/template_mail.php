
<?php echo  _e('Hello! !', FYI_T_DOMAIN) . "\n\n"; ?>

<?php echo  _e('Information that can be updated manually, such as WordPress and plugins, is shown below.', FYI_T_DOMAIN) . "\n"; ?>
<?php echo  _e('If you need to update, take action.', FYI_T_DOMAIN) . "\n\n"; ?>

-----

<?php echo  _e('## Wordpress', FYI_T_DOMAIN) . "\n\n"; ?>
<?php if (empty($plot['core']['updates'])) : ?>
<?php echo  _e('There is no updatable information.', FYI_T_DOMAIN) . "\n"; ?>
<?php else : foreach ($plot['core']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## Plugin', FYI_T_DOMAIN) . "\n\n"; ?>
<?php if (empty($plot['plugins']['updates'])) : ?>
<?php echo  _e('There are no updatable plugins.', FYI_T_DOMAIN) . "\n"; ?>
<?php else : foreach ($plot['plugins']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## Themes', FYI_T_DOMAIN) . "\n\n"; ?>
<?php if (empty($plot['themes']['updates'])) : ?>
<?php echo  _e('There are no updatable themes.', FYI_T_DOMAIN) . "\n"; ?>
<?php else : foreach ($plot['themes']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
<?php echo "\n\n"; ?>
<?php echo  _e('## translation', FYI_T_DOMAIN) . "\n\n"; ?>
<?php if (empty($plot['translation']['updates'])) : ?>
<?php echo  _e('No translations are available for update.', FYI_T_DOMAIN) . "\n"; ?>
<?php else : foreach ($plot['translation']['updates'] as $key => $value) : ?>
- <?php echo $value . "\n"; ?>
<?php endforeach; endif; ?>
