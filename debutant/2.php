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
  <title>Vrai site ou faux ? | FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b1020; --panel:#111833; --panel-2:#0f1530; --border:#1f2a4d;
      --text:#e8eefc; --muted:#a8b3d9; --accent:#5aa1ff; --good:#10d49b; --bad:#ff6b6b;
      --code-bg:#0b132b; --code-bd:#1e2a4a; --kbd-bg:#0e1733;
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
    .crumbs{font-weight:700}
    .crumbs a{color:var(--muted);text-decoration:none}
    .crumbs a:hover{color:var(--accent)}
    .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#b8ffd8;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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

    .callout{border:1px dashed var(--border); background:linear-gradient(180deg, rgba(90,161,255,.06), transparent); padding:14px;border-radius:12px; color:var(--muted); margin:10px 0}
    .ok{border-color:rgba(16,212,155,.45); background:linear-gradient(180deg, rgba(16,212,155,.08), transparent);}

    /* Quiz */
    .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
    .q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
    .q .qbd{padding:16px; display:flex; flex-direction:column; gap:10px}
    .q label{display:flex;gap:10px;align-items:flex-start;cursor:pointer;color:var(--text)}
    .q input{margin-top:2px}
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

    /* Mini-jeu : “barre d’adresse” à inspecter */
    .mini{margin-top:18px}
    .browser{background:#0f1530;border:1px solid var(--border);border-radius:12px;overflow:hidden}
    .bar{display:flex;align-items:center;gap:8px;padding:10px 12px;border-bottom:1px solid var(--border)}
    .lock{width:18px;height:18px;border-radius:4px;background:#182044;display:grid;place-items:center;cursor:pointer}
    .lock.bad{outline:2px solid rgba(255,107,107,.7)}
    .lock.good{outline:2px solid rgba(16,212,155,.6)}
    .addr{flex:1;background:#0b132b;border:1px solid var(--code-bd);border-radius:8px;padding:8px 10px;font-family:ui-monospace,monospace;white-space:nowrap;overflow:auto}
    .addr .sus{cursor:pointer;border-radius:4px}
    .addr .sus:hover{outline:2px dashed rgba(255,107,107,.6);outline-offset:2px}
    .tab{padding:12px}
    .flag{display:none;margin-top:8px;color:#ffd1d1}
    .flag.show{display:block}
    .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
    .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
    .check.done{color:var(--good);font-weight:800}
    .pill{background:#0e1733;border:1px solid var(--border);padding:.25rem .55rem;border-radius:999px;color:#cfe1ff;font-size:.85rem}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Vrai site ou faux ?</span></div>
      <span class="badge">Leçon 2 / 8</span>
    </div>

    <div class="title">
      <h1>Vrai site ou faux ?</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Apprends à juger un site en 10 secondes chrono :</p>
          <div class="callout ok">
            <ul>
              <li><strong>Regarde le domaine racine</strong> (la fin) : <code>banque.com</code>, <code>impots.gouv.fr</code>.</li>
              <li><strong>Cadenas / HTTPS</strong> : pas une garantie absolue, mais un site sans cadenas = danger.</li>
              <li><strong>Signaux visuels</strong> : fautes, logo flou, demande d’infos inhabituelles, pop-ups agressifs.</li>
            </ul>
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : inspecte la barre d’adresse</h3>
            <p style="color:var(--muted)">Clique les éléments suspects (3 à trouver). Astuce : le vrai domaine est la <em>fin</em> avant le premier slash.</p>

            <div class="browser" aria-label="Navigateur simulé">
              <div class="bar">
                <div class="lock bad" id="lock" title="État du certificat">🔓</div>
                <div class="addr" id="addr">
                  https://
                  <span class="sus" id="sub1">paypal.com</span>.
                  <span class="sus" id="root">secure-update.co</span>
                  /login?session=…
                </div>
                <button class="pill" id="viewCert">Certificat</button>
              </div>
              <div class="tab">
                <p style="margin:0 0 6px">Page de connexion — veuillez saisir vos identifiants.</p>
                <ul style="margin:0 0 6px 18px;color:var(--muted)">
                  <li>Logo approximatif</li>
                  <li>Texte traduit automatiquement</li>
                </ul>

                <div class="flag" id="f1">⚠️ <strong>Domaine trompeur :</strong> <em>paypal.com.secure-update.co</em> → le <u>domaine racine</u> est <strong>secure-update.co</strong>, pas paypal.com.</div>
                <div class="flag" id="f2">⚠️ <strong>Cadenas ouvert :</strong> page non sécurisée / certificat invalide.</div>
                <div class="flag" id="f3">⚠️ <strong>Certificat :</strong> CN ne correspond pas à « paypal.com ». Détails incohérents.</div>
              </div>
            </div>

            <div class="checks">
              <div class="check" id="c1">⬜ 1 — Identifier le <strong>domaine racine</strong></div>
              <div class="check" id="c2">⬜ 2 — Vérifier l’<strong>état du cadenas</strong>/HTTPS</div>
              <div class="check" id="c3">⬜ 3 — Ouvrir les <strong>détails du certificat</strong></div>
            </div>
          </div>
          <!-- /MINI-JEU -->

          <div class="callout">
            <strong>Réflexe :</strong> pour les sites sensibles (banque, impôts, mail), tape l’adresse toi-même
            (<em>favoris</em> encore mieux) au lieu de cliquer un lien trouvé dans un message.
          </div>
        </div>
      </section>

      <!-- QUIZ -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien repéré ?</div>
        <div class="bd">
          <form id="quizForm">
            <div class="q" data-q="q1">
              <div class="qhd">1) Dans <code>https://secure-login.amazon.com.client-check.io/</code>, quel est le domaine racine ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> amazon.com</label>
                <label><input type="radio" name="q1" data-correct="1"> client-check.io</label>
                <label><input type="radio" name="q1"> secure-login.amazon.com</label>
                <div class="explain">Le domaine racine se lit juste avant le premier « / » : ici <code>client-check.io</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Le cadenas (HTTPS) signifie :</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Site 100% fiable</label>
                <label><input type="radio" name="q2" data-correct="1"> Connexion chiffrée, mais le site peut être malveillant</label>
                <label><input type="radio" name="q2"> Rien du tout</label>
                <div class="explain">HTTPS chiffre la connexion, pas la moralité du site 😊</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Lequel te semble sûr ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> https://impots.gouv.fr.secure-paiement.net</label>
                <label><input type="radio" name="q3" data-correct="1"> https://www.impots.gouv.fr/</label>
                <label><input type="radio" name="q3"> http://impots-gouv-fr.info</label>
                <div class="explain">Le domaine officiel est <code>impots.gouv.fr</code> (HTTPS + domaine exact).</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Tu as un doute. Le bon geste :</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Cliquer et voir</label>
                <label><input type="radio" name="q4" data-correct="1"> Ouvrir un nouvel onglet et taper l’adresse officielle</label>
                <label><input type="radio" name="q4"> Demander au site via leur formulaire (sur la page douteuse)</label>
                <div class="explain">Toujours vérifier en dehors du message reçu.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Que vérifier dans un certificat ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> Qu’il soit vert</label>
                <label><input type="radio" name="q5" data-correct="1"> Le nom du site (CN/SAN) et la validité</label>
                <label><input type="radio" name="q5"> Le nombre d’étoiles</label>
                <div class="explain">CN/SAN doivent correspondre au domaine, et le certificat doit être valide.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./1.php">← Leçon précédente</a>
              <a href="./3.php">Leçon suivante →</a>
            </div>
          </form>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // ===== Mini-jeu : barre d’adresse =====
    (function(){
      const sub1 = document.getElementById('sub1');
      const root = document.getElementById('root');
      const lock = document.getElementById('lock');
      const viewCert = document.getElementById('viewCert');

      const f1 = document.getElementById('f1');
      const f2 = document.getElementById('f2');
      const f3 = document.getElementById('f3');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      function done(check, flag){ check.classList.add('done'); flag.classList.add('show'); }

      // Domaine trompeur : cliquer sur “paypal.com” ou sur la racine “secure-update.co”
      sub1.addEventListener('click', ()=> done(c1, f1));
      root.addEventListener('click', ()=> done(c1, f1));

      // Cadenas
      lock.addEventListener('click', ()=>{
        done(c2, f2);
        lock.textContent = '🔒'; // on simule un changement après clic
        lock.classList.remove('bad'); lock.classList.add('good');
      });

      // Détails certificat
      viewCert.addEventListener('click', ()=>{
        alert('Certificat :\n- CN: secure-update.co\n- SAN: *.secure-update.co\n- État: invalide pour « paypal.com »');
        done(c3, f3);
      });
    })();

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
  </script>
</body>
</html>
