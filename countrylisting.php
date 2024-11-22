<?php /* Template Name: CountryListing */ ?>

<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header(); ?>
	<style > 
		.citieslisting tr:nth-child(odd) {
            background-color: #f0f0f0;
        }
	</style>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) :
				the_post();

				do_action( 'storefront_page_before' );

				get_template_part( 'content', 'page' );

				/***************start for ajax filter***********************/
				$citylist = new WP_Query([
				    'post_type' => 'cities',
				    'posts_per_page' => -1,
				    'order_by' => 'date',
				    'order' => 'desc',
				  ]);
				echo '<pre>';
				var_dump($citylist->posts);
				echo '</pre>';

				if($citylist->have_posts()): ?>
				  <ul class="city-tiles">
				    <?php
				      //while($citylist->have_posts()) : $citylist->the_post();
				        //include('_components/project-list-item.php');
				        foreach ($citylist->posts as $key){
					        echo '<li>'.$key->post_title.'</li>';
					        // echo '<li></li>';
					        // echo '<li></li>';
					        // echo '<li></li>';
				    	}
				      //endwhile;
				    ?>
				  </ul>
				  <?php wp_reset_postdata(); ?>
				<?php endif; 
				/*****************end for ajax filter***********************/

				// $the_query = new WP_Query( array( 'post_type' => 'cities' ) );

				// while ( $the_query->have_posts() ) : $the_query->the_post();
				//     echo '<tr><td>';
				//     the_title();
				//     echo '</td></tr>';
				// endwhile;

				// wp_reset_postdata();

				//require_once "wp-load.php";
				global $wpdb;
				$myrows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'cities' and post_status = 'publish'" );
				$appid = 'e2a5c33637f0db180522053d2f3654e6';
				// echo '<pre>';
				// var_dump($myrows);
				// echo '</pre>';
				echo '<table class="citieslisting">';
				echo '<tr>
						<td>City</td>
						<td>Country</td>
						<td>Current Temperature</td>
						<td>Latitude</td>
						<td>Longtitude</td>
					</tr>';
				foreach ($myrows as $row){
					echo '<tr>';
					echo '<td class="city">'.$row->post_title.'</td>';
					$terms = wp_get_post_terms( $row->ID, 'countries');
					if(!empty($terms)){						
						foreach ( $terms as $term ) {
							echo '<td class="country">'.$term->name.'</td>';
						}
					}else{
						echo '<td class="country">Undefined</td>';
					}
					$maintemp = 'https://api.openweathermap.org/data/2.5/weather?lat='.get_post_meta($row->ID, 'latitude', true).'&lon='.get_post_meta($row->ID, 'longitude', true).'&appid='.$appid;
					$response = wp_remote_get( $maintemp );
					$bd = $response['body'];
					$mt = json_decode($bd);
					//var_dump($mt);
					if(!empty($mt->main->temp)){
						$maintmp = number_format($mt->main->temp,2);
					}else{
						$maintmp = $mt->message;
					}
					echo '<td class="currenttemp">'.$maintmp.' </td>';
					echo '<td class="latitude">'.get_post_meta($row->ID, 'latitude', true).'</td>';
					echo '<td class="longtitude">'.get_post_meta($row->ID, 'longitude', true).'</td>';
					// $post_type = $row->post_type;
					echo '</tr>';
				}
				echo '</table>';


				echo '*************************************************************************';

				?>




				<?php

				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
<script type="text/javascript">
	$('.cat-list_item').on('click', function() {
	  $('.cat-list_item').removeClass('active');
	  $(this).addClass('active');

	  $.ajax({
	    type: 'POST',
	    url: '/wp-admin/admin-ajax.php',
	    dataType: 'html',
	    data: {
	      action: 'filter_projects',
	      category: $(this).data('slug'),
	    },
	    success: function(res) {
	      $('.project-tiles').html(res);
	    }
	  })
	});
</script>
<?php
do_action( 'storefront_sidebar' );
get_footer();
