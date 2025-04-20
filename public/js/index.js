document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar elementos del DOM
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const navMenu = document.querySelector(".nav-menu");
  const navLinks = document.querySelectorAll(".nav-link");

  // Función para alternar el menú móvil
  function toggleMobileMenu() {
    navMenu.classList.toggle("active");
    mobileMenuBtn.querySelector("i").classList.toggle("fa-bars");
    mobileMenuBtn.querySelector("i").classList.toggle("fa-times");

    // Alternar el overflow del body cuando el menú está abierto
    if (navMenu.classList.contains("active")) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
  }

  // Evento click para el botón del menú móvil
  mobileMenuBtn.addEventListener("click", toggleMobileMenu);

  // Cerrar el menú cuando se hace click en un enlace (para móviles)
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 992) {
        // Solo para móviles
        navMenu.classList.remove("active");
        mobileMenuBtn.querySelector("i").classList.add("fa-bars");
        mobileMenuBtn.querySelector("i").classList.remove("fa-times");
        document.body.style.overflow = "";
      }
    });
  });

  // Efecto de scroll para el header
  window.addEventListener("scroll", function () {
    const header = document.querySelector(".header");
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });
});
