/* EtikaSosial — navigasi seluler sederhana. */
"use strict";

(function () {
  var navToggle = document.getElementById("navToggle");
  var mainNav = document.getElementById("mainNav");

  if (!navToggle || !mainNav) {
    return;
  }

  function setOpen(open) {
    mainNav.classList.toggle("is-open", open);
    navToggle.setAttribute("aria-expanded", open ? "true" : "false");
    navToggle.setAttribute("aria-label", open ? "Tutup menu navigasi" : "Buka menu navigasi");
  }

  navToggle.addEventListener("click", function () {
    setOpen(!mainNav.classList.contains("is-open"));
  });

  mainNav.addEventListener("click", function (event) {
    if (event.target.closest("a")) {
      setOpen(false);
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      setOpen(false);
    }
  });
})();
