<?php
/**
 * Plugin Name: WP-BibTeX
 * Plugin URI: https://github.com/hzxie/WP-BibTeX
 * Description: A plugin helps format BibTeX entries to display a bibliography or cite citations in WordPress.
 * Author: Haozhe Xie
 * Author URI: https://haozhexie.com
 * Version: 1.0.0
 * License: GPL v2.0
 */
define('PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * The require and optional fileds of BibTex entries.
 * Ref: https://en.wikipedia.org/wiki/BibTeX
 * 
 * @var array
 */
define('BIBTEX_ENTRIES', array(
    'article'       => array(
        'required'  => array('title', 'author', 'journal', 'year', 'volume'),
        'optional'  => array('number', 'pages', 'month'),
    ),
    'book'          => array(
        'required'  => array('title', 'author', 'publisher', 'year'),
        'optional'  => array('volume', 'number', 'series', 'edition', 'month', 'address'),
    ),
    'incollection'  => array(
        'required'  => array('title', 'author', 'booktitle', 'publisher', 'year'),
        'optional'  => array('volume', 'number', 'series', 'edition', 'pages', 'chapter', 'month', 'address'),
    ),
    'inproceedings' => array(
        'required'  => array('title', 'author', 'booktitle', 'year'),
        'optional'  => array('volume', 'number', 'series', 'pages', 'month', 'organization', 'publisher', 'address'),
    ),
    'mastersthesis' => array(
        'required'  => array('title', 'author', 'school', 'year'),
        'optional'  => array('month', 'address'),
    ),
    'phdthesis'     => array(
        'required'  => array('title', 'author', 'school', 'year'),
        'optional'  => array('month', 'address'),
    ),
    'unpublished'   => array(
        'required'  => array('title', 'author'),
        'optional'  => array('year', 'month'),
    ),
));

/**
 * Additional fields displayed after [BibTex] link.
 * By default, [Download PDF] is displayed.
 * 
 * @todo Custumize these fields in option page
 * @var array
 */
$additional_fields  = array(
    'url'           => 'Download PDF',
);

/**
 * Generate information for a citation.
 * @param  array  $attrs: an array contains attributes of the citation
 * @param  string $content: dummy parameter, will not used in the function
 * @return a formatted string contains expected information for the citation
 */
function wp_bibtex_shortcode($attrs, $content=null) {
    $missing_attributes = wp_bibtex_is_attrs_missing($attrs);
    if ( count($missing_attributes) != 0 ) {
        return '[ERROR] Missing attributes: '. implode(', ', $missing_attributes);
    }

    $citation_key    = wp_bibtex_get_citation_key($attrs);
    $bibtex_content  = wp_bibtex_get_mla_text($attrs);
    $bibtex_content .= '<div class="wpbibtex-item">';
    $bibtex_content .= '<a href="javascript:void(0);" class="wpbibtex-trigger">[BibTeX]</a> ';
    $bibtex_content .= wp_bibtex_get_additional_fields($attrs);
    $bibtex_content .= '<div class="bibtex"><pre><code>';
    $bibtex_content .= wp_bibtex_get_bibtex_text($citation_key, $attrs);
    $bibtex_content .= '</code></pre></div> <!-- .bibtex -->';
    $bibtex_content .= '</div> <!-- .wpbibtex-item -->';
    return $bibtex_content;
}

/**
 * Check whether there're missing attributes for the citation.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return an array contains name of missing attributes
 */
function wp_bibtex_is_attrs_missing($attrs) {
    $citation_type      = $attrs['type'];
    $missing_attributes = array();

    if ( !array_key_exists($citation_type, BIBTEX_ENTRIES) ) {
        array_push($missing_attributes, 'type');
    } else {
        $required_fields = BIBTEX_ENTRIES[$citation_type]['required'];
        foreach ( $required_fields as $required_field ) {
            if ( !array_key_exists($required_field, $attrs) ) {
                array_push($missing_attributes, $required_field);
            }
        }
    }
    return $missing_attributes;
}

/**
 * Generate BibTeX citation key for the citation.
 * The citation key is a string like 'xie2016comparison'.
 * 
 * @param  array  $attrs: an array contains attributes of the citation
 * @return BibTeX citation key of the citation
 */
function wp_bibtex_get_citation_key($attrs) {
    $year               = $attrs['year'];
    $author             = $attrs['author'];
    $title              = $attrs['title'];
    $first_author       = explode('and', strtolower($author))[0];
    $last_name          = explode(',', $first_author)[0];
    $title_words        = multiexplode(array(' ', '-', ':'), strtolower($title));
    $stop_words         = array(
        "a", "about", "above", "after", "again", "against", "all", "am", "an", 
        "and", "any", "are", "aren't", "as", "at", "be", "because", "been", 
        "before", "being", "below", "between", "both", "but", "by", "can't", 
        "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", 
        "doing", "don't", "down", "during", "each", "few", "for", "from", 
        "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", 
        "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", 
        "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", 
        "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's", 
        "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", 
        "off", "on", "once", "only", "or", "other", "ought", "our", "ours", 
        "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", 
        "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", 
        "that", "that's", "the", "their", "theirs", "them", "themselves", "then", 
        "there", "there's", "these", "they", "they'd", "they'll", "they're", 
        "they've", "this", "those", "through", "to", "too", "under", "until", "up", 
        "very", "was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", 
        "weren't", "what", "what's", "when", "when's", "where", "where's", "which", 
        "while", "who", "who's", "whom", "why", "why's", "with", "won't", "would", 
        "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", 
        "yourself", "yourselves"
    );
    $first_title_word   = '';

    for ( $i = 0; $i < count($title_words); ++ $i ) {
        if ( !in_array($title_words[$i], $stop_words) ) {
            $first_title_word = $title_words[$i];
            break;
        }
    }
    return $last_name. $year. $first_title_word;
}

/**
 * An extension of explode function.
 * @param  array  $delimiters: the array of boundary string
 * @param  string $string: the input string
 * @return an array of strings, each of which is a substring of string formed 
 *         by splitting it on boundaries formed by the delimiter.
 */
function multiexplode($delimiters, $string) {
    $ready  = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

/**
 * [wp_bibtex_get_mla_text description]
 * @param  array  $attrs: an array contains attributes of the citation
 * @return [type]        [description]
 */
function wp_bibtex_get_mla_text($attrs) {
}

/**
 * Display additional fields after [BibTex] link.
 * By default, [Download PDF] is displayed.
 * @param  array  $attrs: an array contains attributes of the citation
 * @return a HTML string contains additional fields' links
 */
function wp_bibtex_get_additional_fields($attrs) {
    global $additional_fields;
    $additional_fields_text         = '';
    
    foreach ( $additional_fields as $additional_field_key => $additional_field_name ) {
        if ( array_key_exists($additional_field_key, $attrs) ) {
            $additional_field_value = $attrs[$additional_field_key];
            $additional_fields_text.= sprintf("<a href='%s'>[%s]</a> ", 
                $additional_field_value, $additional_field_name);
        }
    }
    return $additional_fields_text;
}

/**
 * Get BibTeX text fot the citation.
 * @param  string $citation_key BibTeX citation key of the citation
 * @param  array  $attrs: an array contains attributes of the citation
 * @return BibTeX text fot the citation
 */
function wp_bibtex_get_bibtex_text($citation_key, $attrs) {
    $citation_type      = $attrs['type'];
    $required_fields    = BIBTEX_ENTRIES[$citation_type]['required'];
    $optional_fields    = BIBTEX_ENTRIES[$citation_type]['optional'];
    $bibtex_text        = sprintf("@%s{%s", $citation_type, $citation_key);
    foreach ( $required_fields as $field ) {
        $bibtex_text   .= sprintf(",\n  %s={%s}", $field, $attrs[$field]);
    }
    foreach ( $optional_fields as $field ) {
        if ( !array_key_exists($field, $attrs) ) {
            continue;
        }
        $bibtex_text   .= sprintf(",\n  %s={%s}", $field, $attrs[$field]);
    }
    $bibtex_text       .= sprintf("\n}");
    return $bibtex_text;
}

/**
 * [wp_bibtex_get_citation_authors_text description]
 * @param  [type] $author [description]
 * @return [type]         [description]
 */
function wp_bibtex_get_citation_authors_text($author) {
    $author_text = '';
    $authors     = explode('and', $author);
    foreach ( $authors as $author ) {
        $names          = explode(',', $author);
        $last_name      = trim($names[0]);
        $first_name     = count($names) < 2 ? '' : trim($names[1]);
        $author_name    = sprintf("%s %s", $first_name, $last_name);
        $author_text   .= $author_name. ', ';
    }
    return rtrim($author_text, ', ');
}

/**
 * Display essential JavaScript and CSS here.
 */
function wp_bibtex_generate_styles_and_scripts() { ?>
<style type="text/css">
    .wpbibtex-item .bibtex {
        display: none;
    }
</style>
<script type="text/javascript">
    (function($) {
        $('.wpbibtex-trigger').click(function() {
            var bibtexInfoContainer = $('.bibtex', $(this).parent());
            if ( $(bibtexInfoContainer).is(':visible') ) {
                $(bibtexInfoContainer).slideUp(250);
            } else {
                $(bibtexInfoContainer).slideDown(250);
            }
        });
    })(jQuery);
</script>
<?php
}

add_shortcode('WpBibTeX', 'wp_bibtex_shortcode');
add_action('shutdown', 'wp_bibtex_generate_styles_and_scripts');