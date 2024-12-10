WordPress resumee theme with Antropic AI job desccription analyser.

Compares and analyses a job description against the resumee provided by the theme and it's custom posttypes and fields.

add to wp-config.php:

define('ANTROPIC_KEY', 'YOUR ANTRPIC API KEY');

Use shortcode: [cv_compatibility_checker]

or in templates: <?php echo do_shortcode('[cv_compatibility_checker]') ?>
