<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Libro de Reclamaciones | Cumple, atiende y convierte</title>
  <meta name="description" content="Landing page para Libro de Reclamaciones: digital, fácil, rápido y alineado a tu atención al cliente. Incluye formulario, FAQ, testimonios y CTA." />

  <style>
    :root{
      --bg:#070A12;
      --card:#0E1326cc;
      --card2:#0B1022cc;
      --text:#EAF0FF;
      --muted:#A9B4D6;
      --line:#23305F;
      --brand1:#7C3AED;
      --brand2:#22D3EE;
      --brand3:#F472B6;
      --ok:#34D399;
      --warn:#FBBF24;
      --shadow: 0 25px 60px rgba(0,0,0,.55);
      --radius: 20px;
      --radius2: 28px;
      --max: 1120px;
    }

    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      color:var(--text);
      background:
        radial-gradient(1200px 700px at 15% 12%, rgba(124,58,237,.35), transparent 55%),
        radial-gradient(900px 650px at 85% 20%, rgba(34,211,238,.25), transparent 55%),
        radial-gradient(900px 700px at 40% 90%, rgba(244,114,182,.18), transparent 55%),
        linear-gradient(180deg, #050614 0%, #070A12 40%, #050614 100%);
      overflow-x:hidden;
    }

    a{color:inherit; text-decoration:none}
    .wrap{width: min(var(--max), calc(100% - 48px)); margin:0 auto}
    .top{
      position:sticky; top:0; z-index:50;
      background: linear-gradient(180deg, rgba(5,6,20,.85), rgba(5,6,20,.55));
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(35,48,95,.35);
    }
    .nav{
      display:flex; align-items:center; justify-content:space-between;
      padding: 14px 0;
      gap:16px;
    }
    .brand{
      display:flex; align-items:center; gap:12px;
      font-weight:800; letter-spacing:.2px;
    }
    .logo{
      width:40px; height:40px; border-radius:14px;
      background: conic-gradient(from 180deg, var(--brand2), var(--brand1), var(--brand3), var(--brand2));
      box-shadow: 0 10px 25px rgba(124,58,237,.25), 0 10px 25px rgba(34,211,238,.15);
      position:relative;
      overflow:hidden;
    }
    .logo::after{
      content:"";
      position:absolute; inset:-40%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
      transform: rotate(25deg);
      animation: sheen 3.2s ease-in-out infinite;
    }
    @keyframes sheen{ 0%, 70%{translate:-50% 0} 100%{translate:50% 0} }

    .navlinks{display:flex; align-items:center; gap:18px; color:var(--muted); font-weight:600; font-size:14px}
    .navlinks a{padding:10px 10px; border-radius:12px}
    .navlinks a:hover{background: rgba(255,255,255,.06); color: var(--text)}
    .actions{display:flex; gap:10px; align-items:center}
    .btn{
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      color: var(--text);
      padding: 10px 14px;
      border-radius: 14px;
      font-weight:700;
      font-size:14px;
      cursor:pointer;
      transition: transform .15s ease, background .15s ease, border-color .15s ease;
      display:inline-flex; align-items:center; gap:10px;
      user-select:none;
    }
    .btn:hover{transform: translateY(-1px); background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.20)}
    .btn.primary{
      border:none;
      background: linear-gradient(90deg, var(--brand1), var(--brand2));
      box-shadow: 0 18px 40px rgba(124,58,237,.28), 0 18px 40px rgba(34,211,238,.18);
    }
    .btn.primary:hover{transform: translateY(-2px) scale(1.01)}
    .pill{
      display:inline-flex; align-items:center; gap:10px;
      padding: 8px 12px;
      border: 1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      border-radius: 999px;
      color: var(--muted);
      font-weight:700;
      font-size:13px;
    }
    .dot{
      width:10px; height:10px; border-radius:999px;
      background: radial-gradient(circle at 30% 30%, #fff, rgba(255,255,255,.15) 35%, transparent 65%),
                  linear-gradient(180deg, var(--ok), #16A34A);
      box-shadow: 0 0 0 6px rgba(52,211,153,.10);
    }

    /* HERO */
    .hero{
      padding: 42px 0 18px;
      position:relative;
    }
    .grid{
      display:grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 28px;
      align-items: stretch;
    }
    .h1{
      font-size: clamp(34px, 4.2vw, 56px);
      line-height: 1.02;
      margin: 14px 0 10px;
      letter-spacing: -0.6px;
    }
    .lead{
      color: var(--muted);
      font-size: clamp(16px, 1.6vw, 18px);
      line-height: 1.55;
      margin: 0 0 18px;
      max-width: 62ch;
    }
    .grad{
      background: linear-gradient(90deg, #ffffff, rgba(255,255,255,.85), rgba(255,255,255,.55));
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
    }
    .glow{
      background: linear-gradient(90deg, var(--brand2), var(--brand1), var(--brand3));
      -webkit-background-clip:text; background-clip:text; color:transparent;
      text-shadow: 0 0 32px rgba(124,58,237,.25);
    }
    .heroCard{
      border: 1px solid rgba(255,255,255,.12);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      padding: 18px;
      position:relative;
      overflow:hidden;
      min-height: 430px;
    }
    .heroCard::before{
      content:"";
      position:absolute; inset:-1px;
      background:
        radial-gradient(600px 240px at 20% 0%, rgba(34,211,238,.22), transparent 55%),
        radial-gradient(480px 260px at 90% 0%, rgba(124,58,237,.25), transparent 60%),
        radial-gradient(480px 380px at 60% 100%, rgba(244,114,182,.18), transparent 60%);
      pointer-events:none;
    }
    .miniHeader{
      position:relative;
      display:flex; align-items:center; justify-content:space-between;
      padding: 8px 10px;
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(10,14,30,.55);
      color: var(--muted);
      font-weight:700;
      font-size: 13px;
      margin-bottom: 14px;
    }
    .badge{
      padding:6px 10px; border-radius:999px;
      background: rgba(34,211,238,.12);
      border: 1px solid rgba(34,211,238,.25);
      color: #BFF7FF;
      font-weight:800;
      font-size: 12px;
    }

    .mock{
      position:relative;
      display:grid;
      grid-template-columns: 1fr;
      gap: 12px;
      margin-top: 10px;
    }
    .tile{
      border:1px solid rgba(255,255,255,.10);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius);
      padding: 14px;
      position:relative;
      overflow:hidden;
    }
    .tile h3{margin:0 0 6px; font-size:14px; letter-spacing:.2px}
    .tile p{margin:0; color:var(--muted); font-size:13px; line-height:1.45}
    .row{
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      color: var(--muted); font-weight:700; font-size:12px;
      margin-top:10px;
    }
    .bar{
      height: 10px; border-radius: 999px;
      background: rgba(255,255,255,.08);
      overflow:hidden; border: 1px solid rgba(255,255,255,.10);
    }
    .bar > span{
      display:block; height:100%;
      width: 72%;
      background: linear-gradient(90deg, var(--brand2), var(--brand1));
      filter: saturate(1.15);
    }

    .heroCTAs{display:flex; gap:12px; flex-wrap:wrap; margin: 16px 0 10px}
    .heroMeta{display:flex; gap:16px; flex-wrap:wrap; align-items:center; margin-top: 10px; color: var(--muted); font-weight:700; font-size:13px}
    .check{
      display:inline-flex; gap:10px; align-items:center;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(52,211,153,.10);
      border: 1px solid rgba(52,211,153,.20);
      color:#CFFBEA;
    }
    .icon{
      width:18px; height:18px; display:inline-block; border-radius:6px;
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.12);
      position:relative;
    }
    .icon::after{
      content:"";
      position:absolute; inset:4px;
      border-radius:4px;
      background: linear-gradient(180deg, rgba(255,255,255,.35), rgba(255,255,255,.05));
    }

    /* SECTION */
    section{padding: 44px 0}
    .sectionHead{
      display:flex; align-items:flex-end; justify-content:space-between; gap:18px;
      margin-bottom: 18px;
    }
    .kicker{
      color: var(--muted);
      font-weight:800;
      letter-spacing:.16em;
      text-transform: uppercase;
      font-size: 12px;
    }
    .h2{
      margin:6px 0 0;
      font-size: clamp(22px, 2.5vw, 34px);
      letter-spacing: -.4px;
      line-height:1.15;
    }
    .sub{
      margin:0;
      color: var(--muted);
      max-width: 60ch;
      line-height:1.55;
      font-weight:600;
    }

    .cards{
      display:grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px;
    }
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
        radial-gradient(450px 220px at 20% 0%, rgba(34,211,238,.14), transparent 60%),
        radial-gradient(420px 240px at 85% 0%, rgba(124,58,237,.16), transparent 60%);
      pointer-events:none;
    }
    .card h3{position:relative; margin:0 0 8px; font-size:16px}
    .card p{position:relative; margin:0; color:var(--muted); line-height:1.55; font-weight:600}
    .chip{
      position:relative;
      display:inline-flex; align-items:center; gap:10px;
      padding: 8px 10px;
      border-radius: 14px;
      background: rgba(10,14,30,.50);
      border:1px solid rgba(255,255,255,.10);
      margin-bottom: 12px;
      color: var(--muted);
      font-weight:800;
      font-size:12px;
      width: fit-content;
    }
    .chip .spark{
      width:10px; height:10px; border-radius:999px;
      background: linear-gradient(180deg, var(--brand3), var(--brand1));
      box-shadow: 0 0 0 6px rgba(244,114,182,.10);
    }

    /* HOW */
    .steps{
      display:grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px;
    }
    .step{
      grid-column: span 3;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 18px;
      position:relative;
      overflow:hidden;
      min-height: 170px;
    }
    .num{
      width:42px; height:42px; border-radius: 16px;
      display:grid; place-items:center;
      font-weight:900;
      background: linear-gradient(90deg, rgba(34,211,238,.22), rgba(124,58,237,.22));
      border:1px solid rgba(255,255,255,.14);
      margin-bottom: 12px;
    }
    .step h3{margin: 0 0 8px}
    .step p{margin:0; color:var(--muted); line-height:1.55; font-weight:600}

    /* PRICING */
    .pricing{
      display:grid; grid-template-columns: repeat(12,1fr); gap: 14px;
      align-items: stretch;
    }
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
      border-color: rgba(34,211,238,.35);
      box-shadow: 0 28px 70px rgba(34,211,238,.12), 0 28px 70px rgba(124,58,237,.12);
      transform: translateY(-4px);
    }
    .price h3{margin:0 0 6px}
    .price .tag{color:var(--muted); font-weight:800; font-size:12px; letter-spacing:.14em; text-transform:uppercase}
    .money{
      font-size: 38px; font-weight: 900; letter-spacing:-.8px; margin: 10px 0 2px;
    }
    .per{color:var(--muted); font-weight:700; margin:0 0 12px}
    .list{margin: 0 0 14px; padding: 0; list-style:none; display:grid; gap:8px}
    .list li{
      display:flex; gap:10px; align-items:flex-start;
      color: var(--muted); font-weight:650; line-height:1.4;
    }
    .tick{
      width:18px; height:18px; border-radius:6px;
      background: rgba(52,211,153,.16);
      border:1px solid rgba(52,211,153,.28);
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

    /* TESTIMONIALS */
    .testis{display:grid; grid-template-columns: repeat(12,1fr); gap:14px}
    .quote{
      grid-column: span 4;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(9,12,26,.55);
      border-radius: var(--radius2);
      padding: 18px;
      position:relative;
    }
    .quote p{margin:0 0 12px; color: var(--text); font-weight:650; line-height:1.55}
    .who{display:flex; gap:12px; align-items:center; color: var(--muted); font-weight:800}
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
    .qa{
      border-top: 1px solid rgba(35,48,95,.55);
    }
    .qa:first-child{border-top: none}
    .q{
      width:100%;
      text-align:left;
      background: transparent;
      border: none;
      color: var(--text);
      padding: 16px 18px;
      font-weight:850;
      font-size: 15px;
      display:flex; justify-content:space-between; gap:14px; align-items:center;
      cursor:pointer;
    }
    .q span{color: var(--muted); font-weight:900}
    .a{
      max-height: 0;
      overflow:hidden;
      transition: max-height .25s ease;
    }
    .a p{
      margin:0;
      padding: 0 18px 16px;
      color: var(--muted);
      line-height:1.6;
      font-weight:650;
    }
    .qa.open .a{max-height: 220px}

    /* CTA FORM */
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
        radial-gradient(650px 260px at 18% 0%, rgba(34,211,238,.18), transparent 60%),
        radial-gradient(520px 260px at 85% 0%, rgba(124,58,237,.22), transparent 60%),
        radial-gradient(520px 380px at 60% 110%, rgba(244,114,182,.14), transparent 65%);
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
    label{display:block; font-size:12px; color:var(--muted); font-weight:850; letter-spacing:.08em; text-transform:uppercase; margin: 12px 0 6px}
    input, textarea, select{
      width:100%;
      padding: 12px 12px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      color: var(--text);
      outline:none;
      font-weight:650;
      transition: border-color .15s ease, background .15s ease;
    }
    input:focus, textarea:focus, select:focus{
      border-color: rgba(34,211,238,.35);
      background: rgba(255,255,255,.09);
    }
    textarea{min-height: 96px; resize: vertical}
    .formRow{display:grid; grid-template-columns: 1fr 1fr; gap: 12px}
    .hint{margin:10px 0 0; color: var(--muted); font-weight:650; font-size:13px; line-height:1.45}
    .tiny{font-size:12px; color: rgba(169,180,214,.9)}
    .err{color: #FECACA; font-weight:800; font-size: 12px; margin-top: 8px; display:none}
    .okmsg{color:#CFFBEA; font-weight:800; font-size: 13px; display:none}

    /* FOOTER */
    footer{
      padding: 26px 0 42px;
      color: rgba(169,180,214,.9);
    }
    .foot{
      border-top: 1px solid rgba(35,48,95,.45);
      padding-top: 18px;
      display:flex; justify-content:space-between; gap: 18px; flex-wrap:wrap;
      font-weight:650;
    }
    .links{display:flex; gap:14px; flex-wrap:wrap}
    .links a{opacity:.9}
    .links a:hover{opacity:1; text-decoration: underline}

    /* FLOATING BLOBS */
    .blob{
      position:absolute; inset:auto;
      filter: blur(40px);
      opacity: .45;
      pointer-events:none;
      border-radius: 999px;
      animation: floaty 10s ease-in-out infinite;
      z-index:-1;
    }
    .blob.b1{width: 380px; height: 380px; left:-120px; top: 120px; background: rgba(124,58,237,.55)}
    .blob.b2{width: 420px; height: 420px; right:-180px; top: 220px; background: rgba(34,211,238,.45); animation-duration: 12s}
    .blob.b3{width: 340px; height: 340px; left: 35%; top: 980px; background: rgba(244,114,182,.32); animation-duration: 14s}
    @keyframes floaty{
      0%,100%{transform: translate(0,0) scale(1)}
      50%{transform: translate(0,-22px) scale(1.04)}
    }

    /* RESPONSIVE */
    @media (max-width: 980px){
      .grid{grid-template-columns: 1fr; }
      .heroCard{min-height: unset}
      .card{grid-column: span 6}
      .step{grid-column: span 6}
      .price{grid-column: span 6}
      .quote{grid-column: span 6}
      .ctaInner{grid-template-columns: 1fr}
    }
    @media (max-width: 640px){
      .navlinks{display:none}
      .card,.step,.price,.quote{grid-column: span 12}
      .formRow{grid-template-columns: 1fr}
      .btn{width:100%; justify-content:center}
      .actions{width:100%}
      .actions .btn{flex:1}
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce){
      *{animation:none !important; transition:none !important; scroll-behavior:auto !important;}
    }
  </style>
</head>

<body>
  <div class="blob b1"></div>
  <div class="blob b2"></div>
  <div class="blob b3"></div>

  <header class="top">
    <div class="wrap">
      <nav class="nav">
        <a class="brand" href="#inicio" aria-label="Inicio">
          <div class="logo" aria-hidden="true"></div>
          <div>
            <div style="font-size:14px; opacity:.9">Libro de Reclamaciones</div>
            <div style="font-size:12px; color:var(--muted); font-weight:750">Digital • Rápido • Ordenado</div>
          </div>
        </a>

        <div class="navlinks" role="navigation" aria-label="Secciones">
          <a href="#beneficios">Beneficios</a>
          <a href="#como-funciona">Cómo funciona</a>
          <a href="#planes">Planes</a>
          <a href="#faq">FAQ</a>
        </div>

        <div class="actions">
          <a class="btn" href="#demo">
            <span class="icon" aria-hidden="true"></span>
            Ver demo
          </a>
          <a class="btn primary" href="#contacto">
            <span class="icon" aria-hidden="true"></span>
            Solicitar implementación
          </a>
        </div>
      </nav>
    </div>
  </header>

  <main id="inicio" class="hero">
    <div class="wrap">
      <div class="grid">
        <div>
          <div class="pill">
            <span class="dot" aria-hidden="true"></span>
            Implementación en horas, no en semanas
          </div>

          <h1 class="h1">
            Tu <span class="glow">Libro de Reclamaciones</span> que sí se ve moderno:
            <span class="grad">cumple, atiende y convierte</span>.
          </h1>

          <p class="lead">
            Centraliza reclamos y quejas con un flujo ordenado: registro, seguimiento, reportes y evidencias.
            Diseñado para atención al cliente sin fricción (y con una experiencia visual premium).
          </p>

          <div class="heroCTAs">
            <a class="btn primary" href="#contacto">
              <span class="icon" aria-hidden="true"></span>
              Quiero el libro digital
            </a>
            <a class="btn" href="#planes">
              <span class="icon" aria-hidden="true"></span>
              Ver planes
            </a>
            <a class="btn" href="#faq">
              <span class="icon" aria-hidden="true"></span>
              Resolver dudas
            </a>
          </div>

          <div class="heroMeta">
            <span class="check"><span class="icon" aria-hidden="true"></span> Notificaciones automáticas</span>
            <span class="check"><span class="icon" aria-hidden="true"></span> Reportes y exportación</span>
            <span class="check"><span class="icon" aria-hidden="true"></span> Panel de seguimiento</span>
          </div>
        </div>

        <aside class="heroCard" id="demo" aria-label="Demo visual">
          <div class="miniHeader">
            <span>Vista previa • Panel</span>
            <span class="badge">DEMO</span>
          </div>

          <div class="mock">
            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Nuevo reclamo</div>
              <h3>Registro rápido (menos de 2 minutos)</h3>
              <p>Formulario amigable con campos claros, adjuntos y confirmación de recepción.</p>
              <div class="row">
                <span>Completado</span>
                <span style="color:#CFFBEA">72%</span>
              </div>
              <div class="bar" aria-hidden="true"><span></span></div>
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Seguimiento</div>
              <h3>Estados y trazabilidad</h3>
              <p>“Recibido → En evaluación → Resuelto”. Historial con fechas y responsables.</p>
              <div class="row">
                <span>Tiempo promedio</span>
                <span style="color:#BFF7FF">48h</span>
              </div>
            </div>

            <div class="tile">
              <div class="chip"><span class="spark" aria-hidden="true"></span> Reportes</div>
              <h3>Insights para mejorar</h3>
              <p>Motivos frecuentes, canales, tiempos de respuesta y exportación a Excel/CSV.</p>
              <div class="row">
                <span>Satisfacción</span>
                <span style="color:#FDE68A">4.6/5</span>
              </div>
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
          <h2 class="h2">Ordena tus reclamos sin perder empatía</h2>
        </div>
        <p class="sub">
          Menos fricción para el cliente, más control para tu equipo y mejor visibilidad para la gerencia.
        </p>
      </div>

      <div class="cards">
        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Experiencia</div>
          <h3>Formularios claros y confiables</h3>
          <p>Reduce abandono con una interfaz limpia, validación inteligente y confirmación inmediata.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Gestión</div>
          <h3>Seguimiento y responsables</h3>
          <p>Asigna, clasifica y monitorea cada caso con estados, comentarios internos y evidencias.</p>
        </article>

        <article class="card">
          <div class="chip"><span class="spark" aria-hidden="true"></span> Control</div>
          <h3>Reportes para decisiones</h3>
          <p>Mide tiempos, causas y reincidencia. Exporta y presenta resultados con indicadores.</p>
        </article>
      </div>
    </div>
  </section>

  <section id="como-funciona">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Cómo funciona</div>
          <h2 class="h2">Un flujo simple que tu equipo adopta rápido</h2>
        </div>
        <p class="sub">
          Desde el registro hasta la resolución: todo queda documentado y listo para auditoría interna.
        </p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="num">1</div>
          <h3>El cliente registra</h3>
          <p>Formulario online con datos esenciales, adjuntos y canal de contacto.</p>
        </div>
        <div class="step">
          <div class="num">2</div>
          <h3>Tu equipo recibe</h3>
          <p>Notificación y creación automática del caso con número de seguimiento.</p>
        </div>
        <div class="step">
          <div class="num">3</div>
          <h3>Se gestiona el caso</h3>
          <p>Asignación, etiquetas, comentarios, evidencias y tiempos controlados.</p>
        </div>
        <div class="step">
          <div class="num">4</div>
          <h3>Respuesta y cierre</h3>
          <p>Comunicación al cliente y cierre con trazabilidad + reporte final.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="planes">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Planes</div>
          <h2 class="h2">Elige el nivel de control que necesitas</h2>
        </div>
        <p class="sub">
          Ajusta tu plan por volumen, canales y necesidades de reportes. (Precios referenciales)
        </p>
      </div>

      <div class="pricing">
        <div class="price">
          <div class="tag">Starter</div>
          <h3>Para negocios pequeños</h3>
          <div class="money">S/ 149</div>
          <p class="per">/ mes</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> 1 formulario embebible</li>
            <li><span class="tick" aria-hidden="true"></span> Panel básico de casos</li>
            <li><span class="tick" aria-hidden="true"></span> Exportación CSV</li>
            <li><span class="tick" aria-hidden="true"></span> 2 usuarios</li>
          </ul>
          <a class="btn" href="#contacto"><span class="icon" aria-hidden="true"></span> Solicitar</a>
        </div>

        <div class="price featured">
          <div class="tag">Pro</div>
          <h3>Para equipos de atención</h3>
          <div class="money">S/ 299</div>
          <p class="per">/ mes</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> Multi-sede y canales</li>
            <li><span class="tick" aria-hidden="true"></span> Estados + SLA</li>
            <li><span class="tick" aria-hidden="true"></span> Reportes avanzados</li>
            <li><span class="tick" aria-hidden="true"></span> 10 usuarios</li>
          </ul>
          <a class="btn primary" href="#contacto"><span class="icon" aria-hidden="true"></span> Elegir Pro</a>
        </div>

        <div class="price">
          <div class="tag">Enterprise</div>
          <h3>Para operaciones grandes</h3>
          <div class="money">A medida</div>
          <p class="per">Integraciones + roles</p>
          <ul class="list">
            <li><span class="tick" aria-hidden="true"></span> Roles y permisos</li>
            <li><span class="tick" aria-hidden="true"></span> Integración CRM/Helpdesk</li>
            <li><span class="tick" aria-hidden="true"></span> Auditoría + logs</li>
            <li><span class="tick" aria-hidden="true"></span> Soporte prioritario</li>
          </ul>
          <a class="btn" href="#contacto"><span class="icon" aria-hidden="true"></span> Hablar con ventas</a>
        </div>
      </div>
    </div>
  </section>

  <section aria-label="Testimonios">
    <div class="wrap">
      <div class="sectionHead">
        <div>
          <div class="kicker">Confianza</div>
          <h2 class="h2">Una atención más ordenada se nota</h2>
        </div>
        <p class="sub">Ejemplos de mensajes que suelen recibir equipos cuando el flujo está bien implementado.</p>
      </div>

      <div class="testis">
        <div class="quote">
          <p>“Ahora podemos responder más rápido y con historial completo. Menos discusiones, más solución.”</p>
          <div class="who"><div class="ava" aria-hidden="true"></div> Jefa de Atención • Retail</div>
        </div>
        <div class="quote">
          <p>“El panel nos dio visibilidad del motivo real de los reclamos. Con eso ajustamos procesos.”</p>
          <div class="who"><div class="ava" aria-hidden="true"></div> Operaciones • Servicios</div>
        </div>
        <div class="quote">
          <p>“Se ve profesional y el cliente siente que su caso sí está siendo atendido.”</p>
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
          <h2 class="h2">Preguntas frecuentes</h2>
        </div>
        <p class="sub">Respuestas rápidas para que avances hoy mismo.</p>
      </div>

      <div class="faq" role="list">
        <div class="qa open" role="listitem">
          <button class="q" type="button">
            ¿Se puede incrustar en mi web?
            <span aria-hidden="true">+</span>
          </button>
          <div class="a">
            <p>Sí. Puedes embeber el formulario como widget (iframe) o integrarlo como página propia con tu dominio y estilo.</p>
          </div>
        </div>

        <div class="qa" role="listitem">
          <button class="q" type="button">
            ¿Incluye número de seguimiento y confirmación?
            <span aria-hidden="true">+</span>
          </button>
          <div class="a">
            <p>Incluye confirmación en pantalla y número de caso. También puede enviar notificación por correo según tu configuración.</p>
          </div>
        </div>

        <div class="qa" role="listitem">
          <button class="q" type="button">
            ¿Puedo exportar reportes?
            <span aria-hidden="true">+</span>
          </button>
          <div class="a">
            <p>Sí. Exportación a CSV/Excel y reportes con filtros por fechas, sede, tipo y estado.</p>
          </div>
        </div>

        <div class="qa" role="listitem">
          <button class="q" type="button">
            ¿Se adapta a mi marca?
            <span aria-hidden="true">+</span>
          </button>
          <div class="a">
            <p>Claro. Puedes cambiar colores, tipografías, textos, logo y estilo visual para que se vea 100% de tu marca.</p>
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
          <h2 class="h2">Pídelo listo para usar</h2>
        </div>
        <p class="sub">Déjanos tus datos y te compartimos una propuesta y demo personalizada.</p>
      </div>

      <div class="cta">
        <div class="ctaInner">
          <div>
            <div class="pill" style="margin-bottom:12px">
              <span class="dot" aria-hidden="true"></span>
              Respuesta rápida • Soporte humano
            </div>
            <h3 style="margin:0 0 10px; font-size:20px; letter-spacing:-.2px">Activa tu Libro de Reclamaciones digital</h3>
            <p class="sub" style="margin:0 0 14px">
              Ideal para webs, sedes físicas (QR) y equipos que necesitan trazabilidad real. Si quieres,
              también lo integramos con tu correo/CRM/Helpdesk.
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
            <div class="okmsg" id="okMsg">¡Listo! Hemos registrado tu solicitud (demo). Puedes conectar esto a tu backend.</div>

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
            <div style="font-weight:900; color: var(--text)">Libro de Reclamaciones</div>
            <div style="font-size:12px">© <span id="year"></span> • Hecho para atención al cliente moderna</div>
          </div>
        </div>

        <div class="links" aria-label="Enlaces">
          <a href="#beneficios">Beneficios</a>
          <a href="#como-funciona">Cómo funciona</a>
          <a href="#planes">Planes</a>
          <a href="#contacto">Contacto</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Smooth scroll for in-page anchors
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const id = a.getAttribute('href');
        const el = document.querySelector(id);
        if(!el) return;
        e.preventDefault();
        el.scrollIntoView({behavior: 'smooth', block: 'start'});
      });
    });

    // FAQ accordion
    document.querySelectorAll('.qa .q').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.qa');
        const wasOpen = item.classList.contains('open');
        document.querySelectorAll('.qa').forEach(x => x.classList.remove('open'));
        if(!wasOpen) item.classList.add('open');
      });
    });

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
