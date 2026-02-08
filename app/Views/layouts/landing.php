<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Libro de Reclamaciones | Atención profesional</title>
  <meta name="description" content="Landing profesional para Libro de Reclamaciones: formulario, seguimiento, reportes, FAQ y CTA." />

  <style>
    :root{
      --bg0:#05070F;
      --bg1:#070A14;
      --card:#0C1224cc;
      --card2:#0A1021cc;
      --text:#EAF0FF;
      --muted:#A7B2D6;
      --line:#22305C;

      --brand1:#6D28D9; /* morado */
      --brand2:#06B6D4; /* cian */
      --brand3:#F472B6; /* rosa */

      --ok:#34D399;
      --shadow: 0 26px 70px rgba(0,0,0,.55);
      --radius: 18px;
      --radius2: 26px;
      --max: 1120px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    html{scroll-behavior:smooth}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      background:
        radial-gradient(1100px 680px at 16% 12%, rgba(109,40,217,.26), transparent 56%),
        radial-gradient(900px 620px at 86% 16%, rgba(6,182,212,.20), transparent 56%),
        radial-gradient(800px 620px at 50% 92%, rgba(244,114,182,.12), transparent 56%),
        linear-gradient(180deg, var(--bg0) 0%, var(--bg1) 45%, var(--bg0) 100%);
      overflow-x:hidden;
    }

    a{color:inherit; text-decoration:none}
    .wrap{width: min(var(--max), calc(100% - 48px)); margin:0 auto}

    /* NAV (sticky + compact on scroll) */
    header.top{
      position: sticky; top: 0; z-index: 80;
      background: rgba(5,7,15,.55);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(34,48,92,.45);
    }
    .nav{
      display:flex; align-items:center; justify-content:space-between;
      gap:16px;
      padding: 16px 0;
      transition: padding .18s ease;
    }
    body.scrolled .nav{padding: 10px 0;}

    .brand{
      display:flex; align-items:center; gap:12px;
      font-weight:900; letter-spacing:.2px;
      min-width: 220px;
    }
    .logo{
      width:40px; height:40px; border-radius:14px;
      background: conic-gradient(from 180deg, var(--brand2), var(--brand1), var(--brand3), var(--brand2));
      box-shadow: 0 14px 34px rgba(109,40,217,.25), 0 14px 34px rgba(6,182,212,.15);
      position:relative;
      overflow:hidden;
      flex: 0 0 auto;
    }
    .logo::after{
      content:"";
      position:absolute; inset:-55%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.18), transparent);
      transform: rotate(25deg);
      animation: sheen 3.6s ease-in-out infinite;
    }
    @keyframes sheen{ 0%, 72%{translate:-52% 0} 100%{translate:52% 0} }

    .brand small{display:block; color:var(--muted); font-weight:800; margin-top:2px}

    .navlinks{
      display:flex; align-items:center; gap:8px;
      padding: 6px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
      box-shadow: 0 12px 30px rgba(0,0,0,.25);
    }
    .navlinks a{
      padding: 10px 12px;
      border-radius: 999px;
      color: rgba(234,240,255,.88);
      font-weight: 800;
      font-size: 13px;
      transition: background .15s ease, color .15s ease, transform .15s ease;
      white-space: nowrap;
    }
    .navlinks a:hover{background: rgba(255,255,255,.07); transform: translateY(-1px)}
    .navlinks a.active{
      background: linear-gradient(90deg, rgba(6,182,212,.22), rgba(109,40,217,.18));
      border: 1px solid rgba(6,182,212,.20);
    }

    .actions{display:flex; gap:10px; align-items:center; justify-content:flex-end; min-width: 260px;}
    .btn{
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      color: var(--text);
      padding: 10px 14px;
      border-radius: 14px;
      font-weight: 850;
      font-size: 13px;
      cursor:pointer;
      transition: transform .15s ease, background .15s ease, border-color .15s ease, box-shadow .15s ease;
      display:inline-flex; align-items:center; gap:10px;
      user-select:none;
    }
    .btn:hover{transform: translateY(-1px); background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.22)}
    .btn.primary{
      border:none;
      background: linear-gradient(90deg, var(--brand1), var(--brand2));
      box-shadow: 0 18px 46px rgba(109,40,217,.25), 0 18px 46px rgba(6,182,212,.16);
    }
    .btn.primary:hover{transform: translateY(-2px)}
    .icon{
      width:18px; height:18px; display:inline-block; border-radius:7px;
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.12);
      position:relative;
    }
    .icon::after{
      content:"";
      position:absolute; inset:4px;
      border-radius:5px;
      background: linear-gradient(180deg, rgba(255,255,255,.35), rgba(255,255,255,.05));
    }

    /* HERO */
    main.hero{padding: 42px 0 16px; position:relative;}
    .heroGrid{
      display:grid;
      grid-template-columns: 1.08fr .92fr;
      gap: 26px;
      align-items: stretch;
    }
    .pill{
      display:inline-flex; align-items:center; gap:10px;
      padding: 8px 12px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.05);
      border-radius: 999px;
      color: var(--muted);
      font-weight:850;
      font-size: 13px;
    }
    .dot{
      width:10px; height:10px; border-radius:999px;
      background: linear-gradient(180deg, var(--ok), #16A34A);
      box-shadow: 0 0 0 6px rgba(52,211,153,.10);
    }
    h1{
      font-size: clamp(34px, 4.2vw, 56px);
      line-height: 1.02;
      margin: 14px 0 10px;
      letter-spacing: -0.7px;
    }
    .grad{
      background: linear-gradient(90deg, #ffffff, rgba(255,255,255,.78));
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
    }
    .glow{
      background: linear-gradient(90deg, var(--brand2), var(--brand1));
      -webkit-background-clip:text; background-clip:text; color:transparent;
      text-shadow: 0 0 36px rgba(109,40,217,.22);
    }
    .lead{
      color: var(--muted);
      font-size: clamp(16px, 1.6vw, 18px);
      line-height: 1.6;
      margin: 0 0 18px;
      max-width: 62ch;
      font-weight: 650;
    }
    .heroCTAs{display:flex; gap:12px; flex-wrap:wrap; margin: 16px 0 12px}
    .meta{
      display:flex; gap:10px; flex-wrap:wrap; align-items:center;
      color: var(--muted); font-weight: 750; font-size: 13px;
    }
    .meta .tag{
      display:inline-flex; align-items:center; gap:10px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(52,211,153,.08);
      border: 1px solid rgba(52,211,153,.16);
      color:#CFFBEA;
    }

    /* Demo Card */
    .heroCard{
      border: 1px solid rgba(255,255,255,.12);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      padding: 18px;
      position:relative;
      overflow:hidden;
    }
    .heroCard::before{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(620px 260px at 22% 0%, rgba(6,182,212,.14), transparent 60%),
        radial-gradient(520px 260px at 85% 0%, rgba(109,40,217,.18), transparent 60%),
        radial-gradient(520px 380px at 60% 110%, rgba(244,114,182,.10), transparent 65%);
      pointer-events:none;
    }
    .miniHeader{
      position:relative;
      display:flex; align-items:center; justify-content:space-between;
      padding: 10px 12px;
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(10,14,30,.55);
      color: rgba(234,240,255,.82);
      font-weight:850;
      font-size: 13px;
      margin-bottom: 14px;
    }
    .badge{
      padding:6px 10px; border-radius:999px;
      background: rgba(6,182,212,.10);
      border: 1px solid rgba(6,182,212,.22);
      color: #BFF7FF;
      font-weight: 900;
      font-size: 12px;
      letter-spacing:.08em;
    }
    .mock{position:relative; display:grid; gap:12px}
    .tile{
      border:1px solid rgba(255,255,255,.10);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 14px;
      position:relative;
      overflow:hidden;
    }
    .tile h3{margin:0 0 6px; font-size:14px; letter-spacing:.2px}
    .tile p{margin:0; color:var(--muted); font-size:13px; line-height:1.5; font-weight:650}
    .row{display:flex; align-items:center; justify-content:space-between; gap:12px; color: var(--muted); font-weight:800; font-size:12px; margin-top:10px}
    .bar{
      height: 10px; border-radius: 999px;
      background: rgba(255,255,255,.08);
      overflow:hidden; border: 1px solid rgba(255,255,255,.10);
      margin-top: 8px;
    }
    .bar > span{
      display:block; height:100%;
      width: 72%;
      background: linear-gradient(90deg, var(--brand2), var(--brand1));
    }

    /* Sections */
    section{padding: 46px 0}
    .sectionHead{
      display:flex; align-items:flex-end; justify-content:space-between; gap:18px;
      margin-bottom: 18px;
    }
    .kicker{
      color: var(--muted);
      font-weight: 900;
      letter-spacing:.18em;
      text-transform: uppercase;
      font-size: 12px;
    }
    h2{
      margin:6px 0 0;
      font-size: clamp(22px, 2.5vw, 34px);
      letter-spacing: -.45px;
      line-height:1.15;
    }
    .sub{
      margin:0;
      color: var(--muted);
      max-width: 60ch;
      line-height:1.6;
      font-weight:650;
    }

    .cards{display:grid; grid-template-columns: repeat(12, 1fr); gap: 14px}
    .card{
      grid-column: span 4;
      border: 1px solid rgba(255,255,255,.12);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      border-radius: var(--radius2);
      box-shadow: 0 18px 45px rgba(0,0,0,.35);
      padding: 18px;
      position:relative;
      overflow:hidden;
      min-height: 170px;
    }
    .card::before{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(450px 220px at 20% 0%, rgba(6,182,212,.10), transparent 60%),
        radial-gradient(420px 240px at 85% 0%, rgba(109,40,217,.12), transparent 60%);
      pointer-events:none;
    }
    .chip{
      position:relative;
      display:inline-flex; align-items:center; gap:10px;
      padding: 8px 10px;
      border-radius: 14px;
      background: rgba(10,14,30,.50);
      border:1px solid rgba(255,255,255,.10);
      margin-bottom: 12px;
      color: var(--muted);
      font-weight:900;
      font-size:12px;
      width: fit-content;
      letter-spacing:.06em;
      text-transform: uppercase;
    }
    .spark{
      width:10px; height:10px; border-radius:999px;
      background: linear-gradient(180deg, var(--brand2), var(--brand1));
      box-shadow: 0 0 0 6px rgba(6,182,212,.08);
    }
    .card h3{position:relative; margin:0 0 8px; font-size:16px; letter-spacing:.1px}
    .card p{position:relative; margin:0; color:var(--muted); line-height:1.6; font-weight:650}

    /* How */
    .steps{display:grid; grid-template-columns: repeat(12, 1fr); gap: 14px}
    .step{
      grid-column: span 3;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 18px;
      position:relative;
      min-height: 170px;
    }
    .num{
      width:42px; height:42px; border-radius: 16px;
      display:grid; place-items:center;
      font-weight: 950;
      background: linear-gradient(90deg, rgba(6,182,212,.18), rgba(109,40,217,.18));
      border:1px solid rgba(255,255,255,.14);
      margin-bottom: 12px;
    }
    .step h3{margin:0 0 8px}
    .step p{margin:0; color:var(--muted); line-height:1.6; font-weight:650}

    /* Pricing */
    .pricing{display:grid; grid-template-columns: repeat(12, 1fr); gap: 14px}
    .price{
      grid-column: span 4;
      border: 1px solid rgba(255,255,255,.12);
      border-radius: var(--radius2);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      box-shadow: var(--shadow);
      padding: 18px;
      position:relative;
      overflow:hidden;
    }
    .price.featured{
      border-color: rgba(6,182,212,.30);
      box-shadow: 0 28px 70px rgba(6,182,212,.10), 0 28px 70px rgba(109,40,217,.10);
      transform: translateY(-4px);
    }
    .tag{
      color: var(--muted);
      font-weight: 900;
      font-size: 12px;
      letter-spacing:.18em;
      text-transform: uppercase;
    }
    .money{font-size: 40px; font-weight: 950; letter-spacing:-.9px; margin: 10px 0 2px}
    .per{color:var(--muted); font-weight:750; margin:0 0 12px}
    .list{margin: 0 0 14px; padding: 0; list-style:none; display:grid; gap:8px}
    .list li{display:flex; gap:10px; align-items:flex-start; color: var(--muted); font-weight:700; line-height:1.45}
    .tick{
      width:18px; height:18px; border-radius:6px;
      background: rgba(52,211,153,.14);
      border:1px solid rgba(52,211,153,.24);
      position:relative; flex:0 0 auto; margin-top:1px;
    }
    .tick::after{
      content:"";
      position:absolute;
      left:5px; top:3px;
      width:6px; height:9px;
      border-right:2px solid #BFFBEA;
      border-bottom:2px solid #BFFBEA;
      transform: rotate(35deg);
    }

    /* Testimonials */
    .testis{display:grid; grid-template-columns: repeat(12,1fr); gap:14px}
    .quote{
      grid-column: span 4;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 18px;
    }
    .quote p{margin:0 0 12px; color: rgba(234,240,255,.95); font-weight:700; line-height:1.6}
    .who{display:flex; gap:12px; align-items:center; color: var(--muted); font-weight:900}
    .ava{
      width:44px; height:44px; border-radius: 18px;
      background: linear-gradient(180deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
      border:1px solid rgba(255,255,255,.12);
      box-shadow: 0 18px 40px rgba(0,0,0,.35);
    }

    /* FAQ */
    .faq{
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      overflow:hidden;
    }
    .qa{border-top: 1px solid rgba(34,48,92,.55)}
    .qa:first-child{border-top:none}
    .q{
      width:100%;
      text-align:left;
      background: transparent;
      border: none;
      color: var(--text);
      padding: 16px 18px;
      font-weight: 950;
      font-size: 15px;
      display:flex; justify-content:space-between; gap:14px; align-items:center;
      cursor:pointer;
    }
    .q span{color: var(--muted); font-weight: 950}
    .a{max-height: 0; overflow:hidden; transition: max-height .22s ease}
    .a p{margin:0; padding: 0 18px 16px; color: var(--muted); line-height:1.65; font-weight:650}
    .qa.open .a{max-height: 220px}

    /* CTA + FORM */
    .cta{
      border: 1px solid rgba(255,255,255,.12);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      overflow:hidden;
      position:relative;
    }
    .cta::before{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(650px 260px at 18% 0%, rgba(6,182,212,.14), transparent 60%),
        radial-gradient(520px 260px at 85% 0%, rgba(109,40,217,.18), transparent 60%),
        radial-gradient(520px 380px at 60% 110%, rgba(244,114,182,.10), transparent 65%);
      pointer-events:none;
    }
    .ctaInner{
      position:relative;
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      padding: 18px;
      align-items: start;
    }
    .form{
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 16px;
    }
    label{
      display:block;
      font-size:11px;
      color:rgba(167,178,214,.95);
      font-weight: 950;
      letter-spacing:.14em;
      text-transform:uppercase;
      margin: 12px 0 6px;
    }
    input, textarea, select{
      width:100%;
      padding: 12px 12px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      color: var(--text);
      outline:none;
      font-weight:700;
      transition: border-color .15s ease, background .15s ease;
    }
    input:focus, textarea:focus, select:focus{
      border-color: rgba(6,182,212,.32);
      background: rgba(255,255,255,.09);
    }
    textarea{min-height: 96px; resize: vertical}
    .formRow{display:grid; grid-template-columns: 1fr 1fr; gap: 12px}
    .hint{margin:10px 0 0; color: var(--muted); font-weight:650; font-size:13px; line-height:1.45}
    .tiny{font-size:12px; color: rgba(167,178,214,.9)}
    .err{color: #FECACA; font-weight:900; font-size: 12px; margin-top: 8px; display:none}
    .okmsg{color:#CFFBEA; font-weight:900; font-size: 13px; display:none}

    /* Footer */
    footer{padding: 26px 0 42px; color: rgba(167,178,214,.92)}
    .foot{
      border-top: 1px solid rgba(34,48,92,.45);
      padding-top: 18px;
      display:flex; justify-content:space-between; gap: 18px; flex-wrap:wrap;
      font-weight:700;
    }
    .links{display:flex; gap:14px; flex-wrap:wrap}
    .links a{opacity:.9}
    .links a:hover{opacity:1; text-decoration: underline}

    /* Responsive */
    @media (max-width: 980px){
      .heroGrid{grid-template-columns: 1fr;}
      .card{grid-column: span 6}
      .step{grid-column: span 6}
      .price{grid-column: span 6}
      .quote{grid-column: span 6}
      .ctaInner{grid-template-columns: 1fr}
      .actions{min-width: unset}
      .brand{min-width: unset}
    }
    @media (max-width: 700px){
      .navlinks{display:none}
      .card,.step,.price,.quote{grid-column: span 12}
      .formRow{grid-template-columns: 1fr}
      .btn{width:100%; justify-content:center}
      .actions{width:100%}
      .actions .btn{flex:1}
    }
    @media (prefers-reduced-motion: reduce){
      *{animation:none !important; transition:none !important; scroll-behavior:auto !important;}
    }
  </style>
</head>

<body>
  <header class="top" role="banner">
    <div class="wrap">
      <nav class="nav" aria-label="Menú principal">
        <a class="brand" href="#inicio">
          <div class="logo" aria-hidden="true"></div>
          <div>
            <div style="font-size:14px; opacity:.95">Libro de Reclamaciones</div>
            <small>Digital • Profesional • Ordenado</small>
          </div>
        </a>

        <div class="navlinks" role="navigation" aria-label="Secciones">
          <a href="#beneficios" data-spy>Beneficios</a>
          <a href="#como-funciona" data-spy>Flujo</a>
          <a href="#planes" data-spy>Planes</a>
          <a href="#faq" data-spy>FAQ</a>
          <a href="#contacto" data-spy>Contacto</a>
        </div>

        <div class="actions">
          <a class="btn" href="#demo"><span class="icon" aria-hidden="true"></span>Ver demo</a>
          <a class="btn primary" href="#contacto"><span class="icon" aria-hidden="true"></span>Solicitar</a>
        </div>
      </nav>
    </div>
  </header>

  <main id="inicio" class="hero">
    <div class="wrap">
      <div class="heroGrid">
        <div>
          <div class="pill"><span class="dot" aria-hidden="true"></span>Implementación rápida + experiencia premium</div>

          <h1>
            Un <span class="glow">Libro de Reclamaciones</span> con look profesional:
            <span class="grad">cumple, ordena y mejora tu atención</span>.
          </h1>

          <p class="lead">
            Registra reclamos y quejas con un flujo claro: recepción, seguimiento, evidencias y reportes.
            Una interfaz moderna que genera confianza (y reduce fricción al cliente).
          </p>

          <div class="heroCTAs">
            <a class="btn primary" href="#contacto"><span class="icon" aria-hidden="true"></span>Quiero implementarlo</a>
            <a class="btn" href="#planes"><span class="icon" aria-hidden="true"></span>Ver planes</a>
            <a class="btn" href="#faq"><span class="icon" aria-hidden="true"></span>Dudas</a>
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
              <div class="row"><span>Completado</span><span style="color:#CFFBEA">72%</span></div>
              <div class="bar" aria-hidden="true"><span></span></div>
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Seguimiento</div>
              <h3>Estados + trazabilidad</h3>
              <p>“Recibido → En evaluación → Resuelto”. Historial con fechas y responsables.</p>
              <div class="row"><span>Tiempo promedio</span><span style="color:#BFF7FF">48h</span></div>
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Reportes</div>
              <h3>Decisiones con datos</h3>
              <p>Motivos, canales, tiempos y exportación a CSV/Excel para tu equipo.</p>
              <div class="row"><span>Indicador</span><span style="color:#FDE68A">4.6/5</span></div>
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
          <h3>Interfaz limpia y profesional</h3>
          <p>Diseño claro, validación y confirmación inmediata. Menos abandono, más registros correctos.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Gestión</div>
          <h3>Seguimiento de punta a punta</h3>
          <p>Estados, responsables y evidencias para gestionar sin perder información ni contexto.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Reportes</div>
          <h3>Indicadores para mejorar</h3>
          <p>Analiza causas y tiempos de respuesta. Exporta reportes para reuniones y auditorías.</p>
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
        <p class="sub">Desde el registro hasta el cierre: trazabilidad completa y un panel fácil de usar.</p>
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

  <section id="planes">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Planes</div>
          <h2>Elige el nivel de gestión que necesitas</h2>
        </div>
        <p class="sub">Puedes ajustar volumen, usuarios, sedes e integraciones según tu operación.</p>
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
          <a class="btn" href="#contacto"><span class="icon" aria-hidden="true"></span>Solicitar</a>
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
          <a class="btn primary" href="#contacto"><span class="icon" aria-hidden="true"></span>Elegir Pro</a>
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
          <a class="btn" href="#contacto"><span class="icon" aria-hidden="true"></span>Hablar con ventas</a>
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
        <p class="sub">Ejemplos de mensajes típicos al profesionalizar el flujo de reclamos.</p>
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
          <button class="q" type="button">¿El menú se queda fijo al hacer scroll?<span aria-hidden="true">+</span></button>
          <div class="a"><p>Sí. El header es <strong>sticky</strong> y además se compacta al hacer scroll para no ocupar espacio.</p></div>
        </div>
        <div class="qa" role="listitem">
          <button class="q" type="button">¿Se puede incrustar en mi web?<span aria-hidden="true">+</span></button>
          <div class="a"><p>Sí. Puedes embeber el formulario como widget o usar una página propia con tu dominio.</p></div>
        </div>
        <div class="qa" role="listitem">
          <button class="q" type="button">¿Incluye número de seguimiento?<span aria-hidden="true">+</span></button>
          <div class="a"><p>Incluye número de caso y confirmación. La notificación por correo se activa al conectarlo a tu backend.</p></div>
        </div>
        <div class="qa" role="listitem">
          <button class="q" type="button">¿Puedo exportar reportes?<span aria-hidden="true">+</span></button>
          <div class="a"><p>Sí. CSV/Excel y reportes con filtros por fechas, sede, tipo y estado.</p></div>
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
              Ideal para web, sedes físicas (QR) y equipos que necesitan trazabilidad real. También podemos integrarlo con tu correo/CRM/Helpdesk.
            </p>

            <div class="cards" style="margin-top: 14px">
              <div class="card" style="grid-column: span 6; min-height: unset;">
                <div class="chip"><span class="spark" aria-hidden="true"></span> Incluye</div>
                <p>Formulario + panel + estados + reportes + exportación + personalización.</p>
              </div>
              <div class="card" style="grid-column: span 6; min-height: unset;">
                <div class="chip"><span class="spark" aria-hidden="true"></span> Opcional</div>
                <p>Integración, roles avanzados, SLA, multi-sede, automatizaciones.</p>
              </div>
            </div>
          </div>

          <form class="form" id="leadForm" novalidate>
            <div class="formRow">
              <div>
                <label for="name">Nombre</label>
                <input id="name" name="name" placeholder="Tu nombre" autocomplete="name" required />
              </div>
              <div>
                <label for="company">Empresa</label>
                <input id="company" name="company" placeholder="Nombre de tu negocio" required />
              </div>
            </div>

            <div class="formRow">
              <div>
                <label for="email">Correo</label>
                <input id="email" name="email" placeholder="tucorreo@empresa.com" type="email" autocomplete="email" required />
              </div>
              <div>
                <label for="size">Tamaño</label>
                <select id="size" name="size" required>
                  <option value="" selected disabled>Selecciona…</option>
                  <option>1 sede / pequeño</option>
                  <option>2–5 sedes</option>
                  <option>6+ sedes</option>
                </select>
              </div>
            </div>

            <label for="message">Qué necesitas</label>
            <textarea id="message" name="message" placeholder="Ej: Quiero libro digital con QR + reportes mensuales y 5 usuarios."></textarea>

            <button class="btn primary" type="submit" style="width:100%; margin-top: 12px">
              <span class="icon" aria-hidden="true"></span>
              Enviar solicitud
            </button>

            <div class="err" id="errMsg">Revisa los campos obligatorios (nombre, empresa, correo y tamaño).</div>
            <div class="okmsg" id="okMsg">¡Listo! Hemos registrado tu solicitud (demo). Puedes conectarlo a tu backend.</div>

            <p class="hint tiny">
              Al enviar, aceptas ser contactado para fines de implementación. *Este formulario es demo (sin envío real).
            </p>
          </form>
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
          <a href="#planes">Planes</a>
          <a href="#contacto">Contacto</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Compacta el header al hacer scroll
    const setScrolled = () => {
      document.body.classList.toggle('scrolled', window.scrollY > 8);
    };
    window.addEventListener('scroll', setScrolled, {passive:true});
    setScrolled();

    // FAQ accordion
    document.querySelectorAll('.qa .q').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.qa');
        const wasOpen = item.classList.contains('open');
        document.querySelectorAll('.qa').forEach(x => x.classList.remove('open'));
        if(!wasOpen) item.classList.add('open');
      });
    });

    // Scroll spy (marca la sección activa en el menú)
    const links = Array.from(document.querySelectorAll('[data-spy]'));
    const sections = links
      .map(a => document.querySelector(a.getAttribute('href')))
      .filter(Boolean);

    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting){
          links.forEach(a => a.classList.remove('active'));
          const active = links.find(a => a.getAttribute('href') === `#${entry.target.id}`);
          if(active) active.classList.add('active');
        }
      });
    }, { rootMargin: "-45% 0px -50% 0px", threshold: 0.01 });

    sections.forEach(s => io.observe(s));

    // Demo form validation (no real submit)
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

      if(!name || !company || !emailOk || !size){
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
