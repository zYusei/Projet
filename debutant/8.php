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
  <title>Confidentialité & permissions | FunCodeLab</title>
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#9ae6b4;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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

  /* Privacy Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}

  .pv-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.pv-grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .row button,.row select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:.15rem .6rem;font-size:.8rem;color:#cfe1ff}
  .ok{background:linear-gradient(90deg,#7cffc0,#4df1aa);color:#06121f;border-color:#164238}
  .warnTag{background:linear-gradient(90deg,#ff9f9f,#ff6b6b);color:#1a0c0c;border-color:#4a1a1a}
  .switch{display:flex;align-items:center;gap:8px;margin:6px 0}
  .switch input{transform:scale(1.2)}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Confidentialité & permissions</span></div>
      <span class="badge">Leçon 8 / 8</span>
    </div>

    <div class="title">
      <h1>Confidentialité & permissions</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Le but : <strong>réduire les données collectées</strong> par les sites et applis, sans casser ton usage.</p>

          <h2>1) Bannières cookies (RGPD)</h2>
          <ul>
            <li><strong>Nécessaires</strong> : fonctionnement du site (connexion, panier). Pas de pub.</li>
            <li><strong>Statistiques</strong> : mesure d’audience, souvent anonymisée.</li>
            <li><strong>Marketing/traqueurs</strong> : profilage publicitaire. <em>Tu peux refuser.</em></li>
            <li>Choisis « <strong>Personnaliser</strong> » ⇒ <em>refuser tout sauf nécessaires</em>.</li>
          </ul>

          <h2>2) Permissions d’applications</h2>
          <ul>
            <li>Donne la <strong>localisation</strong> <em>uniquement</em> « pendant l’utilisation ».</li>
            <li><strong>Micro/caméra</strong> : au besoin seulement.</li>
            <li><strong>Photos/Fichiers</strong> : accès limité (album/ficheirs sélectionnés).</li>
          </ul>

          <h2>3) Limiter le pistage</h2>
          <ul>
            <li>Active la <strong>protection anti-pistage</strong> du navigateur (bloqueurs intégrés/extensions).</li>
            <li>Désactive l’ID publicitaire (iOS/Android) : <em>« Suivi publicitaire » → off</em>.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> si on te force « Accepter tout » sans alternative claire, ferme la page et reviens via un autre site ou moteur de recherche—souvent une version correcte existe.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Privacy Lab</h3>
            <p class="rules">Objectif : configurer de façon privative. <strong>Refuse les cookies marketing</strong>, <strong>réduis les permissions</strong> et <strong>bloque le pistage</strong>.</p>

            <div class="play">
              <div class="pv-grid">
                <!-- Zone actions -->
                <div class="box">
                  <!-- Cookies -->
                  <div class="row">
                    <label style="font-weight:800">Bannière cookies</label>
                    <select id="cookieChoice" aria-label="Choix bannière">
                      <option value="accept_all">Accepter tout</option>
                      <option value="refuse_all">Refuser tout</option>
                      <option value="custom">Personnaliser…</option>
                    </select>
                    <button id="btnApplyCookies" class="btn-accent" type="button">Appliquer</button>
                    <span id="cookieTag" class="pill">Cookies : —</span>
                  </div>
                  <div id="customCookies" style="display:none;margin-top:8px">
                    <label class="switch"><input id="cNec" type="checkbox" checked disabled> Nécessaires (toujours actifs)</label>
                    <label class="switch"><input id="cStat" type="checkbox"> Statistiques</label>
                    <label class="switch"><input id="cMkt" type="checkbox"> Marketing/traqueurs</label>
                  </div>

                  <hr style="border:0;border-top:1px solid var(--border);margin:12px 0">

                  <!-- Permissions -->
                  <div>
                    <strong>Permissions d’appli (exemple)</strong>
                    <div class="switch"><input id="pLoc" type="checkbox" checked> Localisation : <span id="locMode" class="pill">Toujours</span></div>
                    <div class="row" style="margin-left:30px">
                      <button id="btnLocWhile" class="btn-ghost" type="button">Pendant l’utilisation</button>
                      <button id="btnLocAlways" class="btn-ghost" type="button">Toujours</button>
                      <button id="btnLocOff" class="btn-ghost" type="button">Désactiver</button>
                    </div>
                    <label class="switch"><input id="pMic" type="checkbox"> Micro</label>
                    <label class="switch"><input id="pCam" type="checkbox"> Caméra</label>
                    <label class="switch"><input id="pPhotos" type="checkbox"> Photos (accès limité)</label>
                  </div>

                  <hr style="border:0;border-top:1px solid var(--border);margin:12px 0">

                  <!-- Pistage -->
                  <div class="row">
                    <label style="font-weight:800">Anti-pistage</label>
                    <button id="btnBlock" class="btn-ghost" type="button">Activer blocage</button>
                    <span id="trackTag" class="pill">Pistage : ON</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Refuser les cookies marketing</div>
                    <div class="check" id="m2">⬜ Mission 2 — Localisation « pendant l’utilisation »</div>
                    <div class="check" id="m3">⬜ Mission 3 — Activer le blocage de pistage</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Note :</strong> refuser les cookies marketing ne casse pas le site—seuls les pubs ciblées et certains traqueurs sont désactivés.
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
              <div class="qhd">1) Sur une bannière, quel choix protège le mieux ta vie privée ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> Accepter tout</label>
                <label><input type="radio" name="q1" data-correct="1"> Personnaliser puis refuser marketing/traqueurs</label>
                <label><input type="radio" name="q1"> Cliquer au hasard</label>
                <div class="explain">Garde les cookies nécessaires, refuse le marketing.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quelle option de localisation est recommandée ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Pendant l’utilisation</label>
                <label><input type="radio" name="q2"> Toujours</label>
                <label><input type="radio" name="q2"> Peu importe</label>
                <div class="explain">Moins d’exposition, assez précis quand l’appli est active.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) À quoi sert l’anti-pistage du navigateur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Bloquer cookies tiers/traqueurs et scripts publicitaires</label>
                <label><input type="radio" name="q3"> Accélérer le Wi-Fi</label>
                <label><input type="radio" name="q3"> Remplacer l’antivirus</label>
                <div class="explain">Il limite le suivi cross-site et la pub ciblée.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Une appli demande accès complet aux photos. Que faire ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Tout autoriser par facilité</label>
                <label><input type="radio" name="q4" data-correct="1"> Autoriser l’accès limité / photos sélectionnées</label>
                <label><input type="radio" name="q4"> Désinstaller Internet</label>
                <div class="explain">Donne <em>le minimum</em> utile.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./7.php">← Leçon précédente</a>
              <a href="../parcours.php">Retour aux parcours →</a>
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

    // ===== Mini-jeu : Privacy Lab =====
    (function(){
      // Cookies
      const choice = document.getElementById('cookieChoice');
      const btnApply = document.getElementById('btnApplyCookies');
      const cookieTag = document.getElementById('cookieTag');
      const customBox = document.getElementById('customCookies');
      const cStat = document.getElementById('cStat');
      const cMkt  = document.getElementById('cMkt');

      // Permissions
      const pLoc = document.getElementById('pLoc');
      const locMode = document.getElementById('locMode');
      const btnLocWhile = document.getElementById('btnLocWhile');
      const btnLocAlways= document.getElementById('btnLocAlways');
      const btnLocOff   = document.getElementById('btnLocOff');

      // Pistage
      const btnBlock = document.getElementById('btnBlock');
      const trackTag = document.getElementById('trackTag');

      // Missions
      const m1 = document.getElementById('m1');
      const m2 = document.getElementById('m2');
      const m3 = document.getElementById('m3');

      function mark(el, txt){ el.classList.add('done'); el.textContent = '✅ ' + txt; }

      // Cookies UI
      choice.addEventListener('change', ()=>{
        customBox.style.display = (choice.value==='custom') ? 'block' : 'none';
      });
      btnApply.addEventListener('click', ()=>{
        let text='—', cls='pill';
        if (choice.value==='accept_all'){ text='Tous acceptés'; cls='pill warnTag'; }
        if (choice.value==='refuse_all'){ text='Refusés (sauf nécessaires)'; cls='pill ok'; mark(m1,'Mission 1 — Cookies marketing refusés'); }
        if (choice.value==='custom'){
          const refused = !cMkt.checked;
          text = `Perso: stat=${cStat.checked?'on':'off'}, mkt=${cMkt.checked?'on':'off'}`;
          cls = 'pill ' + (refused ? 'ok' : 'warnTag');
          if (refused) mark(m1,'Mission 1 — Cookies marketing refusés');
        }
        cookieTag.className = cls;
        cookieTag.textContent = 'Cookies : ' + text;
      });

      // Localisation
      function setLoc(mode){
        if (mode==='while'){ pLoc.checked = true; locMode.textContent='Pendant usage'; locMode.className='pill ok'; mark(m2,'Mission 2 — Localisation « pendant l’utilisation »'); }
        if (mode==='always'){ pLoc.checked = true; locMode.textContent='Toujours'; locMode.className='pill warnTag'; }
        if (mode==='off'){ pLoc.checked = false; locMode.textContent='Désactivée'; locMode.className='pill'; }
      }
      btnLocWhile.addEventListener('click', ()=>setLoc('while'));
      btnLocAlways.addEventListener('click', ()=>setLoc('always'));
      btnLocOff.addEventListener('click', ()=>setLoc('off'));

      // Pistage
      let blocked=false;
      function updateTrack(){
        trackTag.textContent='Pistage : ' + (blocked?'OFF':'ON');
        trackTag.className = 'pill ' + (blocked ? 'ok' : 'warnTag');
      }
      btnBlock.addEventListener('click', ()=>{
        blocked=!blocked;
        btnBlock.textContent = blocked ? 'Désactiver blocage' : 'Activer blocage';
        updateTrack();
        if (blocked) mark(m3,'Mission 3 — Blocage de pistage activé');
      });

      // Init
      updateTrack();
    })();
  </script>
</body>
</html>
