<?php
/**
 *
 * @link              https://github.com/johappel
 * @since             1.0.0
 * @package           Facetwp_Blocksy
 *
 * @wordpress-plugin
 * Plugin Name:       Integrade FacetWP with Blocksy
 * Plugin URI:        https://github.com/rpi-virtuell/facetwp-blocksy
 * Description:       Erleichtert die Integration von FacetWp im Blocksy Theme. Einfach Faccets im Header oder Widget Bereich einer Archive Seite einfügen. Fertig!
Zum Blättern ist der FacetWP Pager zu verwenden. Der Blocksy Pager sollte stattdessen abgeschaltet sein. Getestet mit infinte Sroll
 * Version:           1.0.0
 * Author:            Joachim Happel
 * Author URI:        https://github.com/johappel
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       facetwp-blocksy
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * ermöglicht die archives mit facetwp zu filtern
 * Blocksy Pager muss ausgeschaltet sein!
 */
class facetwp_blocksy
{

    public $post_types = [];

    public function __construct()
    {
        add_action( 'facetwp_query_args', [ $this, 'facetwp_injection' ], 10, 2 );
        add_action( 'wp_head', [ $this, 'inject_javascript' ], 10 );

        $args = array(
            'public'   => true,
            '_builtin' => false // Use false to return only custom post types
        );

        $this->post_types = get_post_types( $args );

    }

    public function facetwp_injection($query_args, $class){

        if ( 'post' === $query_args['post_type'] ) {
            $blocksy = get_option( 'theme_mods_blocksy' );
            if ( $blocksy ) {
                $posts_per_page               = $blocksy['post_archive_archive_per_page'];
                $query_args['posts_per_page'] = $posts_per_page;
            }
        }

        //für Custom Post Types
        foreach ($this->post_types as $cpt){

            if ( $cpt === $query_args['post_type'] ) {
                $blocksy = get_option( 'theme_mods_blocksy' );
                if ( $blocksy ) {
                    $posts_per_page               = $blocksy[$cpt.'_archive_archive_per_page'];
                    $query_args['posts_per_page'] = $posts_per_page;
                }
            }
        }






    }

    public function inject_javascript(){

        ?>
        <script>
            $(document).on('facetwp-loaded', e=>{
                setTimeout(e=>{
                    //remove load more button if last page has loaded
                    if(FWP.settings.pager.page  == FWP.settings.pager.total_pages ){
                        $('.facetwp-facet-paging').hide();
                    }else{
                        $('.facetwp-facet-paging').show();
                    }

                    if($('.facetwp-selections').html().length>0){
                        $('button.facetwp-reset.facetwp-hide-empty').show();
                        //$('.ct-container summary.button').addClass('active');
                    }else{
                        $('button.facetwp-reset.facetwp-hide-empty').hide();
                        //$('.ct-container summary.button').removeClass('active');
                    }
                    //remove result page content from facetwp-template if no results
                    if(FWP.settings.pager.total_pages === 0){
                        $('.entries.facetwp-template').html('');
                        $('.facetwp-facet-paging').hide();
                    }


                },100);

            });
        </script>

        <?php

    }
}
new facetwp_blocksy();
