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
  <title>C++ — Programmation objet | FunCodeLab</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
   :root{
    --bg:#0b1020; --panel:#111833; --panel-2:#0f1530; --border:#1f2a4d;
    --text:#e8eefc; --muted:#a8b3d9; --accent:#5aa1ff; --good:#10d49b; --bad:#ff6b6b;
    --code-bg:#0b132b; --code-bd:#1e2a4a; --kbd-bg:#0e1733; --warn:#fbbf24;
    --danger:#ef6a6a;
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#ffd2d2;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

  .title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:10px 0 18px}
  .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;background:linear-gradient(90deg,var(--accent),#b3c7ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .title .progress-pill{font-weight:800;color:#1a0c0c;background:linear-gradient(90deg,#ff8a8a,#ff5858);padding:.35rem .65rem;border-radius:999px;border:1px solid #612b2b}

  /* Grille 2 colonnes */
  .layout{display:grid;grid-template-columns:1.08fr 0.92fr;gap:20px}
  @media (min-width:1160px){.layout{grid-template-columns:1fr 1fr}}
  @media (max-width:1020px){.layout{grid-template-columns:1fr}}

  .card{background:var(--panel);border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 30px rgba(0,0,0,.35)}
  .card .hd{padding:16px 18px;border-bottom:1px solid var(--border);font-weight:800}
  .card .bd{padding:18px}

  .callout{border:1px dashed var(--border); background:linear-gradient(180deg, rgba(90,161,255,.06), transparent); padding:14px;border-radius:12px; color:var(--muted)}
  .warn{border-color:rgba(251,191,36,.45); background:linear-gradient(180deg, rgba(251,191,36,.07), transparent);}
  .danger{border-color:rgba(239,106,106,.55); background:linear-gradient(180deg, rgba(239,106,106,.08), transparent);}

  pre,code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,"Segoe UI Mono",Consolas,"Liberation Mono",monospace}
  pre{background:var(--code-bg);border:1px solid var(--code-bd);color:#dfe6ff;padding:14px 16px;border-radius:12px;overflow:auto;line-height:1.5;margin:12px 0 10px}
  code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}
  kbd{background:var(--kbd-bg);border:1px solid var(--border);padding:.15rem .4rem;border-radius:6px;color:#cfe1ff}

  .lesson h2{margin:14px 0 6px;font-size:1.1rem}
  .lesson ul{margin:8px 0 12px 18px;color:var(--muted)} .lesson li{margin:6px 0}

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

  /* Mini-jeu OOP Lab (intégré dans la leçon) */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.05fr .95fr;gap:16px;align-items:start}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .sandbox .log{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;padding:10px;min-height:120px;max-height:220px;overflow:auto;font-size:.95rem}
  .toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:10px}
  .toolbar label{display:inline-flex;align-items:center;gap:6px}
  .actionbar{display:flex;gap:8px;flex-wrap:wrap;margin:10px 0}
  .tools{display:flex;flex-direction:column;gap:12px;padding-left:16px}
  .mission-panel{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:12px}
  .checks{margin-top:4px;display:flex;flex-direction:column;gap:8px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted);font-size:.92rem}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">C++ — Programmation objet</span></div>
      <span class="badge">Leçon 11 / 11</span>
    </div>

    <div class="title">
      <h1>C++ — Programmation objet</h1>
      <span class="progress-pill">Niveau : Difficile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>On pousse la POO moderne en C++ : <strong>encapsulation</strong>, <strong>héritage</strong>, <strong>polymorphisme</strong>, <strong>classes abstraites</strong>, <strong>destructeurs virtuels</strong>, <strong>override/final</strong>, <strong>diamant (héritage virtuel)</strong> et <strong>smart pointers</strong>.</p>

          <h2>1) Classe de base, virtuel & override</h2>
<pre><code>// Shape.h
struct Shape {
  virtual ~Shape() = default;             // destructeur virtuel (indispensable)
  virtual double area() const = 0;        // méthode pure : classe abstraite
  virtual const char* name() const { return "Shape"; }
};

struct Rect : Shape {
  double w{}, h{};
  Rect(double w, double h): w(w), h(h) {}
  double area() const override { return w * h; }
  const char* name() const override { return "Rect"; }
};

struct Circle final : Shape {
  double r{};
  explicit Circle(double r): r(r) {}
  double area() const override { return 3.14159 * r * r; }
  const char* name() const override { return "Circle"; }
};</code></pre>

          <h2>2) Polymorphisme par référence/pointeur</h2>
<pre><code>#include &lt;memory&gt;
#include &lt;vector&gt;

int main() {
  std::vector&lt;std::unique_ptr&lt;Shape&gt;&gt; v;
  v.emplace_back(std::make_unique&lt;Rect&gt;(3,4));
  v.emplace_back(std::make_unique&lt;Circle&gt;(2));
  for (const auto&amp; s : v) {
    std::cout &lt;&lt; s-&gt;name() &lt;&lt; " area=" &lt;&lt; s-&gt;area() &lt;&lt; "\n";
  }
}</code></pre>

          <div class="callout danger">
            <strong>Attention :</strong> <em>jamais</em> de polymorphisme avec des objets passés par valeur (risque de <em>object slicing</em>). Utilise <code class="inline">Shape&amp;</code> ou <code class="inline">Shape*</code> (idéalement <code class="inline">std::unique_ptr&lt;Shape&gt;</code>).
          </div>

          <h2>3) Diamant & héritage virtuel</h2>
<pre><code>struct A { int id; A(int id):id(id){} };
struct B : virtual A { B():A(1){} };  // virtual => une seule sous-partie A
struct C : virtual A { C():A(2){} };
struct D : B, C { D(): A(42) {} };    // D choisit le A final</code></pre>

          <h2>4) downcast sûr</h2>
<pre><code>void printIfCircle(Shape* s) {
  if (auto c = dynamic_cast&lt;Circle*&gt;(s)) {
    std::cout &lt;&lt; "Circle r=" &lt;&lt; c-&gt;r &lt;&lt; "\n";
  }
}</code></pre>

          <h2>5) Règle des 5 (gestion ressource)</h2>
          <ul>
            <li>Définis ou supprime : destructeur, copie (ctor/opérateur), move (ctor/opérateur).</li>
            <li>Avec polymorphisme, préfère l’agrégation via <code class="inline">unique_ptr</code> et la “règle des 0”.</li>
          </ul>

          <!-- MINI-JEU intégré sous la leçon -->
          <div class="mini">
            <h3>Mini-jeu : OOP Lab (polymorphisme)</h3>
            <p class="rules">
              <strong>Objectif :</strong> instancie une forme via un <em>pointeur de base</em>, appelle des méthodes virtuelles,
              puis supprime via le pointeur de base pour vérifier le <strong>destructeur virtuel</strong>. Les coches passent au vert ✅
            </p>
            <div class="play">
              <div class="lab">
                <!-- Aire de jeu -->
                <div class="sandbox">
                  <div class="toolbar">
                    <span style="font-weight:800">Base* b =&nbsp;</span>
                    <select id="selDerived" aria-label="Type dérivé">
                      <option value="Rect">new Rect(3,4)</option>
                      <option value="Circle">new Circle(2)</option>
                    </select>
                    <label><input type="checkbox" id="virtDtor" checked /> Destructeur virtuel</label>
                    <button id="btnNew" class="btn-accent" style="margin-left:auto">Instancier</button>
                  </div>

                  <div class="actionbar">
                    <button id="btnName">b-&gt;name()</button>
                    <button id="btnArea">b-&gt;area()</button>
                    <button id="btnDelete" class="btn-ghost">delete b</button>
                  </div>

                  <div class="log" id="log" aria-live="polite"></div>
                </div>

                <!-- Missions (décalées à droite) -->
                <div class="tools">
                  <div class="mission-panel">
                    <div class="checks">
                      <div class="check" id="m1">⬜ Mission 1 — Appeler une méthode virtuelle (name/area)</div>
                      <div class="check" id="m2">⬜ Mission 2 — Supprimer via le pointeur de base et observer le destructeur</div>
                      <div class="check" id="m3">⬜ Mission 3 — Recréer avec un autre type dérivé et vérifier le dispatch dynamique</div>
                    </div>
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
            <!-- 11 questions -->
            <div class="q" data-q="q1">
              <div class="qhd">1) À quoi sert un destructeur <em>virtuel</em> dans une classe de base polymorphe ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> À appeler le destructeur du type dérivé via un pointeur de base</label>
                <label><input type="radio" name="q1"> À interdire la copie</label>
                <label><input type="radio" name="q1"> À accélérer la destruction</label>
                <div class="explain">Sans destructeur virtuel : fuite ou destruction incomplète si <code class="inline">delete base*</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Qu’est-ce qu’une méthode virtuelle pure ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Méthode = 0, rendant la classe abstraite</label>
                <label><input type="radio" name="q2"> Méthode finale non surchargeable</label>
                <label><input type="radio" name="q2"> Méthode statique</label>
                <div class="explain"><code class="inline">virtual T f() = 0;</code> → la classe ne peut pas être instanciée.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quel mot-clé force le compilateur à vérifier la redéfinition d’une virtuelle ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> virtual</label>
                <label><input type="radio" name="q3" data-correct="1"> override</label>
                <label><input type="radio" name="q3"> final</label>
                <div class="explain"><code class="inline">override</code> déclenche une erreur si la signature ne correspond pas.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Que se passe-t-il si tu passes un objet dérivé par valeur à une fonction prenant une base par valeur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Object slicing (perte de la partie dérivée)</label>
                <label><input type="radio" name="q4"> Crash garanti</label>
                <label><input type="radio" name="q4"> Rien de spécial</label>
                <div class="explain">Utilise des références/pointeurs pour préserver le polymorphisme.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle structure moderne recommandes-tu pour gérer un polymorphisme possédant la ressource ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> std::vector&lt;std::unique_ptr&lt;Base&gt;&gt;</label>
                <label><input type="radio" name="q5"> std::vector&lt;Base&gt;</label>
                <label><input type="radio" name="q5"> std::array&lt;Base, N&gt;</label>
                <div class="explain">Évite la copie, gère RAII, garantit la destruction polymorphe.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Dans un diamant, l’<em>héritage virtuel</em> sert à…</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> Partager une unique sous-partie commune</label>
                <label><input type="radio" name="q6"> Interdire l’héritage multiple</label>
                <label><input type="radio" name="q6"> Empêcher toute RTTI</label>
                <div class="explain">Sinon, on a deux sous-objets A distincts dans D.</div>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Quel cast est sûr pour descendre dans la hiérarchie à l’exécution ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7"> static_cast</label>
                <label><input type="radio" name="q7" data-correct="1"> dynamic_cast</label>
                <label><input type="radio" name="q7"> reinterpret_cast</label>
                <div class="explain"><code class="inline">dynamic_cast</code> vérifie le type réel via RTTI.</div>
              </div>
            </div>

            <div class="q" data-q="q8">
              <div class="qhd">8) Quel couple empêche l’héritage et la redéfinition ?</div>
              <div class="qbd">
                <label><input type="radio" name="q8" data-correct="1"> class final + méthode final</label>
                <label><input type="radio" name="q8"> override + virtual</label>
                <label><input type="radio" name="q8"> private + static</label>
                <div class="explain"><code class="inline">final</code> bloque l’extension et/ou la redéfinition.</div>
              </div>
            </div>

            <div class="q" data-q="q9">
              <div class="qhd">9) Que faut-il pour déclencher un dispatch dynamique ?</div>
              <div class="qbd">
                <label><input type="radio" name="q9"> Méthode non-const</label>
                <label><input type="radio" name="q9" data-correct="1"> Appel via une référence/pointeur de base sur une méthode <em>virtuelle</em></label>
                <label><input type="radio" name="q9"> Un template</label>
                <div class="explain">Sans <em>virtual</em> → liaison statique.</div>
              </div>
            </div>

            <div class="q" data-q="q10">
              <div class="qhd">10) Pourquoi un destructeur virtuel <em>= default</em> est-il souvent suffisant ?</div>
              <div class="qbd">
                <label><input type="radio" name="q10" data-correct="1"> Il créé l’entrée vtable et respecte RAII sans code inutile</label>
                <label><input type="radio" name="q10"> Il empêche la copie</label>
                <label><input type="radio" name="q10"> Il supprime la vtable</label>
                <div class="explain">On veut juste la virtualité, pas de logique custom.</div>
              </div>
            </div>

            <div class="q" data-q="q11">
              <div class="qhd">11) Quelle signature est correcte pour redéfinir <code class="inline">double area() const</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q11"> double area()</label>
                <label><input type="radio" name="q11" data-correct="1"> double area() const override</label>
                <label><input type="radio" name="q11"> int area() const override</label>
                <div class="explain">Type de retour et <code class="inline">const</code> doivent correspondre.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./10.php">← Leçon précédente</a>
              <a href="../parcours.php">Retour aux parcours →</a>
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

    // ===== Mini-jeu : OOP Lab =====
    (function(){
      const sel = document.getElementById('selDerived');
      const virtDtor = document.getElementById('virtDtor');
      const log = document.getElementById('log');
      const btnNew = document.getElementById('btnNew');
      const btnName = document.getElementById('btnName');
      const btnArea = document.getElementById('btnArea');
      const btnDelete = document.getElementById('btnDelete');

      const m1 = document.getElementById('m1');
      const m2 = document.getElementById('m2');
      const m3 = document.getElementById('m3');

      let instance = null; // { type: "Rect"|"Circle", alive: bool }

      function writeln(s){ log.innerHTML += s + "<br>"; log.scrollTop = log.scrollHeight; }
      function resetLog(){ log.innerHTML = ""; }

      btnNew.addEventListener('click', ()=>{
        const t = sel.value;
        instance = { type:t, alive:true };
        resetLog();
        writeln(`<span style="color:#7cffc0">b = new ${t}(...);</span>`);
      });

      btnName.addEventListener('click', ()=>{
        if (!instance || !instance.alive) return writeln("<em>b est nul</em>");
        const name = instance.type === "Rect" ? "Rect" : "Circle";
        writeln(`b->name()  // "${name}"`);
        m1.classList.add('done'); m1.textContent = '✅ Mission 1 — Appel virtuel réussi';
      });

      btnArea.addEventListener('click', ()=>{
        if (!instance || !instance.alive) return writeln("<em>b est nul</em>");
        const area = instance.type === "Rect" ? (3*4) : (Math.PI*2*2);
        writeln(`b->area()  // ${area.toFixed(2)}`);
        m1.classList.add('done'); m1.textContent = '✅ Mission 1 — Appel virtuel réussi';
      });

      btnDelete.addEventListener('click', ()=>{
        if (!instance || !instance.alive) return writeln("<em>déjà supprimé</em>");
        if (virtDtor.checked){
          writeln(`delete b  // ~${instance.type}() -> ~Shape()`);
        } else {
          writeln(`delete b  // ~Shape() (⚠️ destructeur non virtuel : partie ${instance.type} ignorée)`);
        }
        instance.alive = false;
        m2.classList.add('done'); m2.textContent = '✅ Mission 2 — Destruction observée';
      });

      // Mission 3 : recréer avec autre type après delete
      sel.addEventListener('change', ()=>{
        if (!instance || !instance.alive){
          m3.classList.add('done'); m3.textContent = '✅ Mission 3 — Dispatch validé avec un autre dérivé';
        }
      });
    })();
  </script>
</body>
</html>
