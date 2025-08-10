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
  <title>Python — Premiers pas | FunCodeLab</title>
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
  button{appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);color:#fff;font-weight:800;border-radius:10px;padding:.6rem 1rem;cursor:pointer}
  button:hover{border-color:#2b3c75}
  .btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#fff;border:0}
  .btn-ghost{background:transparent;color:#fff}
  .score{margin-top:14px;font-weight:800;color:#071421;background:linear-gradient(90deg,#7cffc0,#4df1aa);display:inline-block;padding:.45rem .8rem;border-radius:999px;border:1px solid #164238}

  .footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
  .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:var(--accent)}

  /* Mini-jeu Output Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.08fr .92fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .sandbox .ex{margin-bottom:12px}
  .sandbox .out{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;padding:10px;min-height:44px;margin-top:8px;color:#dfe6ff;white-space:pre-wrap}
  .sandbox input{width:100%;background:#0f1a3b;border:1px solid var(--border);border-radius:10px;color:#fff;padding:.55rem .7rem}
  .tools{display:flex;flex-direction:column;gap:8px}
  .tools .mission{display:flex;align-items:flex-start;gap:8px;color:var(--muted);line-height:1.35}
  .tools .mission.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🐍 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">Python — Premiers pas</span></div>
      <span class="badge">Leçon 7 / 11</span>
    </div>

    <div class="title">
      <h1>Python — Premiers pas</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Les bases : <strong>print</strong>, <strong>variables</strong>, <strong>types simples</strong> et <strong>f-strings</strong>.</p>

          <h2>1) Afficher du texte</h2>
<pre><code>print("Bonjour FunCodeLab!")</code></pre>

          <h2>2) Variables & types</h2>
<pre><code>nom = "Nora"      # str
age = 21          # int
prix = 19.99      # float
ok = True         # bool
</code></pre>

          <h2>3) Concaténation & f-strings</h2>
<pre><code># Concaténation
print("Salut " + nom + ", tu as " + str(age) + " ans")
# f-string (recommandé)
print(f"Salut {nom}, tu as {age} ans")</code></pre>

          <h2>4) Conversion de types</h2>
<pre><code>n = int("42")     # 42
x = float("3.14") # 3.14
s = str(123)      # "123"</code></pre>

          <h2>5) Input (console)</h2>
<pre><code>pseudo = input("Ton pseudo ? ")
print(f"Bienvenue {pseudo}!")</code></pre>

          <div class="callout">
            <strong>Astuce :</strong> Python est <em>typé dynamiquement</em>, mais les types existent. Utilise <code class="inline">type(x)</code> pour inspecter.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Output Lab (devine la sortie)</h3>
            <p class="rules">
              <strong>Objectif :</strong> pour chaque snippet Python, saisis exactement le texte que la console afficherait.
              Valide : quand les 3 sont corrects ✅
            </p>
            <div class="play">
              <div class="lab">
                <!-- Exemples -->
                <div class="sandbox">
                  <div class="ex" data-id="ex1">
<pre><code>nom = "Ada"
print("Hello " + nom)</code></pre>
                    <input placeholder='Sortie attendue, ex: Hello Ada'>
                  </div>

                  <div class="ex" data-id="ex2">
<pre><code>a, b = 2, 3
print(a * b)</code></pre>
                    <input placeholder='Sortie attendue, ex: 6'>
                  </div>

                  <div class="ex" data-id="ex3">
<pre><code>age = 20
print(f"{age} ans")</code></pre>
                    <input placeholder='Sortie attendue, ex: 20 ans'>
                  </div>

                  <button id="checkOut" class="btn-accent" style="margin-top:8px">Vérifier mes sorties</button>
                  <div id="outResult" class="out" aria-live="polite"></div>
                </div>

                <!-- Missions -->
                <div class="tools">
                  <div class="mission" id="m1">⬜ Mission 1 — Snippet 1 correct</div>
                  <div class="mission" id="m2">⬜ Mission 2 — Snippet 2 correct</div>
                  <div class="mission" id="m3">⬜ Mission 3 — Snippet 3 correct</div>
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
              <div class="qhd">1) Quelle instruction affiche du texte ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> print()</label>
                <label><input type="radio" name="q1"> echo()</label>
                <label><input type="radio" name="q1"> console.log()</label>
                <div class="explain"><code class="inline">print()</code> envoie du texte dans la console Python.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quel type correspond à <code class="inline">19.99</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> int</label>
                <label><input type="radio" name="q2" data-correct="1"> float</label>
                <label><input type="radio" name="q2"> str</label>
                <div class="explain">Un nombre à virgule est un <em>float</em>.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Comment interpoler proprement des variables dans une chaîne ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> "Bonjour " + nom</label>
                <label><input type="radio" name="q3" data-correct="1"> f"Bonjour {nom}"</label>
                <label><input type="radio" name="q3"> "Bonjour {}".format(nom)</label>
                <div class="explain">Les f-strings sont concises et lisibles.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Quelle conversion est correcte ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> int("42")</label>
                <label><input type="radio" name="q4"> number("42")</label>
                <label><input type="radio" name="q4"> parseInt("42")</label>
                <div class="explain">Utilise les constructeurs Python : <code class="inline">int()</code>, <code class="inline">float()</code>, <code class="inline">str()</code>…</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Que renvoie <code class="inline">type("abc")</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> &lt;class 'int'&gt;</label>
                <label><input type="radio" name="q5" data-correct="1"> &lt;class 'str'&gt;</label>
                <label><input type="radio" name="q5"> string</label>
                <div class="explain">Une chaîne est de type <code class="inline">str</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Quelle est la sortie de <code class="inline">print("A" + str(3))</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> A3</label>
                <label><input type="radio" name="q6"> A 3</label>
                <label><input type="radio" name="q6"> erreur</label>
                <div class="explain">On convertit 3 en chaîne avant concaténation.</div>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Quelle variable est booléenne ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7"> "True"</label>
                <label><input type="radio" name="q7"> 1</label>
                <label><input type="radio" name="q7" data-correct="1"> True</label>
                <div class="explain"><code class="inline">True</code> / <code class="inline">False</code> sont des booléens.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./6.php">← Leçon précédente</a>
              <a href="./8.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Output Lab =====
    (function(){
      const answers = {
        ex1: "Hello Ada",
        ex2: "6",
        ex3: "20 ans"
      };
      const out = document.getElementById('outResult');
      const m1 = document.getElementById('m1');
      const m2 = document.getElementById('m2');
      const m3 = document.getElementById('m3');

      document.getElementById('checkOut').addEventListener('click', ()=>{
        let ok = 0;
        document.querySelectorAll('.sandbox .ex').forEach(ex => {
          const id = ex.getAttribute('data-id');
          const val = (ex.querySelector('input').value || '').trim();
          const good = (val === answers[id]);
          if (good) ok++;
          // petite marque locale
          ex.querySelector('input').style.borderColor = good ? '#10d49b' : '#ff6b6b';
        });
        out.textContent = `Résultats : ${ok}/3`;
        if (ok >= 1) { m1.classList.add('done'); m1.textContent = '✅ Mission 1 — Snippet 1 correct'; }
        if (ok >= 2) { m2.classList.add('done'); m2.textContent = '✅ Mission 2 — Snippet 2 correct'; }
        if (ok >= 3) { m3.classList.add('done'); m3.textContent = '✅ Mission 3 — Snippet 3 correct'; }
      });
    })();
  </script>
</body>
</html>
