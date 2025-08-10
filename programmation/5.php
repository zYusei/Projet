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
  <title>PHP — Syntaxe & variables | FunCodeLab</title>
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
  button{appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);color:var(--text);font-weight:800;border-radius:10px;padding:.6rem 1rem;cursor:pointer}
  button:hover{border-color:#2b3c75}
  .btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#fff;border:0}
  .btn-ghost{background:transparent;color:#fff}
  .score{margin-top:14px;font-weight:800;color:#071421;background:linear-gradient(90deg,#7cffc0,#4df1aa);display:inline-block;padding:.45rem .8rem;border-radius:999px;border:1px solid #164238}

  .footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
  .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:var(--accent)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">PHP — Syntaxe & variables</span></div>
      <span class="badge">Leçon 5 / 11</span>
    </div>

    <div class="title">
      <h1>PHP — Syntaxe & variables</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Découvre la <strong>syntaxe de base</strong> de PHP : <strong>echo</strong>, <strong>variables</strong> (<code class="inline">$</code>), <strong>types</strong> simples et <strong>tableaux</strong>.</p>

          <h2>1) Démarrer un fichier PHP</h2>
<pre><code>&lt;?php
// Ton code ici
echo "Bonjour FunCodeLab !";
?&gt;</code></pre>

          <h2>2) Variables & types</h2>
<pre><code>&lt;?php
$nom    = "Nora";      // string
$age    = 21;          // int
$prix   = 19.99;       // float
$ok     = true;        // bool
echo "Salut $nom, tu as $age ans.";
// Concaténation avec le point :
echo "Total : " . $prix . " €";
?&gt;</code></pre>

          <h2>3) Tableaux (indexés & associatifs)</h2>
<pre><code>&lt;?php
$notes = [15, 18, 12];                 // indexé
$profil = ["nom" =&gt; "Nora", "ville" =&gt; "Lyon"]; // associatif

echo $notes[1];        // 18
echo $profil["ville"]; // Lyon

$profil["role"] = "Développeuse"; // ajout
?&gt;</code></pre>

          <h2>4) Déboguer rapidement</h2>
<pre><code>&lt;?php
var_dump($profil);   // type + valeur
print_r($notes);     // lisible, pour tableaux/objets
?&gt;</code></pre>

          <div class="callout">
            <strong>Astuce :</strong> <code class="inline">isset()</code> teste si une variable existe, <code class="inline">empty()</code> si elle est vide.
          </div>

          <h2>5) Mini-formulaire (POST) — démo front</h2>
          <p style="color:var(--muted)">Cette démo n’envoie rien au serveur ici, elle illustre juste les attributs HTML utiles pour un futur traitement PHP.</p>
<pre><code>&lt;form action="/traitement.php" method="post"&gt;
  &lt;label for="pseudonyme"&gt;Pseudo&lt;/label&gt;
  &lt;input id="pseudonyme" name="pseudo" required minlength="3"&gt;

  &lt;label for="age"&gt;Âge&lt;/label&gt;
  &lt;input id="age" name="age" type="number" min="1"&gt;

  &lt;button type="submit"&gt;Envoyer&lt;/button&gt;
&lt;/form&gt;</code></pre>
          <div class="callout warn">
            <strong>Important :</strong> en vrai, tu liras côté serveur <code class="inline">$_POST['pseudo']</code> et <code class="inline">$_POST['age']</code>. <em>Valide toujours côté serveur</em> (même si tu valides côté client).
          </div>
        </div>
      </section>

      <!-- QUIZ -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <!-- Q1 -->
            <div class="q" data-q="q1">
              <div class="qhd">1) Quel symbole précède le nom d’une variable en PHP ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> #</label>
                <label><input type="radio" name="q1" data-correct="1"> $</label>
                <label><input type="radio" name="q1"> @</label>
                <div class="explain">Toutes les variables PHP commencent par <code class="inline">$</code> (ex : <code class="inline">$nom</code>).</div>
              </div>
            </div>

            <!-- Q2 -->
            <div class="q" data-q="q2">
              <div class="qhd">2) Quelle est la bonne façon d’afficher du texte ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> echo "Hello";</label>
                <label><input type="radio" name="q2"> printHTML("Hello")</label>
                <label><input type="radio" name="q2"> console.log("Hello")</label>
                <div class="explain"><code class="inline">echo</code> écrit dans la sortie HTTP.</div>
              </div>
            </div>

            <!-- Q3 -->
            <div class="q" data-q="q3">
              <div class="qhd">3) Quel opérateur concatène des chaînes ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> +</label>
                <label><input type="radio" name="q3" data-correct="1"> .</label>
                <label><input type="radio" name="q3"> &amp;</label>
                <div class="explain">En PHP, on concatène avec le point : <code class="inline">"A" . "B"</code>.</div>
              </div>
            </div>

            <!-- Q4 -->
            <div class="q" data-q="q4">
              <div class="qhd">4) Comment accéder à la valeur <em>Lyon</em> ci-dessous ?</div>
              <div class="qbd">
<pre><code>&lt;?php
$profil = ["nom" =&gt; "Nora", "ville" =&gt; "Lyon"];
?&gt;</code></pre>
                <label><input type="radio" name="q4"> $profil["Nora"]</label>
                <label><input type="radio" name="q4" data-correct="1"> $profil["ville"]</label>
                <label><input type="radio" name="q4"> $profil-&gt;ville</label>
                <div class="explain">Tableau associatif : la clé est <code class="inline">"ville"</code>.</div>
              </div>
            </div>

            <!-- Q5 -->
            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle fonction affiche type + structure d’une variable ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> echo()</label>
                <label><input type="radio" name="q5" data-correct="1"> var_dump()</label>
                <label><input type="radio" name="q5"> alert()</label>
                <div class="explain"><code class="inline">var_dump()</code> montre types et valeurs détaillés.</div>
              </div>
            </div>

            <!-- Q6 -->
            <div class="q" data-q="q6">
              <div class="qhd">6) Où retrouves-tu les données envoyées en POST ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> $_POST</label>
                <label><input type="radio" name="q6"> $_GET</label>
                <label><input type="radio" name="q6"> $_SERVER</label>
                <div class="explain">C’est la superglobale <code class="inline">$_POST</code> (pour GET : <code class="inline">$_GET</code>).</div>
              </div>
            </div>

            <!-- Q7 -->
            <div class="q" data-q="q7">
              <div class="qhd">7) Quelle fonction vérifie qu’une variable est définie ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> isset($x)</label>
                <label><input type="radio" name="q7"> exists($x)</label>
                <label><input type="radio" name="q7"> defined($x)</label>
                <div class="explain"><code class="inline">isset()</code> teste l’existence ; <code class="inline">empty()</code> teste « vide ».</div>
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
