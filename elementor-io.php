<?php
/**
 * Plugin Name: Elementor IO
 * Description: Elementor IO brings input and output support to any Elementor and Elementor Pro content. You now can easily import and export content with a few clicks.
 * Plugin URI: https://mateussouzaweb.com/
 * Author: Mateus Souza
 * Version: 1.0.0
 * Author URI: https://mateussouzaweb.com/
 * Text Domain: elementor-io
 */

 // Translation
define('ELEMENTOR_IO_PLUGIN', 'elementor-io');
load_theme_textdomain(ELEMENTOR_IO_PLUGIN, __DIR__. '/i18n');

/**
 * Add admin menu page link
 * @return void
 */
function elementorIoAdminMenuSetup(){

    add_submenu_page(
        'elementor',
        __('Elementor IO', ELEMENTOR_IO_PLUGIN),
        __('Elementor IO', ELEMENTOR_IO_PLUGIN),
        'manage_options',
        'elementor-io-admin',
        'elementorIoAdminPage'
    );

}
add_action('admin_menu', 'elementorIoAdminMenuSetup', 999);

/**
 * Render admin page content
 * @return void
 */
function elementorIoAdminPage(){

    $available = elementorIoGetContents();

    $contentOptions = '';
    $contentOptions = '<option value=""></option>';

    foreach( $available as $item ){
        $contentOptions .= '<option value="'. $item['id']. '">'. strtoupper($item['type']). ' / '. $item['title']. ' ('. $item['slug']. ')</option>';
    }
    ?>
    <div class="wrap elementor-io-wrap">

        <h1 class="wp-heading-inline">
            <?php _e('Elementor IO', ELEMENTOR_IO_PLUGIN) ?>
        </h1>

        <div class="columns">

            <form class="import-area"
                action="#"
                method="POST"
                enctype="multipart/form-data">

                <h2><?php _e('Import / Replace', ELEMENTOR_IO_PLUGIN) ?></h2>

                <div class="tabs">
                    <a href="#"
                        data-tab=".tab-bulk"
                        class="active"><?php _e('Bulk', ELEMENTOR_IO_PLUGIN) ?></a>
                    <a href="#"
                        data-tab=".tab-single"><?php _e('Single', ELEMENTOR_IO_PLUGIN) ?></a>
                </div>

                <div class="tab tab-bulk active">

                    <span class="description">
                        <?php _e('Content are matched from slug inside each file that was exported.', ELEMENTOR_IO_PLUGIN) ?>
                    </span>

                    <p>
                        <label>
                            <b><?php _e('Files:', ELEMENTOR_IO_PLUGIN) ?></b>
                        </label>
                        <input
                            type="file"
                            accept=".json"
                            name="files[]"
                            id="files"
                            multiple
                            required />
                    </p>

                </div>
                <div class="tab tab-single">

                    <p>
                        <label>
                            <b><?php _e('Content:', ELEMENTOR_IO_PLUGIN) ?></b>
                        </label>
                        <select
                            name="import_to"
                            id="import_to"
                            required>
                            <?php echo $contentOptions ?>
                        </select>
                    </p>
                    <p>
                        <label>
                            <b><?php _e('File:', ELEMENTOR_IO_PLUGIN) ?></b>
                        </label>
                        <input
                            type="file"
                            accept=".json"
                            name="file"
                            id="file"
                            required />
                    </p>

                </div>

                <p class="submit">
                    <input
                        type="hidden"
                        name="action"
                        value="import" />
                    <input
                        type="hidden"
                        name="ajax"
                        value="<?php echo wp_create_nonce('elementor-io') ?>" />
                    <input
                        type="submit"
                        class="button button-primary"
                        data-default="<?php _e('Import', ELEMENTOR_IO_PLUGIN) ?>"
                        data-disabled="<?php _e('Please Wait...', ELEMENTOR_IO_PLUGIN) ?>"
                        value="<?php _e('Import', ELEMENTOR_IO_PLUGIN) ?>" />
                </p>
            </form>

            <form class="export-area"
                action="#"
                method="POST"
                enctype="multipart/form-data">

                <h2><?php _e('Export', ELEMENTOR_IO_PLUGIN) ?></h2>
                <p>
                    <label>
                        <b><?php _e('Content:', ELEMENTOR_IO_PLUGIN) ?></b>
                    </label>
                    <select
                        name="export"
                        id="export"
                        required>
                        <?php echo $contentOptions ?>
                    </select>
                </p>
                <p class="submit">
                    <input
                        type="hidden"
                        name="action"
                        value="export" />
                    <input
                        type="hidden"
                        name="ajax"
                        value="<?php echo wp_create_nonce('elementor-io') ?>" />
                    <input
                        type="submit"
                        class="button button-primary"
                        data-default="<?php _e('Export', ELEMENTOR_IO_PLUGIN) ?>"
                        data-disabled="<?php _e('Please Wait...', ELEMENTOR_IO_PLUGIN) ?>"
                        value="<?php _e('Export', ELEMENTOR_IO_PLUGIN) ?>" />
                </p>
            </form>

        </div>

        <div class="results">
            <!-- ajax results -->
        </div>

        <style>
        .elementor-io-wrap .columns{
            display: flex;
            margin-top: 20px;
            max-width: 900px;
        }
        .elementor-io-wrap form{
            background: #FFF;
            border: 1px solid #CCC;
            flex: 1 1 50%;
            padding: 20px;
        }
        .elementor-io-wrap form.export-area{
            background: #F2F2F2;
            margin-left: -1px;
        }
        .elementor-io-wrap form h2{
            margin-top: 0;
        }
        .elementor-io-wrap form label{
            display: block;
        }
        .elementor-io-wrap form input[type="submit"]{
            height: auto;
            padding: 10px 40px;
            text-transform: uppercase;
        }

        .elementor-io-wrap .tabs{
            border-bottom: 1px solid #CCC;
            padding: 0 10px;
        }
        .elementor-io-wrap .tabs a{
            background: #F2F2F2;
            border: 1px solid #CCC;
            display: inline-block;
            font-weight: bold;
            margin-bottom: -1px;
            outline: 0;
            padding: 10px 20px;
            text-decoration: none;
            box-shadow: none;
        }
        .elementor-io-wrap .tabs a.active{
            background: #FFF;
            border-bottom-color: #FFF;
            box-shadow: none;
        }
        .elementor-io-wrap .tab{
            display: none;
            padding-top: 20px;
        }
        .elementor-io-wrap .tab.active{
            display: block;
        }

        .elementor-io-wrap .results{
            padding: 20px 0;
            max-width: 900px;
        }
        </style>
        <script>
        jQuery(document).ready(function($){

            var area = $('.elementor-io-wrap');
            var results = area.find('.results');
            var prevent = true;

            area.find('.tabs a').on('click', function(e){

                e.preventDefault();

                area
                .find('.tab')
                .find('[required]')
                .removeProp('required')
                .prop('disabled', true);

                area.find('.tabs a.active').removeClass('active');
                area.find('.tab.active').removeClass('active');

                $(this).addClass('active');
                $(this.dataset.tab).addClass('active');

                area
                .find(this.dataset.tab)
                .find('input, select')
                .removeProp('disabled')
                .prop('required', true);

            });

            area
            .find('.tabs a:first-child')
            .trigger('click');

            area.find('form').on('submit', function(e){

                if( !prevent ){
                    return;
                }

                e.preventDefault();

                var form = (this.method) ? $(this) : $(this).parents('form');
                var button = form.find('.button');
                var data = new FormData(form[0]);
                var html = '<div class="{CLASS} notice"><p>{MESSAGE}</p></div>';

                button.prop('disabled', true);
                button.prop('value', button.data('disabled'));

                results.html('');

                $.ajax({
                    type: form.attr('method'),
                    enctype: form.attr('enctype'),
                    url: form.attr('action'),
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data){

                        button.removeProp('disabled');
                        button.prop('value', button.data('default'));

                        if( typeof data == 'string' ){
                            data = JSON.parse(data);
                        }

                        // Means download and is valid
                        if( data.id ){
                            window.setTimeout(function(){
                                prevent = false;
                                    form[0].submit();
                                prevent = true;
                            }, 100);
                            return;
                        }

                        html = html.replace('{MESSAGE}', data.message);

                        if( data.success ){
                            results.html( html.replace('{CLASS}', 'updated') );
                        }else{
                            results.html( html.replace('{CLASS}', 'error') );
                        }

                    },
                    error: function (e) {

                        button.removeProp('disabled');
                        button.prop('value', button.data('default'));

                        html = html.replace('{CLASS}', 'error');
                        html = html.replace('{MESSAGE}', e.responseText);

                        results.html(html);

                        console.log("ERROR: ", e);

                    }
                });

            });

        });
        </script>

    </div>

<?php
}

/**
 * Process form submission from admin page
 * @return void
 */
function elementorIoAdminPageProcess(){

    if( !isset($_GET['page'])
        OR $_GET['page'] != 'elementor-io-admin' ){
        return;
    }

    if( !isset($_POST['action'])
        OR !wp_verify_nonce($_POST['ajax'], 'elementor-io') ){
        return;
    }

    try {

        $results = array(
            'success' => false,
            'error' => false,
            'message' => ''
        );

        // Import
        if( $_POST['action'] == 'import' ){

            $files = elementorIoGetUploadedFiles();

            if( isset($_POST['import_to'])
                AND $_POST['import_to'] ){

                elementorIoImportFromFile(
                    $files[0],
                    $_POST['import_to']
                );

            }else{

                foreach( $files as $file ){
                    elementorIoImportFromFile($file);
                }

            }

            Elementor\Plugin::$instance->files_manager->clear_cache();

            $results['success'] = true;
            $results['message'] = __('Content imported.', ELEMENTOR_IO_PLUGIN);

        // Export
        }elseif( $_POST['action'] == 'export' ){

            elementorIoExportToFile(
                $_POST['export']
            );

        }

    }catch( Exception $e ){

        $results['error'] = true;
        $results['message'] = $e->getMessage();

    }

    echo json_encode($results);
    exit;

}
add_action('admin_init', 'elementorIoAdminPageProcess');

/**
 * Retrieve uploaded files
 * @return array
 */
function elementorIoGetUploadedFiles(){

    $files = array();

    if( $_FILES['files']
        AND isset($_FILES['files']['name'])
        AND isset($_FILES['files']['name']['0'])
        AND $_FILES['files']['name']['0'] ){

        foreach( $_FILES['files'] as $key => $all ){
            foreach( $all as $i => $val ){
                $files[$i][$key] = $val;
            }
        }

    }elseif( $_FILES['file'] ){
        $files[] = $_FILES['file'];
    }

    return $files;
}

/**
 * Retrieve a complete list of
 * @return array
 */
function elementorIoGetContents(){
    global $wpdb;

    $contents = array();
    $results = $wpdb->get_results(
        'SELECT * FROM `wp_posts` as `posts`
        WHERE `posts`.`post_status` = "publish"
        AND EXISTS (
           SELECT * FROM `wp_postmeta`
           WHERE `wp_postmeta`.`meta_key` = "_elementor_version"
           AND `wp_postmeta`.`post_id`= `posts`.`ID`
        );'
    );

    foreach( $results as $item ){
        $contents[] = array(
            'id' =>  $item->ID,
            'type' => $item->post_type,
            'title' => $item->post_title,
            'slug' => $item->post_name
        );
    }

    usort($contents, function($a, $b) {
        $ka = $a['type']. ' '. $a['title'];
        $kb = $b['type']. ' '. $b['title'];
        return strcmp($ka, $kb);
    });

    return $contents;
}

/**
 * Retrieve elementor content
 * @param string $contentKey
 * @param mixed $postType
 * @return WP_Post
 */
function elementorIoGetContent($contentKey, $postType = NULL){

    if( is_numeric($contentKey) AND !$postType ){
        $post = get_post($contentKey);
    }else{
        $post = get_page_by_path($contentKey, OBJECT, $postType);
    }

    if( !$post OR !$post->ID ){
        throw new Exception(__('Content does not exists: ', ELEMENTOR_IO_PLUGIN). $contentKey);
    }

    $exists = get_post_meta($post->ID, '_elementor_version', TRUE);

    if( !$exists ){
        throw new Exception(__('Not a Elementor content: ', ELEMENTOR_IO_PLUGIN). $contentKey);
    }

    return $post;
}

/**
 * Add support to SVG mime
 * @param array $mimes
 * @return array
 */
function elementorIoSupportSvgFilter($mimes){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

/**
 * Recursive import SVG attachments and update reference ID to avoid issues with filesystem
 * @param array $data
 * @return array
 */
function elementorIoImportSvgAttachments($data){

    // Found SVG match
    if( isset($data['library'])
        AND $data['library'] == 'svg' ){

        add_filter('upload_mimes', 'elementorIoSupportSvgFilter', 100);

        $import = new Elementor\TemplateLibrary\Classes\Import_Images;
        $value = $import->import($data['value']);
        $data['value'] = $value;

        remove_filter('upload_mimes', 'elementorIoSupportSvgFilter', 100);

        return $data;
    }

    if( !is_array($data) ){
        return $data;
    }

    // Search on child
    foreach( $data as $key => $value ){
        $data[ $key ] = elementorIoImportSvgAttachments($value);
    }

    return $data;
}

/**
 * Recursive import carousel attachments and update reference ID to avoid issues with filesystem
 * @param array $data
 * @return array
 */
function elementorIoImportCarouselAttachments($data){

    // Found carousel match
    if( isset($data['carousel'])
        AND is_array($data['carousel']) ){

        foreach( $data['carousel'] as $index => $item ){
            $import = new Elementor\TemplateLibrary\Classes\Import_Images;
            $item = $import->import($item);
            $data['carousel'][ $index ] = $item;
        }

        return $data;
    }

    if( !is_array($data) ){
        return $data;
    }

    // Search on child
    foreach( $data as $key => $value ){
        $data[ $key ] = elementorIoImportCarouselAttachments($value);
    }

    return $data;
}

/**
 * Import file content to given elementor object
 * @param resource $file
 * @param mixed $contentKey
 * @return void
 */
function elementorIoImportFromFile($file, $contentKey = NULL){

    $name = ($file AND isset($file['name']))
            ? $file['name'] : 'UNDEFINED FILE';

    // Check file
    if( !$file
        OR $file['error'] > 0
        OR !file_exists($file['tmp_name']) ){
        throw new Exception(__('Content file not uploaded or invalid: ', ELEMENTOR_IO_PLUGIN). $name);
    }

    // Read file
    $file = $file['tmp_name'];
    $json = file_get_contents($file);
    $json = json_decode($json, TRUE);

    if( !$json ){
        throw new Exception(__('Content file is empty or bad formated: ', ELEMENTOR_IO_PLUGIN). $name);
    }

    // Check post
    if( !$contentKey ){
        $post = elementorIoGetContent($json['slug'], $json['type']);
    }else{
        $post = elementorIoGetContent($contentKey);
    }

    // Update content
    $content = $json['content'];
    $template = $json['template'];

    $usage = $json['usage'];
    $usage = maybe_unserialize($usage);

    $css = $json['css'];
    $css = maybe_unserialize($css);

    $settings = $json['settings'];
    $settings = maybe_unserialize($settings);

    $data = $json['data'];
    $data = elementorIoImportSvgAttachments($data);
    $data = elementorIoImportCarouselAttachments($data);
    $data = wp_json_encode($data);
    $data = wp_slash($data);

    wp_update_post(array(
        'ID' => $post->ID,
        'post_content' => $content
    ));

    update_post_meta($post->ID, '_elementor_template_type', $template);
    update_post_meta($post->ID, '_elementor_controls_usage', $usage);
    update_post_meta($post->ID, '_elementor_css', $css);
    update_post_meta($post->ID, '_elementor_settings', $settings);
    update_post_meta($post->ID, '_elementor_data', $data);

}

/**
 * Export elementor content to file
 * @param mixed $contentKey
 * @return void
 */
function elementorIoExportToFile($contentKey){

    // Check content
    $post = elementorIoGetContent($contentKey);

    // Export content
    $template = get_post_meta($post->ID, '_elementor_template_type', TRUE);
    $usage = get_post_meta($post->ID, '_elementor_controls_usage', TRUE);
    $css = get_post_meta($post->ID, '_elementor_css', TRUE);
    $settings = get_post_meta($post->ID, '_elementor_page_settings', TRUE);
    $data = get_post_meta($post->ID, '_elementor_data', TRUE);

    if( is_string($data) && !empty($data) ){
        $data = json_decode($data, TRUE);
    }

    if( empty($data) ){
        $data = [];
    }

    $json = array();
    $json['id'] = $post->ID;
    $json['slug'] = $post->post_name;
    $json['type'] = $post->post_type;
    $json['content'] = $post->post_content;

    $json['template'] = $template;
    $json['usage'] = $usage;
    $json['css'] = $css;
    $json['settings'] = $settings;
    $json['data'] = $data;

    $file = $json['slug']. '.json';

    header('Content-disposition: attachment; filename='. $file);
    header('Content-type: application/json');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    echo wp_json_encode($json);
    exit;

}