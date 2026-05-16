<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Libro de Reclamaciones | Profesional</title>
  <meta name="description" content="Landing profesional para Libro de Reclamaciones: formulario, seguimiento, reportes, FAQ y CTA." />
  <link rel="icon" href="https://elementi.me/wp-content/uploads/2020/08/Libro-de-reclamaciones-Azul-300x300.png">

  <style>
    :root {
      /* Light theme */
      --bg: #ffffff;
      --bg2: #F6F8FC;
      --card: #ffffff;
      --text: #0F172A;
      /* slate-900 */
      --muted: #475569;
      /* slate-600 */
      --line: #E2E8F0;
      /* slate-200 */

      --brand: #2563EB;
      /* blue-600 */
      --brand2: #0EA5E9;
      /* sky-500 */
      --ok: #16A34A;

      --shadow: 0 18px 50px rgba(2, 6, 23, .10);
      --shadow2: 0 10px 30px rgba(2, 6, 23, .08);

      --radius: 16px;
      --radius2: 22px;
      --max: 1120px;
      --navH: 76px;
      /* approx header height for anchor offset */
    }

    * {
      box-sizing: border-box
    }

    html,
    body {
      height: 100%
    }

    html {
      scroll-behavior: smooth
    }

    body {
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
      color: var(--text);
      background: linear-gradient(180deg, var(--bg) 0%, var(--bg2) 60%, var(--bg) 100%);
      overflow-x: hidden;
    }

    a {
      color: inherit;
      text-decoration: none
    }

    .wrap {
      width: min(var(--max), calc(100% - 48px));
      margin: 0 auto
    }

    /* ====== STICKY NAV (always visible) ====== */
    header.top {
      position: sticky;
      top: 0;
      z-index: 9999;
      /* ensure on top of ALL sections */
      background: rgba(255, 255, 255, .85);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--line);
      box-shadow: 0 8px 22px rgba(2, 6, 23, .06);
    }

    .nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 16px 0;
      transition: padding .18s ease;
    }

    body.scrolled .nav {
      padding: 10px 0;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 900;
      letter-spacing: .2px;
      min-width: 240px;
    }

    .logo {
      width: 40px;
      height: 40px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 12px 26px rgba(37, 99, 235, .18);
      flex: 0 0 auto;
      position: relative;
      overflow: hidden;
    }

    .logo::after {
      content: "";
      position: absolute;
      inset: -55%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .35), transparent);
      transform: rotate(25deg);
      animation: sheen 3.8s ease-in-out infinite;
    }

    @keyframes sheen {

      0%,
      72% {
        translate: -52% 0
      }

      100% {
        translate: 52% 0
      }
    }

    .brand small {
      display: block;
      color: var(--muted);
      font-weight: 800;
      margin-top: 2px
    }

    .navlinks {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px;
      border-radius: 999px;
      border: 1px solid var(--line);
      background: rgba(248, 250, 252, .9);
    }

    .navlinks a {
      padding: 10px 12px;
      border-radius: 999px;
      color: rgba(15, 23, 42, .86);
      font-weight: 800;
      font-size: 13px;
      transition: background .15s ease, transform .15s ease, color .15s ease;
      white-space: nowrap;
    }

    .navlinks a:hover {
      background: rgba(37, 99, 235, .08);
      transform: translateY(-1px);
    }

    .navlinks a.active {
      background: rgba(37, 99, 235, .12);
      color: rgba(15, 23, 42, .95);
      border: 1px solid rgba(37, 99, 235, .22);
    }

    .actions {
      display: flex;
      gap: 10px;
      align-items: center;
      justify-content: flex-end;
      min-width: 260px;
    }

    .btn {
      border: 1px solid var(--line);
      background: #fff;
      color: var(--text);
      padding: 10px 14px;
      border-radius: 14px;
      font-weight: 850;
      font-size: 13px;
      cursor: pointer;
      transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      user-select: none;
      box-shadow: 0 6px 14px rgba(2, 6, 23, .06);
    }

    .btn:hover {
      transform: translateY(-1px);
      border-color: rgba(37, 99, 235, .30)
    }

    .btn.primary {
      border: 1px solid rgba(37, 99, 235, .35);
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      color: #fff;
      box-shadow: 0 16px 34px rgba(37, 99, 235, .18);
    }

    .btn.primary:hover {
      transform: translateY(-2px)
    }

    .icon {
      width: 18px;
      height: 18px;
      display: inline-block;
      border-radius: 7px;
      background: rgba(15, 23, 42, .08);
      border: 1px solid rgba(15, 23, 42, .10);
      position: relative;
    }

    .btn.primary .icon {
      background: rgba(255, 255, 255, .22);
      border-color: rgba(255, 255, 255, .28);
    }

    /* Anchor offset for sticky header */
    section,
    main {
      scroll-margin-top: calc(var(--navH) + 12px);
    }

    /* ====== HERO ====== */
    main.hero {
      padding: 38px 0 10px
    }

    .heroGrid {
      display: grid;
      grid-template-columns: 1.05fr .95fr;
      gap: 24px;
      align-items: stretch;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      border: 1px solid var(--line);
      background: rgba(248, 250, 252, .9);
      border-radius: 999px;
      color: var(--muted);
      font-weight: 800;
      font-size: 13px;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: linear-gradient(180deg, #22C55E, #16A34A);
      box-shadow: 0 0 0 6px rgba(34, 197, 94, .14);
    }

    h1 {
      font-size: clamp(34px, 4.1vw, 54px);
      line-height: 1.04;
      margin: 14px 0 10px;
      letter-spacing: -0.8px;
    }

    .accent {
      color: var(--brand)
    }

    .lead {
      color: var(--muted);
      font-size: clamp(16px, 1.6vw, 18px);
      line-height: 1.65;
      margin: 0 0 18px;
      max-width: 62ch;
      font-weight: 650;
    }

    .heroCTAs {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin: 16px 0 12px
    }

    .meta {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: center;
      color: var(--muted);
      font-weight: 750;
      font-size: 13px;
    }

    .meta .tag {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(22, 163, 74, .06);
      border: 1px solid rgba(22, 163, 74, .14);
      color: #0F5132;
    }

    /* Demo Card */
    .heroCard {
      border: 1px solid var(--line);
      background: var(--card);
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      padding: 18px;
      position: relative;
      overflow: hidden;
    }

    .miniHeader {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 12px;
      border-radius: 16px;
      border: 1px solid var(--line);
      background: rgba(248, 250, 252, .9);
      color: rgba(15, 23, 42, .84);
      font-weight: 900;
      font-size: 13px;
      margin-bottom: 14px;
    }

    .badge {
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(37, 99, 235, .10);
      border: 1px solid rgba(37, 99, 235, .22);
      color: rgba(30, 64, 175, .95);
      font-weight: 950;
      font-size: 12px;
      letter-spacing: .08em;
    }

    .mock {
      display: grid;
      gap: 12px
    }

    .tile {
      border: 1px solid var(--line);
      background: #fff;
      border-radius: var(--radius2);
      padding: 14px;
      box-shadow: var(--shadow2);
    }

    .tile h3 {
      margin: 0 0 6px;
      font-size: 14px;
      letter-spacing: .2px
    }

    .tile p {
      margin: 0;
      color: var(--muted);
      font-size: 13px;
      line-height: 1.55;
      font-weight: 650
    }

    .row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      color: var(--muted);
      font-weight: 850;
      font-size: 12px;
      margin-top: 10px
    }

    .bar {
      height: 10px;
      border-radius: 999px;
      background: rgba(2, 6, 23, .06);
      overflow: hidden;
      border: 1px solid rgba(2, 6, 23, .08);
      margin-top: 8px;
    }

    .bar>span {
      display: block;
      height: 100%;
      width: 72%;
      background: linear-gradient(90deg, var(--brand2), var(--brand));
    }

    /* ====== SECTIONS ====== */
    section {
      padding: 46px 0
    }

    .sectionHead {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 18px;
    }

    .kicker {
      color: var(--muted);
      font-weight: 950;
      letter-spacing: .18em;
      text-transform: uppercase;
      font-size: 12px;
    }

    h2 {
      margin: 6px 0 0;
      font-size: clamp(22px, 2.5vw, 34px);
      letter-spacing: -.45px;
      line-height: 1.15;
    }

    .sub {
      margin: 0;
      color: var(--muted);
      max-width: 60ch;
      line-height: 1.65;
      font-weight: 650;
    }

    .cards {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px
    }

    .card {
      grid-column: span 4;
      border: 1px solid var(--line);
      background: var(--card);
      border-radius: var(--radius2);
      box-shadow: var(--shadow2);
      padding: 18px;
      min-height: 170px;
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
      border-radius: 14px;
      background: rgba(37, 99, 235, .06);
      border: 1px solid rgba(37, 99, 235, .14);
      margin-bottom: 12px;
      color: rgba(30, 64, 175, .95);
      font-weight: 950;
      font-size: 12px;
      letter-spacing: .06em;
      text-transform: uppercase;
      width: fit-content;
    }

    .spark {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: linear-gradient(180deg, var(--brand2), var(--brand));
      box-shadow: 0 0 0 6px rgba(37, 99, 235, .10);
    }

    .card h3 {
      margin: 0 0 8px;
      font-size: 16px;
      letter-spacing: .1px
    }

    .card p {
      margin: 0;
      color: var(--muted);
      line-height: 1.65;
      font-weight: 650
    }

    /* How */
    .steps {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px
    }

    .step {
      grid-column: span 3;
      border: 1px solid var(--line);
      background: #fff;
      border-radius: var(--radius2);
      padding: 18px;
      box-shadow: var(--shadow2);
      min-height: 170px;
    }

    .num {
      width: 42px;
      height: 42px;
      border-radius: 16px;
      display: grid;
      place-items: center;
      font-weight: 950;
      background: rgba(37, 99, 235, .10);
      border: 1px solid rgba(37, 99, 235, .18);
      color: rgba(30, 64, 175, .95);
      margin-bottom: 12px;
    }

    .step h3 {
      margin: 0 0 8px
    }

    .step p {
      margin: 0;
      color: var(--muted);
      line-height: 1.65;
      font-weight: 650
    }

    /* Pricing */
    .pricing {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px
    }

    .price {
      grid-column: span 4;
      border: 1px solid var(--line);
      border-radius: var(--radius2);
      background: #fff;
      box-shadow: var(--shadow2);
      padding: 18px;
      overflow: hidden;
    }

    .price.featured {
      border-color: rgba(37, 99, 235, .35);
      box-shadow: 0 18px 50px rgba(37, 99, 235, .12);
      transform: translateY(-4px);
    }

    .tag {
      color: var(--muted);
      font-weight: 950;
      font-size: 12px;
      letter-spacing: .18em;
      text-transform: uppercase;
    }

    .money {
      font-size: 40px;
      font-weight: 950;
      letter-spacing: -.9px;
      margin: 10px 0 2px
    }

    .per {
      color: var(--muted);
      font-weight: 750;
      margin: 0 0 12px
    }

    .list {
      margin: 0 0 14px;
      padding: 0;
      list-style: none;
      display: grid;
      gap: 8px
    }

    .list li {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      color: var(--muted);
      font-weight: 700;
      line-height: 1.45
    }

    .tick {
      width: 18px;
      height: 18px;
      border-radius: 6px;
      background: rgba(22, 163, 74, .10);
      border: 1px solid rgba(22, 163, 74, .18);
      position: relative;
      flex: 0 0 auto;
      margin-top: 1px;
    }

    .tick::after {
      content: "";
      position: absolute;
      left: 5px;
      top: 3px;
      width: 6px;
      height: 9px;
      border-right: 2px solid #0F5132;
      border-bottom: 2px solid #0F5132;
      transform: rotate(35deg);
    }

    /* Testimonials */
    .testis {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px
    }

    .quote {
      grid-column: span 4;
      border: 1px solid var(--line);
      background: #fff;
      border-radius: var(--radius2);
      padding: 18px;
      box-shadow: var(--shadow2);
    }

    .quote p {
      margin: 0 0 12px;
      color: rgba(15, 23, 42, .92);
      font-weight: 700;
      line-height: 1.65
    }

    .who {
      display: flex;
      gap: 12px;
      align-items: center;
      color: var(--muted);
      font-weight: 900
    }

    .ava {
      width: 44px;
      height: 44px;
      border-radius: 18px;
      background: linear-gradient(180deg, rgba(37, 99, 235, .14), rgba(37, 99, 235, .05));
      border: 1px solid rgba(37, 99, 235, .16);
    }

    /* FAQ */
    .faq {
      border: 1px solid var(--line);
      background: #fff;
      border-radius: var(--radius2);
      overflow: hidden;
      box-shadow: var(--shadow2);
    }

    .qa {
      border-top: 1px solid var(--line)
    }

    .qa:first-child {
      border-top: none
    }

    .q {
      width: 100%;
      text-align: left;
      background: transparent;
      border: none;
      color: var(--text);
      padding: 16px 18px;
      font-weight: 950;
      font-size: 15px;
      display: flex;
      justify-content: space-between;
      gap: 14px;
      align-items: center;
      cursor: pointer;
    }

    .q span {
      color: var(--muted);
      font-weight: 950
    }

    .a {
      max-height: 0;
      overflow: hidden;
      transition: max-height .22s ease
    }

    .a p {
      margin: 0;
      padding: 0 18px 16px;
      color: var(--muted);
      line-height: 1.7;
      font-weight: 650
    }

    .qa.open .a {
      max-height: 220px
    }

    /* CTA + FORM */
    .cta {
      border: 1px solid var(--line);
      background: #fff;
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .ctaInner {
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
      padding: 18px;
      align-items: start;
    }

    .form {
      border: 1px solid var(--line);
      background: rgba(248, 250, 252, .9);
      border-radius: var(--radius2);
      padding: 16px;
    }

    label {
      display: block;
      font-size: 11px;
      color: rgba(71, 85, 105, .95);
      font-weight: 950;
      letter-spacing: .14em;
      text-transform: uppercase;
      margin: 12px 0 6px;
    }

    input,
    textarea,
    select {
      width: 100%;
      padding: 12px 12px;
      border-radius: 14px;
      border: 1px solid rgba(148, 163, 184, .45);
      background: #fff;
      color: var(--text);
      outline: none;
      font-weight: 700;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    input:focus,
    textarea:focus,
    select:focus {
      border-color: rgba(37, 99, 235, .45);
      box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
    }

    textarea {
      min-height: 96px;
      resize: vertical
    }

    .formRow {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px
    }

    .hint {
      margin: 10px 0 0;
      color: var(--muted);
      font-weight: 650;
      font-size: 13px;
      line-height: 1.5
    }

    .tiny {
      font-size: 12px;
      color: rgba(71, 85, 105, .9)
    }

    .err {
      color: #B91C1C;
      font-weight: 900;
      font-size: 12px;
      margin-top: 8px;
      display: none
    }

    .okmsg {
      color: #0F5132;
      font-weight: 900;
      font-size: 13px;
      display: none
    }

    /* Footer */
    footer {
      padding: 26px 0 42px;
      color: var(--muted)
    }

    .foot {
      border-top: 1px solid var(--line);
      padding-top: 18px;
      display: flex;
      justify-content: space-between;
      gap: 18px;
      flex-wrap: wrap;
      font-weight: 700;
    }

    .links {
      display: flex;
      gap: 14px;
      flex-wrap: wrap
    }

    .links a {
      opacity: .9
    }

    .links a:hover {
      opacity: 1;
      text-decoration: underline
    }

    /* Responsive */
    @media (max-width: 980px) {
      .heroGrid {
        grid-template-columns: 1fr;
      }

      .card {
        grid-column: span 6
      }

      .step {
        grid-column: span 6
      }

      .price {
        grid-column: span 6
      }

      .quote {
        grid-column: span 6
      }

      .ctaInner {
        grid-template-columns: 1fr
      }

      .actions {
        min-width: unset
      }

      .brand {
        min-width: unset
      }

      :root {
        --navH: 72px;
      }
    }

    @media (max-width: 700px) {
      .navlinks {
        display: none
      }

      .card,
      .step,
      .price,
      .quote {
        grid-column: span 12
      }

      .formRow {
        grid-template-columns: 1fr
      }

      .btn {
        width: 100%;
        justify-content: center
      }

      .actions {
        width: 100%
      }

      .actions .btn {
        flex: 1
      }

      :root {
        --navH: 68px;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      * {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
      }
    }

    #contacto .sub {
      max-width: 100%;
    }
  </style>
</head>

<body>
  <header class="top" role="banner">
    <div class="wrap">
      <nav class="nav" aria-label="Menú principal">
        <a class="brand" href="#inicio">
          <div class="logo" aria-hidden="true"><img src="https://elementi.me/wp-content/uploads/2020/08/Libro-de-reclamaciones-Azul-300x300.png" alt="img libro"></div>
          <div>
            <div style="font-size:14px; opacity:.98">Libro de Reclamaciones</div>
            <small>Digital • Profesional • Ordenado</small>
          </div>
        </a>

        <div class="navlinks" role="navigation" aria-label="Secciones">
          <a href="#beneficios" data-spy>Beneficios</a>
          <a href="#como-funciona" data-spy>Flujo</a>
          <!--<a href="#planes" data-spy>Planes</a>-->
          <a href="#faq" data-spy>FAQ</a>
          <a href="https://wa.me/51986067472?text=Hola,%20Me%20interesa%20el%20Libro%20de%20Reclamos" data-spy>Contacto</a>
        </div>

        <div class="actions">
          <a class="btn" href="https://demo.websigi.com">Ver demo</a>
          <a class="btn primary" href="https://wa.me/51986067472?text=Hola,%20Me%20interesa%20el%20Libro%20de%20Reclamos">Solicitar</a>
        </div>
      </nav>
    </div>
  </header>

  <main id="inicio" class="hero">
    <div class="wrap">
      <div class="heroGrid">
        <div>
          <div class="pill"><span class="dot" aria-hidden="true"></span>Experiencia moderna, comunicación clara</div>

          <h1>
            Tu <span class="accent">Libro de Reclamaciones</span> con diseño profesional
            para mejorar la atención.
          </h1>

          <p class="lead">
            Centraliza reclamos y quejas con un flujo ordenado: registro, seguimiento, evidencias y reportes.
            Diseñado para reducir fricción y elevar la confianza del cliente.
          </p>

          <div class="heroCTAs">
            <a class="btn primary" href="https://wa.me/51986067472?text=Hola,%20Me%20interesa%20el%20Libro%20de%20Reclamos">Quiero implementarlo</a>
            <!--<a class="btn" href="#planes">Ver planes</a>-->
            <a class="btn" href="#faq">Dudas</a>
          </div>

          <div class="meta">
            <span class="tag"><span class="icon" aria-hidden="true"></span>Notificaciones</span>
            <span class="tag"><span class="icon" aria-hidden="true"></span>Panel de casos</span>
            <span class="tag"><span class="icon" aria-hidden="true"></span>Exportación</span>
          </div>
        </div>

        <aside class="heroCard" id="demo" aria-label="Demo visual">
          <div class="miniHeader">
            <span>Vista previa • Panel de casos</span>
            <span class="badge">DEMO</span>
          </div>

          <div class="mock">
            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Nuevo registro</div>
              <h3>Formulario claro (menos de 2 minutos)</h3>
              <p>Campos esenciales, adjuntos y confirmación inmediata para el cliente.</p>
              <!--<div class="row"><span>Completado</span><span style="color:#0F5132">72%</span></div>
              <div class="bar" aria-hidden="true"><span></span></div>-->
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Seguimiento</div>
              <h3>Estados + trazabilidad</h3>
              <p>“Recibido → Resuelto”. Historial con fechas y responsables.</p>
              <div class="row"><span>Tiempo promedio</span><span style="color:#1E40AF">48h</span></div>
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Reportes</div>
              <h3>Decisiones con datos</h3>
              <p>Motivos, canales, tiempos y exportación a CSV/Excel para tu equipo.</p>
              <div class="row"><span>Indicador</span><span style="color:#0F172A">4.6/5</span></div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <section id="beneficios">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Beneficios</div>
          <h2>Más confianza para el cliente, más control para tu operación</h2>
        </div>
        <p class="sub">Una experiencia ordenada reduce fricción y mejora la percepción de tu marca.</p>
      </div>

      <div class="cards">
        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Experiencia</div>
          <h3>Interfaz clara y consistente</h3>
          <p>Diseño limpio con validación y confirmación. Menos abandono y datos mejor capturados.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Gestión</div>
          <h3>Seguimiento de punta a punta</h3>
          <p>Estados, responsables y evidencias para gestionar cada caso con trazabilidad completa.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Reportes</div>
          <h3>Indicadores para mejorar</h3>
          <p>Analiza causas y tiempos de respuesta. Exporta reportes para auditorías y reuniones.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="como-funciona">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Flujo</div>
          <h2>Un proceso simple que tu equipo adopta rápido</h2>
        </div>
        <p class="sub">Desde el registro hasta el cierre: trazabilidad y un panel fácil de usar.</p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="num">1</div>
          <h3>Registro</h3>
          <p>El cliente completa el formulario y adjunta evidencias (si aplica).</p>
        </div>
        <div class="step">
          <div class="num">2</div>
          <h3>Recepción</h3>
          <p>Se genera un caso con número de seguimiento y notificación automática.</p>
        </div>
        <div class="step">
          <div class="num">3</div>
          <h3>Gestión</h3>
          <p>Asignación a responsable, seguimiento interno, notas y cambios de estado.</p>
        </div>
        <div class="step">
          <div class="num">4</div>
          <h3>Cierre</h3>
          <p>Respuesta al cliente y cierre con registro final para reportes.</p>
        </div>
      </div>
    </div>
  </section>
  <!--
  <section id="planes">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Planes</div>
          <h2>Elige el nivel de gestión que necesitas</h2>
        </div>
        <p class="sub">Ajusta por volumen, sedes, usuarios e integraciones según tu operación.</p>
      </div>

      <div class="pricing">
        <div class="price">
          <div class="tag">Starter</div>
          <h3>Base sólida</h3>
          <div class="money">S/ 149</div>
          <p class="per">/ mes</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> 1 formulario embebible</li>
            <li><span class="tick" aria-hidden="true"></span> Panel básico de casos</li>
            <li><span class="tick" aria-hidden="true"></span> Exportación CSV</li>
            <li><span class="tick" aria-hidden="true"></span> 2 usuarios</li>
          </ul>
          <a class="btn" href="#contacto">Solicitar</a>
        </div>

        <div class="price featured">
          <div class="tag">Pro</div>
          <h3>Para atención al cliente</h3>
          <div class="money">S/ 299</div>
          <p class="per">/ mes</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> Multi-sede y canales</li>
            <li><span class="tick" aria-hidden="true"></span> Estados + SLA</li>
            <li><span class="tick" aria-hidden="true"></span> Reportes avanzados</li>
            <li><span class="tick" aria-hidden="true"></span> 10 usuarios</li>
          </ul>
          <a class="btn primary" href="#contacto">Elegir Pro</a>
        </div>

        <div class="price">
          <div class="tag">Enterprise</div>
          <h3>Operación avanzada</h3>
          <div class="money">A medida</div>
          <p class="per">Integraciones + roles</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> Roles y permisos</li>
            <li><span class="tick" aria-hidden="true"></span> Integración CRM/Helpdesk</li>
            <li><span class="tick" aria-hidden="true"></span> Auditoría + logs</li>
            <li><span class="tick" aria-hidden="true"></span> Soporte prioritario</li>
          </ul>
          <a class="btn" href="#contacto">Hablar con ventas</a>
        </div>
      </div>
    </div>
  </section>

  <section aria-label="Testimonios">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Confianza</div>
          <h2>Se nota cuando el proceso está ordenado</h2>
        </div>
        <p class="sub">Ejemplos típicos al profesionalizar el flujo de reclamos.</p>
      </div>

      <div class="testis">
        <div class="quote">
          <p>“Ahora respondemos más rápido y con historial completo. Menos fricción, más solución.”</p>
          <div class="who"><div class="ava" aria-hidden="true"></div> Jefa de Atención • Retail</div>
        </div>
        <div class="quote">
          <p>“El panel nos dio visibilidad de causas reales. Pudimos corregir procesos y medir mejoras.”</p>
          <div class="who"><div class="ava" aria-hidden="true"></div> Operaciones • Servicios</div>
        </div>
        <div class="quote">
          <p>“La interfaz se ve seria y el cliente siente que su caso sí está siendo atendido.”</p>
          <div class="who"><div class="ava" aria-hidden="true"></div> Gerencia • Salud</div>
        </div>
      </div>
    </div>
  </section>
-->
  <section id="faq">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">FAQ</div>
          <h2>Preguntas frecuentes</h2>
        </div>
        <p class="sub">Respuestas rápidas para avanzar sin fricción.</p>
      </div>

      <div class="faq" role="list">

        <div class="qa open" role="listitem">
          <button class="q" type="button">¿Cúanto tiempo demora integrar en mi web?<span aria-hidden="true">+</span></button>
          <div class="a">
            <p>El proceso de activación solo demora 10 minutos.</p>
          </div>
        </div>
        <div class="qa" role="listitem">
          <button class="q" type="button">¿Incluye número de seguimiento?<span aria-hidden="true">+</span></button>
          <div class="a">
            <p>Incluye número de caso y confirmación. La notificación por correo al cliente y personal de tu empresa.</p>
          </div>
        </div>
        <div class="qa" role="listitem">
          <button class="q" type="button">¿Puedo exportar reportes?<span aria-hidden="true">+</span></button>
          <div class="a">
            <p>Sí. CSV/Excel y reportes con filtros por fechas, sede, tipo y estado.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="contacto" aria-label="Contacto">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Implementación</div>
          <h2>Pídelo listo para usar</h2>
        </div>
        <p class="sub">Déjanos tus datos y te enviamos una propuesta y demo personalizada.</p>
      </div>
      <div class="cta">
        <div class="ctaInner">
          <div>
            <div class="pill" style="margin-bottom:12px"><span class="dot" aria-hidden="true"></span>Respuesta rápida • Soporte humano</div>
            <h3 style="margin:0 0 10px; font-size:20px; letter-spacing:-.2px">Activa tu Libro de Reclamaciones digital</h3>
            <p class="sub" style="margin:0 0 14px">
              Ideal para web, sedes físicas (QR) y equipos que necesitan trazabilidad real.
            </p>

            <div class="cards" style="margin-top: 14px">
              <div class="card" style="grid-column: span 6; min-height: unset;">
                <div class="chip"><span class="spark" aria-hidden="true"></span> Incluye</div>
                <p>Formulario + panel + estados + reportes + exportación + personalización.</p>
              </div>
              <div class="card" style="grid-column: span 6; min-height: unset;">
                <div class="chip"><span class="spark" aria-hidden="true"></span> Opcional</div>
                <p>Integración, roles avanzados, multi-sede, automatizaciones.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="wrap">
      <div class="foot">
        <div style="display:flex; gap:12px; align-items:center">
          <div class="logo" aria-hidden="true" style="width:34px; height:34px; border-radius:14px"></div>
          <div>
            <div style="font-weight:950; color: var(--text)">Libro de Reclamaciones</div>
            <div style="font-size:12px">© <span id="year"></span> • Atención al cliente profesional</div>
          </div>
        </div>

        <div class="links" aria-label="Enlaces">
          <a href="#beneficios">Beneficios</a>
          <a href="#como-funciona">Flujo</a>
          <!--<a href="#planes">Planes</a>-->
          <a href="#contacto">Contacto</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Sticky menu: se mantiene siempre; además compacta un poco al hacer scroll
    const setScrolled = () => document.body.classList.toggle('scrolled', window.scrollY > 8);
    window.addEventListener('scroll', setScrolled, {
      passive: true
    });
    setScrolled();

    // FAQ accordion
    document.querySelectorAll('.qa .q').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.qa');
        const wasOpen = item.classList.contains('open');
        document.querySelectorAll('.qa').forEach(x => x.classList.remove('open'));
        if (!wasOpen) item.classList.add('open');
      });
    });

    // Scroll spy para resaltar menú
    const links = Array.from(document.querySelectorAll('[data-spy]'));
    const sections = links.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          links.forEach(a => a.classList.remove('active'));
          const active = links.find(a => a.getAttribute('href') === `#${entry.target.id}`);
          if (active) active.classList.add('active');
        }
      });
    }, {
      rootMargin: "-45% 0px -50% 0px",
      threshold: 0.01
    });

    sections.forEach(s => io.observe(s));

    // Demo form validation (sin envío real)
    const form = document.getElementById('leadForm');
    const err = document.getElementById('errMsg');
    const ok = document.getElementById('okMsg');

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      err.style.display = 'none';
      ok.style.display = 'none';

      const name = document.getElementById('name').value.trim();
      const company = document.getElementById('company').value.trim();
      const email = document.getElementById('email').value.trim();
      const size = document.getElementById('size').value;

      const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

      if (!name || !company || !emailOk || !size) {
        err.style.display = 'block';
        return;
      }

      ok.style.display = 'block';
      form.reset();
    });

    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>

</html>