const menuToggle = document.getElementById("menu-toggle");
const navMenu = document.getElementById("nav-menu");
const navLinks = document.querySelectorAll(".nav-link");
const sections = document.querySelectorAll("section");
const header = document.getElementById("header");

if (menuToggle && navMenu) {
  menuToggle.addEventListener("click", () => {
    navMenu.classList.toggle("active");
  });
}

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    if (navMenu) {
      navMenu.classList.remove("active");
    }
  });
});

function updateActiveNav() {
  let currentSection = "home";

  const scrollPosition = window.scrollY + 250;

  sections.forEach((section) => {
    const sectionTop = section.offsetTop;

    const sectionBottom = sectionTop + section.offsetHeight;

    if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
      currentSection = section.id;
    }
  });

  navLinks.forEach((link) => {
    const linkSection = link.getAttribute("href").substring(1);

    if (linkSection === currentSection) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });
}

window.addEventListener("scroll", updateActiveNav);

window.addEventListener("load", updateActiveNav);

function updateHeader() {
  if (!header) {
    return;
  }

  if (window.scrollY > 30) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
}

window.addEventListener("scroll", updateHeader);

window.addEventListener("load", updateHeader);

updateActiveNav();
updateHeader();

const contactForm = document.getElementById("contact-form");

const toast = document.getElementById("toast");

const toastMessage = document.getElementById("toast-message");

let toastTimer;

function showToast(message) {
  if (!toast || !toastMessage) {
    return;
  }

  toastMessage.textContent = message;

  toast.classList.add("show");

  clearTimeout(toastTimer);

  toastTimer = setTimeout(() => {
    toast.classList.remove("show");
  }, 4000);
}

function clearValidation() {
  const errors = contactForm.querySelectorAll(".validation-error");

  errors.forEach((error) => {
    error.remove();
  });

  const invalidFields = contactForm.querySelectorAll(".input-error");

  invalidFields.forEach((field) => {
    field.classList.remove("input-error");
  });
}

function showValidation(input, message) {
  input.classList.add("input-error");

  const error = document.createElement("small");

  error.className = "validation-error";

  error.textContent = message;

  input.parentElement.appendChild(error);
}

function validateContactForm() {
  clearValidation();

  const name = document.getElementById("name");

  const email = document.getElementById("email");

  const message = document.getElementById("message");

  let valid = true;

  const nameValue = name.value.trim();

  const emailValue = email.value.trim();

  const messageValue = message.value.trim();

  if (nameValue === "") {
    showValidation(name, "Please enter your name.");

    valid = false;
  } else if (nameValue.length < 2) {
    showValidation(name, "Name must be at least 2 characters.");

    valid = false;
  } else if (nameValue.length > 50) {
    showValidation(name, "Name must not exceed 50 characters.");

    valid = false;
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (emailValue === "") {
    showValidation(email, "Please enter your email.");

    valid = false;
  } else if (!emailPattern.test(emailValue)) {
    showValidation(email, "Please enter a valid email address.");

    valid = false;
  }

  if (messageValue === "") {
    showValidation(message, "Please enter your message.");

    valid = false;
  } else if (messageValue.length < 10) {
    showValidation(message, "Message must be at least 10 characters.");

    valid = false;
  } else if (messageValue.length > 1000) {
    showValidation(message, "Message must not exceed 1000 characters.");

    valid = false;
  }

  return valid;
}

if (contactForm) {
  const inputs = contactForm.querySelectorAll("input, textarea");

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      input.classList.remove("input-error");

      const error = input.parentElement.querySelector(".validation-error");

      if (error) {
        error.remove();
      }
    });
  });

  contactForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    if (!validateContactForm()) {
      return;
    }

    const submitButton = contactForm.querySelector('button[type="submit"]');

    const formData = new FormData(contactForm);

    if (submitButton) {
      submitButton.disabled = true;

      submitButton.textContent = "Sending...";
    }

    try {
      const response = await fetch("backend/contact.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        contactForm.reset();

        clearValidation();

        showToast("Message sent successfully!");
      } else {
        showToast(result.message);
      }
    } catch (error) {
      console.error("Contact form error:", error);

      showToast("Something went wrong. Please try again.");
    } finally {
      if (submitButton) {
        submitButton.disabled = false;

        submitButton.textContent = "Send Message";
      }
    }
  });
}

const animatedSections = document.querySelectorAll(".section");

const sectionObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }
    });
  },
  {
    threshold: 0.15,
  },
);

animatedSections.forEach((section) => {
  sectionObserver.observe(section);
});
