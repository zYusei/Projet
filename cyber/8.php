<?php
require_once('../session.php');
$user = current_user();
if (!$user) { header('Location: ../connexion.php'); exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestion des vulnérabilités | FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
  :root{
    --bg:#0b1020; --panel:#111833; --panel-2:#0f1530; --border:#1f2a4d;
    --text:#e8eefc; --muted:#a8b3d9; --accent:#5aa1ff; --good:#10d49b; --bad:#ff6b6b;
    --code-bg:#0b132b; --code-bd:#1e2a4a; --kbd-bg:#0e1733; --warn:#fbbf24;
  }
  *{box-sizing:border-box} html,body{height:100%}
  body{
    margin:0; background:
      radial-gradient(1200px 600px at 10% -10%, #16224b 0%, transparent 60%),
      radial-gradient(800px 500px at 100% 10%, #1c2a59 0%, transparent 50%),
      var(--bg);
    color:var(--text); font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; line-height:1.6;
  }
  .wrap{max-width:1200px;margin:0 auto;padding:24px 16px 60px;}

  .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
  .crumbs a{color:var(--muted);text-decoration:none} .crumbs a:hover{color:var(--accent)}
  .crumbs{font-weight:700}
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#9ae6ff;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

  .title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:10px 0 18px}
  .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;background:linear-gradient(90deg,var(--accent),#b3c7ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .title .progress-pill{font-weight:800;color:#06121f;background:linear-gradient(90deg,#7cffc0,#4df1aa);padding:.35rem .65rem;border-radius:999px;border:1px solid #164238}

  /* Grille 2 colonnes */
  .layout{display:grid;grid-template-columns:1.08fr 0.92fr;gap:20px}
  @media (min-width:1160px){.layout{grid-template-columns:1fr 1fr}}
  @media (max-width:1020px){.layout{grid-template-columns:1fr}}

  .card{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.35)}
  .card .hd{padding:16px 18px;border-bottom:1px solid var(--border);font-weight:800}
  .card .bd{padding:18px}

  .callout{border:1px dashed var(--border); background:linear-gradient(180deg, rgba(90,161,255,.06), transparent); padding:14px;border-radius:12px; color:var(--muted)}
  .warn{border-color:rgba(251,191,36,.45); background:linear-gradient(180deg, rgba(251,191,36,.07), transparent);}  
  code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}

  /* Quiz */
  .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
  .q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
  .q .qbd{padding:16px; display:flex; flex-direction:column; gap:10px}
  .q label{display:flex;gap:10px;align-items:flex-start;line-height:1.4}
  .q label input{margin-top:4px;flex-shrink:0}
  .q .explain{display:none;margin-top:6px;color:var(--muted);font-size:.95rem}
  .q.correct{border-color:rgba(16,212,155,.55)} .q.wrong{border-color:rgba(255,107,107,.55)}
  .controls{display:flex;gap:10px;margin-top:12px}

  /* Boutons */
  button{appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);color:#fff;font-weight:800;border-radius:10px;padding:.6rem 1rem;cursor:pointer}
  button:hover{border-color:#2b3c75}
  .btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#fff;border:0}
  .btn-ghost{background:transparent;color:#fff}
  .score{margin-top:14px;font-weight:800;color:#071421;background:linear-gradient(90deg,#7cffc0,#4df1aa);display:inline-block;padding:.45rem .8rem;border-radius:999px;border:1px solid #164238}

  .footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
  .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:#88b6ff}

  /* Mini-jeu : Vuln Management Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .row input,.row select,.row button{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:.15rem .6rem;font-size:.8rem;color:#cfe1ff}
  .ok{background:linear-gradient(90deg,#7cffc0,#4df1aa);color:#06121f;border-color:#164238}
  .warnP{background:linear-gradient(90deg,#ffef9f,#fbbf24);color:#2a1a06;border-color:#6a4c0b}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:block;white-space:normal;padding:4px 0;color:var(--muted)} /* 1 mission = 1 ligne */
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🧩 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Blue Team &nbsp;›&nbsp; <span style="color:var(--accent)">Gestion des vulnérabilités</span></div>
      <span class="badge">Leçon 8 / 10</span>
    </div>

    <div class="title">
      <h1>Gestion des vulnérabilités : scan, priorisation, patching</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Le but : réduire le <strong>risque exploitable</strong>, pas seulement la liste des CVE. Il faut : un <strong>inventaire</strong> fiable, des <strong>scans authentifiés/agents</strong>, une <strong>priorisation par le risque</strong> (CVSS + contexte + <em>EPSS/KEV</em>), des <strong>SLA de patching</strong>, des <strong>exceptions gouvernées</strong> et un <strong>suivi</strong> (tickets &amp; KPI).</p>

          <h2>Cycle simple</h2>
          <ul>
            <li><strong>Découvrir</strong> (assets couverts) → <strong>Scanner</strong> (réseau + agent, auth).</li>
            <li><strong>Prioriser</strong> : gravité <em>et</em> probabilité (EPSS), exploit connu (KEV), exposition, criticité métier.</li>
            <li><strong>Remédier</strong> : patch/mitigation, <em>virtual patching</em> si nécessaire.</li>
            <li><strong>Mesurer</strong> : backlog, temps moyen de correction, taux de conformité aux SLA.</li>
          </ul>

          <div class="callout warn">
            <strong>Raccourci gagnant :</strong> Couverture inventaire ≥ 95% → scans authentifiés/agents → priorisation <em>risk-based</em> (CVSS + EPSS/KEV + contexte) → patching sous SLA → exceptions temporaires et validées → KPIs suivis.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Vulnerability Management Lab</h3>
            <p class="rules">Active les bons réglages pour faire passer les 6 missions au vert ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages VM">
                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Inventaire &amp; couverture</label>
                    <select id="inventory">
                      <option value="partial" selected>Partielle</option>
                      <option value="complete">Complète (actifs suivis)</option>
                    </select>
                    <span id="inventoryTag" class="pill warnP">Couverture faible</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Scans</label>
                    <select id="scans">
                      <option value="unauth" selected>Réseau non authentifié</option>
                      <option value="auth">Auth + agent (planifiés)</option>
                    </select>
                    <span id="scansTag" class="pill warnP">Visibilité limitée</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Priorisation</label>
                    <select id="prio">
                      <option value="cvss" selected>CVSS uniquement</option>
                      <option value="risk">Risk-based (CVSS + contexte)</option>
                    </select>
                    <span id="prioTag" class="pill warnP">Gravité brute</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">EPSS &amp; KEV</label>
                    <input type="checkbox" id="signals">
                    <span id="signalsTag" class="pill warnP">Non utilisés</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">SLA de patching</label>
                    <select id="sla">
                      <option value="none" selected>Ad-hoc</option>
                      <option value="set">Délais définis (ex. crit. 7 jours)</option>
                    </select>
                    <span id="slaTag" class="pill warnP">Aucun engagement</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Exceptions</label>
                    <select id="exceptions">
                      <option value="free" selected>Libres (illimitées)</option>
                      <option value="governed">Validées + expirations</option>
                    </select>
                    <span id="exceptionsTag" class="pill warnP">Non gouvernées</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Reporting &amp; tickets</label>
                    <select id="reporting">
                      <option value="none" selected>Aucun</option>
                      <option value="kpi">KPIs + tickets auto</option>
                    </select>
                    <span id="reportingTag" class="pill warnP">Pas de suivi</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Inventaire complet / actifs couverts</div>
                    <div class="check" id="m2">⬜ Mission 2 — Scans authentifiés et/ou agent planifiés</div>
                    <div class="check" id="m3">⬜ Mission 3 — Prioriser par le risque (CVSS + EPSS/KEV + contexte)</div>
                    <div class="check" id="m4">⬜ Mission 4 — Appliquer des SLA de patching</div>
                    <div class="check" id="m5">⬜ Mission 5 — Encadrer les exceptions (validation + expiration)</div>
                    <div class="check" id="m6">⬜ Mission 6 — Suivre les KPIs via tickets &amp; rapports</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> Inventaire=<em>complete</em>, Scans=<em>auth</em>, Priorisation=<em>risk</em> + <em>EPSS/KEV cochés</em>, SLA=<em>set</em>, Exceptions=<em>governed</em>, Reporting=<em>kpi</em>.
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /MINI-JEU -->
        </div>
      </section>

      <!-- QUIZ -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <div class="q" data-q="q1">
              <div class="qhd">1) Pourquoi les scans authentifiés/agents sont-ils importants ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Ils révèlent des vulnérabilités que le réseau seul ne voit pas (packages, config)</label>
                <label><input type="radio" name="q1"> Parce que c’est plus joli</label>
                <label><input type="radio" name="q1"> Pour augmenter la charge CPU</label>
                <div class="explain">L’auth/agent inspecte versions, services, correctifs réellement présents.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Une priorisation “risk-based” combine…</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> CVSS + probabilité (EPSS) + exploit public (KEV) + contexte</label>
                <label><input type="radio" name="q2"> Uniquement la couleur du CVE</label>
                <label><input type="radio" name="q2"> L’ordre alphabétique des hôtes</label>
                <div class="explain">On cible d’abord ce qui est exploitable et expose l’entreprise.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Les SLA de patching servent à…</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Fixer des délais de correction selon la criticité</label>
                <label><input type="radio" name="q3"> Mesurer la météo</label>
                <label><input type="radio" name="q3"> Réduire le stockage</label>
                <div class="explain">Ex : critique ≤ 7 jours, haut ≤ 30 jours, etc.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Pourquoi gouverner les exceptions (dérogations) ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Pour éviter qu’elles deviennent permanentes et maintenir le risque sous contrôle</label>
                <label><input type="radio" name="q4"> Pour faire joli dans les audits</label>
                <label><input type="radio" name="q4"> Pour empêcher les patchs</label>
                <div class="explain">Validation, justification, date d’expiration et compensations.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quel indicateur suit l’efficacité du programme ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Temps moyen de correction et taux de remédiation sous SLA</label>
                <label><input type="radio" name="q5"> Nombre de réunions par semaine</label>
                <label><input type="radio" name="q5"> Taille moyenne des logs</label>
                <div class="explain">Ces KPIs montrent la vitesse et la discipline de correction.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./7.php">← Leçon précédente</a>
              <a href="./9.php">Leçon suivante →</a>
            </div>
          </form>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // ===== Quiz =====
    (function(){
      const form = document.getElementById('quizForm');
      const btnCheck = document.getElementById('btnCheck');
      const btnReset = document.getElementById('btnReset');
      const scoreEl = document.getElementById('score');
      function evaluate(){
        let total=0, ok=0;
        document.querySelectorAll('.q').forEach(q=>{
          total++;
          q.classList.remove('correct','wrong');
          const name=q.dataset.q;
          const checked=form.querySelector(`input[name="${name}"]:checked`);
          const explain=q.querySelector('.explain');
          if (explain) explain.style.display='block';
          if (checked && checked.hasAttribute('data-correct')){
            ok++; q.classList.add('correct');
          } else { q.classList.add('wrong'); }
        });
        scoreEl.style.display='inline-block';
        scoreEl.textContent=`Score : ${ok}/${total} • ${Math.round((ok/total)*100)}%`;
        scoreEl.scrollIntoView({behavior:'smooth',block:'center'});
      }
      function resetQuiz(){
        form.reset();
        document.querySelectorAll('.q').forEach(q=>{
          q.classList.remove('correct','wrong');
          const ex=q.querySelector('.explain'); if(ex) ex.style.display='none';
        });
        scoreEl.style.display='none';
      }
      btnCheck.addEventListener('click', evaluate);
      btnReset.addEventListener('click', resetQuiz);
    })();

    // ===== Mini-jeu : Vulnerability Management Lab =====
    (function(){
      const inventory = document.getElementById('inventory');
      const scans = document.getElementById('scans');
      const prio = document.getElementById('prio');
      const signals = document.getElementById('signals');
      const sla = document.getElementById('sla');
      const exceptions = document.getElementById('exceptions');
      const reporting = document.getElementById('reporting');

      const tags = {
        inventory: document.getElementById('inventoryTag'),
        scans: document.getElementById('scansTag'),
        prio: document.getElementById('prioTag'),
        signals: document.getElementById('signalsTag'),
        sla: document.getElementById('slaTag'),
        exceptions: document.getElementById('exceptionsTag'),
        reporting: document.getElementById('reportingTag'),
      };

      const m1=document.getElementById('m1');
      const m2=document.getElementById('m2');
      const m3=document.getElementById('m3');
      const m4=document.getElementById('m4');
      const m5=document.getElementById('m5');
      const m6=document.getElementById('m6');

      let touched=false;

      function tag(el, txt, cls){ el.textContent=txt; el.className='pill '+cls; }
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }
      function resetCheck(el, txt){ el.classList.remove('done'); el.textContent='⬜ '+txt; }

      function renderTags(){
        tag(tags.inventory, inventory.value==='complete' ? 'Couverture complète' : 'Couverture faible', inventory.value==='complete' ? 'ok' : 'warnP');
        tag(tags.scans, scans.value==='auth' ? 'Auth + agent' : 'Non auth', scans.value==='auth' ? 'ok' : 'warnP');
        tag(tags.prio, prio.value==='risk' ? 'Risk-based' : 'CVSS brut', prio.value==='risk' ? 'ok' : 'warnP');
        tag(tags.signals, signals.checked ? 'EPSS/KEV actifs' : 'Non utilisés', signals.checked ? 'ok' : 'warnP');
        tag(tags.sla, sla.value==='set' ? 'SLA définis' : 'Ad-hoc', sla.value==='set' ? 'ok' : 'warnP');
        tag(tags.exceptions, exceptions.value==='governed' ? 'Exceptions gouvernées' : 'Libres', exceptions.value==='governed' ? 'ok' : 'warnP');
        tag(tags.reporting, reporting.value==='kpi' ? 'KPIs + tickets' : 'Aucun', reporting.value==='kpi' ? 'ok' : 'warnP');
      }

      function renderMissions(){
        if (!touched) return;

        (inventory.value==='complete')
          ? done(m1,'Mission 1 — Inventaire complet')
          : resetCheck(m1,'Mission 1 — Inventaire complet / actifs couverts');

        (scans.value==='auth')
          ? done(m2,'Mission 2 — Scans auth/agent en place')
          : resetCheck(m2,'Mission 2 — Scans authentifiés et/ou agent planifiés');

        (prio.value==='risk' && signals.checked)
          ? done(m3,'Mission 3 — Priorisation risk-based (EPSS/KEV)')
          : resetCheck(m3,'Mission 3 — Prioriser par le risque (CVSS + EPSS/KEV + contexte)');

        (sla.value==='set')
          ? done(m4,'Mission 4 — SLA de patching appliqués')
          : resetCheck(m4,'Mission 4 — Appliquer des SLA de patching');

        (exceptions.value==='governed')
          ? done(m5,'Mission 5 — Exceptions gouvernées')
          : resetCheck(m5,'Mission 5 — Encadrer les exceptions (validation + expiration)');

        (reporting.value==='kpi')
          ? done(m6,'Mission 6 — KPIs & tickets suivis')
          : resetCheck(m6,'Mission 6 — Suivre les KPIs via tickets & rapports');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }
      [inventory,scans,prio,signals,sla,exceptions,reporting].forEach(el=>el.addEventListener('change', onChange));
      renderTags(); // état initial
    })();
  </script>
</body>
</html>
