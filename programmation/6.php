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
  <title>PHP — Formulaires & $_POST | FunCodeLab</title>
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
  .title .progress-pill{font-weight:800;color:#091326;background:linear-gradient(90deg,#ffd166,#fbbf24);padding:.35rem .65rem;border-radius:999px;border:1px solid #6a4c0b}

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
  pre{background:var(--code-bg);border:1px solid var(--code-bd);color:#dfe6ff;padding:14px 16px;border-radius:12px;overflow:auto;line-height:1.5;margin:12px 0 10px}
  code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}
  kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

  .lesson h2{margin:14px 0 6px;font-size:1.1rem}
  .lesson ul{margin:8px 0 12px 18px;color:var(--muted)} .lesson li{margin:6px 0}
  .tip{color:#b9ffdf;font-weight:700}

  /* Quiz */
  .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
  .q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
  .q .qbd{padding:16px; display:flex; flex-direction:column; gap:10px}
  .q label{display:flex;gap:10px;align-items:flex-start;margin:2px 0;cursor:pointer;color:var(--text); width:100%}
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

  /* Mini-jeu Form Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.08fr .92fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .sandbox .out{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;padding:10px;min-height:44px;margin-top:8px;color:#dfe6ff;white-space:pre-wrap}
  .sandbox label{display:block;margin:6px 0 4px;font-weight:700}
  .sandbox input,.sandbox textarea,.sandbox select{width:100%;background:#0f1a3b;border:1px solid var(--border);border-radius:10px;color:#fff;padding:.55rem .7rem}
  .tools{display:flex;flex-direction:column;gap:8px}
  .tools .mission{display:flex;align-items:flex-start;gap:8px;color:var(--muted);line-height:1.35}
  .tools .mission.done{color:var(--good);font-weight:800}
  .hint{color:#cfe1ff}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">PHP — Formulaires & $_POST</span></div>
      <span class="badge">Leçon 6 / 11</span>
    </div>

    <div class="title">
      <h1>PHP — Formulaires & $_POST</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Traitons des formulaires en PHP : <strong>récupération</strong> avec <code class="inline">$_POST</code>, <strong>validation</strong>, <strong>sanitisation</strong>, sécurité (XSS/CSRF), et rappel sur les requêtes préparées.</p>

          <h2>1) Récupérer et valider</h2>
<pre><code>&lt;?php
// Exemple traitement POST
$nom   = trim($_POST['nom'] ?? '');
$email = $_POST['email'] ?? '';
$age   = (int)($_POST['age'] ?? 0);

// Validation simple
$err = [];
if ($nom === '' || mb_strlen($nom) &lt; 2)    $err['nom'] = 'Nom trop court';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err['email'] = 'Email invalide';
if ($age &lt; 1 || $age &gt; 120)                $err['age'] = 'Âge invalide';

// Échapper avant affichage
$nom_safe = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
?&gt;</code></pre>

          <h2>2) Échapper & sanitiser</h2>
<pre><code>&lt;?php
// Échapper TOUT ce qui repart en HTML
echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');

// Sanitiser
$nom_clean = filter_var($nom, FILTER_SANITIZE_STRING);  // (déprécié 8.1) préférez vos règles
$tel_clean = filter_var($_POST['tel'] ?? '', FILTER_SANITIZE_NUMBER_INT);
?&gt;</code></pre>

          <h2>3) CSRF : token anti-forgery</h2>
<pre><code>&lt;?php
// Au rendu du formulaire (en session)
$_SESSION['csrf'] = bin2hex(random_bytes(32));
?&gt;
&lt;input type="hidden" name="csrf" value="&lt;?= $_SESSION['csrf'] ?&gt;"&gt;

&lt;?php
// À la réception
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
  die('CSRF!');
}
?&gt;</code></pre>

          <div class="callout warn">
            <strong>Sécurité :</strong> encode toujours la sortie (<code class="inline">htmlspecialchars</code>), vérifie les formats (ex : <code class="inline">filter_var</code>), et utilise <strong>des requêtes préparées</strong> côté BDD.
          </div>

          <h2>4) Requête préparée (rappel)</h2>
<pre><code>&lt;?php
$stmt = $pdo-&gt;prepare('INSERT INTO users(nom,email,age) VALUES(?,?,?)');
$stmt-&gt;execute([$nom, $email, $age]); // pas de concat SQL !
?&gt;</code></pre>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Form Lab (simulation serveur)</h3>
            <p class="rules">
              <strong>Objectif :</strong> remplis le formulaire et “envoie-le”. On simule le <code class="inline">$_POST</code>, les erreurs de validation,
              la génération/validation d’un <strong>token CSRF</strong>, puis on affiche la “réponse serveur”.
            </p>
            <div class="play">
              <div class="lab">
                <!-- Aire de jeu -->
                <div class="sandbox">
                  <form id="labForm">
                    <label>Nom</label>
                    <input name="nom" placeholder="Nora" minlength="2" required>

                    <label style="margin-top:6px">Email</label>
                    <input name="email" type="email" placeholder="toi@exemple.com" required>

                    <label style="margin-top:6px">Âge</label>
                    <input name="age" type="number" min="1" max="120" value="21" required style="max-width:140px">

                    <input type="hidden" name="csrf" id="csrfInput">
                    <button class="btn-accent" style="margin-top:10px">Soumettre</button>
                    <small class="hint">Un token CSRF est injecté automatiquement (côté “serveur”).</small>
                  </form>

                  <div class="out" id="serverOut" aria-live="polite"></div>
                </div>

                <!-- Missions -->
                <div class="tools">
                  <div class="mission" id="m1">⬜ Mission 1 — Soumettre avec un email valide</div>
                  <div class="mission" id="m2">⬜ Mission 2 — Obtenir 0 erreur de validation</div>
                  <div class="mission" id="m3">⬜ Mission 3 — Voir “CSRF OK” dans la réponse</div>
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
              <div class="qhd">1) Où sont les données envoyées par méthode POST ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> $_POST</label>
                <label><input type="radio" name="q1"> $_GET</label>
                <label><input type="radio" name="q1"> $_SERVER</label>
                <div class="explain">Les données de formulaire envoyées en POST sont dans <code class="inline">$_POST</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quelle fonction valide un email ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> htmlspecialchars()</label>
                <label><input type="radio" name="q2" data-correct="1"> filter_var($email, FILTER_VALIDATE_EMAIL)</label>
                <label><input type="radio" name="q2"> preg_match_email()</label>
                <div class="explain"><code class="inline">filter_var</code> avec <code class="inline">FILTER_VALIDATE_EMAIL</code> est le plus simple.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) À quoi sert htmlspecialchars() ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> À échapper la sortie HTML pour prévenir la XSS</label>
                <label><input type="radio" name="q3"> À hasher les mots de passe</label>
                <label><input type="radio" name="q3"> À valider un email</label>
                <div class="explain">Toujours échapper ce que tu réaffiches.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Le CSRF se prévient avec…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Un token secret comparé côté serveur</label>
                <label><input type="radio" name="q4"> Un captcha uniquement</label>
                <label><input type="radio" name="q4"> Un cookie SameSite=None sans HTTPS</label>
                <div class="explain">Un token stocké en session et vérifié à la soumission.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Pour éviter l’injection SQL, on utilise…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Des requêtes préparées</label>
                <label><input type="radio" name="q5"> htmlspecialchars() sur les champs</label>
                <label><input type="radio" name="q5"> strip_tags() partout</label>
                <div class="explain">Les requêtes préparées paramétrent les valeurs sans concaténer du SQL.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Quelle est la bonne séquence ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> Valider → Sanitiser → Stocker/Afficher (échappé)</label>
                <label><input type="radio" name="q6"> Afficher → Valider → Stocker</label>
                <label><input type="radio" name="q6"> Stocker → Valider → Afficher</label>
                <div class="explain">On valide d’abord, on nettoie si besoin, puis on stocke/affiche (échappé).</div>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Quelle comparaison de token CSRF est la plus sûre ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> hash_equals($sessionToken, $postedToken)</label>
                <label><input type="radio" name="q7"> $sessionToken === $postedToken</label>
                <label><input type="radio" name="q7"> strcmp($sessionToken, $postedToken) == 0</label>
                <div class="explain"><code class="inline">hash_equals</code> évite certaines attaques temporelles.</div>
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

    // ===== Mini-jeu : Form Lab (simulation serveur) =====
    (function(){
      // Génère un "token CSRF" côté client (pour la démo)
      const csrf = crypto.getRandomValues(new Uint8Array(16)).reduce((s,b)=>s+b.toString(16).padStart(2,'0'),'');
      document.getElementById('csrfInput').value = csrf;

      const form = document.getElementById('labForm');
      const out  = document.getElementById('serverOut');
      const m1 = document.getElementById('m1');
      const m2 = document.getElementById('m2');
      const m3 = document.getElementById('m3');

      form.addEventListener('submit', (e)=>{
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form).entries());

        // === Simulation du traitement serveur ===
        const errors = {};
        const nom   = String((data.nom||'').trim());
        const email = String(data.email||'').trim();
        const age   = parseInt(data.age||'0',10);

        if (nom.length < 2) errors.nom = 'Nom trop court';
        const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        if (!emailOk) errors.email = 'Email invalide';
        if (!(age >= 1 && age <= 120)) errors.age = 'Âge invalide';

        // CSRF
        const csrfOk = (data.csrf === csrf);

        // Sortie "serveur"
        const safe = str => str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
        let resp = '';
        resp += 'CSRF: ' + (csrfOk ? 'OK' : 'KO') + '\n';
        resp += 'Erreurs: ' + Object.keys(errors).length + '\n';
        if (Object.keys(errors).length) {
          for (const k in errors) resp += ` - ${k}: ${errors[k]}\n`;
        } else {
          resp += 'Données nettoyées (échappées):\n';
          resp += ` nom: ${safe(nom)}\n email: ${safe(email)}\n age: ${age}\n`;
        }
        out.textContent = resp;

        // Missions
        if (emailOk) { m1.classList.add('done'); m1.textContent = '✅ Mission 1 — Email valide'; }
        if (!Object.keys(errors).length) { m2.classList.add('done'); m2.textContent = '✅ Mission 2 — 0 erreur'; }
        if (csrfOk) { m3.classList.add('done'); m3.textContent = '✅ Mission 3 — CSRF OK'; }
      });
    })();
  </script>
</body>
</html>
