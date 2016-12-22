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
$DEFAULT_ADDITIONAL_FIELDS = array(
    array(
        'key'   => 'url',
        'value' => 'Download PDF'
    ),
);
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
    'additional_fields' => !empty($additional_fields) ? $additional_fields : $DEFAULT_ADDITIONAL_FIELDS,
);

function wp_bibtex_option_page() {
    global $wp_bibtex_options;

    $blog_owner_name    = $_POST['blog_owner_name'];
    $additional_fields  = $_POST['additional_fields'];
    if ( isset($blog_owner_name) ) {
        update_option('blog_owner_name', $blog_owner_name);
        $wp_bibtex_options['blog_owner_name'] = $blog_owner_name;
    }
    if ( isset($additional_fields) ) {
        update_option('additional_fields', $additional_fields);
        $wp_bibtex_options['additional_fields'] = $additional_fields;
    }
?>
<style type="text/css">
    div.wrap {
        max-width: 640px;
    }

    div.wrap input.regular-text {
        width: 100%;
    }

    div.wrap table.form-table td {
        padding: 15px 0 15px 10px;
    }

    div.wrap h2 {
        position: relative;
    }

    a#new-additional-field {
        text-decoration: none;
        position: absolute;
        right: 0;
    }

    ul#additional-fields {
        list-style: none;
        padding-left: 0;
    }

    ul#additional-fields li {
        margin: 5px 0px;
        border: 1px solid #ccc;
        overflow: hidden;
    }

    ul#additional-fields li div.header {
        background: #fafafa;
        padding: 10px;
        position: relative;
    }

    ul#additional-fields li div.header h4 {
        margin: 0;
        min-height: 18px;
    }

    ul#additional-fields li div.header a {
        display: inline-block;
        text-decoration: none;
        padding: 5px;
        position: absolute;
        top: 3px;
        border-radius: 2px;
    }

    ul#additional-fields li div.header a.action-edit {
        background: #f7f7f7;
        border: 1px solid #666;
        color: #555;
        right: 40px;
    }

    ul#additional-fields li div.header a.action-trash {
        background: #b73b27;
        border: 1px solid #7f291b;
        color: #fff;
        right: 3px;
    }

    ul#additional-fields li div.body {
        background: #fff;
        border-top: 1px solid #ccc;
        display: none;
        padding: 10px;
    }

    ul#additional-fields li div.body h5 {
        margin: 10px 0;
    }
</style>
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
                    <input id="blog-owner-name" name="blog_owner_name" class="regular-text" type="text" value="<?php echo $wp_bibtex_options['blog_owner_name']; ?>" />
                    <p class="description"><? echo __('Your name will be highlighted with bold fonts in bibliography.', 'WP-BibTeX'); ?></p>
                </td>
            </tr>
        </table> <!-- .form-table -->
        <h2>
            <?php echo __('Additional Fields', 'WP-BibTeX'); ?> 
            <a id="new-additional-field" href="javascript:void(0);"><i class="dashicons dashicons-plus-alt"></i></a>
        </h2>
        <ul id="additional-fields">
        <?php 
            foreach ( $wp_bibtex_options['additional_fields'] as $index => $additional_field ):
                $additional_field_key   = $additional_field['key'];
                $additional_field_name  = $additional_field['value'];
        ?>
            <li>
                <div class="header">
                    <h4><?php echo $additional_field_name; ?></h4>
                    <a href="javascript:void(0);" class="action-edit">
                        <i class="dashicons dashicons-edit"></i>
                    </a>
                    <a href="javascript:void(0);" class="action-trash">
                        <i class="dashicons dashicons-trash"></i>
                    </a>
                </div> <!-- .header -->
                <div class="body">
                    <h5><?php echo __('Field Key', 'WP-BibTeX'); ?></h5>
                    <input name="additional_fields[<?php echo $index; ?>][key]" class="regular-text additional-field-key" type="text" value="<?php echo $additional_field_key; ?>" />
                    <p class="description"><? echo __('The key of the field that used in the [WpBibTeX] shortcode. It can ONLY consists of characters and underlines.', 'WP-BibTeX'); ?></p>
                    <h5><?php echo __('Field Name', 'WP-BibTeX'); ?></h5>
                    <input name="additional_fields[<?php echo $index; ?>][value]" class="regular-text additional-field-value" type="text" value="<?php echo $additional_field_name; ?>" />
                    <p class="description"><? echo __('The name of the link displayed in the page.', 'WP-BibTeX'); ?></p>
                </div> <!-- .body -->
            </li>
        <?php endforeach; ?>
        </ul>
        <?php submit_button(); ?>
    </form>
</div> <!-- .wrap -->
<script type="text/javascript">
    /* String Protorype Extension */
    String.prototype.format = function() {
        var newStr = this, i = 0;
        while (/%s/.test(newStr)) {
            newStr = newStr.replace("%s", arguments[i++])
        }
        return newStr;
    }
</script>
<script type="text/javascript">
    (function($) {
        $('#new-additional-field').click(function() {
            var index = $('li', '#additional-fields').length;
            $('#additional-fields').append(
              '<li>' + 
              '    <div class="header">' + 
              '        <h4></h4>' + 
              '        <a href="javascript:void(0);" class="action-edit">' + 
              '            <i class="dashicons dashicons-edit"></i>' + 
              '        </a>' + 
              '        <a href="javascript:void(0);" class="action-trash">' + 
              '            <i class="dashicons dashicons-trash"></i>' + 
              '        </a>' + 
              '    </div> <!-- .header -->' + 
              '    <div class="body">' + 
              '        <h5><?php echo __('Field Key', 'WP-BibTeX'); ?></h5>' + 
              '        <input name="additional_fields[%s][key]" class="regular-text additional-field-key" type="text" />'.format(index) + 
              '        <p class="description"><? echo __('The key of the field that used in the [WpBibTeX] shortcode. It can ONLY consists of characters and underlines.', 'WP-BibTeX'); ?></p>' +
              '        <h5><?php echo __('Field Name', 'WP-BibTeX'); ?></h5>' + 
              '        <input name="additional_fields[%s][value]" class="regular-text additional-field-value" type="text" />'.format(index) + 
              '        <p class="description"><? echo __('The name of the link displayed in the page.', 'WP-BibTeX'); ?></p>' + 
              '    </div> <!-- .body -->' + 
              '</li>');
        });
    })(jQuery);
</script>
<script type="text/javascript">
    (function($) {
        $('#additional-fields').on('keyup', '.additional-field-value', function() {
            var itemContainer = $(this).parent().parent(),
                headerText    = $('h4', itemContainer);

            headerText.html($(this).val());
        });
    })(jQuery);
</script>  
<script type="text/javascript">
    (function($) {
        $('#additional-fields').on('click', '.action-edit', function() {
            var itemContainer = $(this).parent().parent(),
                itemContent   = $('.body', itemContainer);

            if ( itemContent.is(':visible') ) {
                itemContent.css('display', 'none');
            } else {
                itemContent.css('display', 'block');
            }
        });
    })(jQuery);
</script>  
<script type="text/javascript">
    (function($) {
        $('#additional-fields').on('click', '.action-trash', function() {
            if ( confirm('<?php echo __('Are you sure you want to remove this?'); ?>') ) {
                $(this).parent().parent().remove();
            }
        });
    })(jQuery);
</script>  
<?php 
}
