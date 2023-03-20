<?php

namespace Elementor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thim_Ekit_Widget_Image_Box extends Widget_Base {

	public function get_name() {
		return 'thim-image-box';
	}

	public function get_title() {
		return esc_html__( 'Thim: Image Box', 'eduma' );
	}

	public function get_icon() {
		return 'thim-widget-icon thim-widget-icon-image-box';
	}

	public function get_categories() {
		return array( \Thim_EL_Kit\Elementor::CATEGORY );
	}

	public function get_base() {
		return basename( __FILE__, '.php' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			[
				'label' => esc_html__( 'Image box', 'eduma' )
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => esc_html__( 'Style', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'base'     => esc_html__( 'Default', 'eduma' ),
					'layout-2' => esc_html__( 'Layout 2', 'eduma' ),
				],
				'default' => 'base'
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'eduma' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'eduma' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h3' 
			)
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'eduma' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Add your description here', 'eduma' ),
				'label_block' => true,
				'condition'   => [
					'style' => [ 'layout-2' ]
				]
			]
		);
		$this->add_control(
			'desc_position',
			[
				'label'   => esc_html__( 'Description Position', 'eduma' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'before_title' => esc_html__( 'Before Title', 'eduma' ),
					'after_title'  => esc_html__( 'After Title', 'eduma' )
				],
				'default' => 'after_title',
				'condition'   => [
					'style' => [ 'layout-2' ]
				]
			]
		);
		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Upload Image', 'eduma' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);


		$this->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link Title', 'eduma' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'widget_setting',
			[
				'label' => esc_html__( 'Setting', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		
		$this->add_control(
			'image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius Image', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .thim-image-box .wrapper-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_background_overlay',
			[
				'label'     => esc_html__( 'Background Overlay', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thim-image-box .wrapper-image:before' => 'content:""; background-color: {{VALUE}};'
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'title_setting',
			[
				'label' => esc_html__( 'Title', 'eduma' ),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'eduma' ),
				'selector' => '{{WRAPPER}} .thim-image-box .thim-image-info .title .title-tag',

			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .thim-image-box .thim-image-info .title .title-tag' => 'color: {{VALUE}};'
				],
			]
		);
		
		$this->add_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'eduma' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .template-layout-2 .thim-image-info .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => [
					'style' => [ 'layout-2' ]
				]
			)
		);
		$this->add_control(
			'title_bg_color',
			[
				'label'     => esc_html__( 'Title Background Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .template-layout-2 .thim-image-info .title' => 'background-color: {{VALUE}};'
				],
				'condition' => [
					'style' => [ 'layout-2' ]
				]
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow_title',
				'selector' => '{{WRAPPER}} .template-layout-2 .thim-image-info .title',
				'condition' => [
					'style' => [ 'layout-2' ]
				]
			]
		);
		$this->add_responsive_control(
			'title_vertical_align',
			array(
				'label'      => esc_html__( 'Vertically Top (px)', 'eduma' ),
				'type'       => Controls_Manager::SLIDER,
 				'range'      => array(
					'px'  => array(
						'min' => - 200,
						'max' => 200,
					),
 				),
				'selectors'  => array(
					'{{WRAPPER}} .thim-image-box.template-layout-2 .thim-image-info' => 'margin-top:{{SIZE}}px;',
				),
				'condition' => [
					'style' => [ 'layout-2' ]
				]
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'desc_setting',
			[
				'label'     => esc_html__( 'Description', 'eduma' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => [ 'layout-2' ]
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Typography', 'eduma' ),
				'selector' => '{{WRAPPER}} .description',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Text Color', 'eduma' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .description' => 'color: {{VALUE}};'
				]
			]
		);
		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		 
		if ( isset( $settings['image'] ) && isset( $settings['image']['id'] ) ) {
			echo '<div class="thim-image-box template-' . esc_attr( $settings['style'] ) . '">';
			echo '<div class="wrapper-image">'. wp_get_attachment_image( $settings['image']['id'], 'full' ).'</div>';
			echo '<div class="thim-image-info">';

			if($settings['desc_position'] == 'before_title'){
				echo isset( $settings['description'] ) ? '<div class="description">' . esc_html__( $settings['description'] ) . '</div>' : '';
			}

			if ( isset( $settings['title'] ) ) {
				echo '<div class="title"><'.$settings['title_tag'].' class="title-tag">';
				if(isset( $settings['link'] ) &&  $settings['link'] ){
					echo  '<a href="' . esc_url( $settings['link'] ) . '">' . esc_html__( $settings['title'] ) . '</a>';
 				}else{
					echo esc_html__( $settings['title'] );
				}
				echo '</'.$settings['title_tag'].'></div>';
			}

			echo isset( $settings['description'] ) ? '<div class="description">' . esc_html__( $settings['description'] ) . '</div>' : '';

			echo '</div>';

			echo '</div>';
		}
	}
}