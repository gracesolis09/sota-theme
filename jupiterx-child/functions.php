<?php

// Include Jupiter X.
require_once( get_template_directory() . '/lib/init.php' );

/*
* Include all files in inc folder
*/
foreach ( glob( plugin_dir_path( __FILE__ ) . 'inc/*.php' ) as $filename ) {
	require_once $filename;
}

/**
* Enqueue assets.
*
* Add theme style and script to Jupiter X assets files.
*/
jupiterx_add_smart_action( 'wp_enqueue_scripts', 'jupiterx_child_enqueue_scripts', 8 );

function jupiterx_child_enqueue_scripts() {

	// Add the theme style as a fragment to have access to all the variables.
	jupiterx_compiler_add_fragment( 'jupiterx', get_stylesheet_directory_uri() . '/assets/less/style.less', 'less', '', 'v2' );
	wp_enqueue_style( 'jupiterxchild-style', get_stylesheet_directory_uri() . '/style.css', '', 'v2');

	// Add the theme script as a fragment.
	wp_enqueue_script( 'jupiterx-child-script', get_stylesheet_directory_uri() . '/assets/js/script.js', array(), '', true );
	wp_localize_script( 'jupiterx-child-script', 'wpAjax', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );

}

/**
* Example 1
*
* Modify markups and attributes.
*/
// jupiterx_add_smart_action( 'wp', 'jupiterx_setup_document' );

function jupiterx_setup_document() {

	// Header
	jupiterx_add_attribute( 'jupiterx_header', 'class', 'jupiterx-child-header' );

	// Breadcrumb
	jupiterx_remove_action( 'jupiterx_breadcrumb' );

	// Post image
	jupiterx_modify_action_hook( 'jupiterx_post_image', 'jupiterx_post_header_before_markup' );

	// Post read more
	jupiterx_replace_attribute( 'jupiterx_post_more_link', 'class' , 'btn-outline-secondary', 'btn-danger' );

	// Post related
	jupiterx_modify_action_priority( 'jupiterx_post_related', 11 );

}

/**
* Example 2
*
* Modify the sub footer credit text.
*/
// jupiterx_add_smart_action( 'jupiterx_subfooter_credit_text_output', 'jupiterx_child_modify_subfooter_credit' );

function jupiterx_child_modify_subfooter_credit() { ?>

	<a href="https//jupiterx.com" target="_blank">Jupiter X Child</a> theme for <a href="http://wordpress.org" target="_blank">WordPress</a>

<?php }


function hook_header() {
	?>
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
	jQuery(document).ready(function($){
		var data = {};
		data.business = {};
		data.reviews = {};
		data.overall = {};
		data.seo = {};
		data.sources = {};
		data.index = 0;
		data.businessid = 150334693647180;
		data.api_key ='4XupiSpT7yigg0EutZA4l9q67YNJh9EZ';
		$.ajax({
			method: "GET",
			dataType: 'json',
			url: "https://api.birdeye.com/resources/v1/business/" + data.businessid + "/?api_key=" + data.api_key + "&op=1"
		})
		.done(function( response ) {
			data.business = response;
			getReview([]);
			getReviewOverall();
			getSeoData1();
			getSeoData();
		});
		var getReview = function (sources) {
			var req = {"sources":sources};
			$.ajax({
				method: "POST",
				dataType: 'json',
				headers: {"content-type": "application/json"},
				data: JSON.stringify(req),
				url: "https://api.birdeye.com/resources/v1/review/businessId/" + data.businessid + "/?api_key=" + data.api_key + "&op=3&sindex=" + data.index
			})
			.done(function( response ) {
				data.index = data.index + response.length;
				data.reviews = response;
				if(data.reviews.length > 0) {
					var strReviews ="<table class='reviews'>";
					$.each(response, function(i, ele){
						ele.sourceType = ele.sourceType == 'Our Website' ? "BirdEye" : ele.sourceType;
						strReviews += "<tr>";
						<!--strReviews += '<td class="review--pic"><div class="pic"><img src="'+ ele.reviewer.thumbnailUrl +'" alt="reviewer profile image"></div></td>';-->
						strReviews += "<td class='review--info'>";
						strReviews += "<div class='name--date'>";
						ele.reviewer.nickName = ele.reviewer.nickName ? ele.reviewer.nickName : (ele.reviewer.firstName ? ele.reviewer.firstName : "Anonymous");
						strReviews += "<div class='name'>" + ele.reviewer.nickName + "</div>";
						strReviews += "<div class='date'>" +ele.reviewDate + "</div>";
						if(ele.sourceType == 'BirdEye'){
							strReviews += "</div>";
							strReviews += '<div class="lwr-wrp"><a target="_blank" style="color: blue;" href="' + ele.uniqueReviewUrl + '"><!--<div class="source">' + ele.sourceType + '</div></div>--></a>';
						}else{

							strReviews += "<div class='lwr-wrp'>";
							strReviews += '<a target="_blank" style="color: blue;" href="' + ele.reviewUrl + '"><div class="source">' + ele.sourceType + '</div></a>';
						}

						strReviews += "<div class='stars--rating'>";
						strReviews += "<div class='stars'>";
						for(var i=0; i<ele.rating;i++){
							strReviews +="<i class='fa fa-star'></i>";
						}
						strReviews += "</div>";
						strReviews += "</div>";
						strReviews += "</div>";
						if(!ele.comments == '' || !ele.comments == null){
							strReviews += "<div class='comments'>" + ele.comments + "</div>";

						}else{
							strReviews += "<div class='comments'></div>";
						}

						strReviews += "</td>";
						strReviews += "</tr>";
					});
					strReviews += "</table>";
					$(".reviews-container .load-button").remove();
					if (data.reviews.length == 5) {
						strReviews += '<div class="load-button" style="text-align: center;"><a id="LoadReviews" class="button" style="margin: 20px auto;">Load More</a></div>';
					}
					$(".reviews-container").append(strReviews);
					$('.reviews-container').off().on('click', 'a#LoadReviews', function() {
						var selectedSources = [];
						$('.reviews-source input').each(function() {
							if($(this). prop("checked")) {
								if($(this).val() != "all") {
									selectedSources.push($(this).val());
								}
							}
						});
						getReview(selectedSources);
					});
				} else {
					$(".reviews-container").append('No Data Found');
				}
			});
		};


		var getSeoData1 = function () {
			$.ajax({
				method: "GET",
				dataType: 'json',
				url: "https://api.birdeye.com/resources/v1/business/" + data.businessid + "/?api_key=" + data.api_key + "&op=1"
			})
			.done(function( response ) {
				data.seo = response;
				if(response) {
					var strSeoData1 ="<div >";
					strSeoData1 =response.avgRating;
					$("#avg_rate").html(response.avgRating);
				}
			});
		};


		var getSeoData1 = function () {
			$.ajax({
				method: "GET",
				dataType: 'json',
				url: "https://api.birdeye.com/resources/v1/business/" + data.businessid + "/?api_key=" + data.api_key + "&op=1"
			})
			.done(function( response ) {
				data.seo = response;
				if(response) {
					var strSeoData1 ="<div >";
					strSeoData1 =response.avgRating;
					$("#avg_rate").html(response.avgRating);
				}
			});
		};



		var getReviewOverall = function () {
			var req1 = {};
			$.ajax({
				method: "POST",
				dataType: 'json',
				headers: {"content-type": "application/json"},
				data: JSON.stringify(req1),
				url: "https://api.birdeye.com/resources/v1/review/businessid/" + data.businessid + "/summary?api_key=" + data.api_key + "&statuses=published"
			})
			.done(function( response ) {
				data.overall = response;
				if(response) {
					var strOverall = "<div class='star_s'>";
					for(var i=0; i<response.avgRating; i ++) {
						strOverall += "<i class='fa fa-star'></i>";
					}
					strOverall += "</div> <div> <span class='count'>" + response.reviewCount + "</span> reviews </div>";
					$(".reviews-overall").html(strOverall);


					var strSummery = "<div class='reviews-sum'><div class='stars--rating'>";
					$.each(response.ratings.reverse(), function (index, ele){
						if( index < response.ratings.length-1) {
							strSummery += "<div class='stars'>";
							for(var i=0; i<ele.rating; i++) {
								strSummery +="<i class='fa fa-star'></i>";
							}
							strSummery +="<div class='reviews-sum-count'>" + ele.reviewCount + "</div></div>";
						}else {
							if(ele.reviewCount >0){
								strSummery += "<p>"+ele.reviewCount+ " review with no rating </p>";
							}}
						});
						strSummery += "</div></div>";
						$(".reviews-summary").append(strSummery);

						getReviewSources(response)
					}
				});
			};
			var getSeoData = function () {
				$.ajax({
					method: "GET",
					dataType: 'json',
					url: "https://api.birdeye.com/resources/v1/business/" + data.businessid + "/?api_key=" + data.api_key + "&op=1"
				})
				.done(function( response ) {
					data.seo = response;
					if(response) {
						var strSeoData ="<div>";
						strSeoData +="<div class='aggregate' style='color: black; text-align: center;' class='align-center' itemscope='0' itemtype='https://schema.org/LocalBusiness'> <span itemprop='name'>" + response.name + "</span> <div itemprop='aggregateRating' itemscope='0' itemtype='https://schema.org/AggregateRating'>Rated <span itemprop='ratingValue'>" + response.avgRating + "</span>/5.0 based on <span itemprop='reviewCount'><a href='" + response.baseUrl + "' target='_blank' style='color: blue;'>" + response.reviewCount + "</span></a> reviews </div><img title='" + response.name + "' style='display:none;' alt='" + response.name + "' src='" + response.coverImageUrl + "' itemprop='image'><div style='display:none;'><span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'><span itemprop='streetAddress' content='" + response.location.address1 + "'>" + response.location.address1 + "</span><span itemprop='addressLocality' content='" + response.location.city + "'>" + response.location.city + "</span><span itemprop='addressRegion' content='" + response.location.state + "'>" + response.location.state + "</span><span itemprop='postalCode' content='" + response.location.zip + "'>" + response.location.zip + "</span><span itemprop='addressCountry' content='US' style='display: none;'></span></span></div><span style='display:none;' itemprop='priceRange'>exclude</span><span style='display:none;' itemprop='telephone'>" + response.phone + "</span></div> </div>";
					}
				});
			};
			var getReviewSources = function (response) {
				var strSummery = '<div class="reviews-source"><input type="checkbox" name="reviews-filter" value="all"> All</div>';
				for(var i=0; i<response.sources.length; i ++) {
					response.sources[i].sourceAlias = response.sources[i].sourceAlias == 'birdeye' ? "our_website" : response.sources[i].sourceAlias;
					strSummery += '<div class="reviews-source"><input type="checkbox" name="reviews-filter" value="' +response.sources[i].sourceAlias+ '"> ' +response.sources[i].sourceName + ' (' + response.sources[i].reviewCount + ')</div>';
				}
				$('.reviews-sources').append(strSummery);

				$('body').on('change', '.reviews-source input', function(){
					var isValue = $(this).val();
					if(isValue == 'all') {
						if($(this). prop("checked")) {
							$('.reviews-source input'). prop("checked",true);
						}else {
							$('.reviews-source input'). prop("checked",false);
						}
					}
					var arr = [];
					$('.reviews-source input').each(function(){
						if($(this). prop("checked")) {
							if($(this).val() != "all")
							arr.push($(this).val());
						}
					});
					$('.reviews-container').empty();
					data.index = 0;
					getReview(arr);
				});
			};
		});
	</script>
	<?php
}
// add_action('wp_head','hook_header');

function reviews_function(){
	$output = '';
	$output .= '<div id="avg_rate"></div>';
	$output .= '<div class="reviews-overall"></div>';
	$output .= do_shortcode('[BirdEyePlugin]');
	return $output;
}

add_shortcode( 'show_reviews', 'reviews_function' );

add_action( 'template_redirect', 'stwl_custom_redirect' );
function getVisIpAddr() {

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		return $_SERVER['REMOTE_ADDR'];
	}
}
function stwl_custom_redirect() {
	$vis_ip = getVisIPAddr();
	$ip = '67.177.15.145'; // Salt Lake City
	//$ip = '69.216.108.126'; // Dallas
    //$ip = '24.117.195.17'; // Boise

	$test = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));

	$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $vis_ip));
	// echo $vis_ip;
	if ( "Salt Lake City" === $ipdat->geoplugin_city ) {
		if ( is_front_page() ) {
			wp_redirect( home_url('/home-salt-lake-city') );
			exit;
		}

		if ( is_page( 8957 ) ) {
			wp_redirect( home_url('/sota-at-home-program-salt-lake-city') );
			exit;
		}
	} elseif ( "Boise" === $ipdat->geoplugin_city ) {
		if ( is_front_page() ) {
			wp_redirect( home_url('/sota-boise-idaho') );
			exit;
		}

		if ( is_page( 8957 ) ) {
			wp_redirect( home_url('/sota-at-home-nationwide-program-boise-idaho') );
			exit;
		}
	}
		// var_dump($test->geoplugin_city);
}

 /*
* Search Results Load More Ajax
*/
add_action('wp_ajax_nopriv_swl_search_load_more', 'swl_search_load_more');
add_action('wp_ajax_swl_search_load_more', 'swl_search_load_more');

function swl_search_load_more() {
	$page = ( isset( $_POST['pageNumber'] ) ) ? $_POST['pageNumber'] : 0;
	$keyword = $_POST['keyword'];
	$keywordcount = count($keyword);
	$category = $_POST['category'];
	$categories = array($_POST['maincat']);
	$tag = $_POST['tag'];
	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 9,
		'paged'  => $page
	);
	$postIds = array();
	if ( $keyword ) {
		if ( $keywordcount > 1 ) {
			for($i = 0; $i<$keywordcount; $i++) {
				$newkeyQuery = get_posts(array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields' => 'ids',
					's' => $keyword[$i]
				));
				$postIds = array_unique( array_merge( $postIds, $newkeyQuery ) );
			}
			$args['post__in'] = $postIds;
		} else {
			$args['s'] = $keyword[0];
		}
	}
	if(!empty($category)) {
		array_push($categories, $category);
	}
	if(!empty($tag)) {
		$args['tag'] = $tag;
	}
	$args['tax_query'] =  array(
        array(
            'taxonomy' => 'category',
            'field' => 'id',
            'terms' => $categories,
            'operator' => 'AND'
        )
    );
	$the_query = new WP_Query($args);
	$max_page = $the_query->max_num_pages;
	ob_start();
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
		 <div class="raven-grid-item raven-post-item" style="background: linear-gradient(80deg,rgb(110 193 228 / 61%) 0%,#314e91d6 100%),url('<?php echo $image[0]; ?>'); background-size: cover;">
			<div class="raven-post raven-post-inside">
				<div class="category-recipe raven-post-content">
					<div class="raven-post-meta">
					<span class="raven-post-meta-item raven-post-categories">
					<?php foreach ( ( get_the_category() ) as $category ) {
						echo $category->cat_name . ' ';
					} ?>
					</span>
					</div>
					<h3 class="raven-post-title">
					<?php 
						$link =  get_field('download_recipe_link');
						if($link):?>
						<a class="raven-post-title-link" href="<?php echo $link; ?>" target="_blank">
							<?php the_title(); ?>
						</a>
						<?php else : ?>
						<p class="raven-post-title-link">
							<?php the_title(); ?>
						</p>
						<?php endif;?>
					</h3>
					<div class="raven-post-read-more">
						<div class="raven-post-read-more">
							<?php 
							$link =  get_field('download_recipe_link');
							if($link):?>
							<a class="btn-cat-dl raven-post-button" href="<?php echo get_field('download_recipe_link')?>" target="_blank">
								<span class="raven-post-button-text">
									DOWNLOAD RECIPE
								</span>
							</a>
							<?php endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endwhile; wp_reset_postdata();
	$content = ob_get_clean();
	echo json_encode( array( 'content' => $content, 'page' => $page, 'max_page' => $max_page ) );
	exit;
}

add_filter('frm_time_to_check_duplicates', 'change_duplicate_time_limit_one_form', 10, 2);
function change_duplicate_time_limit_one_form( $time_limit, $entry_values ) {
    if ( $entry_values['form_id'] == 2 ) { //change 100 to your form ID
        $time_limit = 31536000;
    }
    return $time_limit;
}

function remove_last_word_post_title( $title, $id = null ) {
    // Check if it's not a singular post
    if ( ! is_singular() ) {
        return $title;
    }

    // Check if it's the specific post type
    if ( 'sota-review' == get_post_type() ) {
        // Explode the title into an array of words
        $words = explode( ' ', $title );
        // Keep only the first word
        $title = $words[0];
    }

    return $title;
}
add_filter( 'the_title', 'remove_last_word_post_title', 10, 2 );

function custom_redirect_from_custom_post_type() {
    // Check if it's a single post of the custom post type
    if ( is_singular( 'sota-review' ) ) {
        // Redirect to the home page
        wp_redirect( home_url() );
        exit();
    }
}
add_action( 'template_redirect', 'custom_redirect_from_custom_post_type' );