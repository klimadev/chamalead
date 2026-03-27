<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ChamaLead - Valor do Ativo</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap');
    :root{--primary:#4b1e28;--secondary:#2f1118;--bg:#f7f1ea;--surface:#fffdfa;--surface-alt:#f1e8df;--surface-strong:#e2d3c2;--text:#1c1718;--text-body:#4f4346;--muted:#7f6f72;--border:#d8c8b7;--accent:#c6a76a;--accent-light:#eadfcb;--accent-soft:#b89452;--success:#6f8a62;--success-light:#e2eadc;--warning:#b78943;--warning-light:#f3e6d2;--danger:#8b4a52;--danger-light:#f1dfe1;--shadow:0 8px 24px rgba(45,17,24,.08);--shadow-lg:0 18px 42px rgba(45,17,24,.14)}
    *{box-sizing:border-box;margin:0;padding:0} body{font-family:'Manrope',sans-serif;background:radial-gradient(circle at top left,rgba(198,167,106,.12),transparent 30%),radial-gradient(circle at right 20%,rgba(75,30,40,.08),transparent 24%),var(--bg);color:var(--text);line-height:1.75;font-size:17px;-webkit-font-smoothing:antialiased} h1,h2,h3,h4{font-family:'Cormorant Garamond',serif;color:var(--primary);font-weight:600;letter-spacing:.01em}
    .page{max-width:1100px;margin:0 auto 24px;padding:48px 56px;background:var(--surface);border:1px solid rgba(184,148,82,.18);border-radius:8px;box-shadow:var(--shadow)} .page--alt{background:var(--surface-alt)} .page--last{margin-bottom:0}
    .hero{min-height:620px;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;padding:64px;position:relative;overflow:hidden}.hero::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at top,rgba(198,167,106,.16),transparent 42%)}
    .eyebrow{font:800 11px 'Manrope',sans-serif;letter-spacing:.18em;text-transform:uppercase;color:var(--accent-soft)} .hero__eyebrow{position:relative;z-index:1;margin-bottom:18px}
    .hero__logo{position:relative;z-index:1;width:100px;height:100px;border-radius:20px;overflow:hidden;border:1px solid var(--border);box-shadow:var(--shadow-lg);margin-bottom:28px;background:var(--surface)} .hero__logo img{width:100%;height:100%;object-fit:cover;display:block}
    .hero__title{position:relative;z-index:1;font-size:54px;line-height:1.02;margin-bottom:16px}.hero__subtitle{position:relative;z-index:1;max-width:760px;font-size:22px;line-height:1.55;color:var(--text-body);margin-bottom:28px}
    .hero__tagline,.pill{display:inline-flex;align-items:center;justify-content:center;padding:10px 18px;border-radius:999px;font:800 11px 'Manrope',sans-serif;letter-spacing:.14em;text-transform:uppercase}
    .hero__tagline{position:relative;z-index:1;background:linear-gradient(180deg,rgba(234,223,203,.8),rgba(241,232,223,.96));color:var(--primary)}
    .hero__stakes,.summary-grid,.reassurance{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.hero__stakes{position:relative;z-index:1;width:100%;max-width:820px;margin-top:28px}
    .stake,.card,.table-card,.budget-box,.cost-box,.panel{padding:22px;background:rgba(255,253,250,.88);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow)} .stake p,.card p,.panel p,.budget-item__text,.cost-note,.footer,.table-note{font-size:14px;line-height:1.65;color:var(--text-body)}
    .stake__label,.card__num,.section-band .eyebrow,.budget-item__label,.cost-item__label{margin-bottom:8px;font:800 10px 'Manrope',sans-serif;letter-spacing:.14em;text-transform:uppercase;color:var(--accent-soft)} .card h3,.stake h3{font-size:26px;margin-bottom:8px}
    .section__header{margin-bottom:30px}.section__header h2{font-size:40px;margin-bottom:8px}.section__header p{font-size:18px;color:var(--text-body)}
    .modules,.section-band,.comparison{padding:26px;border-radius:18px;background:linear-gradient(135deg,rgba(75,30,40,.98),rgba(47,17,24,.98));color:#fff;box-shadow:var(--shadow-lg)} .modules{margin-top:24px}.modules h3,.section-band .eyebrow{color:rgba(255,255,255,.68);margin-bottom:12px}
    .modules__list{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}.modules__item{display:flex;gap:10px;align-items:flex-start;font-size:14px;line-height:1.6}.modules__item span{width:24px;height:24px;flex-shrink:0;border-radius:8px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font:800 11px 'Manrope',sans-serif}
    .section-band__quote{font-size:32px;line-height:1.18;margin-bottom:12px;color:#fff}.section-band p,.comparison p{font-size:15px;line-height:1.7;color:rgba(255,255,255,.84)}
    .price-hero{display:grid;grid-template-columns:1.15fr .85fr;gap:24px;align-items:stretch}.panel__label,.comparison h3,.budget-box h3,.cost-box h3,.table-card h3{font-size:28px;margin-bottom:10px}.price-hero__value{font-size:62px;line-height:1;font-weight:800;color:var(--primary);margin-bottom:10px}.price-hero__value small{font-size:20px;font-weight:700}
    .pillrow{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}.pill{background:var(--surface-alt);color:var(--primary);border:1px solid var(--border)} .pill--accent{background:var(--accent-light)} .pill--success{background:var(--success-light);color:var(--success)} .pill--warning{background:var(--warning-light);color:var(--warning)}
    .comparison h3,.comparison .comparison__big,.comparison strong{color:#fff}.comparison__big{font-size:42px;line-height:1;margin:14px 0}.comparison__list{list-style:none;display:flex;flex-direction:column;gap:10px}.comparison__list li{display:flex;justify-content:space-between;gap:12px;padding-top:10px;border-top:1px solid rgba(255,255,255,.14);font-size:13px}
    .price-table{width:100%;border-collapse:collapse}.price-table th,.price-table td{padding:14px;text-align:left;border-bottom:1px solid var(--surface-strong);font-size:13px;vertical-align:top}.price-table th{background:var(--primary);color:#fff;font:800 10px 'Manrope',sans-serif;letter-spacing:.12em;text-transform:uppercase}.price-table tr:nth-child(even) td{background:rgba(244,242,238,.72)} .price-table td strong{color:var(--primary)}
    .budget-grid,.cost-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}.budget-item,.cost-item{padding:18px;border-radius:14px;background:var(--surface-alt);border:1px solid var(--border)} .budget-item__value,.cost-item__value{font-size:28px;font-weight:800;color:var(--primary);line-height:1;margin-bottom:8px} .budget-item__value small,.cost-item__value small{font-size:14px}
    .cost-box{margin-top:18px}.reassurance__card{padding:24px;border-radius:16px;background:rgba(255,253,250,.9);border:1px solid var(--border);box-shadow:var(--shadow)} .reassurance__card h3{font-size:26px;margin-bottom:8px} .reassurance__card p{font-size:14px;color:var(--text-body);line-height:1.65}
    .final-band{padding:28px;border-radius:18px;background:linear-gradient(135deg,rgba(75,30,40,.98),rgba(47,17,24,.98));box-shadow:var(--shadow-lg);color:#fff;margin-top:24px} .final-band h2{font-size:34px;color:#fff;margin-bottom:10px} .final-band p{font-size:15px;line-height:1.7;color:rgba(255,255,255,.84)}
    .footer{padding-top:22px;margin-top:22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted)} .footer__brand{font-weight:700;color:var(--text-body)}
    @media (max-width:900px){.page{padding:32px}.hero{padding:40px 32px}.hero__title{font-size:42px}.hero__subtitle,.section-band__quote,.final-band h2{font-size:28px}.hero__stakes,.summary-grid,.modules__list,.price-hero,.budget-grid,.cost-grid,.reassurance{grid-template-columns:1fr}.footer{flex-direction:column;align-items:flex-start;gap:8px}}
    @media print{body{background:#fff}.page{margin:0;max-width:100%;box-shadow:none}}
  </style>
</head>
<body>
<?php
$modules = glob('modules/*.html');
natsort($modules);
foreach ($modules as $module) {
    include $module;
}
?>
</body>
</html>
