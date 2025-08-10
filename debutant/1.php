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
  <title>Le phishing (tout simple) | FunCodeLab</title>
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

    /* Mini-jeu : Mail factice à inspecter */
    .mini{margin-top:18px}
    .mini h3{margin:0 0 8px}
    .email{background:#0f1530;border:1px solid var(--border);border-radius:12px;overflow:hidden}
    .email .hdr{display:flex;flex-wrap:wrap;gap:10px;padding:12px 14px;border-bottom:1px solid var(--border);align-items:center}
    .pill{background:#0e1733;border:1px solid var(--border);padding:.25rem .55rem;border-radius:999px;color:#cfe1ff;font-size:.85rem}
    .email .body{padding:14px}
    .email a{color:#8fbaff;text-decoration:underline dotted}
    .sus{position:relative;cursor:pointer;border-radius:6px}
    .sus:hover{outline:2px dashed rgba(255,107,107,.65); outline-offset:2px}
    .flag{display:none;margin-top:6px;font-size:.92rem;color:#ffd1d1}
    .flag.show{display:block}
    .tips{margin-top:10px;color:var(--muted)}
    .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
    .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
    .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Le phishing (tout simple)</span></div>
      <span class="badge">Leçon 1 / 8</span>
    </div>

    <div class="title">
      <h1>Le phishing (tout simple)</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>
            Le <strong>phishing</strong>, c’est une tentative de <strong>piéger</strong> quelqu’un pour lui faire
            <strong>cliquer un lien</strong>, <strong>ouvrir une pièce jointe</strong> ou <strong>donner ses infos</strong> (mot de passe, carte…).
            Imagine un <em>hameçon</em> : on déguise le message pour qu’il paraisse légitime.
          </p>

          <div class="callout ok">
            <strong>Les 3 indices clés pour repérer un faux mail :</strong>
            <ul>
              <li><strong>Adresse de l’expéditeur</strong> douteuse (ex : <code>support@micros0ft-help.com</code>)</li>
              <li><strong>Liens</strong> qui mènent vers un <em>autre domaine</em> que l’officiel</li>
              <li><strong>Urgence / menace</strong> (« Votre compte sera bloqué », « dernier avertissement »)</li>
            </ul>
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : repère les 3 indices suspects</h3>
            <p class="tips">Clique sur les éléments suspects dans l’email factice ci-dessous (3 à trouver). Les coches passent au vert ✅</p>

            <div class="email" aria-label="Email factice">
              <div class="hdr">
                <span class="pill sus" id="from">De : Sécurité Microsoft &lt;support@micros0ft-secure.com&gt;</span>
                <span class="pill">À : vous@example.com</span>
                <span class="pill">Objet : <span class="sus" id="urgent">[ACTION IMMÉDIATE] Votre compte sera fermé</span></span>
              </div>
              <div class="body">
                <p>Bonjour,</p>
                <p>Nous avons détecté une activité inhabituelle. Pour éviter la <strong>fermeture immédiate</strong> de votre compte, merci de
                  <a href="#" class="sus" id="cta">vérifier votre identité ici</a>.
                </p>
                <p>Merci,<br>Équipe Sécurité</p>

                <div class="flag" id="f1">⚠️ <strong>Adresse suspecte :</strong> « micros0ft » avec un zéro → domaine faux.</div>
                <div class="flag" id="f2">⚠️ <strong>Urgence :</strong> ton cerveau panique → c’est voulu pour te faire cliquer vite.</div>
                <div class="flag" id="f3">⚠️ <strong>Lien piégé :</strong> le texte semble bon, mais l’URL réelle n’est pas microsoft.com.</div>
              </div>
            </div>

            <div class="checks">
              <div class="check" id="c1">⬜ 1 — Adresse expéditeur douteuse</div>
              <div class="check" id="c2">⬜ 2 — Message d’urgence / peur</div>
              <div class="check" id="c3">⬜ 3 — Lien qui mène ailleurs</div>
            </div>
          </div>
          <!-- /MINI-JEU -->

          <div class="callout">
            <strong>Réflexe simple :</strong> si un message te demande de « te connecter »,
            <strong>ne clique pas le lien</strong>. Ouvre toi-même le site <em>dans ton navigateur</em> (ou l’app officielle)
            et connecte-toi depuis là. Si c’était vrai, tu verras l’alerte <em>sur le site</em>.
          </div>
        </div>
      </section>

      <!-- QUIZ -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien repéré ?</div>
        <div class="bd">
          <form id="quizForm">
            <div class="q" data-q="q1">
              <div class="qhd">1) Quel est l’indice le plus fiable d’un mail piégé ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> Logo de la marque</label>
                <label><input type="radio" name="q1" data-correct="1"> Adresse/domaine de l’expéditeur</label>
                <label><input type="radio" name="q1"> Signature polie</label>
                <div class="explain">On peut copier un logo, pas un vrai domaine.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Que faire si un mail annonce « votre compte sera bloqué » ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Cliquer vite sur le lien</label>
                <label><input type="radio" name="q2" data-correct="1"> Ouvrir moi-même le site officiel et vérifier</label>
                <label><input type="radio" name="q2"> Répondre au mail</label>
                <div class="explain">Toujours vérifier en dehors du mail.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Où regarder pour voir l’URL réelle d’un lien ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Survole le lien (barre d’état) ou fais clic-droit → copier l’adresse</label>
                <label><input type="radio" name="q3"> Le texte bleu suffit</label>
                <label><input type="radio" name="q3"> Dans le fichier Word joint</label>
                <div class="explain">Le texte peut mentir, l’URL réelle non.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Les pièces jointes « facture.zip » ou « colis.exe » sont…</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Toujours sûres</label>
                <label><input type="radio" name="q4" data-correct="1"> À éviter sans certitude (risque de malware)</label>
                <label><input type="radio" name="q4"> Nécessaires pour lire le mail</label>
                <div class="explain">N’ouvre jamais une pièce jointe inattendue.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Bon réflexe si j’ai cliqué par erreur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> Ne rien faire</label>
                <label><input type="radio" name="q5" data-correct="1"> Changer le mot de passe + activer 2FA + prévenir le support</label>
                <label><input type="radio" name="q5"> Répondre au pirate</label>
                <div class="explain">Réagis vite : mot de passe, 2FA, alerte au support.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="../parcours.php">← Retour parcours</a>
              <a href="./2.php">Leçon suivante →</a>
            </div>
          </form>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // ===== Mini-jeu : détection des 3 indices =====
    (function(){
      const from = document.getElementById('from');
      const urgent = document.getElementById('urgent');
      const cta = document.getElementById('cta');

      const f1 = document.getElementById('f1');
      const f2 = document.getElementById('f2');
      const f3 = document.getElementById('f3');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      function done(check, flag){
        check.classList.add('done');
        if (flag) flag.classList.add('show');
      }

      from.addEventListener('click', ()=> done(c1, f1));
      urgent.addEventListener('click', ()=> done(c2, f2));
      cta.addEventListener('click', (e)=>{
        e.preventDefault();
        // On simule l’URL réelle différente
        alert('URL réelle : https://micros0ft-secure.com/login (FAUX domaine)');
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
