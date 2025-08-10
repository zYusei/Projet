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
  <title>Activer 2FA partout | FunCodeLab</title>
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

  pre,code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,"Segoe UI Mono",Consolas,"Liberation Mono",monospace}
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

  /* Mini-jeu 2FA Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}

  .lab{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}

  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center}
  .row input, .row select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .totp{display:flex;align-items:center;gap:14px;margin-top:10px}
  .qr{width:92px;height:92px;border-radius:8px;border:1px solid var(--border);background:
      linear-gradient(45deg,#0f1b3a 25%,transparent 25%) -8px 0/16px 16px,
      linear-gradient(-45deg,#0f1b3a 25%,transparent 25%) -8px 0/16px 16px,
      linear-gradient(45deg,transparent 75%,#0f1b3a 75%) -8px 0/16px 16px,
      linear-gradient(-45deg,transparent 75%,#0f1b3a 75%) -8px 0/16px 16px,#0b132b}
  .code-box{display:flex;flex-direction:column;gap:6px}
  .code-box .live{font-weight:800;font-size:1.6rem;letter-spacing:.2rem}
  .prog{height:6px;border-radius:999px;background:#1a2247;overflow:hidden;border:1px solid var(--border)}
  .bar{height:100%;width:0%;background:#4df1aa}

  .codes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:10px}
  .codes div{background:#0b132b;border:1px solid var(--code-bd);padding:8px;border-radius:8px;text-align:center}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Activer 2FA partout</span></div>
      <span class="badge">Leçon 4 / 8</span>
    </div>

    <div class="title">
      <h1>Activer 2FA partout</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>La <strong>double authentification (2FA)</strong> ajoute une deuxième preuve d’identité (un code à usage unique) en plus du mot de passe. Résultat : même si ton mot de passe fuite, ton compte reste protégé.</p>

          <h2>1) Les options (du mieux au moins bien)</h2>
          <ul>
            <li><strong>App d’authentification (TOTP)</strong> : codes à 6 chiffres qui changent toutes les 30 s (Google Authenticator, Microsoft Authenticator, Authy…).</li>
            <li><strong>Clé de sécurité</strong> (YubiKey, passkey) : super sûr mais matériel dédié.</li>
            <li><strong>SMS</strong> : mieux que rien, mais vulnérable au <em>SIM swap</em> (vol de numéro).</li>
          </ul>

          <h2>2) Les étapes pour l’activer</h2>
          <ol>
            <li>Va dans les <strong>Paramètres de sécurité</strong> de ton service (compte Google, réseaux sociaux, banque…).</li>
            <li>Choisis <strong>“Application d’authentification”</strong>, scanne le QR avec l’app, <strong>entre le code</strong> affiché.</li>
            <li><strong>Télécharge les codes de secours</strong> et garde-les hors ligne (imprimés, coffre, gestionnaire de mots de passe).</li>
          </ol>

          <div class="callout warn">
            <strong>Important :</strong> ne donne <em>jamais</em> ton code à quelqu’un au téléphone. Les services légitimes ne te le demanderont pas.
          </div>

          <!-- MINI-JEU : 2FA Lab -->
          <div class="mini">
            <h3>Mini-jeu : 2FA Lab</h3>
            <p class="rules">Simule l’activation 2FA : scanne (faux QR), entre le code TOTP à 6 chiffres, puis génère et sauvegarde des codes de secours.</p>

            <div class="play">
              <div class="lab">
                <!-- Zone gauche -->
                <div class="box" id="box2fa">
                  <div class="row">
                    <label style="font-weight:800">Site</label>
                    <select id="site">
                      <option>MonCompte</option>
                      <option>RéseauSocial</option>
                      <option>Webmail</option>
                    </select>
                    <button id="btnStart" class="btn-accent" type="button">Démarrer l’activation</button>
                  </div>

                  <div class="totp" id="totpZone" style="display:none">
                    <div class="qr" title="QR de démonstration"></div>
                    <div class="code-box">
                      <div style="font-size:.9rem;color:var(--muted)">Code de l’app (démonstration)</div>
                      <div class="live" id="liveCode">000000</div>
                      <div class="prog"><div class="bar" id="bar"></div></div>
                    </div>
                  </div>

                  <div id="enterZone" style="display:none;margin-top:10px">
                    <div class="row">
                      <input id="inputCode" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="Entre le code à 6 chiffres">
                      <button id="btnVerify" class="btn-accent" type="button">Vérifier</button>
                    </div>
                    <small class="tips">Astuce : le code change toutes les ~30 secondes.</small>
                  </div>

                  <div id="backupZone" style="display:none;margin-top:14px">
                    <strong>Codes de secours</strong>
                    <div class="codes" id="codesBox"></div>
                    <div class="row" style="margin-top:8px">
                      <button id="btnMarkSaved" class="btn-ghost" type="button">J’ai bien sauvegardé</button>
                    </div>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Démarrer l’activation et afficher le code TOTP</div>
                    <div class="check" id="c2">⬜ Mission 2 — Entrer le bon code à 6 chiffres</div>
                    <div class="check" id="c3">⬜ Mission 3 — Générer et sauvegarder les codes de secours</div>
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
              <div class="qhd">1) Pourquoi l’app d’authentification est-elle recommandée vs SMS ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Les SMS peuvent être détournés (SIM swap), l’app est locale</label>
                <label><input type="radio" name="q1"> Les SMS sont plus rapides et plus sûrs</label>
                <label><input type="radio" name="q1"> L’app envoie ton mot de passe au serveur</label>
                <div class="explain">Le code TOTP est généré sur ton téléphone et ne dépend pas de l’opérateur.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Que faire des codes de secours ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Les sauvegarder hors ligne (imprimés / coffre / manager)</label>
                <label><input type="radio" name="q2"> Les envoyer par email à un ami</label>
                <label><input type="radio" name="q2"> Les publier pour pouvoir les retrouver</label>
                <div class="explain">Garde-les comme des clés physiques. Un seul usage par code.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Un code TOTP à 6 chiffres est…</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> Valable indéfiniment</label>
                <label><input type="radio" name="q3" data-correct="1"> Valable environ 30 secondes</label>
                <label><input type="radio" name="q3"> Valable 24 heures</label>
                <div class="explain">La plupart des services utilisent une fenêtre de 30 s.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Si tu perds ton téléphone, que faire ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Utiliser un code de secours / transférer l’app sur un nouvel appareil</label>
                <label><input type="radio" name="q4"> Donner ton code à l’assistance par téléphone</label>
                <label><input type="radio" name="q4"> Rien, c’est perdu à jamais</label>
                <div class="explain">Les codes de secours te débloquent l’accès pour réinitialiser la 2FA.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quel message est vrai ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Un service légitime ne demandera jamais ton code 2FA par email/téléphone</label>
                <label><input type="radio" name="q5"> Partager le code avec “le support” est normal</label>
                <label><input type="radio" name="q5"> On peut réutiliser un code plusieurs fois</label>
                <div class="explain">Un code TOTP est personnel et à usage unique.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./3.php">← Leçon précédente</a>
              <a href="./5.php">Leçon suivante →</a>
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

    // ===== Mini-jeu 2FA Lab (simulation TOTP + backup codes) =====
    (function(){
      const btnStart = document.getElementById('btnStart');
      const totpZone = document.getElementById('totpZone');
      const enterZone = document.getElementById('enterZone');
      const backupZone = document.getElementById('backupZone');
      const liveCode = document.getElementById('liveCode');
      const bar = document.getElementById('bar');
      const inputCode = document.getElementById('inputCode');
      const btnVerify = document.getElementById('btnVerify');
      const codesBox = document.getElementById('codesBox');
      const btnMarkSaved = document.getElementById('btnMarkSaved');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      let secret = null; let timer = null;

      function randomSecret(n=16){
        const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        let s = '';
        for(let i=0;i<n;i++) s += alphabet[Math.floor(Math.random()*alphabet.length)];
        return s;
      }

      // Pseudo-TOTP (démo) : hash léger secret+timeStep → 6 chiffres
      function demoTotp(secret){
        const step = Math.floor(Date.now()/30000);
        let h = 0;
        const all = secret + '|' + step.toString(16);
        for(let i=0;i<all.length;i++){
          h = (h*31 + all.charCodeAt(i)) >>> 0;
        }
        const code = (h % 1000000).toString().padStart(6,'0');
        // progress 0..100 within 30s window
        const p = Math.floor(((Date.now()/1000)%30)/30 * 100);
        return {code, progress:p};
      }

      function tick(){
        const {code, progress} = demoTotp(secret);
        liveCode.textContent = code;
        bar.style.width = progress + '%';
      }

      function startTotp(){
        if (timer) clearInterval(timer);
        timer = setInterval(tick, 300);
        tick();
      }

      function genBackupCodes(){
        const out = [];
        for(let i=0;i<8;i++){
          const x = Math.floor(Math.random()*1e8).toString().padStart(8,'0');
          out.push(x.slice(0,4)+'-'+x.slice(4));
        }
        codesBox.innerHTML = out.map(c=>`<div>${c}</div>`).join('');
      }

      btnStart.addEventListener('click', ()=>{
        secret = randomSecret();
        totpZone.style.display = 'flex';
        enterZone.style.display = 'block';
        backupZone.style.display = 'none';
        genBackupCodes();
        startTotp();
        c1.classList.add('done'); c1.textContent = '✅ Mission 1 — TOTP affiché';
      });

      btnVerify.addEventListener('click', ()=>{
        if (!secret) return;
        const current = demoTotp(secret).code;
        if (inputCode.value.trim() === current){
          enterZone.style.display = 'none';
          backupZone.style.display = 'block';
          c2.classList.add('done'); c2.textContent = '✅ Mission 2 — Code vérifié';
        } else {
          inputCode.value = '';
          inputCode.placeholder = 'Code incorrect, réessaie…';
        }
      });

      btnMarkSaved.addEventListener('click', ()=>{
        c3.classList.add('done'); c3.textContent = '✅ Mission 3 — Codes de secours sauvegardés';
      });
    })();
  </script>
</body>
</html>
