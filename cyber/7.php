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
  <title>Threat Hunting | FunCodeLab</title>
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

  /* Mini-jeu : Threat Hunting Lab */
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
  .check{display:block;white-space:normal;padding:4px 0;color:var(--muted)} /* chaque mission sur sa propre ligne */
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🕵️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Blue Team &nbsp;›&nbsp; <span style="color:var(--accent)">Threat Hunting</span></div>
      <span class="badge">Leçon 7 / 8</span>
    </div>

    <div class="title">
      <h1>Threat Hunting : hypothèses, IoC &amp; télémétrie</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Le <strong>hunting</strong> est une démarche <em>proactive</em> : on part d’une <strong>hypothèse</strong> (TTP, campagne, surface) puis on la teste dans la <strong>télémétrie</strong> (EDR, logs Windows, DNS, authent, proxy, cloud). On collecte des <strong>IoC/IoA</strong>, on <strong>scoppe</strong> l’impact, on <strong>contient</strong>, et on documente une <strong>timeline</strong>.</p>

          <h2>Cadre simple (PIR → Hypothèse → Tests → Verdict)</h2>
          <ul>
            <li><strong>Hypothèse testable</strong> : “Un accès initial via LNK a permis l’exécution de <code class="inline">powershell.exe -enc</code> depuis Downloads.”</li>
            <li><strong>Télémétrie minimale</strong> : EDR + DNS + Auth + Proxy. Sans logs ➝ pas de chasse.</li>
            <li><strong>Tests</strong> : requêtes sur <em>parent/child</em> process, encodage base64, connexions vers domaines récents, authent anormale.</li>
            <li><strong>IoC</strong> : hash, domaine, IP, chemin, mutex, clé registre. Toujours valider (contexte, fréquence, 1er vu).</li>
          </ul>

          <div class="callout warn">
            <strong>Raccourci gagnant :</strong> Hypothèse claire → pack de requêtes prêt-à-l’emploi → télémétrie EDR/DNS/Auth/Proxy activée → triage IoC → containment + timeline en sortie.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Threat Hunting Lab</h3>
            <p class="rules">Active les bons éléments de chasse. Objectif : valider les 6 missions ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages Hunting">
                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Hypothèse</label>
                    <select id="hypo">
                      <option value="none" selected>Vague / non testable</option>
                      <option value="testable">Claire &amp; testable (TTP + observables)</option>
                    </select>
                    <span id="hypoTag" class="pill warnP">Floue</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Télémétrie</label>
                    <select id="telemetry">
                      <option value="low" selected>Partielle</option>
                      <option value="core">EDR + DNS + Auth + Proxy</option>
                    </select>
                    <span id="telemetryTag" class="pill warnP">Insuffisante</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Pack de requêtes</label>
                    <select id="queries">
                      <option value="none" selected>Aucun</option>
                      <option value="procnet">Process + réseau anormal</option>
                      <option value="rare">+ Événements rares / encodés</option>
                    </select>
                    <span id="queriesTag" class="pill warnP">Pas prêt</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Triage des IoC</label>
                    <input type="checkbox" id="ioc">
                    <span id="iocTag" class="pill warnP">Non triés</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Scope &amp; containment</label>
                    <select id="contain">
                      <option value="none" selected>Rien</option>
                      <option value="isolate">Isoler hôtes + reset comptes</option>
                    </select>
                    <span id="containTag" class="pill warnP">Non contenu</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Rapport / timeline</label>
                    <input type="checkbox" id="report">
                    <span id="reportTag" class="pill warnP">Non rédigée</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Énoncer une hypothèse claire et testable</div>
                    <div class="check" id="m2">⬜ Mission 2 — Disposer de la télémétrie cœur (EDR/DNS/Auth/Proxy)</div>
                    <div class="check" id="m3">⬜ Mission 3 — Lancer les requêtes de chasse adaptées</div>
                    <div class="check" id="m4">⬜ Mission 4 — Trier/valider les IoC (contexte &amp; fréquence)</div>
                    <div class="check" id="m5">⬜ Mission 5 — Scope de l’incident + containment</div>
                    <div class="check" id="m6">⬜ Mission 6 — Rédiger une timeline et partager les leçons</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> Hypothèse=<em>testable</em>, Télémétrie=<em>core</em>, Requêtes=<em>rare</em>, IoC=<em>coché</em>, Containment=<em>isolate</em>, Rapport=<em>coché</em>.
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
              <div class="qhd">1) Une bonne hypothèse de hunting doit être…</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Claire, testable et liée à des TTP/observables</label>
                <label><input type="radio" name="q1"> Large, vague et inspirante</label>
                <label><input type="radio" name="q1"> Basée sur des suppositions non vérifiables</label>
                <div class="explain">Testable = on sait quelles données interroger et quels résultats attendus.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Sans quels <em>minimum</em> de logs, la chasse devient aveugle ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> EDR, DNS, Authentification et Proxy</label>
                <label><input type="radio" name="q2"> Météo et trafic routier</label>
                <label><input type="radio" name="q2"> Uniquement des backups</label>
                <div class="explain">Ces quatre flux couvrent processus, réseau sortant et identités.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Pourquoi valider les IoC (hash/domaines) avant blocage global ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Pour éviter les faux positifs et comprendre le contexte d’usage</label>
                <label><input type="radio" name="q3"> Pour perdre du temps</label>
                <label><input type="radio" name="q3"> Parce que c’est à la mode</label>
                <div class="explain">Regarder fréquence, 1er vu, propriétaire, réputation, chevauchement campagne.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Le containment vise surtout à…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Stopper la progression de l’attaquant (isolation hôtes, reset secrets)</label>
                <label><input type="radio" name="q4"> Améliorer le SEO</label>
                <label><input type="radio" name="q4"> Nettoyer les boîtes mail</label>
                <div class="explain">Réduction rapide du risque et du temps de séjour de l’adversaire.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Une sortie de chasse utile inclut…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Une timeline, les trouvailles, et les leçons à intégrer (détections)</label>
                <label><input type="radio" name="q5"> Un GIF de célébration uniquement</label>
                <label><input type="radio" name="q5"> Rien, tout en tête</label>
                <div class="explain">On formalise pour réutiliser : playbooks, règles SIEM/EDR, indicateurs.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./6.php">← Leçon précédente</a>
              <a href="./8.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Threat Hunting Lab =====
    (function(){
      const hypo = document.getElementById('hypo');
      const telemetry = document.getElementById('telemetry');
      const queries = document.getElementById('queries');
      const ioc = document.getElementById('ioc');
      const contain = document.getElementById('contain');
      const report = document.getElementById('report');

      const tags = {
        hypo: document.getElementById('hypoTag'),
        telemetry: document.getElementById('telemetryTag'),
        queries: document.getElementById('queriesTag'),
        ioc: document.getElementById('iocTag'),
        contain: document.getElementById('containTag'),
        report: document.getElementById('reportTag'),
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
        tag(tags.hypo, hypo.value==='testable' ? 'Hypothèse testable' : 'Floue', hypo.value==='testable' ? 'ok' : 'warnP');
        tag(tags.telemetry, telemetry.value==='core' ? 'EDR+DNS+Auth+Proxy' : 'Insuffisante', telemetry.value==='core' ? 'ok' : 'warnP');
        const qtxt = (queries.value==='rare') ? 'Requêtes avancées' : (queries.value==='procnet' ? 'Proc+réseau' : 'Aucune');
        tag(tags.queries, qtxt, queries.value!=='none' ? 'ok' : 'warnP');
        tag(tags.ioc, ioc.checked ? 'IoC triés' : 'Non triés', ioc.checked ? 'ok' : 'warnP');
        tag(tags.contain, contain.value==='isolate' ? 'Containment actif' : 'Non contenu', contain.value==='isolate' ? 'ok' : 'warnP');
        tag(tags.report, report.checked ? 'Timeline rédigée' : 'Non rédigée', report.checked ? 'ok' : 'warnP');
      }

      function renderMissions(){
        if (!touched) return;
        (hypo.value==='testable')
          ? done(m1,'Mission 1 — Hypothèse claire et testable')
          : resetCheck(m1,'Mission 1 — Énoncer une hypothèse claire et testable');

        (telemetry.value==='core')
          ? done(m2,'Mission 2 — Télémétrie cœur disponible')
          : resetCheck(m2,'Mission 2 — Disposer de la télémétrie cœur (EDR/DNS/Auth/Proxy)');

        (queries.value==='rare')
          ? done(m3,'Mission 3 — Requêtes de chasse lancées')
          : resetCheck(m3,'Mission 3 — Lancer les requêtes de chasse adaptées');

        (ioc.checked)
          ? done(m4,'Mission 4 — IoC triés/validés')
          : resetCheck(m4,'Mission 4 — Trier/valider les IoC (contexte & fréquence)');

        (contain.value==='isolate')
          ? done(m5,'Mission 5 — Scope + containment effectués')
          : resetCheck(m5,'Mission 5 — Scope de l’incident + containment');

        (report.checked)
          ? done(m6,'Mission 6 — Timeline & leçons produites')
          : resetCheck(m6,'Mission 6 — Rédiger une timeline et partager les leçons');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }
      [hypo,telemetry,queries,ioc,contain,report].forEach(el=>el.addEventListener('change', onChange));
      renderTags(); // état initial neutre
    })();
  </script>
</body>
</html>
