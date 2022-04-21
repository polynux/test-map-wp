var $j = jQuery.noConflict();
(function($j) {
	$j(document).ready(function(){
		
		//Go To top
		$j('body').append('<a href="#top" class="top_link" title="Revenir en haut de page"><i class="fa fa-chevron-up" aria-hidden="true"></i></span></a>');   
		$j(window).scroll(function(){  
			posScroll = $j(document).scrollTop();  
			posScroll2 = $j(window).width();
			if(posScroll >=550)   
				if(posScroll2 <=1100)   
					$j('.top_link').fadeOut(600);  
				else  
					$j('.top_link').fadeIn(600); 
			else  
				$j('.top_link').fadeOut(600); 		
		});
		$j("a[href='#top']").click(function() {
			 $j("html, body").animate({ scrollTop: 0 }, "slow");
			 	return false;
		});  

	    $j("a[href*='#']:not([href='#'])").click(function() {
	        if (
	            location.hostname == this.hostname
	            && this.pathname.replace(/^\//,"") == location.pathname.replace(/^\//,"")
	        ) {
	            var anchor = $j(this.hash);
	            anchor = anchor.length ? anchor : $j("[name=" + this.hash.slice(1) +"]");
	            if ( anchor.length ) {
	            	$j("html, body").animate( { scrollTop: anchor.offset().top }, 500);
	            }
	        }
	    });
	});  
})(jQuery);