<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <title><?php echo get_bloginfo(); ?></title>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QBBMCHC9F2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'G-QBBMCHC9F2');
    </script>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="site-title">Julius Keijzer</h1>
                <p class="lead">Full Stack Developer & Technical Expert</p>
                <p class="print-only">
                    Irisplein 27<br/>
                    2565 TC Den Haag<br>
                    <br/>
                    12 april 1973 <br/>


                    <strong>Telefoon</strong><br>
                    06-41389927


                </p>
            </div>
            <div class="col-md-6 text-end no-print">
                <!--a href="https://juliuskeijzer.nl/wp-content/uploads/2024/12/julius-keijzer-–-c.v.pdf" class="btn btn-primary">Download C.V.</a>-->
                <a class="btn btn-primary" href="javascript:window.print()">Print/Opslaan als PDF</a>
            </div>
        </div>
    </div>
    <!--<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <?php
/*            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'navbar-nav me-auto mb-2 mb-lg-0'
            ));
            */?>
        </div>
    </nav>-->
</header>