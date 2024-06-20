 jQuery(document).ready(function($) {
     var blogPageNumber = 1;
	 var fcsearchterms = [];
	 var canBeLoaded = true;
	 $(window).bind('scroll', function() {
		if(($(window).scrollTop() >= $('.js-blog-list').offset().top + $('.js-blog-list').outerHeight() - window.innerHeight) && canBeLoaded == true ){
			var maincat = $('.js-blog-list').data('main-cat');
			var cat = $('.js-blog-list').data('cat');
			var tag = $('.js-blog-list').data('tag');
			blogPageNumber++;
			$('.js-blog-load-more').show();
			$.ajax({
				url : wpAjax.ajaxUrl,
				data : { 
					action: 'swl_search_load_more',
					pageNumber: blogPageNumber,
					keyword: fcsearchterms,
					maincat: maincat,
					category: cat,
					tag: tag
				},
				type : 'post',
				dataType: 'json',
				beforeSend : function( xhr ) {
					canBeLoaded = false;
				},
				success : function( posts ) {
					$('.js-blog-list').append(posts.content);
					$('.js-blog-load-more').hide();
					if(posts.page == posts.max_page) {
						canBeLoaded = false;
					}else {
						canBeLoaded = true;
					}
				}
			});
		}
	});

   $('.blog-search-form').on('submit', function(e) {
		e.preventDefault();
		var search = $('.blog-search').val();
	   	var maincat = $('.js-blog-list').data('main-cat');
	    var cat = $('.js-blog-list').data('cat');
		var tag = $('.js-blog-list').data('tag');
		if(search !== '') {
			$('.js-recipe-search-terms').append('<div><span class="js-term">'+search+'</span> <a href="#" class="fa fa-remove js-remove"></a></div>');
			fcsearchterms.push(search);
			$('.blog-search').val('');
			$('.blog-search').focus();
		}
		blogPageNumber = 1;
		$.ajax({
			url : wpAjax.ajaxUrl,
			data : { 
				action: 'swl_search_load_more',
				keyword: fcsearchterms,
				maincat: maincat,
				category: cat,
				tag: tag
			},
			type : 'post',
			dataType: 'json',
			success : function( posts ) {
				$('.js-blog-list').html(posts.content);
				if(posts.max_page > 1) {
					canBeLoaded = true;
				}else {
					canBeLoaded = false;
				}
			}
		});
	});
	 $('body').on('click', '.js-recipe-search-terms .js-remove', function(e) {
		 e.preventDefault();
		 var term = $(this).parent().find('.js-term').text();
		 var maincat = $('.js-blog-list').data('main-cat');
		 var cat = $('.js-blog-list').data('cat');
		 var tag = $('.js-blog-list').data('tag');
		 fcsearchterms = $.grep(fcsearchterms, function(value) {
			 return value != term;
		 });
		 $(this).parent().remove();
		 blogPageNumber = 1;
		 $.ajax({
			 url : wpAjax.ajaxUrl,
			 data : { 
				 action: 'swl_search_load_more',
				 keyword: fcsearchterms,
				 maincat: maincat,
				 category: cat,
				 tag: tag
			 },
			 type : 'post',
			 dataType: 'json',
			 success : function( posts ) {
				 $('.js-blog-list').html(posts.content);
				 if(posts.max_page > 1) {
					canBeLoaded = true;
				}else {
					canBeLoaded = false;
				}
			 }
		 });
	 });

$('.js-recipe-cat .cat-item').on('click', function(e) {
    e.preventDefault();
    var cat = $(this).data('value');
	$('.js-recipe-tags .cat-item').removeClass('active');
	$('.js-recipe-cat .cat-item').not(this).removeClass('active');
	$(this).addClass('active');
	$('.js-blog-list').data('cat', cat);
	$('.js-blog-list').data('tag', '');
	$('.blog-search-form').submit();
});
	 
$('.js-recipe-tags .cat-item').on('click', function(e) {
    e.preventDefault();
    var tag = $(this).data('value'),
		maincat = $('.js-blog-list').data('main-cat');
	$('.js-recipe-cat .cat-item').removeClass('active');
	$('.js-recipe-tags .cat-item').not(this).removeClass('active');
	$(this).addClass('active');
	$('.js-blog-list').data('tag', tag);
	$('.js-blog-list').data('cat', '');
	$('.blog-search-form').submit();
});

// Reviews CPT Load more
	var pageNumber = 1;
	$('.js-loadmore-reviews').on('click', function(e) {
		console.log('clicked');
		e.preventDefault();
		$(this).hide();
		pageNumber++;
		$.ajax({
			url : wpAjax.ajaxUrl,
			data : { 
				action: 'cpt_reviews_load_more_posts',
				pageNumber: pageNumber
			},
			type : 'post',
			dataType: 'json',
			success : function( posts ) {
				$('.js-review-list').append(posts.content);
				if(posts.page == posts.max_page) {
					$('.js-loadmore-reviews').hide();
				} else {
					$('.js-loadmore-reviews').show();
				}
			}
		});
	});

	$(".becontainer .reviews-container .load-button").remove();
});
