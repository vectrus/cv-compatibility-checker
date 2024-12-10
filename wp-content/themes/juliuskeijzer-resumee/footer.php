<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3>Contact</h3>
                <p>contact@vectrus.nl<br>
                   </p>
            </div>

            <div class="col-md-4">
                <h3>Connect</h3>
                <div class="social-links">

                    <img class="social-link-icon" src="https://content.linkedin.com/content/dam/me/brand/en-us/brand-home/logos/In-Blue-Logo.png.original.png" alt="linkedin icon">
                    <a href="https://www.linkedin.com/in/juliuskeijzer/" target="_blank" class="social-link">
                        LinkedIn
                    </a>
                </div>
            </div>

            <div class="col-md-4">
               <!-- --><?php
/*                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'footer-nav'
                ));
                */?>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p>&copy; <?php echo date('Y'); ?> Julius Keijzer. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>


<body onload="generatePDF()">
</body>
</html>