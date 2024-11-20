<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/estilos.css">

    <meta name="theme-color" content="#2091F9">

    <meta name="title" content="Aprende con Spintech">
    <meta name="description" content="Spintech: Plataforma de aprendizaje en línea inclusiva y accesible para todos.">


    <meta property="og:type" content="website">
    <meta property="og:url" content="https://spintech.com">
    <meta property="og:title" content="Aprende con Spintech">
    <meta property="og:description" content="Spintech: Plataforma de aprendizaje en línea inclusiva y accesible para todos.">
    <meta property="og:image" content="https://spintech.com/images/og-image.jpg">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://spintech.com/">
    <meta property="twitter:title" content="Aprende con Spintech">
    <meta property="twitter:description" content="Spintech: Plataforma de aprendizaje en línea inclusiva y accesible para todos.">
    <meta property="twitter:image" content="https://spintech.com/images/twitter-image.jpg">
</head>

<body>

    <header class="hero">
        <nav class="nav container">
            <div class="nav__logo">
                <h2 class="nav__title">Spintech.</h2>
            </div>

            <ul class="nav__link nav__link--menu">
                <li class="nav__items">
                    <a href="#Inicio" class="nav__links">Inicio</a>
                </li>
                <li class="nav__items">
                    <a href="#Acerca de" class="nav__links">Acerca de</a>
                </li>
                <li class="nav__items">
                    <a href="cursos.php" class="nav__links">Cursos</a>
                </li>
                
                <li class="nav__items">
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="logout.php" class="nav__links">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="login.php" class="nav__links">Iniciar Sesión / Registrarse</a>
                    <?php endif; ?>
                </li>

                <img src="./images/close.svg" class="nav__close">
            </ul>

            <div class="nav__menu">
                <img src="./images/menu.svg" class="nav__img">
            </div>
        </nav>

        <section class="hero__container container">
            <h1 class="hero__title">Spintech</h1>
            <p class="hero__paragraph">"Todos merecen una educación de calidad, Spintech lo hace posible"</p>
            <a href="login.php" class="cta">Comienza ahora</a>
        </section>
    </header>

    <main>
        <section class="container about">
            <h2 class="subtitle">¿Qué ofrecemos para ti?</h2>
            <p class="about__paragraph">En Spintech, ofrecemos una amplia variedad de cursos en línea en distintas áreas, desde programación y diseño hasta mecánica y medicina.</p>

            <h2 class="subtitle">Principales características</h2>

            <div class="about__main">
                <article class="about__icons">
                    <img src="./images/aprender-en-linea.gif" class="about__icon">
                    <h3 class="about__title">Realiza cursos gratis</h3>
                    <p class="about__paragrah">Realiza cursos gratis visualizando la introducción de algún curso que te interese
                    </p>
                </article>

                <article class="about__icons">
                    <img src="./images/geografia.gif " class="about__icon">
                    <h3 class="about__title">Accesibilidad</h3>
                    <p class="about__paragrah">Existen muchas formas de adaptar tus estudios, usando intérpretes o subtitulos y otra serie de ayudas </p>
                </article>

                <article class="about__icons">
                    <img src="./images/diploma.gif" class="about__icon">
                    <h3 class="about__title">Certificacion </h3>
                    <p class="about__paragrah">Obtén certificados reconocidos al completar nuestros cursos y programas.</p>
                </article>
            </div>
        </section>
        <section id="Acerca de">
            <section class="knowledge">
                <div class="knowledge__container container">
                    <div class="knowledege__texts">
                        <h2 class="subtitle">Misión</h2>
                        <p class="knowledge__paragraph">Misión: En Spintech, nuestra misión es proporcionar cursos en línea de alta calidad que sean accesibles y adaptables a personas con diferentes tipos de discapacidades. Nos comprometemos a brindar una experiencia de aprendizaje
                            en línea inclusiva y efectiva, que permita a nuestros estudiantes mejorar sus habilidades y alcanzar sus objetivos personales y profesionales.

                        </p>
                        <h2 class="subtitle">Visión</h2>
                        <p class="knowledge__paragraph">Visión: En Spintech, nuestra visión es liderar la educación en línea accesible e inclusiva para personas con discapacidades de todo el mundo. Nos comprometemos a innovar y adaptar nuestras soluciones educativas para satisfacer
                            las necesidades cambiantes del mercado y las comunidades a las que servimos. Queremos ser reconocidos por nuestro compromiso con la excelencia académica, la accesibilidad y la inclusión.</p>
                    </div>

                    <figure class="knowledge__picture">
                        <img src="./images/Spintech.png" class="knowledge__img">
                    </figure>
                </div>
            </section>
        </section>

        <section id="Cursos">
            <section class="features container">
                <h2 class="subtitle">Nuestros Cursos</h2>

                <div class="features__table">
                    <div class="features__element">
                        <p class="features__name">Cursos Cognitivos</p>
                        <img src="./images/brain.png" alt="Ícono de cerebro" class="features__icon">
                        <div class="features__items">
                            <p class="features__features">Materiales adaptados</p>
                            <p class="features__features">Ritmo personalizado</p>
                            <p class="features__features">Apoyo especializado</p>
                        </div>
                        <a href="cursos.php?tipo=cognitiva" class="features__cta">Explorar cursos</a>
                    </div>

                    <div class="features__element features__element--best">
                        <p class="features__name">Cursos Visuales</p>
                        <img src="./images/eye.png" alt="Ícono de ojo" class="features__icon">
                        <div class="features__items">
                            <p class="features__features">Descripción de audio</p>
                            <p class="features__features">Textos adaptados</p>
                            <p class="features__features">Materiales táctiles</p>
                        </div>
                        <a href="cursos.php?tipo=visual" class="features__cta">Explorar cursos</a>
                    </div>

                    <div class="features__element">
                        <p class="features__name">Cursos Auditivos</p>
                        <img src="./images/ear.png" alt="Ícono de oído" class="features__icon">
                        <div class="features__items">
                            <p class="features__features">Subtítulos detallados</p>
                            <p class="features__features">Lenguaje de señas</p>
                            <p class="features__features">Transcripciones completas</p>
                        </div>
                        <a href="cursos.php?tipo=auditiva" class="features__cta">Explorar cursos</a>
                    </div>
                </div>
            </section>
        </section>

        <section class="testimony">
            <div class="testimony__container container">
                <img src="./images/leftarrow.svg" class="testimony__arrow" id="before">

                <section class="testimony__body testimony__body--show" data-id="1">
                    <div class="testimony__texts">
                        <h2 class="subtitle">Mi nombre es Jordan Alexander, <span class="testimony__course">Docente de
                                Gestión de proyectos
                                .</span></h2>
                        <p class="testimony__review">Spintech me ha permitido compartir mis conocimientos con estudiantes de todo el mundo. La plataforma es intuitiva y accesible, lo que facilita la creación de contenido inclusivo.
                        </p>
                    </div>

                    <figure class="testimony__picture">
                        <img src="./images/face3.jpg" class="testimony__img">
                    </figure>
                </section>

                <section class="testimony__body" data-id="2">
                    <div class="testimony__texts">
                        <h2 class="subtitle">Mi nombre es Alejandra Perez, <span class="testimony__course">Docente de
                            Contabilidad.</span></h2>
                        <p class="testimony__review">Como profesora con discapacidad visual, Spintech me ha brindado las herramientas necesarias para crear y enseñar cursos de contabilidad de manera efectiva. Es una plataforma verdaderamente inclusiva.
                        </p>
                    </div>

                    <figure class="testimony__picture">
                        <img src="./images/face2.jpg" class="testimony__img">
                    </figure>
                </section>

                <section class="testimony__body" data-id="3">
                    <div class="testimony__texts">
                        <h2 class="subtitle">Mi nombre es Karen Arteaga, <span class="testimony__course">Docente 
                           de Artes Visuales.</span></h2>
                        <p class="testimony__review">Spintech ha revolucionado la forma en que enseño artes visuales a estudiantes con diferentes capacidades. Las herramientas de accesibilidad son excepcionales.</p>
                    </div>

                    <figure class="testimony__picture">
                        <img src="./images/face.jpg" class="testimony__img">
                    </figure>
                </section>

                <section class="testimony__body" data-id="4">
                    <div class="testimony__texts">
                        <h2 class="subtitle">Mi nombre es Kevin Ramirez, <span class="testimony__course">Docente
                                de Redes y seguridad.</span></h2>
                        <p class="testimony__review">La plataforma de Spintech me ha permitido crear cursos de redes y seguridad que son accesibles para todos. Es gratificante ver cómo estudiantes con diferentes capacidades pueden aprender y crecer en el campo de la tecnología.</p>
                    </div>

                    <figure class="testimony__picture">
                        <img src="./images/face4.jpg" class="testimony__img">
                    </figure>
                </section>


                <img src="./images/rightarrow.svg" class="testimony__arrow" id="next">
            </div>
        </section>

        <section class="questions container">
            <h2 class="subtitle">Preguntas frecuentes</h2>
            <p class="questions__paragraph">Aquí encontrarás respuestas a las preguntas más comunes sobre Spintech y nuestros servicios.</p>

            <section class="questions__container">
                <article class="questions__padding">
                    <div class="questions__answer">
                        <h3 class="questions__title">¿Quienes somos?
                            <span class="questions__arrow">
                                <img src="./images/arrow.svg" class="questions__img">
                            </span>
                        </h3>

                        <p class="questions__show">Somos Spintech, una plataforma de educación en línea dedicada a proporcionar cursos accesibles e inclusivos para personas con diferentes tipos de discapacidades. Nuestro objetivo es eliminar las barreras en la educación y ofrecer oportunidades de aprendizaje para todos.</p>
                    </div>
                </article>

                <article class="questions__padding">
                    <div class="questions__answer">
                        <h3 class="questions__title">¿Qué aprenderé en los cursos de Spintech?
                            <span class="questions__arrow">
                                <img src="./images/arrow.svg" class="questions__img">
                            </span>
                        </h3>

                        <p class="questions__show">En Spintech, ofrecemos una amplia gama de cursos en diversas áreas, desde tecnología y negocios hasta artes y ciencias. Cada curso está diseñado para ser accesible y adaptable a diferentes necesidades, garantizando que puedas adquirir nuevas habilidades y conocimientos independientemente de tus capacidades.</p>
                    </div>
                </article>

                <article class="questions__padding">
                    <div class="questions__answer">
                        <h3 class="questions__title">¿Cómo funciona la accesibilidad en Spintech?
                            <span class="questions__arrow">
                                <img src="./images/arrow.svg" class="questions__img">
                            </span>
                        </h3>

                        <p class="questions__show">Spintech utiliza tecnologías avanzadas para hacer que nuestros cursos sean accesibles para todos. Esto incluye subtítulos para personas con discapacidad auditiva, descripciones de audio para personas con discapacidad visual, y diseño adaptativo para personas con discapacidades motoras. Además, nuestro contenido está estructurado de manera que sea fácil de navegar y comprender para personas con discapacidades cognitivas.</p>
                    </div>
                </article>
            </section>

            <section class="questions__offer">
                <h2 class="subtitle">¿Estás listo para aprender en Spintech?</h2>
                <p class="questions__copy">En Spintech, creemos que la educación debe ser accesible para todos. Nuestra plataforma está diseñada para adaptarse a tus necesidades y ayudarte a alcanzar tus metas de aprendizaje. ¡Únete a nuestra comunidad y comienza tu viaje educativo hoy!</p>
                <a href="#" class="cta">Aprende ahora</a>
            </section>
        </section>
    </main>
    <section id="Contacto">
        <footer class="footer">
            <section class="footer__container container">
                <nav class="nav nav--footer">
                    <h2 class="footer__title">Spintech.</h2>

                    <ul class="nav__link nav__link--footer">
                        <li class="nav__items">
                            <a href="#" class="nav__links">Inicio</a>
                        </li>
                        <li class="nav__items">
                            <a href="#" class="nav__links">Acerca de</a>
                        </li>
                        <li class="nav__items">
                            <a href="#" class="nav__links">Categoria</a>
                        </li>
                        <li class="nav__items">
                            <a href="#" class="nav__links">Contacto</a>
                        </li>
                    </ul>
                </nav>

                <form class="footer__form" action="https://formspree.io/f/mknkkrkj" method="POST">
                    <h2 class="footer__newsletter">Suscríbete a Spintech</h2>
                    <div class="footer__inputs">
                        <input type="email" placeholder="Email:" class="footer__input" name="_replyto">
                        <input type="submit" value="Suscríbete" class="footer__submit">
                    </div>
                </form>
            </section>

            <section class="footer__copy container">
                <div class="footer__social">
                    <a href="#" class="footer__icons"><img src="./images/facebook.svg" class="footer__img"></a>
                    <a href="#" class="footer__icons"><img src="./images/twitter.svg" class="footer__img"></a>
                    <a href="#" class="footer__icons"><img src="./images/youtube.svg" class="footer__img"></a>
                </div>

                <h3 class="footer__copyright">Derechos reservados &copy; Spintech <?php echo date("Y"); ?></h3>
            </section>
        </footer>
    </section>
    <script src="./js/slider.js"></script>
    <script src="./js/questions.js"></script>
    <script src="./js/menu.js"></script>
</body>

</html>