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
  <title>Python — Listes & boucles | FunCodeLab</title>
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

  /* Mini-jeu: Boucle Trainer */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.05fr .95fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .terminal{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;min-height:160px;padding:10px 12px;font-family:ui-monospace,Consolas,monospace}
  .terminal .line{white-space:pre-wrap}
  .tools{display:flex;flex-direction:column;gap:10px}
  .tools input,.tools select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .tools .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .tools .muted{color:var(--muted);font-size:.92rem}
  .pill{display:inline-block;background:#0e1534;border:1px solid var(--border);border-radius:999px;padding:.15rem .5rem;color:#cfe1ff;font-weight:700}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">Python — Listes & boucles</span></div>
      <span class="badge">Leçon 8 / 11</span>
    </div>

    <div class="title">
      <h1>Python — Listes & boucles</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS + MINI-JEU (gauche) -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>On passe à la vitesse supérieure : parcours de listes, boucles <code class="inline">for</code>/<code class="inline">while</code>, fonctions utilitaires (<code class="inline">range</code>, <code class="inline">enumerate</code>), compréhensions, et contrôles de flux (<code class="inline">break</code>/<code class="inline">continue</code>).</p>

          <h2>Listes & parcours</h2>
<pre><code>fruits = ["pomme", "banane", "kiwi"]
for f in fruits:
    print(f)

# Avec index
for i, f in enumerate(fruits):
    print(i, f)</code></pre>

          <h2>range() & boucles</h2>
<pre><code>for n in range(1, 6):      # 1..5
    print(n)

i = 0
while i &lt; 3:
    print("tour", i)
    i += 1</code></pre>

          <h2>break / continue</h2>
<pre><code>for n in range(10):
    if n == 5:
        break          # sort de la boucle
    if n % 2 == 0:
        continue       # saute les pairs
    print(n)           # 1, 3</code></pre>

          <h2>Compréhensions de liste</h2>
<pre><code>carres = [x*x for x in range(6)]             # [0,1,4,9,16,25]
impairs = [x for x in range(10) if x % 2]    # [1,3,5,7,9]

# Liste de listes (imbriqué)
pairs = [(i, j) for i in range(2) for j in range(3)]
# [(0,0),(0,1),(0,2),(1,0),(1,1),(1,2)]</code></pre>

          <div class="callout warn">
            <strong>Attention :</strong> évite les <em>boucles infinies</em> (while sans mise à jour de la condition) et ne surcharge pas les compréhensions : au-delà d’une ligne lisible, préfère une boucle classique.
          </div>

          <!-- MINI-JEU: Boucle Trainer -->
          <div class="mini">
            <h3>Mini-jeu : Boucle Trainer <span class="pill">front-only</span></h3>
            <p class="rules">
              Simule les sorties d’une boucle. Entre tes paramètres et clique <strong>Exécuter</strong>.
              <span class="muted">C’est une simulation JS, aucun code Python n’est exécuté côté serveur.</span>
            </p>
            <div class="play">
              <div class="lab">
                <!-- Outils -->
                <div class="tools">
                  <div class="row">
                    <label style="font-weight:800;min-width:110px">Type de boucle</label>
                    <select id="loopType">
                      <option value="for">for (range)</option>
                      <option value="while">while</option>
                      <option value="comp">compréhension</option>
                    </select>
                  </div>

                  <div id="paramsFor">
                    <div class="row">
                      <span class="muted">range(</span>
                      <input id="start" type="number" value="0" style="width:90px">
                      <span class="muted">,</span>
                      <input id="stop" type="number" value="5" style="width:90px">
                      <span class="muted">,</span>
                      <input id="step" type="number" value="1" style="width:90px">
                      <span class="muted">)</span>
                    </div>
                  </div>

                  <div id="paramsWhile" style="display:none">
                    <div class="row">
                      <label style="min-width:110px">i départ</label>
                      <input id="wStart" type="number" value="0" style="width:100px">
                    </div>
                    <div class="row">
                      <label style="min-width:110px">condition</label>
                      <select id="wCond">
                        <option value="lt">&lt;</option>
                        <option value="lte">&le;</option>
                      </select>
                      <input id="wLimit" type="number" value="3" style="width:100px">
                    </div>
                    <div class="row">
                      <label style="min-width:110px">incrément</label>
                      <input id="wStep" type="number" value="1" style="width:100px">
                    </div>
                  </div>

                  <div id="paramsComp" style="display:none">
                    <div class="row">
                      <span class="muted">[ x*x for x in range(</span>
                      <input id="cStop" type="number" value="6" style="width:90px">
                      <span class="muted">) if x % </span>
                      <input id="cMod" type="number" value="2" style="width:90px">
                      <span class="muted">!= 0 ]</span>
                    </div>
                  </div>

                  <div class="row">
                    <button id="runSim" class="btn-accent">Exécuter</button>
                    <button id="clearSim" class="btn-ghost">Effacer</button>
                  </div>

                  <div class="terminal" id="term" aria-live="polite"></div>
                </div>

                <!-- Sandbox de rappel -->
                <div class="sandbox">
<pre><code># Exemples équivalents

# for simple
for n in range(3):
    print(n)

# while équivalent
i = 0
while i &lt; 3:
    print(i)
    i += 1

# compréhension filtrée (carrés impairs &lt; 10)
[x*x for x in range(10) if x % 2]</code></pre>
                </div>
              </div>
            </div>
          </div>
          <!-- /MINI-JEU -->
        </div>
      </section>

      <!-- QUIZ (droite) -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <div class="q" data-q="q1">
              <div class="qhd">1) Que renvoie <code class="inline">list(range(2, 7, 2))</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> [2, 4, 6]</label>
                <label><input type="radio" name="q1"> [2, 3, 4, 5, 6]</label>
                <label><input type="radio" name="q1"> [2, 6]</label>
                <div class="explain">range(start, stop, step) exclut <em>stop</em> → 2,4,6.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quelle instruction <em>quitte</em> la boucle immédiatement ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> break</label>
                <label><input type="radio" name="q2"> continue</label>
                <label><input type="radio" name="q2"> pass</label>
                <div class="explain"><code class="inline">continue</code> saute à l’itération suivante; <code class="inline">pass</code> ne fait rien.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quel est le type de <code class="inline">enumerate(["a","b"])</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> un itérable renvoyant (index, valeur)</label>
                <label><input type="radio" name="q3"> une liste de tuples</label>
                <label><input type="radio" name="q3"> un dictionnaire</label>
                <div class="explain">C’est un itérateur; convertir via <code class="inline">list(enumerate(...))</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Que produit <code class="inline">[x for x in range(5) if x % 2 == 0]</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> [0, 2, 4]</label>
                <label><input type="radio" name="q4"> [1, 3]</label>
                <label><input type="radio" name="q4"> [2, 4]</label>
                <div class="explain">Filtre les pairs de 0 à 4.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle différence entre <code class="inline">while</code> et <code class="inline">for</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> while répète tant qu’une condition est vraie; for itère sur une séquence/itérable</label>
                <label><input type="radio" name="q5"> aucune</label>
                <label><input type="radio" name="q5"> for ne peut pas utiliser range()</label>
                <div class="explain">for consomme un itérable; while dépend d’une condition.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Dans <code class="inline">for i, v in enumerate(lst)</code>, <code class="inline">i</code> vaut…</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> l’index courant (0,1,2,…)</label>
                <label><input type="radio" name="q6"> la valeur courante</label>
                <label><input type="radio" name="q6"> la longueur</label>
                <div class="explain">enumerate fournit un couple (index, valeur).</div>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Sortie ? <code class="inline">for n in range(4):<br>&nbsp;&nbsp;if n==2: continue<br>&nbsp;&nbsp;print(n)</code></div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> 0 1 3</label>
                <label><input type="radio" name="q7"> 0 1 2 3</label>
                <label><input type="radio" name="q7"> 2</label>
                <div class="explain">continue saute l’itération où n==2.</div>
              </div>
            </div>

            <div class="q" data-q="q8">
              <div class="qhd">8) Quelle compréhension crée la liste des carrés de 1 à 5 inclus ?</div>
              <div class="qbd">
                <label><input type="radio" name="q8" data-correct="1"> [x*x for x in range(1,6)]</label>
                <label><input type="radio" name="q8"> [x^2 for x in 1..5]</label>
                <label><input type="radio" name="q8"> [x*x for x in range(1,5)]</label>
                <div class="explain">range(1,6) s’arrête avant 6.</div>
              </div>
            </div>

            <div class="q" data-q="q9">
              <div class="qhd">9) Une boucle <code class="inline">while</code> sans mise à jour de la variable de condition risque…</div>
              <div class="qbd">
                <label><input type="radio" name="q9" data-correct="1"> une boucle infinie</label>
                <label><input type="radio" name="q9"> une erreur de syntaxe</label>
                <label><input type="radio" name="q9"> de trier la liste</label>
                <div class="explain">Toujours faire évoluer la condition.</div>
              </div>
            </div>

            <div class="q" data-q="q10">
              <div class="qhd">10) Que donne <code class="inline">[(i,j) for i in range(2) for j in range(2)]</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q10"> [(0,1),(1,0)]</label>
                <label><input type="radio" name="q10" data-correct="1"> [(0,0),(0,1),(1,0),(1,1)]</label>
                <label><input type="radio" name="q10"> [(0,0),(1,1)]</label>
                <div class="explain">Produit cartésien des deux ranges.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./7.php">← Leçon précédente</a>
              <a href="./9.php">Leçon suivante →</a>
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

    // ===== Mini-jeu: Boucle Trainer (simulation JS) =====
    (function(){
      const loopType = document.getElementById('loopType');
      const sections = {
        for: document.getElementById('paramsFor'),
        while: document.getElementById('paramsWhile'),
        comp: document.getElementById('paramsComp'),
      };
      const term = document.getElementById('term');
      const run = document.getElementById('runSim');
      const clear = document.getElementById('clearSim');

      function showSection(key){
        Object.entries(sections).forEach(([k,el]) => el.style.display = (k===key)?'block':'none');
      }
      loopType.addEventListener('change', e => showSection(e.target.value));
      showSection(loopType.value);

      function println(txt){
        const div = document.createElement('div');
        div.className = 'line';
        div.textContent = String(txt);
        term.appendChild(div);
        term.scrollTop = term.scrollHeight;
      }
      function clearTerm(){ term.innerHTML = ''; }

      run.addEventListener('click', e=>{
        e.preventDefault();
        clearTerm();
        const type = loopType.value;
        try{
          if(type==='for'){
            let start = parseInt(document.getElementById('start').value||'0',10);
            let stop  = parseInt(document.getElementById('stop').value||'0',10);
            let step  = parseInt(document.getElementById('step').value||'1',10);
            if(step === 0) { println('❌ step ne peut pas être 0'); return; }
            const dir = step>0 ? 1 : -1;
            for(let n=start; (dir>0 ? n<stop : n>stop); n+=step){ println(n); }
          } else if(type==='while'){
            let i = parseInt(document.getElementById('wStart').value||'0',10);
            const limit = parseInt(document.getElementById('wLimit').value||'0',10);
            const step  = parseInt(document.getElementById('wStep').value||'1',10);
            const cond  = document.getElementById('wCond').value; // lt/lte
            if(step===0){ println('❌ incrément ne peut pas être 0'); return; }
            let guard = 0, maxGuard = 1000;
            while( (cond==='lt'? i<limit : i<=limit) ){
              println(i);
              i += step;
              if(++guard > maxGuard){ println('⚠️ garde-fou: boucle potentiellement infinie'); break; }
            }
          } else {
            const stop = parseInt(document.getElementById('cStop').value||'0',10);
            const mod  = parseInt(document.getElementById('cMod').value||'2',10);
            const out = [];
            for(let x=0;x<stop;x++){ if(mod===0 || x%mod!==0){ out.push(x*x); } }
            println('['+out.join(', ')+']');
          }
        }catch(err){ println('❌ Erreur: '+err.message); }
      });

      clear.addEventListener('click', e=>{ e.preventDefault(); clearTerm(); });
    })();
  </script>
</body>
</html>
