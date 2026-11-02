/* ===== Lab Sheet 6 - Portfolio scripts ===== */
document.addEventListener("DOMContentLoaded", () => {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  /* ---------- Task 4: Hamburger menu ---------- */
  const hamburger = $("#hamburger");
  const nav = $("#nav");
  if (hamburger && nav) {
    hamburger.addEventListener("click", () => {
      const open = nav.classList.toggle("open");
      hamburger.classList.toggle("open", open);
      hamburger.setAttribute("aria-expanded", String(open));
    });
    $$("a", nav).forEach(a =>
      a.addEventListener("click", () => {
        nav.classList.remove("open");
        hamburger.classList.remove("open");
        hamburger.setAttribute("aria-expanded", "false");
      })
    );
  }

  /* ---------- Task 7: Dark / light mode (swaps :root variables) ---------- */
  const themes = {
    light: {
      "--bg": "#ffffff", "--bg-alt": "#f3f5fa", "--text": "#1f2937", "--muted": "#6b7280",
      "--primary": "#4f46e5", "--card": "#ffffff", "--border": "#e5e7eb",
      "--shadow": "rgba(17, 24, 39, 0.12)", "--header-bg": "rgba(255, 255, 255, 0.92)"
    },
    dark: {
      "--bg": "#0f172a", "--bg-alt": "#162036", "--text": "#e5e7eb", "--muted": "#9ca3af",
      "--primary": "#818cf8", "--card": "#1e293b", "--border": "#334155",
      "--shadow": "rgba(0, 0, 0, 0.5)", "--header-bg": "rgba(15, 23, 42, 0.92)"
    }
  };
  const themeBtn = $("#themeToggle");
  function applyTheme(name) {
    const root = document.documentElement;
    Object.entries(themes[name]).forEach(([k, v]) => root.style.setProperty(k, v));
    if (themeBtn) {
      themeBtn.textContent = name === "dark" ? "☀️" : "🌙";
      themeBtn.setAttribute("aria-label", name === "dark" ? "Switch to light mode" : "Switch to dark mode");
    }
    try { localStorage.setItem("theme", name); } catch (e) { /* storage may be blocked */ }
  }
  let saved = "light";
  try { saved = localStorage.getItem("theme") || (matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"); } catch (e) { /* ignore */ }
  applyTheme(saved);
  if (themeBtn) {
    themeBtn.addEventListener("click", () => {
      const current = document.documentElement.style.getPropertyValue("--bg").trim() === themes.dark["--bg"] ? "dark" : "light";
      applyTheme(current === "dark" ? "light" : "dark");
    });
  }

  /* ---------- Task 9: Smooth scrolling for in-page anchor links ---------- */
  $$('a[href^="#"]').forEach(link => {
    link.addEventListener("click", e => {
      const id = link.getAttribute("href");
      if (id.length < 2) return;
      const target = $(id);
      if (!target) return;
      e.preventDefault();
      const offset = ($(".site-header") || { offsetHeight: 0 }).offsetHeight;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: "smooth" });
    });
  });

  /* ---------- Task 13: Back to top button ---------- */
  const backBtn = $("#backToTop");
  if (backBtn) {
    window.addEventListener("scroll", () => {
      backBtn.classList.toggle("visible", window.scrollY > 300);
    });
    backBtn.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  }

  /* ---------- Task 6: Contact form validation ---------- */
  const form = $("#contactForm");
  if (form) {
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const rules = {
      name: v => (v.trim().length >= 2 ? "" : "Please enter your name (at least 2 characters)."),
      email: v => (v.trim() === "" ? "Email is required." : emailRe.test(v.trim()) ? "" : "Please enter a valid email address."),
      message: v => (v.trim().length >= 10 ? "" : "Message must be at least 10 characters.")
    };
    function check(field) {
      const group = field.closest(".form-group");
      const msg = rules[field.name](field.value);
      group.classList.toggle("invalid", msg !== "");
      $(".error-msg", group).textContent = msg;
      return msg === "";
    }
    $$("input, textarea", form).forEach(f => f.addEventListener("input", () => check(f)));
    form.addEventListener("submit", e => {
      e.preventDefault();
      const ok = $$("input, textarea", form).map(check).every(Boolean);
      const success = $("#formSuccess");
      if (ok) {
        success.style.display = "block";
        success.textContent = "Thanks, " + form.name.value.trim() + "! Your message has been validated and is ready to send.";
        form.reset();
      } else {
        success.style.display = "none";
        const firstBad = $(".invalid input, .invalid textarea", form);
        if (firstBad) firstBad.focus();
      }
    });
  }

  /* ---------- Task 10: FAQ accordion ---------- */
  $$(".faq-item").forEach(item => {
    const q = $(".faq-question", item);
    const a = $(".faq-answer", item);
    q.addEventListener("click", () => {
      const isOpen = item.classList.contains("open");
      $$(".faq-item.open").forEach(other => {
        other.classList.remove("open");
        $(".faq-answer", other).style.maxHeight = null;
        $(".faq-question", other).setAttribute("aria-expanded", "false");
      });
      if (!isOpen) {
        item.classList.add("open");
        a.style.maxHeight = a.scrollHeight + "px";
        q.setAttribute("aria-expanded", "true");
      }
    });
  });

  /* ---------- Task 11: Testimonials carousel (array + opacity fade) ---------- */
  const box = $("#testimonialBox");
  if (box) {
    const testimonials = [
      { text: "Delivered a clean, fast and beautifully responsive website ahead of schedule.", name: "Aarav Mehta, Startup Founder" },
      { text: "Great attention to detail. The semantic markup and accessibility were top notch.", name: "Priya Sharma, Product Manager" },
      { text: "Communicative, creative and reliable. I would happily hire again.", name: "Rohan Verma, Agency Director" },
      { text: "The dark mode and animations gave our dashboard a real premium feel.", name: "Neha Kapoor, UX Lead" }
    ];
    const dotsWrap = $("#dots");
    let idx = 0, timer;
    testimonials.forEach((_, i) => {
      const d = document.createElement("span");
      d.className = "dot";
      d.addEventListener("click", () => { show(i); restart(); });
      dotsWrap.appendChild(d);
    });
    function show(i) {
      idx = (i + testimonials.length) % testimonials.length;
      box.classList.add("fade");
      setTimeout(() => {
        $("blockquote", box).textContent = "“" + testimonials[idx].text + "”";
        $("cite", box).textContent = "— " + testimonials[idx].name;
        $$(".dot", dotsWrap).forEach((d, n) => d.classList.toggle("active", n === idx));
        box.classList.remove("fade");
      }, 350);
    }
    function restart() { clearInterval(timer); timer = setInterval(() => show(idx + 1), 5000); }
    $("#prevT").addEventListener("click", () => { show(idx - 1); restart(); });
    $("#nextT").addEventListener("click", () => { show(idx + 1); restart(); });
    $("blockquote", box).textContent = "“" + testimonials[0].text + "”";
    $("cite", box).textContent = "— " + testimonials[0].name;
    $$(".dot", dotsWrap)[0].classList.add("active");
    restart();
  }

  /* ---------- Task 15: Statistics counter ---------- */
  const counters = $$(".num[data-target]");
  if (counters.length) {
    const run = el => {
      const target = +el.dataset.target;
      const suffix = el.dataset.suffix || "";
      const duration = 1800;
      const start = performance.now();
      function tick(now) {
        const p = Math.min((now - start) / duration, 1);
        el.textContent = Math.floor(p * target) + suffix;
        if (p < 1) requestAnimationFrame(tick);
        else el.textContent = target + suffix;
      }
      requestAnimationFrame(tick);
    };
    if ("IntersectionObserver" in window) {
      const io = new IntersectionObserver(entries => {
        entries.forEach(en => {
          if (en.isIntersecting) { run(en.target); io.unobserve(en.target); }
        });
      }, { threshold: 0.4 });
      counters.forEach(c => io.observe(c));
    } else {
      counters.forEach(run);
    }
  }

  /* ---------- Task 17: Modal popup ---------- */
  const modal = $("#modal");
  if (modal) {
    const openers = $$("[data-open-modal]");
    const close = () => modal.classList.remove("show");
    openers.forEach(b => b.addEventListener("click", () => modal.classList.add("show")));
    $(".modal-close", modal).addEventListener("click", close);
    modal.addEventListener("click", e => { if (e.target === modal) close(); });
    document.addEventListener("keydown", e => { if (e.key === "Escape") close(); });
  }

  /* ---------- Task 19: Live project search filter ---------- */
  const search = $("#projectSearch");
  if (search) {
    const cards = $$("#projectGrid .card");
    const empty = $("#noResults");
    search.addEventListener("input", () => {
      const q = search.value.trim().toLowerCase();
      let visible = 0;
      cards.forEach(c => {
        const match = c.textContent.toLowerCase().includes(q);
        c.classList.toggle("hidden", !match);
        if (match) visible++;
      });
      if (empty) empty.style.display = visible === 0 ? "block" : "none";
    });
  }
});
