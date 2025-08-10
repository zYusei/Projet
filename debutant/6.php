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
  <title>Pièces jointes & téléchargements | FunCodeLab</title>
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
  kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

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

  /* Mini-jeu File Inspector */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}

  .fi-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.fi-grid{grid-template-columns:1fr}}

  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center}
  .row input,.row select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .list{display:flex;flex-direction:column;gap:8px;margin-top:8px}
  .item{display:flex;justify-content:space-between;align-items:center;gap:8px;background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;padding:8px}
  .tag{display:inline-block;border:1px solid var(--border);padding:.1rem .45rem;border-radius:999px;font-size:.8rem;color:#cfe1ff}
  .ok{color:#06121f;background:linear-gradient(90deg,#7cffc0,#4df1aa);border-color:#164238}
  .warnTag{color:#1a0c0c;background:linear-gradient(90deg,#ff9f9f,#ff6b6b);border-color:#4a1a1a}

  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Pièces jointes & téléchargements</span></div>
      <span class="badge">Leçon 6 / 8</span>
    </div>

    <div class="title">
      <h1>Pièces jointes & téléchargements</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Avant d’ouvrir un fichier reçu par mail ou téléchargé, on vérifie <strong>qui l’envoie</strong>, <strong>ce que c’est</strong> (extension), et <strong>d’où il vient</strong>. En cas de doute : <strong>on n’ouvre pas</strong>.</p>

          <h2>1) Indices rapides</h2>
          <ul>
            <li><strong>Expéditeur / site</strong> crédible ? Domaine correct ? (ex : <code class="inline">@entreprise.com</code> et non <code class="inline">@entreprise-support.com</code>)</li>
            <li><strong>Extension</strong> du fichier : méfiance sur <code class="inline">.exe .scr .bat .js .vbs .msi .apk</code>. Préfère <code class="inline">.pdf .jpg .png</code>.</li>
            <li><strong>Double extension</strong> suspecte : <code class="inline">facture.pdf.exe</code>.</li>
            <li><strong>Zips protégés</strong> ou message pressant = red flag.</li>
          </ul>

          <h2>2) Télécharger malin</h2>
          <ul>
            <li>Privilégie les <strong>sites officiels</strong> (éditeur ou store).</li>
            <li>Évite les cracks et packs “tout-en-un”.</li>
            <li>Si possible, envoie le fichier douteux vers un antivirus/scan en ligne.</li>
          </ul>

          <div class="callout warn">
            <strong>Règle d’or :</strong> si quelque chose te semble bizarre (urgence, fautes, promesse trop belle), <em>stop</em>. Vérifie par un autre canal (téléphone, site officiel).
          </div>

          <!-- MINI-JEU : File Inspector -->
          <div class="mini">
            <h3>Mini-jeu : File Inspector</h3>
            <p class="rules">Analyse des fichiers reçus. Ton but : <strong>Marquer dangereux</strong> les mauvais et <strong>Approuver</strong> les bons (au moins 2 corrects et 2 dangereux).</p>

            <div class="play">
              <div class="fi-grid">
                <!-- Zone d'analyse -->
                <div class="box">
                  <div class="row">
                    <label style="font-weight:800">Fichier</label>
                    <select id="fileSel"></select>
                    <button id="btnAnalyse" class="btn-accent" type="button">Analyser</button>
                  </div>

                  <div id="details" style="margin-top:10px;display:none">
                    <p style="margin:0 0 8px"><strong>Nom :</strong> <span id="dName"></span></p>
                    <p style="margin:0 0 8px"><strong>Source :</strong> <span id="dSource" class="tag"></span></p>
                    <p style="margin:0 0 8px"><strong>Extension :</strong> <span id="dExt" class="tag"></span></p>
                    <p style="margin:0"><strong>Notes :</strong> <span id="dNotes" class="tag"></span></p>

                    <div class="row" style="margin-top:12px">
                      <button id="btnApprove" class="btn-ghost" type="button">Approuver (ouvrir)</button>
                      <button id="btnBlock" class="btn-ghost" type="button">Marquer dangereux</button>
                    </div>
                  </div>

                  <hr style="border:0;border-top:1px solid var(--border);margin:14px 0">

                  <strong>Historique</strong>
                  <div class="list" id="history"></div>
                </div>

                <!-- Missions -->
                <div class="box">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Analyser 1 fichier</div>
                    <div class="check" id="c2">⬜ Mission 2 — Marquer dangereux au moins 2 malveillants</div>
                    <div class="check" id="c3">⬜ Mission 3 — Approuver au moins 2 fichiers sûrs</div>
                  </div>

                  <div class="callout" style="margin-top:12px">
                    <strong>Astuce :</strong> attention aux <em>double extensions</em> et aux exécutables. Si l’expéditeur insiste pour que tu “déverrouilles” le document, méfiance.
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
              <div class="qhd">1) Laquelle est une extension risquée à ouvrir depuis un mail ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> .pdf</label>
                <label><input type="radio" name="q1"> .jpg</label>
                <label><input type="radio" name="q1" data-correct="1"> .exe</label>
                <div class="explain">Les exécutables peuvent lancer du code arbitraire.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) “Facture_2024.pdf.exe” est…</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Un fichier suspect à éviter (double extension)</label>
                <label><input type="radio" name="q2"> Un PDF classique</label>
                <label><input type="radio" name="q2"> Sans danger</label>
                <div class="explain">La vraie extension est .exe (dangereux).</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) D’où télécharger une application ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Site officiel / store</label>
                <label><input type="radio" name="q3"> Lien dans un commentaire YouTube</label>
                <label><input type="radio" name="q3"> Un site inconnu “tout-gratuit”</label>
                <div class="explain">Réduis les risques en privilégiant les sources officielles.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Que faire en cas de doute sur une pièce jointe ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Ne pas ouvrir et vérifier par un autre canal</label>
                <label><input type="radio" name="q4"> Ouvrir vite pour vérifier</label>
                <label><input type="radio" name="q4"> Transférer à tout le monde</label>
                <div class="explain">On vérifie l’authenticité d’abord.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Les archives ZIP protégées par mot de passe…</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> Sont toujours sûres</label>
                <label><input type="radio" name="q5" data-correct="1"> Peuvent masquer des malwares, prudence</label>
                <label><input type="radio" name="q5"> Sont bloquées par tous les antivirus</label>
                <div class="explain">Elles peuvent contourner certains scans automatiques.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./5.php">← Leçon précédente</a>
              <a href="./7.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : File Inspector =====
    (function(){
      const files = [
        {name:'Facture_2024.pdf.exe', ext:'.exe', source:'Email inconnu', notes:'Double extension', bad:true},
        {name:'photos.zip', ext:'.zip', source:'Ami (mail)', notes:'Archive compressée', bad:false},
        {name:'rapport_Q2.pdf', ext:'.pdf', source:'Collègue @entreprise.com', notes:'Document attendu', bad:false},
        {name:'setup-win-update.scr', ext:'.scr', source:'Lien pub', notes:'Écran de veille exécutable', bad:true},
        {name:'billets_train.jpg', ext:'.jpg', source:'Partage familial', notes:'Image', bad:false},
        {name:'bon_cadeau.pdf.js', ext:'.js', source:'Promo inconnue', notes:'Double extension script', bad:true},
      ];

      const sel = document.getElementById('fileSel');
      const details = document.getElementById('details');
      const dName = document.getElementById('dName');
      const dSource = document.getElementById('dSource');
      const dExt = document.getElementById('dExt');
      const dNotes = document.getElementById('dNotes');
      const history = document.getElementById('history');

      const btnAnalyse = document.getElementById('btnAnalyse');
      const btnApprove = document.getElementById('btnApprove');
      const btnBlock = document.getElementById('btnBlock');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      let current = null;
      let approved = 0, blocked = 0;

      function renderSelect(){
        sel.innerHTML = files.map((f,i)=>`<option value="${i}">${f.name}</option>`).join('');
      }
      function showDetails(f){
        dName.textContent = f.name;
        dSource.textContent = f.source; dSource.className = 'tag ' + (f.source.includes('@') || f.source.includes('Collègue') || f.source.includes('Partage') ? 'ok' : 'warnTag');
        dExt.textContent = f.ext; dExt.className = 'tag ' + (['.exe','.scr','.js','.bat','.vbs','.msi','.apk'].includes(f.ext) ? 'warnTag' : 'ok');
        dNotes.textContent = f.notes; dNotes.className = 'tag ' + (f.notes.toLowerCase().includes('double') ? 'warnTag' : '');
        details.style.display = 'block';
      }
      function pushHistory(text, ok){
        const el = document.createElement('div');
        el.className = 'item';
        el.innerHTML = `<span>${text}</span><span class="tag ${ok?'ok':'warnTag'}">${ok?'Approuvé':'Dangereux'}</span>`;
        history.prepend(el);
      }
      function checkMissions(){
        if(history.children.length>0){ c1.classList.add('done'); c1.textContent='✅ Mission 1 — 1 fichier analysé'; }
        if(blocked>=2){ c2.classList.add('done'); c2.textContent='✅ Mission 2 — 2 dangereux bloqués'; }
        if(approved>=2){ c3.classList.add('done'); c3.textContent='✅ Mission 3 — 2 fichiers sûrs approuvés'; }
      }

      btnAnalyse.addEventListener('click', ()=>{
        current = files[parseInt(sel.value,10)];
        showDetails(current);
      });

      btnApprove.addEventListener('click', ()=>{
        if(!current) return;
        const safeByLogic = !current.bad;
        pushHistory(current.name, safeByLogic);
        if(safeByLogic) approved++;
        checkMissions();
      });

      btnBlock.addEventListener('click', ()=>{
        if(!current) return;
        pushHistory(current.name, false);
        if(current.bad) blocked++;
        checkMissions();
      });

      renderSelect();
      showDetails(files[0]); current = files[0];
    })();
  </script>
</body>
</html>
