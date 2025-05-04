<?php

namespace MooWoodle;

defined( 'ABSPATH' ) || exit;

/**
 * MooWoodle Block class
 *
 * @class 		Block class
 * @version		6.0.0
 * @author 		Dualcube
 */
class Block {
    private $blocks;

    public function __construct() {
        $this->blocks = $this->initialize_blocks();
        // Register the block
        add_action( 'init', [$this, 'register_blocks'] );
        // Localize the script for block
        add_action( 'enqueue_block_assets', [ $this,'enqueue_all_block_assets'] );

    }
    
    public function initialize_blocks() {


        $blocks[] = [
            'name' => 'my-courses', // block name
            'textdomain' => 'moowoodle',
            'block_path' => MooWoodle()->plugin_url  . 'build/blocks/',
        ];
        //this path is set for load the translation   
        MooWoodle()->block_paths  += [
            'blocks/my-courses' => 'build/blocks/my-courses/index.js',
        ];


        return apply_filters('moowoodle_initialize_blocks', $blocks);
    }

    public function enqueue_all_block_assets() {
        FrontendScripts::load_scripts();
        foreach ($this->blocks as $block_script) {
            FrontendScripts::localize_scripts($block_script['textdomain'] . '-' . $block_script['name'] . '-editor-script');
            FrontendScripts::localize_scripts($block_script['textdomain'] . '-' . $block_script['name'] . '-script');
        }
    }

    
    public function register_blocks() {
        foreach ($this->blocks as $block) {
            register_block_type( $block['block_path'] . $block['name']);
        }
    }
    
}