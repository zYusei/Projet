<?php
require_once('../session.php');
$user = current_user();
if (!$user) {
    header('Location: ../connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HTML — Structure | FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg:#0b1020;         /* fond */
      --panel:#111833;      /* cartes/panels */
      --panel-2:#0f1530;
      --border:#1f2a4d;
      --text:#e8eefc;
      --muted:#a8b3d9;
      --accent:#5aa1ff;     /* bleu neon */
      --accent-2:#7cffc0;   /* vert neon */
      --bad:#ff6b6b;
      --good:#10d49b;
      --code-bg:#0b132b;
      --code-bd:#1e2a4a;
      --kbd-bg:#0e1733;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:
        radial-gradient(1200px 600px at 10% -10%, #16224b 0%, transparent 60%),
        radial-gradient(800px 500px at 100% 10%, #1c2a59 0%, transparent 50%),
        var(--bg);
      color:var(--text);
      font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      line-height:1.6;
    }

    .wrap{max-width:1100px;margin:0 auto;padding:24px 16px 48px;}
    .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .crumbs a{color:var(--muted);text-decoration:none}
    .crumbs a:hover{color:var(--accent)}
    .crumbs{font-weight:700}
    .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);
      color:var(--muted);padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

    .title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:10px 0 18px}
    .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;
      background:linear-gradient(90deg,var(--accent),#b3c7ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .title .progress-pill{font-weight:800;color:#091326;background:linear-gradient(90deg,#7cffc0,#4df1aa);
      padding:.35rem .65rem;border-radius:999px;border:1px solid #1a3b3a}

    .layout{display:grid;grid-template-columns:1.15fr .85fr;gap:18px}
    @media (max-width: 1020px){ .layout{grid-template-columns:1fr} }

    .card{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.35)}
    .card .hd{padding:16px 18px;border-bottom:1px solid var(--border);font-weight:800}
    .card .bd{padding:18px}

    .callout{
      border:1px dashed var(--border); background:linear-gradient(180deg, rgba(90,161,255,.06), transparent);
      padding:14px;border-radius:12px; color:var(--muted)
    }

    pre, code{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Segoe UI Mono", Consolas, "Liberation Mono", monospace}
    pre{
      background:var(--code-bg); border:1px solid var(--code-bd); color:#dfe6ff;
      padding:14px 16px;border-radius:12px; overflow:auto; line-height:1.5; margin:12px 0 10px
    }
    code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}
    kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

    .lesson h2{margin:14px 0 6px;font-size:1.1rem}
    .lesson ul{margin:8px 0 12px 18px;color:var(--muted)}
    .lesson li{margin:6px 0}
    .tip{color:#b9ffdf;font-weight:700}

    /* Quiz */
    .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:12px 0}
    .q .qhd{padding:12px 14px;border-bottom:1px solid var(--border);font-weight:700}
    .q .qbd{padding:14px}
    .q label{display:flex;gap:10px;align-items:flex-start;margin:6px 0;cursor:pointer;color:var(--text)}
    .q input{margin-top:2px}
    .q .explain{display:none;margin-top:6px;color:var(--muted);font-size:.95rem}
    .q.correct{border-color:rgba(16,212,155,.55)}
    .q.wrong  {border-color:rgba(255,107,107,.55)}
    .controls{display:flex;gap:10px;margin-top:12px}
    button{
      appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);
      color:var(--text);font-weight:800;border-radius:10px;padding:.6rem .9rem;cursor:pointer
    }
    button:hover{border-color:#2b3c75}
    .btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#0b1020;border:0}
    .btn-ghost{background:transparent}
    .score{
      margin-top:14px; font-weight:800; color:#071421;
      background:linear-gradient(90deg,#7cffc0,#4df1aa); display:inline-block; padding:.4rem .7rem;
      border-radius:999px; border:1px solid #164238
    }

    .footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
    .footer-nav a{color:var(--muted);text-decoration:none}
    .footer-nav a:hover{color:var(--accent)}

    .checklist{display:flex;flex-direction:column;gap:8px;color:var(--muted)}
    .checklist .ok{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">HTML — Structure</span></div>
      <span class="badge">Leçon 1 / 11</span>
    </div>

    <div class="title">
      <h1>HTML — Structure</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- Cours -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>
            Une page HTML a une structure minimale : un <code class="inline">&lt;!DOCTYPE html&gt;</code>,
            une racine <code class="inline">&lt;html&gt;</code>, une section <code class="inline">&lt;head&gt;</code>
            pour les métadonnées et une section <code class="inline">&lt;body&gt;</code> qui contient le contenu visible.
          </p>

          <h2>Structure de base</h2>
<pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="fr"&gt;
  &lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;Mon titre de page&lt;/title&gt;
  &lt;/head&gt;
  &lt;body&gt;
    &lt;h1&gt;Bonjour monde&lt;/h1&gt;
    &lt;p&gt;Ceci est un paragraphe.&lt;/p&gt;
  &lt;/body&gt;
&lt;/html&gt;</code></pre>

          <ul>
            <li><strong>&lt;!DOCTYPE html&gt;</strong> indique au navigateur que c’est du HTML5.</li>
            <li><strong>&lt;html&gt;</strong> : élément racine (attribut <code class="inline">lang</code> recommandé).</li>
            <li><strong>&lt;head&gt;</strong> : métadonnées (encodage, titre, liens CSS, scripts différés, etc.).</li>
            <li><strong>&lt;body&gt;</strong> : tout ce que l’utilisateur voit (titres, paragraphes, images…).</li>
          </ul>

          <h2>Titres et paragraphes</h2>
          <p>
            Les titres vont de <code class="inline">&lt;h1&gt;</code> (le plus important) à <code class="inline">&lt;h6&gt;</code>.
            Le texte courant va dans <code class="inline">&lt;p&gt;</code>.
          </p>
<pre><code>&lt;body&gt;
  &lt;h1&gt;Titre principal&lt;/h1&gt;
  &lt;h2&gt;Sous-titre&lt;/h2&gt;
  &lt;p&gt;Un paragraphe qui explique quelque chose.&lt;/p&gt;
&lt;/body&gt;</code></pre>

          <div class="callout">
            <span class="tip">Astuce :</span> tu peux avoir techniquement plusieurs <code class="inline">&lt;h1&gt;</code>,
            mais pour l’accessibilité et le SEO on <em>recommande</em> généralement un seul <code class="inline">&lt;h1&gt;</code> principal.
          </div>

          <h2>Checklist rapide</h2>
          <div class="checklist">
            <div>• Le doctype HTML5 est <code class="inline">&lt;!DOCTYPE html&gt;</code>.</div>
            <div>• Le contenu visible est dans <code class="inline">&lt;body&gt;</code>.</div>
            <div>• Le titre de l’onglet se met dans <code class="inline">&lt;title&gt;</code> (dans <code class="inline">&lt;head&gt;</code>).</div>
            <div>• Utilise des niveaux de titres logiques (h1 → h2 → h3 …).</div>
          </div>
        </div>
      </section>

      <!-- Quiz -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <!-- Q1 -->
            <div class="q" data-q="q1">
              <div class="qhd">1) Où se trouve le contenu <em>visible</em> d’une page ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> &lt;head&gt;</label>
                <label><input type="radio" name="q1" data-correct="1"> &lt;body&gt;</label>
                <label><input type="radio" name="q1"> &lt;title&gt;</label>
                <div class="explain">Le contenu visible se trouve dans <code class="inline">&lt;body&gt;</code>.</div>
              </div>
            </div>

            <!-- Q2 -->
            <div class="q" data-q="q2">
              <div class="qhd">2) Quel est le doctype correct pour HTML5 ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2">&lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"&gt;</label>
                <label><input type="radio" name="q2" data-correct="1">&lt;!DOCTYPE html&gt;</label>
                <label><input type="radio" name="q2">&lt;!doctype HTML5&gt;</label>
                <div class="explain">En HTML5, on utilise simplement <code class="inline">&lt;!DOCTYPE html&gt;</code>.</div>
              </div>
            </div>

            <!-- Q3 -->
            <div class="q" data-q="q3">
              <div class="qhd">3) Où va la balise &lt;title&gt; ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Dans &lt;head&gt;</label>
                <label><input type="radio" name="q3"> Dans &lt;body&gt;</label>
                <label><input type="radio" name="q3"> N’importe où</label>
                <div class="explain"><code class="inline">&lt;title&gt;</code> fait partie des métadonnées : il se met dans <code class="inline">&lt;head&gt;</code>.</div>
              </div>
            </div>

            <!-- Q4 -->
            <div class="q" data-q="q4">
              <div class="qhd">4) Quel élément représente le <strong>titre principal</strong> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1">&lt;h1&gt;</label>
                <label><input type="radio" name="q4">&lt;h3&gt;</label>
                <label><input type="radio" name="q4">&lt;p&gt;</label>
                <div class="explain">Le plus haut niveau de titre est <code class="inline">&lt;h1&gt;</code>.</div>
              </div>
            </div>

            <!-- Q5 -->
            <div class="q" data-q="q5">
              <div class="qhd">5) Vrai ou Faux : on peut <em>techniquement</em> avoir plusieurs &lt;h1&gt; dans une page.</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Vrai</label>
                <label><input type="radio" name="q5"> Faux</label>
                <div class="explain">C’est possible techniquement, mais on recommande en pratique d’avoir un seul &lt;h1&gt; principal.</div>
              </div>
            </div>

            <!-- Q6 -->
            <div class="q" data-q="q6">
              <div class="qhd">6) Choisis l’ordre correct des éléments principaux :</div>
              <div class="qbd">
                <label><input type="radio" name="q6"> &lt;html&gt; → &lt;body&gt; → &lt;head&gt;</label>
                <label><input type="radio" name="q6" data-correct="1"> &lt;html&gt; → &lt;head&gt; → &lt;body&gt;</label>
                <label><input type="radio" name="q6"> &lt;head&gt; → &lt;html&gt; → &lt;body&gt;</label>
                <div class="explain">L’ordre correct est <code class="inline">&lt;html&gt;</code> → <code class="inline">&lt;head&gt;</code> → <code class="inline">&lt;body&gt;</code>.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>
          </form>

          <div class="footer-nav">
            <a href="../parcours.php">← Retour aux parcours</a>
            <a href="./2.php">Leçon suivante →</a>
          </div>
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
          if(explain) explain.style.display = 'block';

          if(checked && checked.hasAttribute('data-correct')){
            ok++;
            q.classList.add('correct');
          }else{
            q.classList.add('wrong');
          }
        });
        scoreEl.style.display = 'inline-block';
        scoreEl.textContent = `Score : ${ok}/${total} • ${Math.round((ok/total)*100)}%`;
        // Facultatif : scroll vers le score
        scoreEl.scrollIntoView({behavior:'smooth',block:'center'});
      }

      function resetQuiz(){
        form.reset();
        document.querySelectorAll('.q').forEach(q=>{
          q.classList.remove('correct','wrong');
          const ex = q.querySelector('.explain');
          if(ex) ex.style.display = 'none';
        });
        scoreEl.style.display = 'none';
      }

      btnCheck.addEventListener('click', evaluate);
      btnReset.addEventListener('click', resetQuiz);
    })();
  </script>
</body>
</html>
