<?php

require_once "includes/conexion.php";

$propertyStmt = $conn->prepare("
    SELECT
        p.id,
        p.name,
        p.canton,
        p.province,
        p.price,
        p.size,
        p.size_unit,
        p.property_type,
        p.bedrooms,
        p.bathrooms,
        p.status,

        u.name AS user_name,
        u.photo AS user_photo,

        pi.image

    FROM properties p

    INNER JOIN users u
        ON u.id = p.user_id

    LEFT JOIN property_images pi
        ON pi.property_id = p.id
        AND pi.id = (
            SELECT MIN(pi2.id)
            FROM property_images pi2
            WHERE pi2.property_id = p.id
        )

    WHERE p.status = 'active'

    ORDER BY p.created_at DESC

    LIMIT 3
");

$propertyStmt->execute();

$propertiesResult = $propertyStmt->get_result();
$properties = $propertiesResult->fetch_all(MYSQLI_ASSOC);

$propertyStmt->close();


$recentStmt = $conn->prepare("
    SELECT
        p.id,
        p.canton,
        p.province,
        p.created_at,
        pi.image
    FROM properties p

    LEFT JOIN property_images pi
        ON pi.property_id = p.id
        AND pi.id = (
            SELECT MIN(pi2.id)
            FROM property_images pi2
            WHERE pi2.property_id = p.id
        )

    WHERE p.status = 'active'

    ORDER BY p.created_at DESC

    LIMIT 2
");

$recentStmt->execute();

$recentResult = $recentStmt->get_result();
$recentProperties = $recentResult->fetch_all(MYSQLI_ASSOC);

$recentStmt->close();


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Locally</title>

</head>

<body>


    <div class="locally-loader" id="locallyLoader">
        <div class="loader-bg"></div>

        <div class="topography-lines" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nature-particles" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
        </div>

        <div class="leaf leaf-one" aria-hidden="true"></div>
        <div class="leaf leaf-two" aria-hidden="true"></div>
        <div class="leaf leaf-three" aria-hidden="true"></div>

        <div class="bird bird-one" aria-hidden="true"></div>
        <div class="bird bird-two" aria-hidden="true"></div>

        <div class="sun-pulse" aria-hidden="true"></div>

        <div class="loader-content">
            <div class="logo-glow"></div>

            <img src="img/logoLoader.svg" alt="Locally Costa Rica" class="loader-logo">

            <p class="loader-text">connecting nature, places and people</p>

            <div class="loader-line" aria-hidden="true">
                <span></span>
            </div>
        </div>

        <div class="leaf-reveal"></div>
    </div>


    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">

                <ul class="navbar-nav d-none d-lg-flex me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                </ul>

                <a href="index.php">
                    <img src="img/logoLocally.png" class="img-fluid" alt="Locally logo">
                </a>

                <ul class="navbar-nav d-none d-lg-flex ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="properties.php">Properties</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#projects">Portfolio</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#blog">Blog</a>
                    </li>

                </ul>
                <button class="btnMobileMenu d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Open menu">

                    <i class="ri-menu-3-line" aria-hidden="true"></i>

                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                    <div class="offcanvas-header">
                        <img src="img/logoLocally.png" class="img-fluid logoMobile" alt="Locally logo">
                        <button type="button" class="customCloseBtn" data-bs-dismiss="offcanvas"
                            aria-label="Close menu">

                            <i class="ri-close-line"></i>

                        </button>
                    </div>

                    <div class="offcanvas-body">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#services">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#blog">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#projects">Portfolio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="properties.php">Properties</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </nav>
    </header>


    <section class="carousel" role="region" aria-label="Hero image carousel Locally">
        <div class="list">

            <article class="item">
                <img src="img/banner1.webp" alt="Locally creative concept">
                <div class="content">
                    <h1 class="title">Locally</h1>
                    <p class="des">
                        With a focus on building trust, safety, and success,
                        we are your go-to partner in the real estate
                        industry. Contact us today to experience the
                        difference.
                    </p>
                    <a class="buttons" href="#properties">See more</a>
                </div>
            </article>

            <article class="item">
                <img src="img/banner2.webp" alt="Locally visual communication">
                <div class="content">
                    <h1 class="title">Locally</h1>
                    <p class="des">
                        Our approach combines strategy, creativity, and human connection to create experiences that
                        inspire, communicate, and ultimately add value.
                    </p>
                    <a class="buttons" href="#properties">See more</a>
                </div>
            </article>

            <article class="item">
                <img src="img/banner3.webp" alt="Locally brand experience">
                <div class="content">
                    <h1 class="title">Locally</h1>
                    <p class="des">
                        Every project we develop is designed to reflect a brand’s identity, purpose, and sense of place,
                        enabling brands to communicate in a more natural and memorable way.
                    </p>
                    <a class="buttons" href="#properties">See more</a>
                </div>
            </article>

            <article class="item">
                <img src="img/banner4.webp" alt="Locally experience workshop">
                <div class="content">
                    <h1 class="title">Locally</h1>
                    <p class="des">
                        Because when people genuinely connect with a brand, communication becomes experience and
                        experience becomes lasting value.
                    </p>
                    <a class="buttons" href="#properties">See more</a>
                </div>
            </article>

        </div>

        <div class="thumbnail" aria-hidden="true">
            <div class="item">
                <img src="img/banner1.webp" alt="">
            </div>

            <div class="item">
                <img src="img/banner2.webp" alt="">
            </div>

            <div class="item">
                <img src="img/banner3.webp" alt="">
            </div>

            <div class="item">
                <img src="img/banner4.webp" alt="">
            </div>
        </div>

        <div class="arrows">
            <button id="prev" type="button" aria-label="Previous slide">
                <i class="ri-arrow-left-s-line" aria-hidden="true"></i>
            </button>

            <button id="next" type="button" aria-label="Next slide">
                <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
            </button>
        </div>
    </section>


    <section id="about" class="s1 container py-5">
        <div class="row align-items-center">

            <!-- imagen -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="img/photoAboutUs.webp" alt="Real estate properties" class="img-fluid">
            </div>

            <!-- contenido -->
            <div class="col-lg-6">

                <!-- titulo -->
                <div class="d-flex align-items-center mb-4">
                    <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" aria-hidden="true">
                    <h2 class="ms-2 mb-0">About us</h2>
                </div>

                <!-- texto -->
                <p>
                    For over a decade, our team has been committed to delivering reliable, safe, and effective real
                    estate services for our clients.
                </p>
                <p>
                    We believe in creating not just successful business, but happier lives.
                </p>

                <div class="row my-4">
                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li><i class="ri-lightbulb-flash-line me-2"></i>Execution</li>
                            <li><i class="ri-hand-heart-line me-2"></i>Commitment</li>
                        </ul>
                    </div>

                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li><i class="ri-chat-3-line me-2"></i>Communication</li>
                            <li><i class="ri-focus-3-line me-2"></i>Dedication</li>
                        </ul>
                    </div>
                </div>

                <!-- stats -->
                <div class="row text-center g-3 mb-5">

                    <div class="col-6">
                        <div class="boxOne">
                            <span>+ 13 Years of experience</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="boxTwo">
                            <span>200 Happy clients</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="boxTwo">
                            <span>Over 400 Units sold</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="boxOne">
                            <span>+ 20 Projects as a strategic partner</span>
                        </div>
                    </div>

                </div>

                <!-- boton -->
                <a class="btnHome" href="aboutMe">More information</a>

            </div>
        </div>
    </section>


    <section class="container logoSlider py-3 my-3" aria-label="Our partners">
        <div class="logoTrack">

            <!-- grupo original -->
            <div class="logoGroup">
                <img src="img/armabloque.webp" alt="Armabloque construcción">
                <img src="img/alturas.webp" alt="Casa Alturas">
                <img src="img/numandiLogo.webp" alt="Numandi">
                <img class="cs" src="img/theView.webp" alt="The View Private Village Guanacaste">
                <img class="cs" src="img/luahLogo.webp" alt="Luah">
                <img class="hc" src="img/logoHacienda.webp" alt="Hacienda Río Corobicí">
                <img class="centralPark" src="img/centralPark.webp" alt="Central Park">
                <img class="baulas" src="img/logoBaulas.webp" alt="Baulas">
                <img class="hm2" src="img/harmony.webp" alt="Harmony Homes">
                <img class="cs" src="img/cs.webp" alt="Casa Sole">
                <img src="img/yugen.webp" alt="Yugen">
                <img class="centralPark" src="img/37dent.webp" alt="37 Dent">
                <img class="baulas" src="img/vistasBejuco.png" alt="Vistas del Bejuco">
                <img class="hm" src="img/balconnia.webp" alt="Balconnia">
                <img src="img/terralta.webp" alt="Terralta">
                <img src="img/espavel.webp" alt="Espavel">
                <img class="hm" src="img/brisas.webp" alt="Brisas">
                <img class="hm" src="img/rioEscondido.webp" alt="Río Escondido">
                <img class="cs" src="img/pickIt.webp" alt="Pick It">
            </div>

            <div class="logoGroup" aria-hidden="true">
                <img src="img/armabloque.webp" alt="">
                <img src="img/alturas.webp" alt="">
                <img src="img/numandiLogo.webp" alt="">
                <img class="cs" src="img/theView.webp" alt="">
                <img class="cs" src="img/luahLogo.webp" alt="">
                <img class="hc" src="img/logoHacienda.webp" alt="">
                <img class="centralPark" src="img/centralPark.webp" alt="">
                <img class="baulas" src="img/logoBaulas.webp" alt="">
                <img class="hm2" src="img/harmony.webp" alt="">
                <img class="cs" src="img/cs.webp" alt="">
                <img src="img/yugen.webp" alt="">
                <img class="centralPark" src="img/37dent.webp" alt="">
                <img class="baulas" src="img/vistasBejuco.png" alt="">
                <img class="hm" src="img/balconnia.webp" alt="">
                <img src="img/terralta.webp" alt="">
                <img src="img/espavel.webp" alt="">
                <img class="hm" src="img/brisas.webp" alt="">
                <img class="hm" src="img/rioEscondido.webp" alt="">
                <img class="cs" src="img/pickIt.webp" alt="">
            </div>

        </div>
    </section>

    <section id="aboutMe" class="s2 container py-5">
        <div class="row align-items-center">

            <!-- contenido -->
            <div class="col-lg-6">


                <div class="d-flex align-items-center mb-4">
                    <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" aria-hidden="true">
                    <h2 class="ms-2 mb-0">About me</h2>
                </div>




                <p class="mb-5">
                    With over 13 years in real estate, I’ve built my career around understanding people, anticipating possibilities, and creating meaningful connections between projects and those who bring them to life.
                    Locally is the expression of that experience: connecting projects, channels, and buyers through trust, insight, and genuine relationships.
                </p>

                <!-- video 
                <div class="bgVideo d-flex justify-content-center align-items-center mb-5">
                    <button class="playBtn d-flex align-items-center justify-content-center" data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop" aria-label="Play video">
                        <i class="ri-play-line" aria-hidden="true"></i>
                    </button>
                </div>

                -->

                <!-- boton -->
                <a class="btnHome" href="#projects">See portfolio</a>

            </div>
            <!-- visual -->
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="aboutVisual">

                    <div class="blob" aria-hidden="true"></div>

                    <div class="aboutName">
                        <p>María José Rojas Jiménez</p>
                    </div>

                </div>
            </div>

        </div>
    </section>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-21x9">
                        <iframe width="560" height="315"
                            src="https://www.youtube.com/embed/pJxeyK7it0o?si=AlWsKEHzRGLfvUFK"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <section id="services" class="s3 container py-5">
        <div class="row">

            <!-- contenido sticky -->
            <div class="col-lg-4">
                <div class="s3-left">

                    <div class="d-flex align-items-center mb-4">
                        <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" aria-hidden="true">
                        <h2 class="ms-2 mb-0">Services</h2>
                    </div>

                    <p>
                        Unlock the potential of understanding and creativity with our services.
                        Our team listens, studies, creates, and embraces your vision to make a lasting impact.
                    </p>

                    <p>Thanks to our knowledge of different markets, we bring an international perspective to representing the interests of both local and global clients.</p>

                </div>
            </div>

            <!-- cards -->
            <!-- cards -->
            <div class="col-lg-8">
                <div class="stack-wrapper">

                    <!-- CARD 01 -->
                    <article class="stack-card">

                        <div class="stack-card-top">
                            <span class="service-number">01</span>
                            <span class="service-label">SERVICE</span>
                        </div>

                        <div class="row align-items-center">

                            <div class="col-md-7">

                                <div class="service-accent"></div>

                                <h3>Real Estate</h3>

                                <p>
                                    From negotiating offers to guiding you through paperwork,
                                    we work on creating smooth and hassle-free transactions.
                                </p>

                            </div>

                            <div class="col-md-5">

                                <div class="card-img">
                                    <img
                                        class="img-fluid"
                                        src="img/serviceRealEstate.webp"
                                        alt="Real estate property consultation">
                                </div>

                            </div>

                        </div>

                    </article>


                    <!-- CARD 02 -->
                    <article class="stack-card">

                        <div class="stack-card-top">
                            <span class="service-number">02</span>
                            <span class="service-label">SERVICE</span>
                        </div>

                        <div class="row align-items-center">

                            <div class="col-md-7">

                                <div class="service-accent"></div>

                                <h3>Professional Brokers</h3>

                                <p>
                                    We work diligently to match your criteria, negotiate on your behalf,
                                    and prepare offers that meet your needs.
                                </p>

                            </div>

                            <div class="col-md-5">

                                <div class="card-img">
                                    <img
                                        class="img-fluid"
                                        src="img/serviceBrokers.webp"
                                        alt="Professional broker helping a client">
                                </div>

                            </div>

                        </div>

                    </article>


                    <!-- CARD 03 -->
                    <article class="stack-card">

                        <div class="stack-card-top">
                            <span class="service-number">03</span>
                            <span class="service-label">SERVICE</span>
                        </div>

                        <div class="row align-items-center">

                            <div class="col-md-7">

                                <div class="service-accent"></div>

                                <h3>Commercial Development Solutions</h3>

                                <p>
                                    We specialize in creating strong, well-developed marketing strategies
                                    and sales processes. Experience, seamless integration, and unparalleled
                                    efficiency are always on the table.
                                </p>

                            </div>

                            <div class="col-md-5">

                                <div class="card-img">
                                    <img
                                        class="img-fluid"
                                        src="img/photoBrokers.webp"
                                        alt="Commercial development strategy meeting">
                                </div>

                            </div>

                        </div>

                    </article>

                </div>
            </div>



        </div>
    </section>


    <section id="projects" class="s4 container py-5">

        <div class="d-flex mb-4 align-items-center justify-content-center">

            <img
                src="img/iconT.webp"
                alt=""
                class="iconTitles img-fluid"
                aria-hidden="true">

            <h2 class="ms-2 mb-0">
                Portfolio
            </h2>

        </div>


        <div class="tabs-scroll-wrapper">

            <ul
                class="nav nav-tabs projects-tabs"
                role="tablist">


                <!-- TOWERS -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link active show"
                        id="towers-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#towers"
                        type="button"
                        role="tab"
                        aria-controls="towers"
                        aria-selected="true">

                        <i
                            class="ri-building-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Towers
                        </span>

                    </button>

                </li>


                <!-- CONDOMINIUMS -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link"
                        id="condominiums-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#condominiums"
                        type="button"
                        role="tab"
                        aria-controls="condominiums"
                        aria-selected="false">

                        <i
                            class="ri-community-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Condominiums
                        </span>

                    </button>

                </li>


                <!-- PERSONAL -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link"
                        id="personal-project-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#personalProject"
                        type="button"
                        role="tab"
                        aria-controls="personalProject"
                        aria-selected="false">

                        <i
                            class="ri-home-smile-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Personal
                        </span>

                    </button>

                </li>


                <!-- CURRENT PROJECTS -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link"
                        id="current-projects-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#currentProjects"
                        type="button"
                        role="tab"
                        aria-controls="currentProjects"
                        aria-selected="false">

                        <i
                            class="ri-building-3-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Current Projects
                        </span>

                    </button>

                </li>


                <!-- CONSTRUCTION -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link"
                        id="construction-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#construction"
                        type="button"
                        role="tab"
                        aria-controls="construction"
                        aria-selected="false">

                        <i
                            class="ri-home-gear-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Construction
                        </span>

                    </button>

                </li>


                <!-- COMMERCIAL -->

                <li class="nav-item" role="presentation">

                    <button
                        class="nav-link"
                        id="commercial-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#commercial"
                        type="button"
                        role="tab"
                        aria-controls="commercial"
                        aria-selected="false">

                        <i
                            class="ri-building-4-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Commercial
                        </span>

                    </button>

                </li>

            </ul>

        </div>


        <div
            class="tab-content mt-5"
            id="allTabs">


            <!-- =====================================================
             TOWERS
        ====================================================== -->

            <div
                class="tab-pane fade active show"
                id="towers"
                role="tabpanel"
                aria-labelledby="towers-tab">

                <div
                    class="accordion"
                    id="accordeonTowers">

                    <div class="row">

                        <div class="col-lg-6 mb-3">


                            <!-- CENTRAL PARK -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#centralPark"
                                        aria-expanded="false"
                                        aria-controls="centralPark">

                                        Central Park

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="centralPark"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            The Central Park residential condominium consists of three modern buildings, with units spread across 13 stories. Located in one of the most desirable areas of Escazú, San José, it combines spacious layouts, contemporary architecture, and a setting surrounded by nature.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- NEST FRESES -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#nestFreses"
                                        aria-expanded="false"
                                        aria-controls="nestFreses">

                                        Nest Freses

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="nestFreses"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            It is a high-rise residential project featuring 26 one- and two-bedroom units. A landmark in Curridabat, it combines contemporary architecture, a strategic location, and amenities designed to promote well-being and urban living.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- NOTA ESCALANTE -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#notaEscalante"
                                        aria-expanded="false"
                                        aria-controls="notaEscalante">

                                        Nota Escalante

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="notaEscalante"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            Located in the heart of Barrio Escalante, San José, offering a vibrant urban lifestyle. This residential development stands out for its modern design and strategic location, close to a wide range of dining, cultural, and entertainment options.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- BAKIA FLATS -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#bakiaFlats"
                                        aria-expanded="false"
                                        aria-controls="bakiaFlats">

                                        Bakia Flats

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="bakiaFlats"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            A residential project developed by BEE, consisting of 39 units with 1, 2, and 3 bedrooms. It features contemporary architecture characterized by open spaces, natural lighting and ventilation, innovative materials, and high-quality finishes.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- 37 DENT -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#dent"
                                        aria-expanded="false"
                                        aria-controls="dent">

                                        37 Dent

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="dent"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            A development located on Calle 37, right at the intersection between the central metropolitan area and the Montes de Oca canton, developed by BEE. Its extraordinary location offers a convenient and cosmopolitan urban lifestyle.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-lg-6 mb-3">


                            <!-- ARBOREA -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#arborea"
                                        aria-expanded="false"
                                        aria-controls="arborea">

                                        Arborea

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="arborea"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            With 66 units, this is a boutique development in a strategic location surrounded by nature; it also offers a wide variety of services within a few meters.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- VISTAS DE BEJUCO -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#vistasBejuco"
                                        aria-expanded="false"
                                        aria-controls="vistasBejuco">

                                        Vistas de Bejuco

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="vistasBejuco"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            Two towers and five levels, each with 30 apartments, in Playa Bejuco, located in Parrita, Puntarenas, 1 hour and 20 minutes from San José. With an excellent location just 2 minutes from the Pacific Ocean in Playa Bejuco.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- BALCONNIA -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#balconnia"
                                        aria-expanded="false"
                                        aria-controls="balconnia">

                                        Balconnia

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="balconnia"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            Located in Rohrmoser, San José, and featuring 39 apartments, this residential tower is designed to complement a modern lifestyle, standing out for its use of balconies and functional spaces.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- LUAH -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#luah"
                                        aria-expanded="false"
                                        aria-controls="luah">

                                        Luah

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="luah"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonTowers">

                                    <div class="accordion-body">

                                        <p>
                                            A boutique residential project in Jaboncillos, Escazú, where contemporary design, natural light, and nature come together to create a serene, elegant, and mindful lifestyle.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
             CONDOMINIUMS
        ====================================================== -->

            <div
                class="tab-pane fade"
                id="condominiums"
                role="tabpanel"
                aria-labelledby="condominiums-tab">

                <div
                    class="accordion"
                    id="accordeonCondo">

                    <div class="row">

                        <div class="col-lg-6 mb-3">


                            <!-- TERRALTA -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#terralta"
                                        aria-expanded="false"
                                        aria-controls="terralta">

                                        Terralta

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="terralta"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            With a prime location in San Antonio de Escazú and luxury finishes, TERRALTA FAMILY HOMES is for those who want to give their families the best, who are looking for a safe environment, and who value privacy and connection with nature.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- RÍO ESCONDIDO -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#rioEscondido"
                                        aria-expanded="false"
                                        aria-controls="rioEscondido">

                                        Río Escondido

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="rioEscondido"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            Located in Piedades, Santa Ana, the project features 104 units and offers a comprehensive amenity package that includes a pool, a gym, a clubhouse, and recreational areas.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- SAN RAFAEL -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#sanRafael"
                                        aria-expanded="false"
                                        aria-controls="sanRafael">

                                        San Rafael

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="sanRafael"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            A condominium complex with 342 units, located in San Rafael de Alajuela, designed for a peaceful, family-oriented lifestyle. The project features shared green spaces, controlled security, and convenient access to services and major roads.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- BRISAS DE RÍO -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#brisasDelRio"
                                        aria-expanded="false"
                                        aria-controls="brisasDelRio">

                                        Brisas de Río

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="brisasDelRio"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            It is a 51-home residential development in San Felipe de Alajuelita that combines functional homes, family-friendly amenities, green spaces, and 24/7 security in a location with easy access to San José.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-lg-6 mb-3">


                            <!-- ESPAVEL / HACIENDA MOVED TO CURRENT PROJECTS -->



                            <!-- BAULAS -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#baulas"
                                        aria-expanded="false"
                                        aria-controls="baulas">

                                        Baulas Sunset Community

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="baulas"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            It is located in Huacas, Guanacaste, Costa Rica. Strategically close to the main tourism hubs of the country and just minutes away from several breathtaking beaches, incredible surf sports, restaurants, local stores, and a broad variety of social activities.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- HARMONY -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#harmony"
                                        aria-expanded="false"
                                        aria-controls="harmony">

                                        Harmony Homes

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="harmony"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordeonCondo">

                                    <div class="accordion-body">

                                        <p>
                                            A community on a human scale, with 26 wooden homes—a concept that invites you to live differently located in Curubandé, Liberia, Guanacaste.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- NOTA: CASA SOLE AND CASA ALTURAS MOVED -->

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
             PERSONAL
        ====================================================== -->

            <div
                class="tab-pane fade"
                id="personalProject"
                role="tabpanel"
                aria-labelledby="personal-project-tab">

                <div
                    class="accordion"
                    id="accordionPersonalProject">

                    <div class="accordion-item mb-4">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#casaTicoSueno"
                                aria-expanded="false"
                                aria-controls="casaTicoSueno">

                                Casa Tico Sueño

                                <i
                                    class="ri-add-line icon-open ms-auto"
                                    aria-hidden="true">
                                </i>

                                <i
                                    class="ri-subtract-line icon-close ms-auto"
                                    aria-hidden="true">
                                </i>

                            </button>

                        </h2>


                        <div
                            id="casaTicoSueno"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionPersonalProject">

                            <div class="accordion-body">

                                <p>
                                    A custom-built home for a peaceful life, just 5 minutes from Avellanas Beach. We made the process enjoyable for the buyers, handling everything from purchasing the land and paperwork to construction and post-sale support.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
             CURRENT PROJECTS
        ====================================================== -->

            <div
                class="tab-pane fade"
                id="currentProjects"
                role="tabpanel"
                aria-labelledby="current-projects-tab">

                <div
                    class="accordion"
                    id="accordionCurrentProjects">

                    <div class="row">

                        <div class="col-lg-6 mb-3">


                            <!-- ARMABLOQUE -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentArmabloque"
                                        aria-expanded="false"
                                        aria-controls="currentArmabloque">

                                        Armabloque

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentArmabloque"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            With more than 14 years of experience optimizing construction in Costa Rica, he is our partner in developing projects that guarantee: luxury finishes, savings in time and money, projects of various scales, single-family homes, and residential developments.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- NUMANDI -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentNumandi"
                                        aria-expanded="false"
                                        aria-controls="currentNumandi">

                                        Numandi

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentNumandi"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            It is a private residential community of 80 single-story homes in Playa Avellanas, Costa Rica. Designed by Gensler and shaped by the natural terrain, each home respects the land, promotes well-being, and nurtures a true sense of belonging.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- CASA SOLE -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentCasaSole"
                                        aria-expanded="false"
                                        aria-controls="currentCasaSole">

                                        Casa Sole

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentCasaSole"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            At Sardinal de Carrillo, Guanacaste, Casa Sole, a beachfront condominium featuring 2- and 3-bedroom homes designed to offer a once-in-a-lifetime investment opportunity just 10 minutes from the ocean.

                                            It offers true proximity to the beach, its sunsets, and the relaxed coastal lifestyle, while ensuring the privacy and security of each homeowner.

                                            All this is part of a development featuring superior quality and design that perfectly complement our beautiful common areas.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- CASA ALTURAS -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentCasaAlturas"
                                        aria-expanded="false"
                                        aria-controls="currentCasaAlturas">

                                        Casa Alturas

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentCasaAlturas"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            San Pablo de Heredia, Casa Alturas is a residential development with 83 homes, featuring three different floor plans.

                                            Spacious green areas, magnificent facilities, a social club, an indoor pool, and a shopping area.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-lg-6 mb-3">


                            <!-- HACIENDA -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentHacienda"
                                        aria-expanded="false"
                                        aria-controls="currentHacienda">

                                        Hacienda

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentHacienda"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            Located in Cañas,
                                            Guanacaste, it features 66 lots, 128 hectares, and homes managed under a homeowners’ association. Hacienda Río Corobicí is a place where you can design a life that makes the dream of living under the warm rays of the sun a reality—feeling the gentle touch of the wind, listening to the soft rustling of the leaves, and enjoying the cool embrace of fresh water.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- THE VIEW -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentTheView"
                                        aria-expanded="false"
                                        aria-controls="currentTheView">

                                        The View

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentTheView"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>
                                            Plots with endless views
                                            Discover carefully selected properties offering breathtaking landscapes, fresh air, and an exceptional living experience.
                                            39 properties in Phase I, starting at 5,000 m²
                                            Belén, Carrillo, Guanacaste
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <!-- YUGEN -->

                            <div class="accordion-item mb-4">

                                <h2 class="accordion-header">

                                    <button
                                        class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#currentYugen"
                                        aria-expanded="false"
                                        aria-controls="currentYugen">

                                        Yugen

                                        <i
                                            class="ri-add-line icon-open ms-auto"
                                            aria-hidden="true">
                                        </i>

                                        <i
                                            class="ri-subtract-line icon-close ms-auto"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </h2>


                                <div
                                    id="currentYugen"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionCurrentProjects">

                                    <div class="accordion-body">

                                        <p>

                                            Located in Tilarán, Guanacaste, this development features 36 carefully designed plots surrounded by breathtaking landscapes. It is a community that embodies well-being, connection, and peace, offering an unparalleled setting for those seeking a profound and meaningful lifestyle.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
             CONSTRUCTION
        ====================================================== -->

            <div
                class="tab-pane fade"
                id="construction"
                role="tabpanel"
                aria-labelledby="construction-tab">

                <div
                    class="accordion"
                    id="accordionConstruction">


                    <!-- NATIVOS -->

                    <div class="accordion-item mb-4">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#nativos"
                                aria-expanded="false"
                                aria-controls="nativos">

                                Nativos

                                <i
                                    class="ri-add-line icon-open ms-auto"
                                    aria-hidden="true">
                                </i>

                                <i
                                    class="ri-subtract-line icon-close ms-auto"
                                    aria-hidden="true">
                                </i>

                            </button>

                        </h2>


                        <div
                            id="nativos"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionConstruction">

                            <div class="accordion-body">

                                <p>
                                    Known for its innovative ideas and concepts spanning various styles, the firm carries out both commercial and residential projects. Customer service, professionalism, and personalized oversight. We take projects from concept to completion.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- TRES LÍNEAS -->

                    <div class="accordion-item mb-4">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#tresLineas"
                                aria-expanded="false"
                                aria-controls="tresLineas">

                                Tres Líneas Arquitectura

                                <i
                                    class="ri-add-line icon-open ms-auto"
                                    aria-hidden="true">
                                </i>

                                <i
                                    class="ri-subtract-line icon-close ms-auto"
                                    aria-hidden="true">
                                </i>

                            </button>

                        </h2>


                        <div
                            id="tresLineas"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionConstruction">

                            <div class="accordion-body">

                                <p>
                                    Specialists in the field of architectural design and construction, with a particular focus on customizing spaces to suit the client's tastes. We have extensive experience in consulting and implementing high-end finishes and services.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- =====================================================
             COMMERCIAL
        ====================================================== -->

            <div
                class="tab-pane fade"
                id="commercial"
                role="tabpanel"
                aria-labelledby="commercial-tab">

                <div
                    class="accordion"
                    id="accordeonCommercial">

                    <div class="accordion-item mb-4">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#pickItPlaza"
                                aria-expanded="false"
                                aria-controls="pickItPlaza">

                                Pick it Plaza

                                <i
                                    class="ri-add-line icon-open ms-auto"
                                    aria-hidden="true">
                                </i>

                                <i
                                    class="ri-subtract-line icon-close ms-auto"
                                    aria-hidden="true">
                                </i>

                            </button>

                        </h2>


                        <div
                            id="pickItPlaza"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordeonCommercial">

                            <div class="accordion-body">

                                <p>
                                    Located in Santa Ana, we participated in the comprehensive development of this shopping center, supporting the process from the definition of the concept and commercial strategy through the structuring of the retail mix and the onboarding of key tenants.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>







    <section class="s5 container py-5">
        <div class="d-flex align-items-center mb-5">
            <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" aria-hidden="true">
            <h2 class="ms-2 mb-0">Testimonials</h2>
        </div>

        <div class="testimonial-wrapper">
            <div class="row align-items-center">

                <!-- imagenes -->
                <div class="col-lg-6">
                    <div class="swiper testimonialImages">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <img src="img/testi1.png" class="img-fluid" alt="Client testimonial">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- contenido -->
                <div class="col-lg-6">
                    <div class="swiper testimonialContent">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <article class="testimonial-box">

                                    <i
                                        class="ri-double-quotes-l quote-icon"
                                        aria-hidden="true">
                                    </i>

                                    <div
                                        class="stars mb-3"
                                        aria-label="5 out of 5 stars">

                                        <i class="ri-star-fill" aria-hidden="true"></i>
                                        <i class="ri-star-fill" aria-hidden="true"></i>
                                        <i class="ri-star-fill" aria-hidden="true"></i>
                                        <i class="ri-star-fill" aria-hidden="true"></i>
                                        <i class="ri-star-fill" aria-hidden="true"></i>

                                    </div>

                                    <blockquote class="testimonial-text">
                                        “I wanted to send you this testimonial for your website. Please feel free to use it wherever you feel it would be helpful.”
                                    </blockquote>

                                    <button
                                        type="button"
                                        class="btn-testimonial"
                                        data-bs-toggle="modal"
                                        data-bs-target="#testimonialModal">

                                        Read full testimonial
                                        <i class="ri-arrow-right-line" aria-hidden="true"></i>

                                    </button>

                                    <footer class="testimonial-footer">

                                        <h3 class="mb-0">
                                            Mark Mullinax
                                        </h3>

                                        <p class="role">
                                            Retired Registered Nurse
                                        </p>

                                    </footer>

                                </article>
                            </div>


                        </div>

                        <div class="swiper-pagination"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- TESTIMONIAL MODAL -->
    <div
        class="modal fade testimonial-modal"
        id="testimonialModal"
        tabindex="-1"
        aria-labelledby="testimonialModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header">

                    <div class="modal-title-wrapper">

                        <i
                            class="ri-double-quotes-l modal-quote"
                            aria-hidden="true">
                        </i>

                        <div>

                            <h2
                                class="modal-title"
                                id="testimonialModalLabel">

                                Mark Mullinax

                            </h2>

                            <p class="modal-role">
                                Retired Registered Nurse
                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="modal-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">

                        <i
                            class="ri-close-line"
                            aria-hidden="true">
                        </i>

                    </button>

                </div>


                <!-- BODY -->
                <div class="modal-body">

                    <div
                        class="stars"
                        aria-label="5 out of 5 stars">

                        <i class="ri-star-fill" aria-hidden="true"></i>
                        <i class="ri-star-fill" aria-hidden="true"></i>
                        <i class="ri-star-fill" aria-hidden="true"></i>
                        <i class="ri-star-fill" aria-hidden="true"></i>
                        <i class="ri-star-fill" aria-hidden="true"></i>

                    </div>


                    <blockquote class="full-testimonial">

                        <p>
                            “A Trusted Professional in Costa Rica

                            Building a home in Costa Rica is an exciting experience, but it also requires having the right people beside you—people who understand the country, the real estate market, the due diligence involved in purchasing property, and the many details that come with designing and building a home.

                            I feel very fortunate to have found Maria.

                            From the beginning, Maria has provided the kind of knowledgeable, straightforward, and thoughtful guidance that gives you confidence you are making good decisions. Her understanding goes far beyond simply finding the right property. She understands the entire process—from evaluating a property and performing the necessary due diligence to navigating the many decisions and challenges that arise throughout the building process.

                            Just as importantly, Maria genuinely cares about the people she works with. She listens, communicates exceptionally well, and has always made me feel that my best interests come first. Her English is excellent, which has made communication effortless, but what has impressed me most is her professionalism, integrity, and willingness to help.

                            I spent my career as a Registered Nurse, and much of my professional life was devoted to helping people through situations where they needed someone knowledgeable whom they could trust. I always tried to give my patients the same level of care and guidance that I would want for myself or my own family. Because of that experience, I recognize and deeply appreciate those qualities when I encounter them in others.

                            That is what I have experienced with Maria.

                            I believe we are on this earth, in part, to help one another. Maria approaches her work with that spirit. She is not simply providing a real estate service; she is helping people navigate an important chapter in their lives.

                            For anyone considering purchasing property or building a home in Costa Rica, I recommend Maria without hesitation. Having someone with her knowledge, professionalism, integrity, and genuine concern for her clients is invaluable.”
                        </p>

                    </blockquote>

                </div>


                <!-- FOOTER -->
                <div class="modal-footer">

                    <span>
                        Client testimonial
                    </span>

                    <button
                        type="button"
                        class="btn-modal-close"
                        data-bs-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>



    <section id="properties" class="s6 container py-5">

        <div class="d-flex mb-5 align-items-center">
            <img
                src="img/iconT.webp"
                class="iconTitles img-fluid"
                alt=""
                aria-hidden="true">

            <h2 class="ms-2 mt-2">Properties</h2>
        </div>


        <div class="row g-4">

            <?php foreach ($properties as $property): ?>

                <?php

                /*
             * Imagen de propiedad
             */
                $propertyImage = !empty($property["image"])
                    ? "uploads/properties/" . $property["image"]
                    : "img/cardHome.jpg";


                /*
             * Foto del usuario
             */
                $userPhoto = !empty($property["user_photo"])
                    ? "uploads/profiles/" . $property["user_photo"]
                    : "img/mari.jpg";

                ?>

                <div class="col-xl-4 col-lg-6 col-md-6">

                    <a
                        href="propertyUser.php?id=<?= (int) $property["id"]; ?>"
                        class="propertyCard"
                        aria-label="View property <?= htmlspecialchars($property["name"]); ?>">

                        <div class="propertyImg d-flex justify-content-center align-items-center">

                            <img
                                src="<?= htmlspecialchars($propertyImage); ?>"
                                alt="<?= htmlspecialchars($property["name"]); ?>">


                            <div class="badges">

                                <p class="featured">
                                    Featured
                                </p>

                                <p class="sale">
                                    For Sale
                                </p>

                            </div>


                            <div class="location">

                                <i
                                    class="ri-map-pin-2-line"
                                    aria-hidden="true">
                                </i>

                                <?= htmlspecialchars($property["canton"]); ?>,
                                <?= htmlspecialchars($property["province"]); ?>

                            </div>

                        </div>


                        <div class="bodyCard">

                            <h3>
                                <?= htmlspecialchars($property["name"]); ?>
                            </h3>


                            <div class="propertyFeatures">

                                <p>
                                    <i
                                        class="ri-hotel-bed-line"
                                        aria-hidden="true">
                                    </i>

                                    <?= (int) $property["bedrooms"]; ?>

                                    Beds
                                </p>


                                <p>
                                    <i
                                        class="uil uil-bath"
                                        aria-hidden="true">
                                    </i>

                                    <?= (int) $property["bathrooms"]; ?>

                                    Baths
                                </p>


                                <p>
                                    <i
                                        class="ri-ruler-line"
                                        aria-hidden="true">
                                    </i>

                                    <?= htmlspecialchars($property["size"]); ?>

                                    <?= htmlspecialchars($property["size_unit"]); ?>
                                </p>

                            </div>


                            <div class="propertyFooter">

                                <div class="agent">

                                    <img
                                        src="<?= htmlspecialchars($userPhoto); ?>"
                                        alt="<?= htmlspecialchars($property["user_name"]); ?>">

                                    <span>
                                        <?= htmlspecialchars($property["user_name"]); ?>
                                    </span>

                                </div>


                                <span class="price">

                                    $<?= number_format(
                                            (float) $property["price"],
                                            0,
                                            ".",
                                            ","
                                        ); ?>

                                </span>

                            </div>

                        </div>

                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </section>

    <section class="s7 container py-5">
        <div class="s7-box">
            <div class="row align-items-center">

                <div class="col-lg-6">
                    <h2>
                        We have more <span>properties</span>
                    </h2>

                    <p>Find more properties that match what you are looking for.</p>

                    <a href="properties.php" class="btn btn-outline-primary" aria-label="See more available properties">
                        See properties
                    </a>
                </div>

            </div>
        </div>
    </section>


    <section class="s8 container-fluid pt-5">
        <div class="container">
            <div class="cardForm">
                <div class="row g-5 align-items-center">

                    <div class="col-lg-6">
                        <div class="d-flex mb-5 align-items-center">
                            <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" aria-hidden="true">
                            <h2 class="ms-2 mt-3">Let's talk!</h2>
                        </div>

                        <form
                            id="contactForm"
                            action="actions/sendContact.php"
                            method="POST">

                            <!-- Honeypot anti-spam -->
                            <div class="d-none" aria-hidden="true">
                                <label>
                                    Website
                                    <input
                                        type="text"
                                        name="website"
                                        tabindex="-1"
                                        autocomplete="off">
                                </label>
                            </div>


                            <!-- Tiempo de renderizado -->
                            <input
                                type="hidden"
                                name="form_time"
                                id="contactFormTime">


                            <div class="formInputs">

                                <input
                                    type="text"
                                    name="name"
                                    id="contactName"
                                    placeholder="Your Name"
                                    aria-label="Your name"
                                    maxlength="100">

                                <i
                                    class="ri-user-line"
                                    aria-hidden="true">
                                </i>

                            </div>


                            <div class="formInputs">

                                <input
                                    type="email"
                                    name="email"
                                    id="contactEmail"
                                    placeholder="Your Email"
                                    aria-label="Your email"
                                    maxlength="150">

                                <i
                                    class="ri-mail-line"
                                    aria-hidden="true">
                                </i>

                            </div>


                            <div class="formInputs">

                                <input
                                    type="tel"
                                    name="phone"
                                    id="contactPhone"
                                    placeholder="Your Phone"
                                    aria-label="Your phone"
                                    maxlength="30">

                                <i
                                    class="ri-smartphone-line"
                                    aria-hidden="true">
                                </i>

                            </div>


                            <div class="formInputs">

                                <textarea
                                    name="message"
                                    id="contactMessage"
                                    rows="4"
                                    placeholder="Type Your Message"
                                    aria-label="Type your message"
                                    maxlength="1500"></textarea>

                                <i
                                    class="ri-chat-3-line message"
                                    aria-hidden="true">
                                </i>

                            </div>


                            <button
                                class="formSubmit"
                                type="submit"
                                id="contactSubmit">

                                Send

                            </button>

                        </form>



                    </div>

                    <div class="col-lg-6">
                        <div class="imageBox">
                            <img class="img-fluid" src="img/photoContact.webp" alt="Modern property exterior">
                            <div class="overlay">
                                <h3>Need Any Help?</h3>
                                <a href="https://wa.me/50661189054" target="_blank" class="getInTouch">
                                    Get In Touch <i class="bi bi-send" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="footer-wrapper">

        <div class="cta-over">
            <div class="container">
                <div class="cta-box d-flex align-items-center justify-content-between flex-wrap">

                    <div class="cta-img">
                        <img src="img/mjFooter.webp" alt="Locally specialist" class="img-fluid">
                    </div>

                    <div class="cta-content text-center text-md-start">
                        <h2>Check out our projects</h2>
                    </div>

                    <div class="cta-action text-center text-md-end">
                        <a href="#projects" class="btn-cta">
                            Discover them now
                            <span><i class="ri-arrow-right-up-line" aria-hidden="true"></i></span>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="footer pt-5">
            <div class="container">
                <div class="row gy-4 mt-5">

                    <div class="col-lg-3 col-md-6">
                        <img src="img/logoLocally.png" alt="Locally Costa Rica logo" class="img-fluid mb-3">
                        <p>
                            Every project we develop is designed to reflect a brand’s identity, purpose, and sense of place, enabling brands to communicate in a more natural and memorable way.
                        </p>

                        <div class="social-icons mt-3">
                            <a href="#" aria-label="Visit our Facebook">
                                <i class="ri-facebook-fill" aria-hidden="true"></i>
                            </a>
                            <a href="#" aria-label="Visit our YouTube">
                                <i class="ri-youtube-fill" aria-hidden="true"></i>
                            </a>
                            <a href="#" aria-label="Visit our LinkedIn">
                                <i class="ri-linkedin-fill" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 pt-5">
                        <h5 class="footer-title">Quick Links</h5>
                        <ul class="footer-links">
                            <li>
                                <a href="index.php"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Home</a>
                            </li>
                            <li>
                                <a href="#about"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> About</a>
                            </li>
                            <li>
                                <a href="#services"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Services</a>
                            </li>

                            <li>
                                <a href="properties.php"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Properties</a>
                            </li>

                            <li>
                                <a href="#projects"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Portfolio</a>
                            </li>

                            <li>
                                <a href="#blog"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Blog</a>
                            </li>


                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-6 pt-5">
                        <h5 class="footer-title">Recent Properties</h5>

                        <?php if (!empty($recentProperties)): ?>

                            <?php foreach ($recentProperties as $recentProperty): ?>

                                <?php

                                $recentImage = !empty($recentProperty["image"])
                                    ? "uploads/properties/" . $recentProperty["image"]
                                    : "img/cardHome.jpg";

                                $recentDate = date(
                                    "d M, Y",
                                    strtotime($recentProperty["created_at"])
                                );

                                ?>

                                <a
                                    href="propertyUser.php?id=<?= (int) $recentProperty["id"]; ?>"
                                    class="post d-flex mb-3 text-decoration-none">

                                    <img
                                        src="<?= htmlspecialchars($recentImage); ?>"
                                        alt="Property in <?= htmlspecialchars($recentProperty["canton"]); ?>, <?= htmlspecialchars($recentProperty["province"]); ?>">

                                    <div class="ms-3">

                                        <small>
                                            <i
                                                class="ri-calendar-line"
                                                aria-hidden="true">
                                            </i>

                                            <?= htmlspecialchars($recentDate); ?>
                                        </small>

                                        <p>
                                            <?= htmlspecialchars($recentProperty["canton"]); ?>,
                                            <?= htmlspecialchars($recentProperty["province"]); ?>
                                        </p>

                                    </div>

                                </a>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p>No properties available.</p>

                        <?php endif; ?>

                    </div>

                    <div class="col-lg-2 col-md-6 pt-5 contactInfo">
                        <h5 class="footer-title">Contact Us</h5>

                        <p>
                            <i class="ri-mail-line" aria-hidden="true"></i>
                            <a href="mailto:sales@locally.cr">sales@locally.cr</a>
                        </p>

                        <p>
                            <i class="ri-phone-line" aria-hidden="true"></i>
                            <a href="tel:+50661189054">(+506) 6118-9054</a>
                        </p>
                    </div>

                </div>
            </div>

            <div class="footer-bottom mt-5">
                <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p>© All Copyright 2026 by Locally Costa Rica</p>

                    <div>
                        <a href="#">Terms & Conditions</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>

    </footer>



    <!-- WHATSAPP FLOAT -->
    <a
        href="https://wa.me/50661189054"
        class="whatsappFloat"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Contact Locally on WhatsApp">

        <span class="whatsappPulse"></span>

        <span class="whatsappIcon">
            <i class="ri-whatsapp-line" aria-hidden="true"></i>
        </span>

        <span class="whatsappText">
            Let's talk
        </span>

        <span class="whatsappDot"></span>

    </a>




    <div
        class="scrollTop"
        id="scrollTopBtn"
        aria-label="Scroll to top"
        role="button"
        tabindex="0">

        <svg
            viewBox="0 0 100 100"
            class="liquid"
            aria-hidden="true">

            <clipPath id="clipWave">
                <path id="wavePath"></path>
            </clipPath>

            <circle
                cx="50"
                cy="50"
                r="48"
                class="water-bg" />

            <circle
                cx="50"
                cy="50"
                r="48"
                class="water"
                clip-path="url(#clipWave)" />

        </svg>

        <i
            class="ri-arrow-up-s-line arrow"
            aria-hidden="true">
        </i>

    </div>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/carousel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="js/index.js"></script>




</body>

</html>