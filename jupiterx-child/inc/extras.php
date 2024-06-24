<?php
function cpt_reviews_shortcode() {
    // $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
     // Arguments for the query
     $the_query = new WP_Query(array(
        'post_type'      => 'sota-review',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'meta_key'       => 'created_date', // Replace 'date_field' with your ACF field name
        'orderby'        => 'meta_value',
        'order'          => 'DESC',  //  Order direction: DESC for descending, ASC for ascending.
        // 'paged'          => $paged
        'meta_query' => array(
            array(
                'key' => 'review_rating',  // Replace with the key of your ACF field
                'value' => 4,
                'type' => 'NUMERIC',
                'compare' => '>=',  // Show posts where the ACF value is greater than or equal to 3
            ),
        ),
    ));
    // The Query
    // Check if the query returns any posts
    if ( $the_query->have_posts() ) {
        $output .='<div class="reviews-cpt-container">';
        $output .= '<table class="reviews js-review-list">';

        while ($the_query->have_posts()) {  $the_query->the_post();
            $rating = get_field( 'review_rating' );
            $profileImage = get_field( 'featured' )['url'];
            $profileImage = $profileImage ? $profileImage : get_stylesheet_directory_uri().'/assets/images/default-profile.png';
            $createdDate = get_field( 'created_date' );
            $reviewComment = get_field( 'review_comment' );
            $reviewersName = get_the_title();
            $firstName = explode(' ', trim( $reviewersName ))[0];
            $output .='<tr>';
            $output .='<td class="review--pic" style="width: 0;">
                <div class="pic"><img src="'. $profileImage .'" alt="Profile Image"</div>
            </td>';
            $output .='<td class="review--info">';
            $output .='<div class="name--date">
                        <div class="name">'. $firstName .'</div>
                        <div class="date">'. $createdDate .'</div>
                    </div>';
            $output .='<div class="stars--rating">
                        <div class="stars">';
                        for ($i = 0; $i < $rating; $i++) {
                            $output .='<i class="material-icons">grade</i>';
                        }
            $output .='</div></div>'; 
            if ( ( $reviewComment ) ) {
            $output .='<div class="comments">'. $reviewComment .'</div>';
            }
            
            $output .='</td>';
        }
        wp_reset_postdata();
        $output .='</table>';

        if ( $the_query->max_num_pages > 1 ) { 
            $output .='<div class="review-cpt-load-button" style="text-align: center;"><a id="LoadReviews" class="js-loadmore-reviews button" data-posts-per-page="5" style="margin: 20px 0;">Load More</a></div>';
        }
        
    } else {
        // No posts found
        $output .= '<p>No reviews found.</p>';
    }

    return $output; // Return the output
}
// Register the shortcode with WordPress
add_shortcode('cpt_reviews_shortcode', 'cpt_reviews_shortcode');

// Load More Reviews CPT
add_action( 'wp_ajax_cpt_reviews_load_more_posts', 'cpt_reviews_load_more_posts' );
add_action( 'wp_ajax_nopriv_cpt_reviews_load_more_posts', 'cpt_reviews_load_more_posts' );
function cpt_reviews_load_more_posts() {
    $page = (isset($_POST['pageNumber'])) ? $_POST['pageNumber'] : 0;
    header("Content-Type: text/html");

    $args = array(
        'post_type'      => 'sota-review', // Replace 'my_custom_post' with your CPT
        'posts_per_page' => 5,
        'post_status'    => 'publish', 
        'meta_key'       => 'created_date', // Replace 'date_field' with your ACF field name
        'orderby'        => 'meta_value',
        'order'          => 'DESC',  //  Order direction: DESC for descending, ASC for ascending.
        'paged'          => $page,
        'meta_query' => array(
            array(
                'key' => 'review_rating',  // Replace with the key of your ACF field
                'value' => 4,
                'type' => 'NUMERIC',
                'compare' => '>=',  // Show posts where the ACF value is greater than or equal to 3
            ),
        ),
    );
    
    $the_query = new WP_Query($args);
    $max_page = $the_query->max_num_pages;
    ob_start();
?>
    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); 
        $rating = get_field( 'review_rating' );
        $profileImage = get_field( 'featured' )['url'];
        $profileImage = $profileImage ? $profileImage : get_stylesheet_directory_uri().'/assets/images/default-profile.png';
        // $profileImage = get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'reviewer-profile-image'));
        $createdDate = get_field( 'created_date' );
        $reviewComment = get_field( 'review_comment' );
        $reviewersName = get_the_title();
        $firstName = explode(' ', trim( $reviewersName ))[0];
    ?>
        <tr>
            <td class="review--pic" style="width: 0;">
                <div class="pic"> <img src="<?php echo $profileImage; ?>" alt="Profile Image"></div>
            </td>
            <td class="review--info">
                <div class="name--date">
                    <div class="name"><?php echo $firstName; ?></div>
                    <div class="date"><?php echo $createdDate; ?></div>
                </div>
                <div class="star--rating">
                    <div class="stars">
                        <?php  for ($i = 0; $i < $rating; $i++) {
                            echo '<i class="material-icons">grade</i>';
                        } ?>
                    </div>
                </div>
            <?php if ( ( $reviewComment ) ) { ?>
                <div class="comments"><?php echo $reviewComment; ?></div>
            <?php } ?>
            </td>
        </tr>
    <?php endwhile;
    wp_reset_postdata();
    $content = ob_get_clean();
    echo json_encode(array('content' => $content, 'page' => $page, 'max_page' => $max_page));
    exit;
}