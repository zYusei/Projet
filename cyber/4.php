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
  <title>Défense anti-phishing | FunCodeLab</title>
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

  /* Mini-jeu : Phishing Defense Lab */
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
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Cyber &nbsp;›&nbsp; <span style="color:var(--accent)">Défense anti-phishing</span></div>
      <span class="badge">Leçon 4 / 8</span>
    </div>

    <div class="title">
      <h1>Défense anti-phishing</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Protéger les boîtes mail : <strong>filtrage</strong> (SPF/DKIM/DMARC + passerelle), <strong>formation</strong> (simulations régulières) et <strong>réponse</strong> (bouton signalement, quarantaine, blocage rapide).</p>

          <h2>1) Authentifier les mails (SPF, DKIM, DMARC)</h2>
          <ul>
            <li><strong>SPF</strong> : liste des serveurs autorisés à émettre pour ton domaine.</li>
            <li><strong>DKIM</strong> : signature cryptographique des messages.</li>
            <li><strong>DMARC</strong> : politique si SPF/DKIM échouent (none → quarantine → <strong>reject</strong>).</li>
          </ul>

          <h2>2) Filtrage & bannière externe</h2>
          <ul>
            <li>Activer un <strong>filtre antispam/antiphishing</strong> avec sandbox URL/pièces jointes.</li>
            <li>Ajouter une <strong>bannière “Externe”</strong> sur les emails hors domaine.</li>
          </ul>

          <h2>3) Former & répondre</h2>
          <ul>
            <li>Campagnes de <strong>sensibilisation</strong> 3–4×/an avec simulations.</li>
            <li>Mettre un <strong>bouton de signalement</strong> → envoi à la sécurité + <strong>quarantaine</strong> automatique.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> combo efficace : DMARC=<em>reject</em> + bannière externe + simulations trimestrielles + bouton “Signaler” relié à une quarantaine.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Phishing Defense Lab</h3>
            <p class="rules">Active les bons réglages. Au chargement, rien n’est validé : à toi de configurer pour faire passer les 4 missions au vert ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages anti-phishing">
                  <div class="row">
                    <label style="font-weight:800;min-width:170px">SPF/DKIM</label>
                    <select id="auth">
                      <option value="off" selected>Désactivés</option>
                      <option value="partial">Partiels</option>
                      <option value="on">Correctement configurés</option>
                    </select>
                    <span id="authTag" class="pill warnP">Non authentifié</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:170px">DMARC</label>
                    <select id="dmarc">
                      <option value="none" selected>none (monitoring)</option>
                      <option value="quarantine">quarantine</option>
                      <option value="reject">reject (recommandé)</option>
                    </select>
                    <span id="dmarcTag" class="pill warnP">Tolérant</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:170px">Bannière externe</label>
                    <input type="checkbox" id="banner">
                    <span id="bannerTag" class="pill warnP">Désactivée</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:170px">Bouton “Signaler” →</label>
                    <select id="reportFlow">
                      <option value="off" selected>Non installé</option>
                      <option value="mail">Mail à l’IT</option>
                      <option value="quarantine">Mail à Sec + Quarantaine auto</option>
                    </select>
                    <span id="reportTag" class="pill warnP">Aucune réponse</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:170px">Formation</label>
                    <select id="training">
                      <option value="none" selected>Jamais</option>
                      <option value="yearly">1×/an</option>
                      <option value="quarterly">Trimestrielle (reco.)</option>
                    </select>
                    <span id="trainingTag" class="pill warnP">À planifier</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Avoir SPF/DKIM OK et DMARC = reject</div>
                    <div class="check" id="c2">⬜ Mission 2 — Activer une bannière “Externe”</div>
                    <div class="check" id="c3">⬜ Mission 3 — Installer “Signaler” avec quarantaine automatique</div>
                    <div class="check" id="c4">⬜ Mission 4 — Planifier une formation trimestrielle</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> Auth=<em>on</em>, DMARC=<em>reject</em>, Bannière cochée, Report=<em>quarantine</em>, Formation=<em>quarterly</em>.
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
              <div class="qhd">1) Quel est le rôle de DMARC ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Définir la politique quand SPF/DKIM échouent</label>
                <label><input type="radio" name="q1"> Chiffrer les mails en transit</label>
                <label><input type="radio" name="q1"> Ajouter une signature visuelle</label>
                <div class="explain">DMARC indique quoi faire si l’authentification échoue (none/quarantine/reject).</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quel réglage aide les utilisateurs à repérer un mail externe ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Une bannière “Externe”</label>
                <label><input type="radio" name="q2"> Un fond rose</label>
                <label><input type="radio" name="q2"> Un emoji aléatoire</label>
                <div class="explain">La bannière “Externe” limite l’usurpation perçue.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Un bouton “Signaler” utile doit…</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Envoyer à la sécu et placer en quarantaine</label>
                <label><input type="radio" name="q3"> Supprimer sans trace</label>
                <label><input type="radio" name="q3"> Répondre au pirate</label>
                <div class="explain">La quarantaine permet une réponse rapide centralisée.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Quelle fréquence de formation est raisonnable ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Tous les 5 ans</label>
                <label><input type="radio" name="q4"> Chaque semaine</label>
                <label><input type="radio" name="q4" data-correct="1"> Trimestrielle (3–4×/an)</label>
                <div class="explain">Répéter sans lasser, avec simulations réalistes.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) SPF/DKIM servent principalement à…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Authentifier l’expéditeur et l’intégrité du message</label>
                <label><input type="radio" name="q5"> Compresser les pièces jointes</label>
                <label><input type="radio" name="q5"> Augmenter la taille de la boîte</label>
                <div class="explain">SPF liste les émetteurs autorisés ; DKIM signe le contenu.</div>
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

    // ===== Mini-jeu : Phishing Defense Lab =====
    (function(){
      const auth = document.getElementById('auth');
      const dmarc = document.getElementById('dmarc');
      const banner = document.getElementById('banner');
      const reportFlow = document.getElementById('reportFlow');
      const training = document.getElementById('training');

      const authTag = document.getElementById('authTag');
      const dmarcTag = document.getElementById('dmarcTag');
      const bannerTag = document.getElementById('bannerTag');
      const reportTag = document.getElementById('reportTag');
      const trainingTag = document.getElementById('trainingTag');

      const c1=document.getElementById('c1');
      const c2=document.getElementById('c2');
      const c3=document.getElementById('c3');
      const c4=document.getElementById('c4');

      let touched = false; // rien validé avant action

      function tag(el, txt, cls){ el.textContent=txt; el.className='pill '+cls; }
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }

      function render(){
        tag(authTag, auth.value==='on' ? 'Auth OK' : auth.value==='partial' ? 'Partiel' : 'Non authentifié', auth.value==='on' ? 'ok':'warnP');
        tag(dmarcTag, dmarc.value==='reject' ? 'Reject' : dmarc.value==='quarantine' ? 'Quarantine' : 'None', dmarc.value==='reject' ? 'ok' : 'warnP');
        tag(bannerTag, banner.checked ? 'Bannière active' : 'Désactivée', banner.checked ? 'ok':'warnP');
        tag(reportTag, reportFlow.value==='quarantine' ? 'Signalement + Quarantaine' : reportFlow.value==='mail' ? 'Mail à l’IT' : 'Aucune réponse', reportFlow.value==='quarantine' ? 'ok':'warnP');
        tag(trainingTag, training.value==='quarterly' ? 'Trimestrielle' : training.value==='yearly' ? 'Annuelle' : 'Aucune', training.value==='quarterly' ? 'ok':'warnP');

        if (!touched) return; // missions seulement après interaction

        (auth.value==='on' && dmarc.value==='reject')
          ? done(c1,'Mission 1 — SPF/DKIM OK + DMARC reject')
          : (c1.classList.remove('done'), c1.textContent='⬜ Mission 1 — Avoir SPF/DKIM OK et DMARC = reject');

        banner.checked
          ? done(c2,'Mission 2 — Bannière externe activée')
          : (c2.classList.remove('done'), c2.textContent='⬜ Mission 2 — Activer une bannière “Externe”');

        reportFlow.value==='quarantine'
          ? done(c3,'Mission 3 — Signalement + Quarantaine')
          : (c3.classList.remove('done'), c3.textContent='⬜ Mission 3 — Installer “Signaler” avec quarantaine automatique');

        training.value==='quarterly'
          ? done(c4,'Mission 4 — Formation trimestrielle')
          : (c4.classList.remove('done'), c4.textContent='⬜ Mission 4 — Planifier une formation trimestrielle');
      }

      [auth,dmarc,banner,reportFlow,training].forEach(el=>el.addEventListener('change', ()=>{ touched=true; render(); }));
      render(); // état initial neutre
    })();
  </script>
</body>
</html>
