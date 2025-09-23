<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap filepress-wrap">
<h1><?php esc_html_e( 'FilePress', 'filepress' ); ?></h1>


<div class="filepress-controls">
<input type="file" id="filepress-file" />
<button class="button button-primary" id="filepress-upload"><?php esc_html_e( 'Upload', 'filepress' ); ?></button>
<input type="text" id="filepress-search" placeholder="<?php esc_attr_e( 'Search files...', 'filepress' ); ?>" />
</div>


<div id="filepress-messages"></div>


<div id="filepress-list" class="filepress-list"></div>


</div>