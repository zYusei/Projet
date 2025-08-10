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
  <title>Security Engineer — Principes & bonnes pratiques | FunCodeLab</title>
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

  /* Mini-jeu Hardening Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .row button,.row select,.row input[type="text"]{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
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
      <div class="crumbs">🔐 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Cyber &nbsp;›&nbsp; <span style="color:var(--accent)">Security Engineer — Principes & bonnes pratiques</span></div>
      <span class="badge">Leçon 1 / 8</span>
    </div>

    <div class="title">
      <h1>Security Engineer — Principes & bonnes pratiques</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectif : connaître les <strong>fondamentaux sécurité</strong> et savoir “durcir” (hardening) un service rapidement.</p>

          <h2>1) CIA triad</h2>
          <ul>
            <li><strong>Confidentialité</strong> : seules les personnes autorisées lisent (ex : chiffrement, contrôle d’accès).</li>
            <li><strong>Intégrité</strong> : les données ne sont pas altérées (hash, signatures, checksums).</li>
            <li><strong>Disponibilité</strong> : le service reste accessible (redondance, monitoring, sauvegardes).</li>
          </ul>

          <h2>2) Principes d’architecture</h2>
          <ul>
            <li><strong>Moindre privilège</strong> : chaque compte/clé a juste le nécessaire, pas plus.</li>
            <li><strong>Segmentation</strong> : séparer front/back/BDD, réseaux, environnements (dev/test/prod).</li>
            <li><strong>Surface d’attaque minimale</strong> : supprimer services/ports inutiles.</li>
            <li><strong>Journalisation</strong> (logs) + <strong>alertes</strong> : il faut <em>voir</em> quand ça dérape.</li>
          </ul>

          <h2>3) Opérations & hygiène</h2>
          <ul>
            <li>Appliquer <strong>patchs</strong> & mises à jour.</li>
            <li><strong>MFA/2FA</strong> partout, rotation régulière des <strong>secrets</strong>.</li>
            <li><strong>Backups</strong> testés (restauration!), chiffrés, hors ligne/dehors de l’org.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> si tu dois prioriser, pense “<em>prévenir/voir/réagir</em>” : MFA + logs + sauvegardes.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Hardening Lab</h3>
            <p class="rules">But : sécuriser un petit service web. Mets les bons réglages pour valider les 4 missions ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Zone actions -->
                <div class="box" aria-label="Paramètres du service">
                  <div class="switch"><input id="mfa" type="checkbox"> Activer MFA sur les comptes admin</div>

                  <div class="row"><label style="font-weight:800">Rôle service BDD</label>
                    <select id="role">
                      <option>admin</option>
                      <option>writer</option>
                      <option>reader</option>
                    </select>
                    <span id="roleTag" class="pill">Privilèges : admin</span>
                  </div>

                  <div class="row"><label style="font-weight:800">Pare-feu</label>
                    <select id="ports">
                      <option value="all">Tous ports ouverts</option>
                      <option value="80-443">80/443 seulement</option>
                      <option value="443">443 uniquement</option>
                    </select>
                    <span id="fwTag" class="pill">FW : —</span>
                  </div>

                  <div class="row"><label style="font-weight:800">Secrets</label>
                    <input id="secretAge" type="text" placeholder="Âge du secret (jours)" style="width:170px">
                    <button id="rotate" class="btn-ghost" type="button">Rotation</button>
                    <span id="secTag" class="pill">Secret: inconnu</span>
                  </div>

                  <div class="switch"><input id="patch" type="checkbox"> Patches système appliqués</div>
                  <div class="switch"><input id="logging" type="checkbox"> Logs & alertes activés</div>

                  <div class="row" style="margin-top:8px">
                    <button id="apply" class="btn-accent" type="button">Évaluer la posture</button>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — MFA activé</div>
                    <div class="check" id="c2">⬜ Mission 2 — Moindre privilège sur la BDD</div>
                    <div class="check" id="c3">⬜ Mission 3 — Surface d’attaque réduite (ports)</div>
                    <div class="check" id="c4">⬜ Mission 4 — Secrets récents + patchs + logs</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> “reader” + port 443 + rotation &lt;= 30 jours + patches + logs 😉
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
              <div class="qhd">1) Dans la triade CIA, le “C” signifie…</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Confidentialité</label>
                <label><input type="radio" name="q1"> Conformité</label>
                <label><input type="radio" name="q1"> Cryptographie uniquement</label>
                <div class="explain">C = Confidentialité, I = Intégrité, A = Disponibilité.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Le principe du moindre privilège consiste à…</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Donner admin à tout le monde pour aller plus vite</label>
                <label><input type="radio" name="q2" data-correct="1"> Donner uniquement les droits nécessaires</label>
                <label><input type="radio" name="q2"> Supprimer tous les accès</label>
                <div class="explain">On limite l’impact en cas de compromission.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quelle option réduit <em>le plus</em> la surface d’attaque réseau ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> Tous ports ouverts</label>
                <label><input type="radio" name="q3"> 80/443 seulement</label>
                <label><input type="radio" name="q3" data-correct="1"> 443 uniquement</label>
                <div class="explain">Moins d’expositions = moins de risques.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Pourquoi activer les logs & alertes ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Pour détecter et répondre aux incidents</label>
                <label><input type="radio" name="q4"> Pour consommer du disque</label>
                <label><input type="radio" name="q4"> Pour ralentir le service</label>
                <div class="explain">Sans visibilité, pas de réponse efficace.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) La rotation des secrets sert surtout à…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Limiter l’impact si un secret fuit</label>
                <label><input type="radio" name="q5"> Faire joli</label>
                <label><input type="radio" name="q5"> Éviter les sauvegardes</label>
                <div class="explain">Un secret compromis récent est moins utile longtemps.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="../parcours.php">← Retour</a>
              <a href="./2.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Hardening Lab =====
    (function(){
      const mfa = document.getElementById('mfa');
      const role = document.getElementById('role');
      const roleTag = document.getElementById('roleTag');
      const ports = document.getElementById('ports');
      const fwTag = document.getElementById('fwTag');
      const secretAge = document.getElementById('secretAge');
      const rotate = document.getElementById('rotate');
      const patch = document.getElementById('patch');
      const logging = document.getElementById('logging');
      const apply = document.getElementById('apply');

      const c1=document.getElementById('c1'), c2=document.getElementById('c2'), c3=document.getElementById('c3'), c4=document.getElementById('c4');
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }

      role.addEventListener('change', ()=>{
        roleTag.textContent = 'Privilèges : ' + role.value;
        roleTag.className = 'pill ' + (role.value==='reader' ? 'ok' : 'warnTag');
      });
      ports.addEventListener('change', ()=>{
        const map={ 'all':'Tous ports', '80-443':'80/443', '443':'443 uniquement' };
        fwTag.textContent='FW : '+map[ports.value];
        fwTag.className='pill ' + (ports.value==='443' ? 'ok' : 'warnTag');
      });
      rotate.addEventListener('click', ()=>{
        secretAge.value='0';
      });

      apply.addEventListener('click', ()=>{
        if (mfa.checked) done(c1,'Mission 1 — MFA activé');
        if (role.value==='reader') done(c2,'Mission 2 — Moindre privilège validé');
        if (ports.value==='443') done(c3,'Mission 3 — Ports minimaux');
        const age = parseInt(secretAge.value||'999',10);
        if (!isNaN(age) && age<=30 && patch.checked && logging.checked){
          done(c4,'Mission 4 — Secrets récents + patchs + logs');
        }
      });

      // init visuels
      role.dispatchEvent(new Event('change'));
      ports.dispatchEvent(new Event('change'));
    })();
  </script>
</body>
</html>
