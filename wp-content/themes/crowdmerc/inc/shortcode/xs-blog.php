<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;

class Xs_Post_Widget extends Widget_Base {

  public $base;

    public function get_name() {
        return 'xs-blog';
    }

    public function get_title() {
        return esc_html__( 'Crowdmerc Post', 'crowdmerc' );
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return [ 'crowdmerc-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_tab',
            [
                'label' => esc_html__('Post', 'crowdmerc'),
            ]
        );

        $this->add_control(
            'blog_style',
            [
                'label'     => esc_html__( 'Select Style', 'crowdmerc' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 4,
                'options'   => [
                  'style1'     => esc_html__( 'Blog Grid Style 1', 'crowdmerc' ),
                  'style2'     => esc_html__( 'Blog Grid Style 2', 'crowdmerc' ),
                  'style3'     => esc_html__( 'Blog Grid Style 3', 'crowdmerc' ),
                  'style4'     => esc_html__( 'Blog listing', 'crowdmerc' ),
                ],
                'default' => 'style1'
            ]
        );

        $this->add_control(
          'post_count',
          [
            'label'         => esc_html__( 'Post count', 'crowdmerc' ),
            'type'          => Controls_Manager::NUMBER,
            'default'       => esc_html__( '3', 'crowdmerc' ),

          ]
        );
        
        $this->add_control(
            'count_col',
            [
                'label'     => esc_html__( 'Select Column', 'crowdmerc' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 4,
                'options'   => [
                      '6'     => esc_html__( '2 Column', 'crowdmerc' ),
                      '4'     => esc_html__( '3 Column', 'crowdmerc' ),
                ],
                'condition' => [
                    'blog_style!' => 'style4',
                ]
            ]
        );
        
        $this->add_control(
          'xs_post_cat',
          [
             'label'    =>esc_html__( 'Select category', 'crowdmerc' ),
             'type'     => Controls_Manager::SELECT,
             'options'  => xs_category_list( 'category' ),
             'default'  => '0'
          ]
        );

        $this->end_controls_section();
    }

    protected function render( ) {
          $settings = $this->get_settings();
          $style = $settings['blog_style'];
          $xs_post_cat = $settings['xs_post_cat'];
          $count_col = $settings['count_col'];
          $post_count = $settings['post_count'];

          $paged = 1;
          if ( get_query_var('paged') ) $paged = get_query_var('paged');
          if ( get_query_var('page') ) $paged = get_query_var('page');
          $query = array(
              'post_type'      => 'post',
              'post_status'    => 'publish',
              'posts_per_page' => $post_count,
              'cat' => $xs_post_cat,
              'paged' => $paged,
          );
          $xs_query = new \WP_Query( $query );
          if($xs_query->have_posts()):
          ?>
              <div class="row xs-blog-grid">
                <?php
                while ($xs_query->have_posts()) :
                    $xs_query->the_post();
                    $terms  = get_the_terms( get_the_ID(), 'category' );
                    if ( $terms && ! is_wp_error( $terms ) ) : 
                      $cat_temp = '';
                      foreach ( $terms as $term ) {
                          $cat_temp .= '<a href="'.get_category_link($term->term_id).'" class="xs-blog-meta-tag green-bg bold color-white xs-border-radius" rel="category tag">'.$term->name.'</a>';
                      }
                    endif;
                
                    switch ($style) {
                      case 'style1':
                        require CROWDMERC_SHORTCODE_DIR_STYLE.'/blog/style1.php';
                        break;
                      
                      case 'style2':
                        require CROWDMERC_SHORTCODE_DIR_STYLE.'/blog/style2.php';
                        break;

                      case 'style3':
                        require CROWDMERC_SHORTCODE_DIR_STYLE.'/blog/style3.php';
                        break;

                      case 'style4':
                        require CROWDMERC_SHORTCODE_DIR_STYLE.'/blog/style4.php';
                        break;
                    }
                endwhile;
                ?>
              </div>
            <?php
          endif;
          wp_reset_postdata();
    }
    protected function _content_template() { }
}