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
  <title>HTML — Liens & formulaires | FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
  :root{
  --bg:#0b1020; --panel:#111833; --panel-2:#0f1530; --border:#1f2a4d;
  --text:#e8eefc; --muted:#a8b3d9; --accent:#5aa1ff; --good:#10d49b; --bad:#ff6b6b;
  --code-bg:#0b132b; --code-bd:#1e2a4a; --kbd-bg:#0e1733;
}

/* Reset & base */
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;
  background:
    radial-gradient(1200px 600px at 10% -10%, #16224b 0%, transparent 60%),
    radial-gradient(800px 500px at 100% 10%, #1c2a59 0%, transparent 50%),
    var(--bg);
  color:var(--text);
  font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  line-height:1.6;
}

/* Conteneur un peu plus large pour donner de la place au quiz */
.wrap{
  max-width:1400px; /* au lieu de 1100 ou 1200 */
  margin:0 auto;
  padding:24px 16px 48px;
}
/* Barre du haut */
.topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
.crumbs a{color:var(--muted);text-decoration:none}
.crumbs a:hover{color:var(--accent)}
.crumbs{font-weight:700}
.badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:var(--muted);padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

/* Titre */
.title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:10px 0 18px}
.title h1{
  font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;
  background:linear-gradient(90deg,var(--accent),#b3c7ff 70%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent
}
.title .progress-pill{font-weight:800;color:#091326;background:linear-gradient(90deg,#7cffc0,#4df1aa);padding:.35rem .65rem;border-radius:999px;border:1px solid #1a3b3a}

/* === Mise en page : on donne plus de place au quiz ===
   - Par défaut: cours 1.08fr / quiz 0.92fr (léger élargissement)
   - Sur écrans larges: 1fr / 1fr (largeur confortable pour les réponses)
*/
.layout{
  display:grid;
  grid-template-columns:1.08fr 0.92fr;
  gap:18px;
}
@media (min-width:1160px){
  .layout{ grid-template-columns:1fr 1fr; }
}
@media (max-width:1020px){
  .layout{ grid-template-columns:1fr; }
}

/* Cartes */
.card{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.35)}
.card .hd{padding:16px 18px;border-bottom:1px solid var(--border);font-weight:800}
.card .bd{padding:18px}

/* Callout */
.callout{
  border:1px dashed var(--border);
  background:linear-gradient(180deg, rgba(90,161,255,.06), transparent);
  padding:14px;border-radius:12px; color:var(--muted)
}

/* Code */
pre,code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,"Segoe UI Mono",Consolas,"Liberation Mono",monospace}
pre{
  background:var(--code-bg);border:1px solid var(--code-bd);color:#dfe6ff;
  padding:14px 16px;border-radius:12px;overflow:auto;line-height:1.5;margin:12px 0 10px
}
code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}
kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

/* Leçon */
.lesson h2{margin:14px 0 6px;font-size:1.1rem}
.lesson ul{margin:8px 0 12px 18px;color:var(--muted)}
.lesson li{margin:6px 0}
.tip{color:#b9ffdf;font-weight:700}

/* === Quiz (élargi & plus lisible) === */
.q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
.q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
.q .qbd{
  padding:16px;
  display:flex;
  flex-direction:column;
  gap:10px;         /* espace entre réponses */
}
.q label{
  display:flex;
  gap:10px;
  align-items:flex-start;
  margin:2px 0;
  cursor:pointer;
  color:var(--text);
  width:100%;       /* la réponse prend toute la largeur dispo => moins de retours à la ligne */
}
.q input{margin-top:2px}
.q .explain{display:none;margin-top:6px;color:var(--muted);font-size:.95rem}
.q.correct{border-color:rgba(16,212,155,.55)}
.q.wrong{border-color:rgba(255,107,107,.55)}

/* Boutons */
.controls{display:flex;gap:10px;margin-top:12px}
button{
  appearance:none;border:1px solid var(--border);
  background:linear-gradient(180deg,#1a244a,#111937);
  color:var(--text);font-weight:800;border-radius:10px;padding:.6rem .9rem;cursor:pointer
}
button:hover{border-color:#2b3c75}
.btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#0b1020;border:0}
.btn-ghost{background:transparent}

/* Score */
.score{
  margin-top:14px;font-weight:800;color:#071421;
  background:linear-gradient(90deg,#7cffc0,#4df1aa);
  display:inline-block;padding:.4rem .7rem;border-radius:999px;border:1px solid #164238
}

/* Liens bas de page */
.footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
.footer-nav a{color:var(--muted);text-decoration:none}
.footer-nav a:hover{color:var(--accent)}

/* Grille 2 colonnes pour petites démos */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:820px){.grid2{grid-template-columns:1fr}}

/* Formulaire démo */
.demo-form{display:flex;flex-direction:column;gap:8px}
.demo-form input,.demo-form select,.demo-form textarea{
  background:#0e1534;border:1px solid var(--border);color:var(--text);
  padding:.6rem .7rem;border-radius:10px
}
.demo-form label{font-weight:700}

  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">HTML — Liens & formulaires</span></div>
      <span class="badge">Leçon 2 / 11</span>
    </div>

    <div class="title">
      <h1>HTML — Liens & formulaires</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- Cours -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Dans cette leçon, on voit comment créer des <strong>liens</strong>, afficher des <strong>images</strong>, et construire des <strong>formulaires</strong> basiques.</p>

          <h2>Liens avec &lt;a&gt;</h2>
<pre><code>&lt;a href="https://example.com"&gt;Visiter le site&lt;/a&gt;
&lt;a href="/contact.html"&gt;Page contact&lt;/a&gt;
&lt;a href="#section-id"&gt;Aller à une section&lt;/a&gt;
&lt;a href="doc.pdf" download&gt;Télécharger le PDF&lt;/a&gt;
&lt;a href="https://ex.com" target="_blank" rel="noopener noreferrer"&gt;Nouvel onglet&lt;/a&gt;</code></pre>
          <ul>
            <li><code class="inline">href</code> : URL absolue, relative ou ancre (<code class="inline">#id</code>).</li>
            <li><code class="inline">target="_blank"</code> ouvre dans un nouvel onglet — utilise aussi <code class="inline">rel="noopener noreferrer"</code> pour la sécurité.</li>
          </ul>

          <h2>Images avec &lt;img&gt;</h2>
<pre><code>&lt;img src="/img/logo.png" alt="Logo FunCodeLab" width="120" height="120"&gt;</code></pre>
          <ul>
            <li><code class="inline">alt</code> décrit l’image pour l’accessibilité (lecteurs d’écran) et le SEO.</li>
            <li>Spécifier <code class="inline">width</code>/<code class="inline">height</code> aide à éviter le décalage de mise en page (CLS).</li>
          </ul>

          <h2>Formulaire de base</h2>
<pre><code>&lt;form action="/subscribe" method="post"&gt;
  &lt;label for="email"&gt;Email&lt;/label&gt;
  &lt;input id="email" name="email" type="email" placeholder="toi@exemple.com" required&gt;

  &lt;label for="role"&gt;Rôle&lt;/label&gt;
  &lt;select id="role" name="role"&gt;
    &lt;option value="dev"&gt;Développeur&lt;/option&gt;
    &lt;option value="designer"&gt;Designer&lt;/option&gt;
  &lt;/select&gt;

  &lt;label for="bio"&gt;Bio&lt;/label&gt;
  &lt;textarea id="bio" name="bio" rows="4"&gt;&lt;/textarea&gt;

  &lt;button type="submit"&gt;Envoyer&lt;/button&gt;
&lt;/form&gt;</code></pre>
          <ul>
            <li><code class="inline">method</code> : <strong>GET</strong> (paramètres dans l’URL) ou <strong>POST</strong> (dans le corps de la requête).</li>
            <li>Chaque champ doit avoir un <code class="inline">name</code> (clé envoyée au serveur) et idéalement un <code class="inline">id</code> relié à un <code class="inline">&lt;label for="..."&gt;</code>.</li>
            <li>Attributs utiles : <code class="inline">required</code>, <code class="inline">minlength</code>, <code class="inline">maxlength</code>, <code class="inline">pattern</code>, <code class="inline">placeholder</code>, <code class="inline">value</code>.</li>
          </ul>

          <div class="grid2">
            <div class="callout">
              <span class="tip">Astuce accessibilité :</span> un <code class="inline">&lt;label&gt;</code> lié améliore le focus (clic sur le texte sélectionne le champ).
            </div>
            <div class="callout">
              <span class="tip">Astuce sécurité :</span> côté HTML on peut valider, mais <strong>valide toujours côté serveur</strong> aussi.
            </div>
          </div>

          <h2>Mini démo (statique)</h2>
          <form class="demo-form" onsubmit="event.preventDefault(); alert('Formulaire de démo envoyé (front uniquement)');">
            <label for="demoEmail">Email</label>
            <input id="demoEmail" type="email" placeholder="toi@exemple.com" required>
            <label for="demoPass">Mot de passe</label>
            <input id="demoPass" type="password" minlength="6" placeholder="••••••" required>
            <label for="demoMsg">Message</label>
            <textarea id="demoMsg" rows="3" placeholder="Votre message"></textarea>
            <button type="submit">Envoyer</button>
          </form>
        </div>
      </section>

      <!-- Quiz -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <!-- Q1 -->
            <div class="q" data-q="q1">
              <div class="qhd">1) Quel attribut d’un lien définit sa destination ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> href</label>
                <label><input type="radio" name="q1"> target</label>
                <label><input type="radio" name="q1"> rel</label>
                <div class="explain"><code class="inline">href</code> contient l’URL cible.</div>
              </div>
            </div>

            <!-- Q2 -->
            <div class="q" data-q="q2">
              <div class="qhd">2) Pourquoi ajouter <code class="inline">rel="noopener noreferrer"</code> avec <code class="inline">target="_blank"</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Pour éviter certains risques sécurité/perf (tabnabbing, accès au contexte)</label>
                <label><input type="radio" name="q2"> Pour fermer la fenêtre automatiquement</label>
                <label><input type="radio" name="q2"> Pour charger l’image plus vite</label>
                <div class="explain">Le couple empêche la page cible d’accéder au contexte de la page d’origine (meilleure sécurité).</div>
              </div>
            </div>

            <!-- Q3 -->
            <div class="q" data-q="q3">
              <div class="qhd">3) Quel attribut est indispensable sur &lt;img&gt; pour l’accessibilité ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> alt</label>
                <label><input type="radio" name="q3"> title</label>
                <label><input type="radio" name="q3"> loading</label>
                <div class="explain"><code class="inline">alt</code> décrit l’image pour les lecteurs d’écran.</div>
              </div>
            </div>

            <!-- Q4 -->
            <div class="q" data-q="q4">
              <div class="qhd">4) Dans un formulaire, à quoi sert l’attribut <code class="inline">name</code> sur un champ ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> À lier le label</label>
                <label><input type="radio" name="q4" data-correct="1"> À définir la clé envoyée au serveur</label>
                <label><input type="radio" name="q4"> À afficher une info-bulle</label>
                <div class="explain">Le <code class="inline">name</code> devient la clé des données soumises (ex: <code class="inline">name=email</code>).</div>
              </div>
            </div>

            <!-- Q5 -->
            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle différence entre GET et POST ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> GET met les données dans l’URL, POST les envoie dans le corps</label>
                <label><input type="radio" name="q5"> Aucune, c’est identique</label>
                <label><input type="radio" name="q5"> POST renvoie toujours du JSON</label>
                <div class="explain">GET : paramètres dans l’URL. POST : données dans le corps de la requête.</div>
              </div>
            </div>

            <!-- Q6 -->
            <div class="q" data-q="q6">
              <div class="qhd">6) Quel attribut fait qu’un champ ne peut pas être laissé vide ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> required</label>
                <label><input type="radio" name="q6"> minlength</label>
                <label><input type="radio" name="q6"> placeholder</label>
                <div class="explain"><code class="inline">required</code> rend le champ obligatoire (validation HTML5 côté client).</div>
              </div>
            </div>

            <!-- Q7 -->
            <div class="q" data-q="q7">
              <div class="qhd">7) Comment lier correctement un label à un input ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1">&lt;label for="email"&gt;Email&lt;/label&gt; + &lt;input id="email"&gt;</label>
                <label><input type="radio" name="q7">&lt;label id="email"&gt;Email&lt;/label&gt; + &lt;input for="email"&gt;</label>
                <label><input type="radio" name="q7">Aucun lien n’est nécessaire</label>
                <div class="explain">Le <code class="inline">for</code> du label doit correspondre à l’<code class="inline">id</code> de l’input.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>
          </form>

          <div class="footer-nav">
            <a href="./1.php">← Leçon précédente</a>
            <a href="./3.php">Leçon suivante →</a>
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
