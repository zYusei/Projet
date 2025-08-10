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
  <title>C++ — Bases | FunCodeLab</title>
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
  .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;background:linear-gradient(90deg,#7ae1ff,#7aa8ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .title .progress-pill{font-weight:800;color:#0d1426;background:linear-gradient(90deg,#ffd166,#fbbf24);padding:.35rem .65rem;border-radius:999px;border:1px solid #6a4c0b}

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

  /* Mini-lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.05fr .95fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .tools{display:flex;flex-direction:column;gap:10px}
  .row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .tools input, .tools select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .terminal{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;min-height:150px;padding:10px 12px;font-family:ui-monospace,Consolas,monospace}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">C++ — Bases</span></div>
      <span class="badge">Leçon 10 / 11</span>
    </div>

    <div class="title">
      <h1>C++ — Bases</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS + MINI-LAB -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectifs : connaître les <strong>types</strong> courants, <code class="inline">&lt;iostream&gt;</code>, les <strong>boucles</strong>, la base des <strong>références</strong> et <strong>vecteurs</strong>. Tu devras lire/écrire sur la console et raisonner sur les types/flux.</p>

          <h2>1) Entrée/Sortie avec &lt;iostream&gt;</h2>
<pre><code>#include &lt;iostream&gt;
int main() {
    std::cout &lt;&lt; "Bonjour " &lt;&lt; 42 &lt;&lt; "\n";
    int x;
    std::cout &lt;&lt; "Entrez un nombre: ";
    std::cin &gt;&gt; x;
    std::cout &lt;&lt; "x² = " &lt;&lt; (x*x) &lt;&lt; std::endl; // flush
}</code></pre>
          <ul>
            <li><code class="inline">std::cout</code> (sortie), <code class="inline">std::cin</code> (entrée), opérateurs <code class="inline">&lt;&lt;</code> et <code class="inline">&gt;&gt;</code>.</li>
            <li><code class="inline">std::endl</code> = saut de ligne + flush (préfère <code class="inline">"\n"</code> la plupart du temps).</li>
          </ul>

          <h2>2) Types de base</h2>
<pre><code>bool b = true;
char c = 'A';          // caractère
int n = 42;            // entier "naturel"
long long big = 1'000'000'000LL;
double pi = 3.14159;   // flottant
auto k = 12u;          // inference (unsigned)</code></pre>
          <div class="callout"><strong>Astuce :</strong> <code class="inline">auto</code> déduit le type. Reste explicite quand la lisibilité l’exige.</div>

          <h2>3) Boucles : for / while / range-based</h2>
<pre><code>// for classique
for (int i = 0; i &lt; 5; ++i) std::cout &lt;&lt; i &lt;&lt; " ";
// while
int j = 5;
while (j--) std::cout &lt;&lt; j &lt;&lt; " ";
// range-based (C++11+)
#include &lt;vector&gt;
std::vector&lt;int&gt; v{1,2,3};
for (int x : v) std::cout &lt;&lt; x &lt;&lt; " ";</code></pre>

          <h2>4) Fonctions, références et const</h2>
<pre><code>int add(int a, int b){ return a+b; }

void increment(int &amp;ref){ ++ref; }         // passe par référence (modifie l'original)
int size_of(const std::vector&lt;int&gt;&amp; v){     // const ref = no copy, non modifiable
    return (int)v.size();
}</code></pre>

          <h2>5) Vecteurs (std::vector)</h2>
<pre><code>#include &lt;vector&gt;
std::vector&lt;int&gt; nums;
nums.push_back(10);
nums.push_back(20);
nums[0] = 5;                // non vérifié
nums.at(1) = 21;            // vérifié (peut lancer out_of_range)
for (size_t i = 0; i &lt; nums.size(); ++i) {
    std::cout &lt;&lt; nums[i] &lt;&lt; " ";
}</code></pre>

          <div class="callout warn">
            <strong>Compilation locale :</strong> <code class="inline">g++ -std=c++17 -O2 main.cpp -o app &amp;&amp; ./app</code>
          </div>

          <!-- MINI-LAB -->
          <div class="mini">
            <h3>Mini-lab : Boucle Builder</h3>
            <p class="rules">Choisis un type de boucle, les bornes et observe le pseudo-output (simulation front). Ça t’aide à raisonner sur les bornes, le pas et le <code class="inline">++i</code>.</p>
            <div class="play">
              <div class="lab">
                <div class="tools">
                  <div class="row">
                    <label>Type :</label>
                    <select id="loopType">
                      <option value="for">for (i=debut; i&lt;fin; ++i)</option>
                      <option value="while">while (i&lt;fin) { ++i; }</option>
                      <option value="range">range-based (vector)</option>
                    </select>
                  </div>
                  <div class="row">
                    <label>Début</label><input id="start" type="number" value="0" style="width:100px">
                    <label>Fin</label><input id="end" type="number" value="5" style="width:100px">
                    <label>Pas</label><input id="step" type="number" value="1" style="width:100px">
                  </div>
                  <div class="row">
                    <button id="runLab" class="btn-accent">Générer l'output</button>
                    <button id="resetLab" class="btn-ghost">Réinitialiser</button>
                  </div>
                  <div class="terminal" id="termLab" aria-live="polite"></div>
                </div>
                <div class="sandbox">
<pre><code>// Exemple for
for (int i = 0; i &lt; 5; ++i) {
    std::cout &lt;&lt; i &lt;&lt; " ";
}</code></pre>
                </div>
              </div>
            </div>
          </div>
          <!-- /MINI-LAB -->
        </div>
      </section>

      <!-- QUIZ -->
      <aside class="card" id="quiz">
        <div class="hd">Quiz — As-tu bien lu ?</div>
        <div class="bd">
          <form id="quizForm">
            <div class="q" data-q="q1">
              <div class="qhd">1) Que fait <code class="inline">std::endl</code> de plus que <code class="inline">"\n"</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> ajoute un saut de ligne et <em>flushe</em> le flux</label>
                <label><input type="radio" name="q1"> rien, c’est identique</label>
                <label><input type="radio" name="q1"> efface la console</label>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quel est le bon opérateur d’insertion dans <code class="inline">std::cout</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1">&lt;&lt;</label>
                <label><input type="radio" name="q2">&gt;&gt;</label>
                <label><input type="radio" name="q2">::</label>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quelle déclaration utilise une référence modifiable ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> int x;</label>
                <label><input type="radio" name="q3" data-correct="1"> int &amp;ref = x;</label>
                <label><input type="radio" name="q3"> const int x;</label>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Le range-based for itère sur…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> les éléments d’un conteneur (ex : <code class="inline">std::vector</code>)</label>
                <label><input type="radio" name="q4"> uniquement des tableaux C</label>
                <label><input type="radio" name="q4"> des flux d’E/S</label>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle signature évite une copie inutile du vecteur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> <code class="inline">int f(const std::vector&lt;int&gt;&amp; v)</code></label>
                <label><input type="radio" name="q5"> <code class="inline">int f(std::vector&lt;int&gt; v)</code></label>
                <label><input type="radio" name="q5"> <code class="inline">int f(std::vector&lt;int&gt;* v)</code></label>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Quelle sortie pour <code class="inline">for(int i=0;i&lt;3;++i) std::cout&lt;&lt;i&lt;&lt;" "</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> 0 1 2 </label>
                <label><input type="radio" name="q6"> 1 2 3 </label>
                <label><input type="radio" name="q6"> 0 1 2 3 </label>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Quelle méthode de <code class="inline">std::vector</code> vérifie les bornes ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> <code class="inline">at()</code></label>
                <label><input type="radio" name="q7"> <code class="inline">operator[]</code></label>
                <label><input type="radio" name="q7"> <code class="inline">get()</code></label>
              </div>
            </div>

            <div class="q" data-q="q8">
              <div class="qhd">8) Quelle déclaration est la plus sûre/ lisible ?</div>
              <div class="qbd">
                <label><input type="radio" name="q8" data-correct="1"> <code class="inline">for (const int x : v) { ... }</code></label>
                <label><input type="radio" name="q8"> <code class="inline">for (int&amp;&amp; x : v) { ... }</code></label>
                <label><input type="radio" name="q8"> <code class="inline">for (register int x : v) { ... }</code></label>
              </div>
            </div>

            <div class="q" data-q="q9">
              <div class="qhd">9) Quel en-tête inclure pour <code class="inline">std::vector</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q9" data-correct="1">&lt;vector&gt;</label>
                <label><input type="radio" name="q9">&lt;list&gt;</label>
                <label><input type="radio" name="q9">&lt;iostream&gt;</label>
              </div>
            </div>

            <div class="q" data-q="q10">
              <div class="qhd">10) Pourquoi éviter <code class="inline">using namespace std;</code> dans les exemples sérieux ?</div>
              <div class="qbd">
                <label><input type="radio" name="q10" data-correct="1"> risque de collisions de noms et moindre lisibilité</label>
                <label><input type="radio" name="q10"> ralentit l’exécution</label>
                <label><input type="radio" name="q10"> empêche l’optimisation</label>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./9.php">← Leçon précédente</a>
              <a href="./11.php">Leçon suivante →</a>
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

    // ===== Mini-lab: Boucle Builder (simulation JS) =====
    (function(){
      const term = document.getElementById('termLab');
      const t = id => document.getElementById(id);

      function println(s){
        const d = document.createElement('div');
        d.textContent = s;
        term.appendChild(d); term.scrollTop = term.scrollHeight;
      }
      function clearTerm(){ term.innerHTML=''; }

      function simulate(){
        clearTerm();
        const kind = t('loopType').value;
        let start = parseInt(t('start').value||'0',10);
        let end   = parseInt(t('end').value||'0',10);
        let step  = parseInt(t('step').value||'1',10);
        if (step === 0) { println('❌ Pas ne peut pas être 0'); return; }

        let out = [];
        if (kind === 'for'){
          if (step > 0){ for (let i=start;i<end;i+=step) out.push(i); }
          else { for (let i=start;i>end;i+=step) out.push(i); }
          println('for → ' + out.join(' '));
        } else if (kind === 'while'){
          let i = start;
          if (step > 0){ while(i<end){ out.push(i); ++i; if(step!==1) i+=(step-1); } }
          else { while(i>end){ out.push(i); --i; if(step!==-1) i+=(step+1); } }
          println('while → ' + out.join(' '));
        } else {
          // range-based: simule un vector de start..end (exclu)
          let v=[]; if(step>0){ for(let i=start;i<end;i+=step) v.push(i); }
          else{ for(let i=start;i>end;i+=step) v.push(i); }
          println('vector = {'+v.join(', ')+'}');
          println('for (const int x : vector) → ' + v.join(' '));
        }
      }

      t('runLab').addEventListener('click', e=>{ e.preventDefault(); simulate(); });
      t('resetLab').addEventListener('click', e=>{
        e.preventDefault(); t('start').value=0; t('end').value=5; t('step').value=1; clearTerm();
      });
    })();
  </script>
</body>
</html>
