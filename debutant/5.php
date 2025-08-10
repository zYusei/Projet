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
  <title>Mises à jour & correctifs | FunCodeLab</title>
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:var(--muted);padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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
  .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:var(--accent)}

  /* Mini-jeu Update Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}

  .update-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.update-grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center}
  .row input,.row select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .tag{display:inline-block;border:1px solid var(--border);padding:.1rem .45rem;border-radius:999px;font-size:.8rem;color:#cfe1ff}
  .ok{color:#06121f;background:linear-gradient(90deg,#7cffc0,#4df1aa);border-color:#164238}
  .bad{color:#1a0c0c;background:linear-gradient(90deg,#ff9f9f,#ff6b6b);border-color:#4a1a1a}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  .ext-list{margin-top:8px;display:grid;grid-template-columns:1fr 1fr;gap:8px}
  .ext-item{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;padding:8px;display:flex;justify-content:space-between;align-items:center}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Mises à jour & correctifs</span></div>
      <span class="badge">Leçon 5 / 8</span>
    </div>

    <div class="title">
      <h1>Mises à jour & correctifs</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Les mises à jour corrigent des <strong>failles de sécurité</strong> et des bugs. Un système ou un navigateur non à jour est une porte ouverte aux attaques. Le réflexe : <strong>activer les mises à jour automatiques</strong> et <strong>relancer</strong> quand c’est demandé.</p>

          <h2>1) À mettre à jour en priorité</h2>
          <ul>
            <li><strong>OS</strong> (Windows, macOS, iOS, Android) : correctifs critiques réguliers.</li>
            <li><strong>Navigateur</strong> (Chrome, Firefox, Edge, Safari) : surface d’attaque majeure.</li>
            <li><strong>Extensions</strong> du navigateur et <strong>logiciels</strong> fréquemment utilisés (Zoom, Office, Spotify…).</li>
          </ul>

          <h2>2) Automatique + manuel</h2>
          <ul>
            <li>Active l’<strong>auto-update</strong> partout où c’est possible.</li>
            <li>Complète manuellement de temps en temps : « Rechercher des mises à jour ».</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> méfie-toi des pop-ups « Votre ordinateur est infecté, téléchargez ceci ! ». Passe toujours par le <strong>menu officiel</strong> du système ou du logiciel.
          </div>

          <!-- MINI-JEU : Update Lab -->
          <div class="mini">
            <h3>Mini-jeu : Update Lab</h3>
            <p class="rules">Vérifie et mets à jour un logiciel, puis active l’auto-update et mets à jour 2 extensions.</p>

            <div class="play">
              <div class="update-grid">
                <!-- Zone d'action -->
                <div class="box">
                  <div class="row">
                    <label style="font-weight:800">Logiciel</label>
                    <select id="app">
                      <option value="Chrome">Chrome</option>
                      <option value="Firefox">Firefox</option>
                      <option value="Zoom">Zoom</option>
                      <option value="Spotify">Spotify</option>
                      <option value="Windows">Windows</option>
                    </select>
                    <button id="btnCheck" class="btn-accent" type="button">Rechercher les mises à jour</button>
                  </div>

                  <div id="status" style="margin-top:10px;color:#dfe6ff">
                    Version locale : <span id="vLocal" class="tag">—</span> •
                    Dernière version : <span id="vRemote" class="tag">—</span>
                    <span id="badge" class="tag" style="margin-left:6px">état</span>
                  </div>

                  <div class="row" style="margin-top:10px">
                    <button id="btnUpdate" class="btn-ghost" type="button">Mettre à jour</button>
                    <label style="display:flex;gap:8px;align-items:center;margin-left:auto">
                      <input type="checkbox" id="auto" />
                      <span>Activer mises à jour automatiques</span>
                    </label>
                  </div>

                  <hr style="border:0;border-top:1px solid var(--border);margin:14px 0">

                  <strong>Extensions navigateur</strong>
                  <div class="ext-list" id="exts">
                    <!-- rempli en JS -->
                  </div>
                  <div class="row" style="margin-top:8px">
                    <button id="btnExtUpdate" class="btn-accent" type="button">Mettre à jour les extensions</button>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Trouver une mise à jour et l’installer</div>
                    <div class="check" id="c2">⬜ Mission 2 — Activer les mises à jour automatiques</div>
                    <div class="check" id="c3">⬜ Mission 3 — Mettre à jour au moins deux extensions</div>
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
              <div class="qhd">1) Pourquoi les mises à jour sont-elles importantes ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Elles corrigent des failles de sécurité</label>
                <label><input type="radio" name="q1"> Elles rendent l’ordinateur plus lourd</label>
                <label><input type="radio" name="q1"> C’est uniquement cosmétique</label>
                <div class="explain">Beaucoup d’updates patchent des vulnérabilités critiques.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Que faire si une fenêtre web te propose “un correctif urgent” ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Télécharger depuis la pop-up</label>
                <label><input type="radio" name="q2" data-correct="1"> Ouvrir le menu officiel du système/logiciel et vérifier depuis là</label>
                <label><input type="radio" name="q2"> Ignorer toutes les mises à jour</label>
                <div class="explain">Évite les liens externes : passe par les réglages officiels.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quels éléments mettre à jour en priorité ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> OS, navigateur et extensions</label>
                <label><input type="radio" name="q3"> Fonds d’écran</label>
                <label><input type="radio" name="q3"> Aucun, ce n’est pas nécessaire</label>
                <div class="explain">Ce sont les cibles les plus exposées.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) L’auto-update sert à…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Installer automatiquement les correctifs</label>
                <label><input type="radio" name="q4"> Désactiver les mises à jour</label>
                <label><input type="radio" name="q4"> Effacer des fichiers</label>
                <div class="explain">Ça évite d’oublier un patch important.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Après une mise à jour système, il faut parfois…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Redémarrer l’appareil</label>
                <label><input type="radio" name="q5"> Jeter l’ordinateur</label>
                <label><input type="radio" name="q5"> Rien, jamais</label>
                <div class="explain">Le redémarrage applique certains correctifs.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./4.php">← Leçon précédente</a>
              <a href="./6.php">Leçon suivante →</a>
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
        let total = 0, ok = 0;
        document.querySelectorAll('.q').forEach(q=>{
          total++;
          q.classList.remove('correct','wrong');
          const name = q.dataset.q;
          const checked = form.querySelector(`input[name="${name}"]:checked`);
          const explain = q.querySelector('.explain');
          if (explain) explain.style.display = 'block';
          if (checked && checked.hasAttribute('data-correct')){
            ok++; q.classList.add('correct');
          } else { q.classList.add('wrong'); }
        });
        scoreEl.style.display = 'inline-block';
        scoreEl.textContent = `Score : ${ok}/${total} • ${Math.round((ok/total)*100)}%`;
        scoreEl.scrollIntoView({behavior:'smooth',block:'center'});
      }

      function resetQuiz(){
        form.reset();
        document.querySelectorAll('.q').forEach(q=>{
          q.classList.remove('correct','wrong');
          const ex = q.querySelector('.explain'); if(ex) ex.style.display = 'none';
        });
        scoreEl.style.display = 'none';
      }

      btnCheck.addEventListener('click', evaluate);
      btnReset.addEventListener('click', resetQuiz);
    })();

    // ===== Mini-jeu : Update Lab =====
    (function(){
      const latest = {
        Chrome: '124.0.2',
        Firefox: '126.0',
        Zoom: '6.1.0',
        Spotify: '1.2.32',
        Windows: '23H2'
      };
      // on simule une version locale parfois en retard
      function randomLocal(app){
        const r = Math.random();
        if(app==='Windows') return r<0.6 ? '22H2' : '23H2';
        const parts = latest[app].split('.');
        // baisser parfois le dernier/avant-dernier
        if(r<0.7){
          parts[parts.length-1] = String(Math.max(0, (parseInt(parts[parts.length-1])||0)- (1+Math.floor(Math.random()*2))));
        }
        return parts.join('.');
      }

      const extSamples = [
        {name:'uBlock Origin', v:'1.54', latest:'1.56'},
        {name:'Password Manager', v:'2.3', latest:'2.4'},
        {name:'Dark Reader', v:'4.9', latest:'4.9'},
        {name:'Shopping Helper', v:'1.0', latest:'1.1'}
      ];

      const appSel = document.getElementById('app');
      const vLocal = document.getElementById('vLocal');
      const vRemote = document.getElementById('vRemote');
      const badge = document.getElementById('badge');
      const btnCheck = document.getElementById('btnCheck');
      const btnUpdate = document.getElementById('btnUpdate');
      const auto = document.getElementById('auto');
      const exts = document.getElementById('exts');
      const btnExtUpdate = document.getElementById('btnExtUpdate');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      let currentLocal = '—';

      function renderState(app){
        vLocal.textContent = currentLocal;
        vRemote.textContent = latest[app];
        const need = currentLocal !== latest[app];
        badge.textContent = need ? 'En retard' : 'À jour';
        badge.className = 'tag ' + (need ? 'bad' : 'ok');
      }

      function renderExts(){
        exts.innerHTML = extSamples.map((e,i)=>`
          <div class="ext-item" data-i="${i}">
            <span>${e.name}</span>
            <span>
              <span class="tag">v ${e.v}</span>
              <span class="tag" style="margin-left:6px">dern. ${e.latest}</span>
            </span>
          </div>
        `).join('');
      }

      btnCheck.addEventListener('click', ()=>{
        const app = appSel.value;
        currentLocal = randomLocal(app);
        renderState(app);
      });

      btnUpdate.addEventListener('click', ()=>{
        const app = appSel.value;
        if(currentLocal === '—') return;
        currentLocal = latest[app];
        renderState(app);
        c1.classList.add('done'); c1.textContent = '✅ Mission 1 — Mise à jour installée';
      });

      auto.addEventListener('change', ()=>{
        if(auto.checked){
          c2.classList.add('done'); c2.textContent = '✅ Mission 2 — Auto-update activé';
        }
      });

      btnExtUpdate.addEventListener('click', ()=>{
        let updated = 0;
        extSamples.forEach(e=>{
          if(e.v !== e.latest){ e.v = e.latest; updated++; }
        });
        renderExts();
        if(updated >= 2){
          c3.classList.add('done'); c3.textContent = '✅ Mission 3 — 2 extensions mises à jour';
        }
      });

      // init
      currentLocal = '—';
      renderState(appSel.value);
      renderExts();
    })();
  </script>
</body>
</html>
