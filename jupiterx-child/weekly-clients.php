<?php
/**
  Template Name: Weekly Clients
*/
$custom_query = new WP_Query('posts_per_page=-1&category_name=weekly-clients');
get_header();
?>
<div class="main-wrap">
   <section class="food-categorys">
      <div class="container custom-width">
         <div class="col-md-12" id="category-filter">
            <div class="filter-wrap">
               <h2 class="search-title">Search all recipes by keyword:</h2>
               <form class="jupiterx-search-form form-inline blog-search-form">
				  <div class="jupiterx-search-form__terms js-recipe-search-terms"></div>
                  <input class="form-control blog-search" type="text" placeholder="Type Keyword(s) then press Enter">
                  <button class="btn jupiterx-icon-search-1"></button>
               </form>
            </div>
         </div>
         <div class="row">
         <div class="col-md-3">
            <div id="categories-2" class="jupiterx-widget widget_categories categories-2 widget_categories">
               <h3 class="card-title">Recipes by Category</h3>
               <span class="line"></span>
               <div class="jupiterx-widget-content">
                  <ul class="js-recipe-cat">
                  <?php
                        $all_cat = array();
                        $includecat = '';
                        if ($custom_query->have_posts()) :
                            while ($custom_query->have_posts()) : $custom_query->the_post();
                                $postCats = get_the_category();
                                if ($postCats) {
                                    foreach($postCats as $cat) {
										if ( $cat->term_id != get_category_by_slug('weekly-clients')->term_id ) {
                                        	$all_cat[] = $cat->term_id;
										}
                                    }
                                }
                            endwhile; wp_reset_postdata();
                        endif;

                        $cat_args = array(
                            'number'     => 8,
                            'orderby'    => 'count',
                            'order'      => 'DESC'
                        );

                        if ( !empty($all_cat) ) {
                            $cat_arr = array_unique($all_cat);
                            $includecat = implode(",", $cat_arr);
                            $cat_args['include'] = $includecat;
                        }
                        $categories = get_categories($cat_args);
						if ($categories) :
						 echo '<li class="cat-item" data-value="">'.__('All Recipes','rmt').'</li>';
							foreach($categories as $cat) :
					  ?>
					  		<li class="cat-item" data-value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></li>
					  <?php
							endforeach;
						endif;
					 ?> 
                  </ul>
               </div>
            </div>
            <div id="categories-3" class="jupiterx-widget widget_categories categories-2 widget_categories">
               <h3 class="card-title">Popular Tags</h3>
               <span class="line"></span>
               <div class="jupiterx-widget-content">
                  <ul class="js-recipe-tags">
                  <?php   
                            $all_tags = array();
                            $includetags = '';
                            if ($custom_query->have_posts()) :
                                while ($custom_query->have_posts()) : $custom_query->the_post();
                                    $posttags = get_the_tags();
                                    if ($posttags) {
                                        foreach($posttags as $tag) {
                                            $all_tags[] = $tag->term_id;
                                        }
                                    }
                                endwhile; wp_reset_postdata();
                            endif;
                        
                            $tag_args = array(
                                'number'     => 8,
                                'orderby'    => 'count',
                                'order'      => 'DESC',
                                'include'    => $tags_str
                            );
                            
                            if ( !empty($all_tags) ) {
                                $tags_arr = array_unique($all_tags);
                                $includetags = implode(",", $tags_arr);
                                $tag_args['include'] = $includetags;
                            }
                            $taglist = get_tags($tag_args);
                            foreach( $taglist as $tag ) :
                        ?>
                            <li data-value="<?php echo $tag->slug; ?>" class="cat-item"><?php echo $tag->name; ?></li>
                        <?php endforeach; ?>
                  </ul>
               </div>
            </div>
         </div>
         <div class="col-md-9 cat-listing">
			 <?php
               $args = array(
				   'post_type' => 'post',
				   'posts_per_page' => 9,
				   'category_name' => 'weekly-clients'
			   );

				$post_query = new WP_Query($args);

				if($post_query->have_posts() ) {
            ?>
            <div class="raven-posts raven-grid raven-grid-3 raven-grid-tablet-2 raven-grid-mobile-1 js-blog-list" data-cat="" data-tag="" data-main-cat="<?php echo get_category_by_slug('weekly-clients')->term_id;?>">
            <?php
               while($post_query->have_posts() ) {
                  $post_query->the_post();   
            ?>
                     <?php if (has_post_thumbnail( $post->ID ) ):
                        // echo '<pre>';
                        // print_r($post -> download_recipe_link);
                        // echo '</pre><hr/>';
                        ?>
                     <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
                        <div class="raven-grid-item raven-post-item <?php echo $termsSlug;?>" 
                         style="background: linear-gradient(80deg,rgb(110 193 228 / 61%) 0%,#314e91d6 100%),url('<?php echo $image[0]; ?>'); background-size: cover;">
                         <?php else : ?>
                        <div class="raven-grid-item raven-post-item <?php echo $termsSlug;?>" 
                     style="background: linear-gradient(80deg,rgb(110 193 228 / 61%) 0%,#314e91d6 100%),url(''); background-size: cover;">
                     <?php endif; ?>
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
                     <?php
                  }
               ?>
            </div>
         <?php
            }
            ?>
			 <div class="custom-loader js-blog-load-more"></div>
         </div>
      </div>
   </section>
</div>
<?php
get_footer(); ?>