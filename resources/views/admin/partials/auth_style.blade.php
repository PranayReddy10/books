{{-- Shared look for the admin sign-in pages (login, forgot password). --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--ink:#0f172a;--violet:#2563eb;--violet-2:#60a5fa;--gold:#38bdf8;--line:#e5e9f0;--muted:#64748b}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',system-ui,sans-serif;background:#f4f6fb;color:var(--ink);min-height:100vh}
    .login-shell{display:grid;grid-template-columns:1.05fr .95fr;min-height:100vh}
    /* Left brand panel */
    .brand-panel{position:relative;overflow:hidden;background:linear-gradient(160deg,#111c36,#0b1222);color:#fff;padding:56px;display:flex;flex-direction:column;justify-content:center}
    .brand-panel:before{content:'';position:absolute;inset:0;background:
      radial-gradient(50% 50% at 20% 20%, rgba(37,99,235,.38), transparent 60%),
      radial-gradient(45% 45% at 85% 80%, rgba(56,189,248,.20), transparent 60%);pointer-events:none}
    .brand-panel .logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:22px;position:relative}
    .brand-panel .logo .dot{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,var(--violet),var(--gold));transform:rotate(8deg)}
    .brand-panel h1{font-weight:800;font-size:clamp(28px,3.4vw,44px);line-height:1.08;margin-top:34px;position:relative}
    .brand-panel h1 .hl{background:linear-gradient(120deg,var(--violet-2),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
    .brand-panel p{color:#a9b6cc;margin-top:16px;max-width:420px;position:relative;font-size:16px}
    .brand-panel .badges{display:flex;gap:10px;margin-top:28px;position:relative;flex-wrap:wrap}
    .brand-panel .badge{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:999px;padding:8px 14px;font-size:13px;font-weight:600}
    /* Right form panel */
    .form-panel{display:flex;align-items:center;justify-content:center;padding:40px}
    .login-card{background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 30px 60px rgba(15,23,42,.10);padding:38px;width:100%;max-width:410px}
    .login-card .kicker{color:var(--violet);font-weight:600;letter-spacing:.08em;text-transform:uppercase;font-size:12px}
    .login-card h2{font-size:26px;margin:6px 0 4px}
    .login-card .sub{color:var(--muted);font-size:14px;margin-bottom:24px}
    .fld{margin-bottom:16px}
    .fld label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
    .fld input[type=text],.fld input[type=password]{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--line);font-size:15px;background:#f8fafc;transition:.15s}
    .fld input:focus{outline:none;border-color:var(--violet);background:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.14)}
    .row-between{display:flex;align-items:center;justify-content:space-between;margin:6px 0 20px;font-size:13px}
    .row-between label{color:var(--muted);display:flex;align-items:center;gap:6px;margin:0}
    .row-between a{color:var(--violet);font-weight:600;text-decoration:none}
    .btn-login{width:100%;padding:13px;border:none;border-radius:12px;background:var(--violet);color:#fff;font-weight:700;font-size:15px;cursor:pointer;box-shadow:0 10px 24px rgba(37,99,235,.28);transition:.18s;font-weight:800}
    .btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(37,99,235,.38)}
    .card-logo{max-width:150px;margin-bottom:20px}
    .alt-link{display:block;text-align:center;margin-top:18px;font-size:13px;color:var(--muted);text-decoration:none;font-weight:600}
    .alt-link:hover{color:var(--violet)}
    @media(max-width:880px){.login-shell{grid-template-columns:1fr}.brand-panel{display:none}}
  </style>
