<?php
/**
 * Customizer type: tp_notice.
 *
 * Creates a new custom control.

 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TP_Kirki_Controls_Notice_Control extends Kirki_Control_Base {
    public $type = 'tp_notice';
    public function render_content() {  
        if($this->description){
            echo '<span class="description customize-control-description">'.$this->description.'</span>';
        } 
    }
} 