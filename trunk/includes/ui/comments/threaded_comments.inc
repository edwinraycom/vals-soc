<?php
module_load_include('php', 'vals_soc', 'includes/classes/ThreadedComments');
module_load_include('php', 'vals_soc', 'includes/classes/ThreadUIBuilder');


function initComments($entity_id, $entity_type){
	$threads = ThreadedComments::getInstance()->getThreadsForEntity($entity_id, $entity_type);
	$threaded_comments = new ThreadUIBuilder($entity_id, $entity_type);
	$threaded_comments -> init($threads);
	$rendered_threads = $threaded_comments->print_comments();
	$post_forms =  getJsHandler();
	return $rendered_threads . $post_forms;
}

 function getJsHandler(){
	$output = '
		<script type="text/javascript">
		$jq(function(){	
			$jq(document).off("click", ".reply-comment");
			$jq(document).on("click", ".reply-comment", function(e){			
				e.preventDefault();
				$jq(this).parent().parent().next(".reply-comment-form").toggle("slow");
			});

			$jq(".comment-toggle").on("click", function(e){
				var link = $jq(this);
				var hideText = "'.t("hide").'";
				var showText = "'.t("show").'";
				e.preventDefault();
				$jq(".comments-parent-container").toggle("slow", function() {
					if (link.attr("value") == showText) {
						link.attr("value", hideText);
					} else {
						link.attr("value",showText);
					}
				});
			});

		});
		</script>
	';
	return $output;
}