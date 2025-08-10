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
  <title>JavaScript — Bases | FunCodeLab</title>
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

    /* conteneur un peu large pour que le quiz respire */
    .wrap{max-width:1200px;margin:0 auto;padding:24px 16px 60px;}

    .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .crumbs a{color:var(--muted);text-decoration:none} .crumbs a:hover{color:var(--accent)}
    .crumbs{font-weight:700}
    .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:var(--muted);padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

    .title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:10px 0 18px}
    .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;background:linear-gradient(90deg,var(--accent),#b3c7ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .title .progress-pill{font-weight:800;color:#091326;background:linear-gradient(90deg,#7cffc0,#4df1aa);padding:.35rem .65rem;border-radius:999px;border:1px solid #1a3b3a}

    /* grille : un peu plus d'espace pour le quiz */
    .layout{display:grid;grid-template-columns:1.08fr 0.92fr;gap:20px}
    @media (min-width:1160px){.layout{grid-template-columns:1fr 1fr}}
    @media (max-width:1020px){.layout{grid-template-columns:1fr}}

    .card{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.35)}
    .card .hd{padding:16px 18px;border-bottom:1px solid var(--border);font-weight:800}
    .card .bd{padding:18px}

    .callout{border:1px dashed var(--border); background:linear-gradient(180deg, rgba(90,161,255,.06), transparent); padding:14px;border-radius:12px; color:var(--muted)}

    pre,code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,"Segoe UI Mono",Consolas,"Liberation Mono",monospace}
    pre{background:var(--code-bg);border:1px solid var(--code-bd);color:#dfe6ff;padding:14px 16px;border-radius:12px;overflow:auto;line-height:1.5;margin:12px 0 10px}
    code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}
    kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

    .lesson h2{margin:14px 0 6px;font-size:1.1rem}
    .lesson ul{margin:8px 0 12px 18px;color:var(--muted)} .lesson li{margin:6px 0}
    .tip{color:#b9ffdf;font-weight:700}

    /* quiz */
    .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
    .q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
    .q .qbd{padding:16px; display:flex; flex-direction:column; gap:10px}
    .q label{display:flex;gap:10px;align-items:flex-start;margin:2px 0;cursor:pointer;color:var(--text); width:100%}
    .q input{margin-top:2px}
    .q .explain{display:none;margin-top:6px;color:var(--muted);font-size:.95rem}
    .q.correct{border-color:rgba(16,212,155,.55)} .q.wrong{border-color:rgba(255,107,107,.55)}
    .controls{display:flex;gap:10px;margin-top:12px}
    button{appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);color:var(--text);font-weight:800;border-radius:10px;padding:.6rem 1rem;cursor:pointer}
    button:hover{border-color:#2b3c75}
    .btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#0b1020;border:0}
    .btn-ghost{background:transparent}
    .score{margin-top:14px;font-weight:800;color:#071421;background:linear-gradient(90deg,#7cffc0,#4df1aa);display:inline-block;padding:.45rem .8rem;border-radius:999px;border:1px solid #164238}

    .footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
    .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:var(--accent)}

    /* mini-jeu */
    .mini{margin-top:22px}
    .mini .rules{color:var(--muted);margin:6px 0 10px}
    .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
    .mini textarea{width:100%; background:#0e1534; border:1px solid var(--border); color:var(--text); border-radius:10px; padding:10px; min-height:120px}
    .mini .out{margin-top:10px; background:#0e1534; border:1px solid var(--border); border-radius:10px; padding:10px; color:#cfe1ff; white-space:pre-wrap}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">JavaScript — Bases</span></div>
      <span class="badge">Leçon 3 / 11</span>
    </div>

    <div class="title">
      <h1>JavaScript — Bases</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Bienvenue dans les bases de <strong>JavaScript</strong> : variables, types de données et opérateurs essentiels.</p>

          <h2>Déclarer des variables</h2>
<pre><code>// moderne
let compteur = 0;       // variable réassignable
const PI = 3.14159;     // constante (non réassignable)

// ancien (à éviter sauf cas spécifiques)
var legacy = 'ok';</code></pre>
          <ul>
            <li><code class="inline">let</code> : portée bloc, peut être réassignée.</li>
            <li><code class="inline">const</code> : portée bloc, <em>ne peut pas</em> être réassignée (mais un objet/array reste mutable).</li>
            <li><code class="inline">var</code> : portée fonction + hoisting. Préfère <code class="inline">let/const</code>.</li>
          </ul>

          <h2>Types (primitifs + objet)</h2>
<pre><code>typeof 42           // "number"
typeof 'hello'      // "string"
typeof true         // "boolean"
typeof undefined    // "undefined"
typeof null         // "object"   // (historique)
typeof {a:1}        // "object"
typeof [1,2,3]      // "object"
typeof Symbol('x')  // "symbol"
typeof 123n         // "bigint"</code></pre>

          <h2>Opérateurs utiles</h2>
<pre><code>1 + 2        // addition => 3
'Hello ' + 'JS' // concaténation => "Hello JS"
2 ** 3       // puissance => 8
7 % 3        // modulo => 1

// égalité
2 == '2'     // true  (coercition)
2 === '2'    // false (strict, types comparés)

// templates
const user = 'Sam';
`Bonjour ${user}!`</code></pre>

          <div class="callout">
            <span class="tip">Conseil :</span> utilise toujours <code class="inline">===</code> et <code class="inline">!==</code> pour éviter les surprises de la coercition de type.
          </div>

          <h2>Contrôle de flux</h2>
<pre><code>const n = 5;
if (n &gt; 3) {
  console.log('grand');
} else {
  console.log('petit');
}

for (let i=0; i&lt;3; i++){
  console.log(i);
}</code></pre>

          <h2>Mini démo (playground)</h2>
          <div class="mini">
            <p class="rules">
              Tape quelques lignes JS (ex : <code class="inline">let x=2; x**3</code> ou <code class="inline">typeof 'hey'</code>) puis exécute.
              Rien n’est envoyé au serveur, c’est juste dans ton navigateur.
            </p>
            <div class="play">
              <textarea id="jsInput" placeholder="Exemples :
let x = 7;
const y = 3;
x + y
// Essaie aussi : typeof null, [1,2].length, `Hello ${'JS'}`"></textarea>
              <div class="controls" style="margin-top:10px">
                <button id="run" class="btn-accent">Exécuter</button>
                <button id="clear" class="btn-ghost">Effacer</button>
                <button id="samples" class="btn-ghost">Insérer des exemples</button>
              </div>
              <div id="jsOut" class="out">Sortie :</div>
            </div>
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
              <div class="qhd">1) Quelle déclaration empêche la réassignation ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> var</label>
                <label><input type="radio" name="q1"> let</label>
                <label><input type="radio" name="q1" data-correct="1"> const</label>
                <div class="explain"><code class="inline">const</code> ne peut pas être réassignée (mais un objet/array reste mutable).</div>
              </div>
            </div>

            <!-- Q2 -->
            <div class="q" data-q="q2">
              <div class="qhd">2) Quelle est la valeur de <code class="inline">typeof null</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> "object"</label>
                <label><input type="radio" name="q2"> "null"</label>
                <label><input type="radio" name="q2"> "undefined"</label>
                <div class="explain">C’est un vieux bug historique : <code class="inline">typeof null === "object"</code>.</div>
              </div>
            </div>

            <!-- Q3 -->
            <div class="q" data-q="q3">
              <div class="qhd">3) Lequel est <em>vrai</em> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> 2 == '2' retourne false</label>
                <label><input type="radio" name="q3" data-correct="1"> 2 === '2' retourne false</label>
                <label><input type="radio" name="q3"> 'a' + 1 === 2</label>
                <div class="explain"><code class="inline">2 === '2'</code> compare aussi les types ⇒ false.</div>
              </div>
            </div>

            <!-- Q4 -->
            <div class="q" data-q="q4">
              <div class="qhd">4) Quel opérateur donne le reste de la division ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> **</label>
                <label><input type="radio" name="q4" data-correct="1"> %</label>
                <label><input type="radio" name="q4"> //</label>
                <div class="explain"><code class="inline">a % b</code> renvoie le reste de la division entière.</div>
              </div>
            </div>

            <!-- Q5 -->
            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle syntaxe de chaîne insère des variables ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Template literals : <code class="inline">`Salut ${nom}`</code></label>
                <label><input type="radio" name="q5"> "Salut " + nom</label>
                <label><input type="radio" name="q5"> 'Salut ' , nom</label>
                <div class="explain">Les backticks <code class="inline">`</code> permettent l’interpolation <code class="inline">${...}</code>.</div>
              </div>
            </div>

            <!-- Q6 -->
            <div class="q" data-q="q6">
              <div class="qhd">6) Que vaut <code class="inline">[1,2].length</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6"> "2"</label>
                <label><input type="radio" name="q6" data-correct="1"> 2</label>
                <label><input type="radio" name="q6"> undefined</label>
                <div class="explain">La propriété <code class="inline">length</code> renvoie un nombre.</div>
              </div>
            </div>

            <!-- Q7 -->
            <div class="q" data-q="q7">
              <div class="qhd">7) Quel mot-clé a une portée <em>bloc</em> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> let</label>
                <label><input type="radio" name="q7"> var</label>
                <label><input type="radio" name="q7"> function</label>
                <div class="explain"><code class="inline">let</code> (et <code class="inline">const</code>) ont une portée bloc, contrairement à <code class="inline">var</code>.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>
          </form>

          <div class="footer-nav">
            <a href="./2.php">← Leçon précédente</a>
            <a href="./4.php">Leçon suivante →</a>
          </div>
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

    // ===== Mini démo JS (playground local) =====
    (function(){
      const input = document.getElementById('jsInput');
      const out = document.getElementById('jsOut');
      const run = document.getElementById('run');
      const clearBtn = document.getElementById('clear');
      const samples = document.getElementById('samples');

      function println(msg){
        out.textContent += "\\n" + String(msg);
      }

      run.addEventListener('click', ()=>{
        out.textContent = "Sortie :";
        try {
          // évalue dans une IIFE pour éviter de polluer le scope global
          const result = (function(){ return eval(input.value); })();
          if (result !== undefined) println(result);
          println("✔️ Terminé");
        } catch (e){
          println("❌ Erreur: " + e.message);
        }
      });

      clearBtn.addEventListener('click', ()=>{
        input.value = "";
        out.textContent = "Sortie :";
      });

      samples.addEventListener('click', ()=>{
        input.value = `let a = 10;
const b = 3;
const msg = \`a+b=\${a+b}\`;
msg
typeof null
2 === '2'
7 % 3
[1,2].length`;
      });
    })();
  </script>
</body>
</html>
