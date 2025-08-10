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
  <title>Endpoint Investigations | FunCodeLab</title>
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
  .title .progress-pill{font-weight:800;color:#1b0a0a;background:linear-gradient(90deg,#ffb4b4,#ff6b6b);padding:.35rem .65rem;border-radius:999px;border:1px solid #5a1d1d}

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

  /* Mini-jeu : Endpoint DFIR Lab */
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
      <div class="crumbs">🧊 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Blue Team &nbsp;›&nbsp; <span style="color:var(--accent)">Endpoint Investigations</span></div>
      <span class="badge">Leçon 9 / 10</span>
    </div>

    <div class="title">
      <h1>Endpoint Investigations : DFIR, artefacts &amp; timeline</h1>
      <span class="progress-pill">Niveau : Difficile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>En <strong>DFIR endpoint</strong>, on cherche à <strong>préserver l’évidence</strong>, <strong>collecter les artefacts</strong> (mémoire + disque), <strong>reconstruire la chronologie</strong> et <strong>étayer</strong> par des hashes et une chaîne de conservation (<em>chain of custody</em>). Les erreurs classiques : manipuler la machine avant acquisition, ne pas capturer la RAM, oublier l’horodatage/UTC, et ne pas consigner les actions.</p>

          <h2>Artefacts clés (Windows)</h2>
          <ul>
            <li><strong>Volatils</strong> : mémoire, connexions réseau, process, handles.</li>
            <li><strong>Persistants</strong> : $MFT, USN Journal, Prefetch, Amcache, Shimcache, SRUM, Event Logs, Browser History.</li>
            <li><strong>Exécution</strong> : WMI, Scheduled Tasks, Services, Run Keys.</li>
          </ul>

          <div class="callout warn">
            <strong>Mémo :</strong> Isoler ➝ <em>acquérir</em> RAM + disque ➝ hacher &amp; sceller ➝ super-timeline ➝ hypothèses &amp; conclusions. Tout noter (qui, quoi, quand, comment).
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Endpoint DFIR Lab</h3>
            <p class="rules">Choisis les bonnes actions pour faire valider les 6 missions ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages DFIR">
                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Isolation</label>
                    <select id="isolate">
                      <option value="none" selected>Pas d’isolement</option>
                      <option value="network">Isoler réseau (EDR/pare-feu)</option>
                    </select>
                    <span id="isolateTag" class="pill warnP">Risque de contamination</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Acquisition</label>
                    <select id="acq">
                      <option value="live" selected>Triage live partiel</option>
                      <option value="full">Image disque forensique (read-only)</option>
                    </select>
                    <span id="acqTag" class="pill warnP">Acquisition faible</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Mémoire (RAM)</label>
                    <select id="mem">
                      <option value="off" selected>Non capturée</option>
                      <option value="on">Capture mémoire complète</option>
                    </select>
                    <span id="memTag" class="pill warnP">Volatiles perdus</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Pack d’artefacts</label>
                    <select id="art">
                      <option value="min" selected>Minimal</option>
                      <option value="core">Core (MFT/USN/Logs/Prefetch)</option>
                      <option value="ext">Étendu (+ Amcache/Browser/SRUM)</option>
                    </select>
                    <span id="artTag" class="pill warnP">Artefacts incomplets</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Intégrité</label>
                    <select id="integ">
                      <option value="none" selected>Aucun hash / pas de CoC</option>
                      <option value="hash">Hash (SHA-256) + chain of custody</option>
                    </select>
                    <span id="integTag" class="pill warnP">Preuve fragile</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Timeline</label>
                    <select id="timeline">
                      <option value="none" selected>Aucune</option>
                      <option value="basic">Timeline basique (EVTX)</option>
                      <option value="super">Super-timeline (plaso/Timesketch)</option>
                    </select>
                    <span id="timelineTag" class="pill warnP">Histoire incomplète</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Préserver la scène (isolement réseau)</div>
                    <div class="check" id="m2">⬜ Mission 2 — Acquérir le disque en lecture seule</div>
                    <div class="check" id="m3">⬜ Mission 3 — Capturer la mémoire (RAM)</div>
                    <div class="check" id="m4">⬜ Mission 4 — Collecter un pack d’artefacts complet</div>
                    <div class="check" id="m5">⬜ Mission 5 — Garantir l’intégrité (hash + chain of custody)</div>
                    <div class="check" id="m6">⬜ Mission 6 — Construire une super-timeline d’investigation</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> Isolation=<em>network</em>, Acquisition=<em>full</em>, RAM=<em>on</em>, Artefacts=<em>ext</em>, Intégrité=<em>hash</em>, Timeline=<em>super</em>.
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
              <div class="qhd">1) Pourquoi capturer la RAM lors d’une réponse à incident ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Pour conserver des éléments volatils (process, clés déchiffrées, connexions)</label>
                <label><input type="radio" name="q1"> Pour réduire la taille du disque</label>
                <label><input type="radio" name="q1"> Pour accélérer l’ordinateur</label>
                <div class="explain">Beaucoup d’artefacts critiques n’existent qu’en mémoire.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Pourquoi hacher les images et tenir une chaîne de conservation ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Pour prouver l’intégrité et la traçabilité de la preuve</label>
                <label><input type="radio" name="q2"> Pour économiser du stockage</label>
                <label><input type="radio" name="q2"> Pour rendre l’analyse plus rapide</label>
                <div class="explain">Hash + CoC rendent la preuve opposable et reproductible.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quel couple d’artefacts aide à reconstituer l’exécution d’un binaire ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Prefetch + Amcache</label>
                <label><input type="radio" name="q3"> Papier peint + thèmes</label>
                <label><input type="radio" name="q3"> Drivers audio</label>
                <div class="explain">Prefetch trace l’exécution; Amcache conserve des métadonnées d’exécutables.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) La super-timeline sert à…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Agréger des sources multiples et ordonner les évènements</label>
                <label><input type="radio" name="q4"> Sauvegarder les postes</label>
                <label><input type="radio" name="q4"> Générer des mots de passe</label>
                <div class="explain">On croise MFT, USN, logs, navigateur, etc., sur un axe temps unique.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle action doit venir <em>avant</em> toute manipulation de l’hôte ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Isoler l’hôte et lancer l’acquisition en lecture seule</label>
                <label><input type="radio" name="q5"> Lancer un correctif Windows Update</label>
                <label><input type="radio" name="q5"> Redémarrer pour “réparer”</label>
                <div class="explain">Préserver les preuves d’abord — toute action peut les altérer.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./8.php">← Leçon précédente</a>
              <a href="./10.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Endpoint DFIR Lab =====
    (function(){
      const isolate = document.getElementById('isolate');
      const acq = document.getElementById('acq');
      const mem = document.getElementById('mem');
      const art = document.getElementById('art');
      const integ = document.getElementById('integ');
      const timeline = document.getElementById('timeline');

      const tags = {
        isolate: document.getElementById('isolateTag'),
        acq: document.getElementById('acqTag'),
        mem: document.getElementById('memTag'),
        art: document.getElementById('artTag'),
        integ: document.getElementById('integTag'),
        timeline: document.getElementById('timelineTag'),
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
        tag(tags.isolate, isolate.value==='network' ? 'Isolé' : 'Non isolé', isolate.value==='network' ? 'ok' : 'warnP');
        tag(tags.acq, acq.value==='full' ? 'Image disque' : 'Triage live', acq.value==='full' ? 'ok' : 'warnP');
        tag(tags.mem, mem.value==='on' ? 'RAM capturée' : 'Non capturée', mem.value==='on' ? 'ok' : 'warnP');
        const artTxt = art.value==='ext' ? 'Artefacts étendus' : art.value==='core' ? 'Artefacts core' : 'Minimal';
        tag(tags.art, artTxt, art.value!=='min' ? 'ok' : 'warnP');
        tag(tags.integ, integ.value==='hash' ? 'SHA-256 + CoC' : 'Aucune', integ.value==='hash' ? 'ok' : 'warnP');
        tag(tags.timeline, timeline.value==='super' ? 'Super-timeline' : (timeline.value==='basic'?'Timeline basique':'Aucune'), timeline.value!=='none' ? 'ok' : 'warnP');
      }

      function renderMissions(){
        if (!touched) return;

        (isolate.value==='network')
          ? done(m1,'Mission 1 — Hôte isolé')
          : resetCheck(m1,'Mission 1 — Préserver la scène (isolement réseau)');

        (acq.value==='full')
          ? done(m2,'Mission 2 — Image disque acquise')
          : resetCheck(m2,'Mission 2 — Acquérir le disque en lecture seule');

        (mem.value==='on')
          ? done(m3,'Mission 3 — RAM capturée')
          : resetCheck(m3,'Mission 3 — Capturer la mémoire (RAM)');

        (art.value==='ext')
          ? done(m4,'Mission 4 — Pack d’artefacts complet')
          : resetCheck(m4,'Mission 4 — Collecter un pack d’artefacts complet');

        (integ.value==='hash')
          ? done(m5,'Mission 5 — Intégrité garantie')
          : resetCheck(m5,'Mission 5 — Garantir l’intégrité (hash + chain of custody)');

        (timeline.value==='super')
          ? done(m6,'Mission 6 — Super-timeline construite')
          : resetCheck(m6,'Mission 6 — Construire une super-timeline d’investigation');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }
      [isolate,acq,mem,art,integ,timeline].forEach(el=>el.addEventListener('change', onChange));
      renderTags(); // état initial neutre
    })();
  </script>
</body>
</html>
