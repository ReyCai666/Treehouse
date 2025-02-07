<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>course_review</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/courseReview.css'); ?>">
</head>

<body>
	<header>
		<h1>Treehouse Review</h1>
	</header>
	<?php echo form_open(base_url().'course_review/review'); ?>
		<button type="submit" class="create-button">Review a course</button>
	<?= form_close() ?>
	<div class="search-container">
		<div class="row">
			<input type="text" id="input-box" placeholder="Search a course code..." autocomplete="<?php echo base_url('course_review/autocomplete'); ?>">
			<button type="submit">Search</button>
		</div>
		<div class="result-box">
		</div>
	</div>
	<div class="review-container">
		<?php 
		$i=0;
		foreach ($courses as $course):
            if ($i < 240) { ?>
			<div class="course-box">
				<h2 class="course-code"><?php echo $course['course_code']; ?></h2>
				<p class="course-title"><?php echo $course['course_name']; ?></p>
			</div>
        <?php $i++; } endforeach; ?>
	</div>
</body>

<script>
    const resultsBox = document.querySelector(".result-box");
    const inputBox = document.getElementById("input-box");

    inputBox.onkeyup = function() {
        let input = inputBox.value;
        // use fetch to populate the result of autocomplete method in the controller
        if (input.length >= 2) {
            resultsBox.style.display = "block";
            fetch("<?php echo base_url('course_review/autocomplete'); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({title:input})
            })
            .then(response => response.json())
            .then(response => {
                let availableKeywords = response;
                let result = [];
                if (input.length) {
                    result = availableKeywords.filter((keyword)=>{
                        return keyword.toLowerCase().includes(input.toLowerCase());
                    });
                    console.log(result);
                }
                display(result);
                if (!result.length) {
                    resultBox.innerHTML = '';
                }
            })
            .catch(error => console.error(error));
        } else {
            resultsBox.style.display = "none";
            resultsBox.innerHTML = '';
        }
    }

    function display(result) {
        const content = result.map((list)=>{
            return "<li onclick=selectInput(this)>" + list + "</li>";
        });
        resultsBox.innerHTML = "<ul>" + content.join('') + "</ul>";
    }

    function selectInput(list) {
        inputBox.value = list.innerHTML;
        resultsBox.style.display = "none";
        resultsBox.innerHTML = '';
    }

    const body = document.querySelector('body');
    body.style.overflowX = 'hidden';

    // maintain scroll position
    window.addEventListener('beforeunload', function() {
        sessionStorage.setItem('scrollPos', window.scrollY);
    });

    window.addEventListener('load', function() {
        if (sessionStorage.getItem('scrollPos') != null) {
            window.scrollTo(0, parseInt(sessionStorage.getItem('scrollPos')));
            sessionStorage.removeItem('scrollPos');
        }
    });
</script>
</html>