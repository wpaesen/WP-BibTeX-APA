<?php
add_action('admin_menu', 'wp_bibtex_option_admin_menu');
function wp_bibtex_option_admin_menu() {
    add_options_page(
        'WP-BibTeX Options',
        'WP-BibTeX',
        'manage_options',
        'WP-BibTeX',
        'wp_bibtex_option_page'
    );
}

/**
 * Additional fields displayed after [BibTex] link.
 * By default, [Download PDF] is displayed.
 * @todo Custumize these fields in option page
 * @var array
 */
define('DEFAULT_ADDITIONAL_FIELDS', array(
    'url'   => 'Download PDF',
));
$additional_fields  = get_option('additional_fields');

/**
 * The name of the blog owner.
 */
define('DEFAULT_BLOG_OWNER_NAME', '');
$blog_owner_name    = get_option('blog_owner_name');

/**
 * Options of WP-BibTeX plugin.
 * @var array
 */
$wp_bibtex_options      = array(
    'blog_owner_name'   => !empty($blog_owner_name)   ? $blog_owner_name   : DEFAULT_BLOG_OWNER_NAME,
    'additional_fields' => !empty($additional_fields) ? $additional_fields : DEFAULT_ADDITIONAL_FIELDS,
);

function wp_bibtex_option_page() {
    global $wp_bibtex_options;

    $blog_owner_name    = $_POST['blog_owner_name'];
    if ( isset($blog_owner_name) ) {
        update_option('blog_owner_name', $blog_owner_name);
        $wp_bibtex_options['blog_owner_name'] = $blog_owner_name;
    }
?>
<div class="wrap">
    <h1><?php echo __('WP-BibTeX Options', 'WP-BibTeX'); ?></h1>
    <form method="POST" action="" novalidate="novalidate">
        <h2><?php echo __('General Settings'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="blog-owner-name"><?php echo __('Your Name', 'WP-BibTeX'); ?></label>
                </th>
                <td>
                    <input id="blog-owner-name" name="blog_owner_name" class="regular-text" type="text" />
                    <p class="description"><? echo __('Your name will be highlight with bold fonts in bibliography.', 'WP-BibTeX'); ?></p>
                </td>
            </tr>
        </table> <!-- .form-table -->
        <?php submit_button(); ?>
    </form>
</div> <!-- .wrap -->    
<script type="text/javascript">
    (function($) {
        $(function() {
            $('#blog-owner-name').val('<?php echo $wp_bibtex_options['blog_owner_name']; ?>');
        });
    })(jQuery);
</script>    
<?php 
}
