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
	<style> 
		.citieslisting tr:nth-child(odd) {
            background-color: #f0f0f0;
        }
        div.search_result {
		  display: none;
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

				?>
				<div class="search_bar">
				    <form action="/" method="get" autocomplete="off">
				        <input type="text" name="s" placeholder="Search city here..." id="keyword" class="input_search" onkeyup="ajax_search_fetch()">
				        <button>
				            Search City
				        </button>
				        <button onClick="window.location.reload();">Clear</button>
				    </form>
				    <div class="search_result" id="datafetch">
				        <ul>
				            <li>Please wait..</li>
				        </ul>
				    </div>
				</div>



				<?php
				/*****************end for ajax filter***********************/

				

				global $wpdb;
				$myrows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'cities' and post_status = 'publish'" );
				$appid = 'e2a5c33637f0db180522053d2f3654e6'; //openweathermap app id
				// echo '<pre>';
				// var_dump($myrows);
				// echo '</pre>';
				echo '<table class="citieslisting" id="citieslisting">';
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
	<!-- wp filter with js -->
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
