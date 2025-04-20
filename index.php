<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Veterinaria | VetKBSci</title>
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/js/index.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Veterinaria | VetKBSci</title>
        <link rel="stylesheet" href="public/css/index.css">
        <script src="public/js/index.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </head>

    <body>

        <header class="header">
            <div class="container">
                <div class="logo">
                    <a href="#home">
                        <span>VetKBSci</span>
                        <i class="fas fa-paw logo-icon"></i>
                    </a>
                </div>
                <nav class="navbar">
                    <ul class="nav-menu">
                        <li class="nav-item"><a href="#home" class="nav-link active"><i class="fas fa-home nav-icon"></i> Inicio</a></li>
                        <li class="nav-item"><a href="#services" class="nav-link"><i class="fas fa-clipboard-list nav-icon"></i> Servicios</a></li>
                        <li class="nav-item"><a href="#about" class="nav-link"><i class="fas fa-info-circle nav-icon"></i> Nosotros</a></li>
                        <li class="nav-item"><a href="#team" class="nav-link"><i class="fas fa-users nav-icon"></i> Equipo</a></li>
                        <li class="nav-item"><a href="#testimonios" class="nav-link"><i class="fas fa-comment-alt nav-icon"></i> Testimonios</a></li>
                        <li class="nav-item"><a href="#contacto" class="nav-link"><i class="fas fa-envelope nav-icon"></i> Contacto</a></li>
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-login">
                                <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                            </a>
                        </li>
                    </ul>
                    <div class="mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </div>
                </nav>
            </div>
        </header>
        <section class="hero" id="home">
            <div class="container">
                <div class="hero-text">
                    <h1>Cuidado veterinario <span>excepcional</span> para tu mascota</h1>
                    <p class="subtitle">Tecnología avanzada y atención compasiva en un solo lugar</p>
                    <div class="hero-btns">
                        <a href="#services" class="btn btn-secondary">Nuestros servicios</a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">2,500+</span>
                            <span class="stat-label">Mascotas atendidas</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">10+</span>
                            <span class="stat-label">Años de experiencia</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Satisfacción</span>
                        </div>
                    </div>
                </div>
                <div class="hero-img">
                    <img src="public/img/Kenzo-hero.jpg" alt="Mascota feliz" class="main-img">
                    <div class="img-decoration"></div>
                    <div class="hero-badge">
                        <div class="badge-content">
                            <i class="fas fa-heart"></i>
                            <span>Amamos lo que hacemos</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Citas online</h3>
                    <p>Agenda tu cita fácilmente desde nuestra plataforma</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-ambulance"></i>
                    </div>
                    <h3>Emergencias 24/7</h3>
                    <p>Atención inmediata cuando más la necesitas</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <h3>Cuidado especializado</h3>
                    <p>Profesionales para cada tipo de mascota</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3>Hospitalización</h3>
                    <p>Instalaciones equipadas para recuperación</p>
                </div>
            </div>
        </section>

        <section class="section" id="services">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Lo que ofrecemos</span>
                    <h2 class="section-title">Nuestros servicios</h2>
                    <p class="section-description">Cuidado integral para la salud y bienestar de tu mascota</p>
                </div>
                <div class="grid-3">
                    <div class="card">
                        <img src="public/img/consultas.jpg" alt="Consultas">
                        <h3>Consultas</h3>
                        <p>Evaluación y tratamiento profesional para cualquier síntoma con equipos de última generación.</p>

                    </div>
                    <div class="card">
                        <img src="public/img/vacunacion.jpg" alt="Vacunación">
                        <h3>Vacunación</h3>
                        <p>Protección efectiva con un plan personalizado para tu mascota según su edad y estilo de vida.</p>

                    </div>
                    <div class="card">
                        <img src="public/img/emergencia.jpg" alt="Emergencias">
                        <h3>Emergencias</h3>
                        <p>Respuesta inmediata para situaciones críticas con equipo de urgencias disponible las 24 horas.</p>

                    </div>
                    <div class="card">
                        <img src="public/img/cirugia.jpg" alt="Cirugías">
                        <h3>Cirugías</h3>
                        <p>Procedimientos quirúrgicos con tecnología avanzada y seguimiento postoperatorio.</p>

                    </div>
                    <div class="card">
                        <img src="public/img/odontologia.jpg" alt="Odontología">
                        <h3>Odontología</h3>
                        <p>Cuidado dental profesional incluyendo limpiezas, extracciones y ortodoncia.</p>

                    </div>
                    <div class="card">
                        <img src="public/img/laboratorio.jpg" alt="Laboratorio">
                        <h3>Laboratorio</h3>
                        <p>Análisis clínicos, pruebas de diagnóstico y estudios especializados in situ.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section" id="about">
            <div class="container">
                <div class="about-img">
                    <img src="public/img/veterinaria.jpg" alt="Nuestra clínica">
                </div>
                <div class="about-content">
                    <span class="section-subtitle">Sobre nosotros</span>
                    <h2 class="section-title">Cuidando a tus mascotas desde 2013</h2>
                    <p>En VetKBSci nos dedicamos con pasión y profesionalismo al bienestar animal. Nuestra clínica fue fundada con la visión de ofrecer medicina veterinaria de excelencia combinando tecnología avanzada con trato cálido y personalizado.</p>

                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Equipo multidisciplinario</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Instalaciones modernas</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Equipo diagnóstico completo</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Farmacia veterinaria</span>
                        </div>
                    </div>

                    <div class="about-mission">
                        <h4><i class="fas fa-heart"></i> Nuestra misión</h4>
                        <p>Proporcionar atención médica veterinaria excepcional que mejore la calidad de vida de las mascotas y fortalezca el vínculo humano-animal.</p>
                    </div>

                    <a href="#team" class="btn btn-primary">Conoce a nuestro equipo</a>
                </div>
            </div>
        </section>

        <section class="section bg-light" id="team">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Profesionales</span>
                    <h2 class="section-title">Nuestro equipo</h2>
                    <p class="section-description">Conoce a los especialistas que cuidarán de tu mascota</p>
                </div>
                <div class="grid-3">
                    <div class="team-card">
                        <div class="team-img">
                            <img src="public/img/dr1.jpg" alt="Dra. Sofía">
                        </div>
                        <h3>Dra. Sofía Rodríguez</h3>
                        <span class="team-role">Medicina Interna</span>
                        <p>Especialista en medicina interna y cirugía general con más de 12 años de experiencia clínica.</p>
                        <div class="team-skills">
                            <span>Cirugía</span>
                            <span>Diagnóstico</span>
                            <span>Oncología</span>
                        </div>
                    </div>
                    <div class="team-card">
                        <div class="team-img">
                            <img src="public/img/dr2.jpg" alt="Dr. Carlos">
                        </div>
                        <h3>Dr. Carlos Méndez</h3>
                        <span class="team-role">Urgencias</span>
                        <p>Urgenciólogo veterinario con enfoque en atención rápida y efectiva. Amante de los perros grandes.</p>
                        <div class="team-skills">
                            <span>Traumatología</span>
                            <span>Reanimación</span>
                            <span>TAC</span>
                        </div>
                    </div>
                    <div class="team-card">
                        <div class="team-img">
                            <img src="public/img/dr3.jpg" alt="Dr. Kyan">

                        </div>
                        <h3>Dr. Kyan Luna</h3>
                        <span class="team-role">Preventiva</span>
                        <p>Especialista en medicina preventiva, vacunación y control de peso con enfoque nutricional.</p>
                        <div class="team-skills">
                            <span>Nutrición</span>
                            <span>Vacunas</span>
                            <span>Prevención</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section testimonials" id="testimonios">
            <div class="container">
                <div class="section-header">
                    <span class="section-subtitle">Opiniones</span>
                    <h2 class="section-title">Lo que dicen nuestros clientes</h2>
                </div>
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Excelente servicio. Mi gata Nina fue tratada con mucho cariño y la recuperación fue muy rápida. Las instalaciones son impecables y el equipo muy profesional."</p>
                        <div class="client-info">
                            <img src="public/img/ayleen.jpg" alt="clienta1">
                            <div>
                                <h4>Ayleen Araya S.</h4>
                                <span>Dueña de Nina (Gata)</span>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"El equipo médico es increíble. Nos explicaron todo con claridad y salvaron a nuestro perrito cuando tuvo una obstrucción intestinal. Estamos eternamente agradecidos."</p>
                        <div class="client-info">
                            <img src="public/img/Armando.jpg" alt="Cliente2">
                            <div>
                                <h4>Armando Paredes A.</h4>
                                <span>Dueño de Rocky (Perro)</span>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p>"100% recomendados. Llevo a mis 3 gatos aquí desde hace años. Instalaciones limpias, modernas y atención personalizada. Los precios son justos para la calidad recibida."</p>
                        <div class="client-info">
                            <img src="public/img/fabian.jpg" alt="Cliente3">
                            <div>
                                <h4>Fabián Salazar G.</h4>
                                <span>Dueño de 3 gatos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="#" class="btn btn-outline">Deja tu testimonio</a>
                </div>
            </div>
        </section>
        <section class="section contact-section" id="contacto">
            <div class="container">
                <div class="contact-info">
                    <div class="section-header">
                        <span class="section-subtitle">Contacto</span>
                        <h2 class="section-title">Cómo podemos ayudarte</h2>
                        <p class="section-description">Estamos aquí para responder cualquier pregunta sobre el cuidado de tu mascota.</p>
                    </div>


                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Teléfono</h3>
                            <p>+506 8877 5391</p>
                            <p>Emergencias: +506 8877 5392</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>contacto@vetkb.com</p>
                            <p>emergencias@vetkb.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>Horario</h3>
                            <p>Lunes a Viernes: 8am - 8pm</p>
                            <p>Sábados: 9am - 4pm</p>
                            <p>Emergencias: 24/7</p>
                        </div>
                    </div>


                </div>
            </div>

            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6864.550432676497!2d-83.79261993969449!3d10.213482756353184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8fa0b8100cd0926d%3A0xaba36611d67da863!2zTGltw7NuLCBHdcOhcGlsZXM!5e0!3m2!1ses-419!2scr!4v1745072878225!5m2!1ses-419!2scr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>

        <footer>
            <div class="footer-bottom">
                <p>&copy; 2025 VetKBSci. Todos los derechos reservados.</p>
        </footer>

    </body>

    </html>