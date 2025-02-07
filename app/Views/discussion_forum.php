<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Discussion Forum</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/discussionForum.css'); ?>">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
	<header>
		<h1>Treehouse Forum</h1>
	</header>
	<?php echo form_open(base_url().'discussion_forum/create'); ?>
		<button type="submit" class="create-button">Make a post</button>
	<?= form_close() ?>
	<div class="search-container">
		<div class="row">
			<input type="text" id="input-box" placeholder="Search..." autocomplete="<?php echo base_url('discussion_forum/autocomplete'); ?>">
			<button type="submit">Search</button>
		</div>
		<div class="result-box">
		</div>
	</div>
	<div class="posts-container">
		<?php 
		$i=0;
		foreach ($posts as $post):
			if ($i < 4) { ?>
			<a href="<?php echo base_url('discussion_forum/post_content/'.$post['id']); ?>" class="post-link">
				<div class="post-box">
					<h2 class="post-title"><?php echo $post['title']; ?></h2>
					<p class="post-author">Author: <?php echo $post['author']; ?></p>
					<p class="post-description"><?php echo $post['description']; ?></p>
					<p class="post-views">Views: <?php echo $post['views']; ?></p>
					<p class="post-likes">Likes: <?php echo $post['likes']; ?></p>
				</div>
			</a>
		<?php $i++; } endforeach; ?>
		<div id="loading" class="loading">
			<div class="circle"></div>
			<div class="circle"></div>
			<div class="circle"></div>
		</div>
	</div>
	<div class="no-result"></div>

	<!-- for auto-complete -->
	<script>
		const resultsBox = document.querySelector(".result-box");
		const inputBox = document.getElementById("input-box");

		inputBox.onkeyup = function() {
			let input = inputBox.value;
			// use AJAX fetch to populate the result of autocomplete method in the controller
			if (input.length >= 2) { // auto-complete start when user input two characters.
				resultsBox.style.display = "block";
				fetch("<?php echo base_url('discussion_forum/autocomplete'); ?>", {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					// convert input to JSON-formatted string
					body: JSON.stringify({title:input})
				})
				.then(response => response.json())
				.then(response => {
					let availableKeywords = response;
					let result = [];
					if (input.length) {
						// filter the post titles stored in the backend phpmyadmin database by user's input.
						// define the keyword to map to the result if includes return true.
						result = availableKeywords.filter((keyword)=>{
							return keyword.toLowerCase().includes(input.toLowerCase());
						});
						console.log(result);
					}
					display(result);
					// if no matching result, the result box should disappear.
					if (!result.length) {
						resultBox.innerHTML = '';
					}
				})
				.catch(error => console.error(error));
			} else {
				// by default when user is not typing, result box is hidden.
				resultsBox.style.display = "none";
				resultsBox.innerHTML = '';
			}
		}

		function display(result) {
			const content = result.map((list)=>{
				// when user click on the result shown in the drop down box, input field is replaced with the selected data.
				return "<li onclick=selectInput(this)>" + list + "</li>";
			});
			// configure HTML for the data displayed in the drop down box.
			resultsBox.innerHTML = "<ul>" + content.join('') + "</ul>";
		}

		function selectInput(list) {
			inputBox.value = list.innerHTML;
			// trigger input event to update the search results.
			const event = new Event('input');
    		inputBox.dispatchEvent(event);
			// after selection click, hide the drop down box.
			resultsBox.style.display = "none";
			resultsBox.innerHTML = '';
		}

		// disable horizontal scroll
		const body = document.querySelector('body');
		body.style.overflowX = 'hidden';

		// infinite scroll
		let page = 2;
		let loading = false; // prevent multiple fetch request at once.

		function addPosts(posts) {
			const postsContainer = document.querySelector(".posts-container");
			posts.forEach(post => {
				// automatically generate HTML for the post box.
				let postBox = document.createElement('div');
				postBox.classList.add('post-box');
				postBox.innerHTML = `<h2 class="post-title">${post.title}</h2>
					<p class="post-author">Author: ${post.author}</p>
					<p class="post-description">${post.description}</p>
					<p class="post-views">Views: ${post.views}</p>
					<p class="post-likes">Likes: ${post.likes}</p>`;
				postsContainer.appendChild(postBox);
			});
			let offset = (page - 1) * 4 + posts.length;
			loading = false;
		}

		function handleScroll() {
			const windowHeight = window.innerHeight;
			const fullHeight =document.body.offsetHeight;
			// get the number of pixels user has scrolled.
			const scrolled = window.pageYOffset || document.documentElement.scrollTop;
			// if bottom page reached
			if (!loading && windowHeight + scrolled >= fullHeight) {
				const loading = document.getElementById("loading");
				loading.style.display = "flex";
				// use ajax to send HTTP request to the URL and fetch the result of loadPost in controller.
				fetch("<?php echo base_url('discussion_forum/loadPost'); ?>?page=" + page)
					.then(response => response.json())
					.then(response => {
						if (response.length) {
							page++;
							// delay the loading for loading animation to occur.
							setTimeout(() => {
								// start loading the posts fetched.
								addPosts(response);
								console.log("handleScroll called");
								loading.style.display = "none";
							}, 1500);
						} else {
							window.removeEventListener('scroll', handleScroll);
							loading.style.display = "none";
						}
					})
      				.catch(error => console.error(error));
			} else {
				const loading = document.getElementById("loading");
				loading.style.display = "none";
			}
		}
		window.addEventListener('scroll', handleScroll);	

		// real-time filter 
		$(document).ready(function() {
    		$('#input-box').on('input', function() {
        		const title = $('#input-box').val().toLowerCase();
				if (title.length > 0) {
					$.ajax({
						url: "<?php echo base_url('discussion_forum/search'); ?>",
						method: "POST",
						data: { title: title },
						dataType: "json",
						success: function(response) {
							displayResults(response);
						}
					});
				} else {
					// location.reload(); // when user is not inputting or when the input size is reduced to 0, it should go back to the original view
				}
			});
		});

		function displayResults(posts) {
			const postContainer = $(".posts-container");
			const noResultContainer = $(".no-result");
			// prepare for real-time display on the posts=container area...
			postContainer.empty();
			noResultContainer.empty();
			const title = $('#input-box').val().toLowerCase();
			if (title.length > 0 && posts.length == 0) {
        		noResultContainer.append('<p>No result matching the search criteria.</p>');
				postContainer.append(noResultContainer);
    		} else if (posts.length>0) {
				$.each(posts, function(index, post) {
					postContainer.append(`<a href="<?php echo base_url('discussion_forum/post_content/'); ?>${post.id}" class="post-link">
												<div class="post-box">
													<h2 class="post-title">${post.title}</h2>
													<p class="post-author">Author: ${post.author}</p>
													<p class="post-description">${post.description}</p>
													<p class="post-views">Views: ${post.views}</p>
													<p class="post-likes">Likes: ${post.likes}</p>
												</div>
											</a>
					`);
				});
			}
		}
    </script>
</body>
</html>
