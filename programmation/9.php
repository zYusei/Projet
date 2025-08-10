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
  <title>Python — Avancé | FunCodeLab</title>
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
  .title h1{font-size:clamp(1.6rem,1.2rem + 2vw,2.2rem);margin:0;font-weight:800;background:linear-gradient(90deg,#ff7ad9,#7aa8ff 70%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .title .progress-pill{font-weight:800;color:#2a091f;background:linear-gradient(90deg,#ff8ab8,#ffa94d);padding:.35rem .65rem;border-radius:999px;border:1px solid #52243b}

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

  /* Mini-jeu: Decorator Forge */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .lab{display:grid;grid-template-columns:1.05fr .95fr;gap:16px}
  @media(max-width:900px){.lab{grid-template-columns:1fr}}
  .tools{display:flex;flex-direction:column;gap:10px}
  .tools .row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .tools input[type="text"], .tools select{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .terminal{background:#0b132b;border:1px solid var(--code-bd);border-radius:10px;min-height:160px;padding:10px 12px;font-family:ui-monospace,Consolas,monospace}
  .terminal .line{white-space:pre-wrap}
  .pill{display:inline-block;background:#0e1534;border:1px solid var(--border);border-radius:999px;padding:.15rem .5rem;color:#cfe1ff;font-weight:700}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">Python — Avancé</span></div>
      <span class="badge">Leçon 9 / 11</span>
    </div>

    <div class="title">
      <h1>Python — Avancé</h1>
      <span class="progress-pill">Niveau : Difficile</span>
    </div>

    <div class="layout">
      <!-- COURS + MINI-JEU (gauche) -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>On plonge dans des briques “pro” de Python : <strong>décorateurs</strong>, <strong>générateurs</strong> & itérateurs, <strong>context managers</strong>, <strong>descripteurs</strong>, et <strong>gestion mémoire</strong> (références, <code class="inline">__slots__</code>, <code class="inline">weakref</code>, GC).</p>

          <h2>Décorateurs (fonctions qui en enveloppent d’autres)</h2>
<pre><code>import functools, time

def timeit(fn):
    @functools.wraps(fn)     # préserve __name__, __doc__, annotations
    def wrapper(*args, **kw):
        t0 = time.perf_counter()
        try:
            return fn(*args, **kw)
        finally:
            dt = (time.perf_counter() - t0)*1000
            print(f"{fn.__name__} took {dt:.2f} ms")
    return wrapper

@timeit
def compute(n:int) -> int:
    return sum(i*i for i in range(n))
</code></pre>
          <ul>
            <li><code class="inline">@decorateur</code> ≡ <code class="inline">func = decorateur(func)</code>.</li>
            <li><code class="inline">functools.wraps</code> évite de perdre le nom/la doc/les annotations (utile pour introspection et outils).</li>
          </ul>

          <h2>Générateurs & itérateurs “paresseux”</h2>
<pre><code>def odds(limit):
    for i in range(limit):
        if i % 2:      # paresseux: produit à la demande
            yield i

# générateur infini + take
def naturals():
    n = 0
    while True:
        yield n; n += 1

from itertools import islice
list(islice(naturals(), 5))   # [0,1,2,3,4]</code></pre>
          <ul>
            <li>Un générateur conserve son <em>état</em> entre deux appels (pile “suspendue”).</li>
            <li><code class="inline">yield from</code> délègue à un sous-itérable.</li>
          </ul>

          <h2>Context managers</h2>
<pre><code>from contextlib import contextmanager

@contextmanager
def timing(label):
    import time; t0=time.perf_counter()
    try:
        yield
    finally:
        print(label, (time.perf_counter()-t0)*1000, "ms")

with timing("job"):
    do_something()</code></pre>
          <ul>
            <li>Gèrent l’acquisition/libération de ressources (<code class="inline">with open(...)</code>, locks, transactions…).</li>
          </ul>

          <h2>Descripteurs (magie d’attributs)</h2>
<pre><code>class Positive:
    def __set_name__(self, owner, name): self.name = name
    def __get__(self, obj, owner): return obj.__dict__.get(self.name, 0)
    def __set__(self, obj, value):
        if value &lt; 0: raise ValueError("negatif")
        obj.__dict__[self.name] = value

class Produit:
    prix = Positive()     # contrôle sur l'attribut
    def __init__(self, p): self.prix = p</code></pre>

          <h2>Mémoire : références, __slots__, weakref, GC</h2>
<pre><code>import weakref, gc

class Node:
    __slots__ = ("val", "next", "__weakref__")   # pas de __dict__ => + léger
    def __init__(self, val): self.val, self.next = val, None

n1 = Node(1); n2 = Node(2); n1.next = n2
wr = weakref.ref(n1)   # référence faible (n'empêche pas le GC)
del n1
assert wr() is None or isinstance(wr(), Node)  # peut être collecté plus tôt

gc.collect()  # force un cycle de GC (utile pour démos/tests)</code></pre>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Decorator Forge <span class="pill">visualiser l’enrobage</span></h3>
            <p class="rules">
              Choisis des options (chronométrage, cache simple, validation) puis clique <strong>Générer & Exécuter</strong>.
              La console montre l’ordre d’appel et le nom préservé via <code class="inline">wraps</code>. (Simulation JS front-only)
            </p>
            <div class="play">
              <div class="lab">
                <div class="tools">
                  <div class="row"><label><input type="checkbox" id="optTime" checked> Ajouter <code class="inline">@timeit</code></label></div>
                  <div class="row"><label><input type="checkbox" id="optCache"> Ajouter <code class="inline">@memoize</code> (clé = args JSON)</label></div>
                  <div class="row"><label><input type="checkbox" id="optValidate"> Ajouter <code class="inline">@ensurePositive</code> (n &gt;= 0)</label></div>
                  <div class="row">
                    <span>Argument n :</span>
                    <input id="argN" type="text" value="20000" style="width:120px">
                    <button id="runForge" class="btn-accent">Générer & Exécuter</button>
                    <button id="clearForge" class="btn-ghost">Effacer</button>
                  </div>
                  <div class="terminal" id="termForge" aria-live="polite"></div>
                </div>
                <div class="sandbox">
<pre><code># Pipeline type (côté Python réel)
@timeit
@memoize
@ensurePositive
def compute(n: int) -> int:
    "Somme des carrés 0..n-1"
    return sum(i*i for i in range(n))</code></pre>
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
              <div class="qhd">1) Le rôle principal de <code class="inline">functools.wraps</code> dans un décorateur est :</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> copier nom/doc/annotations de la fonction décorée</label>
                <label><input type="radio" name="q1"> mesurer le temps</label>
                <label><input type="radio" name="q1"> empêcher l’exception</label>
                <div class="explain">wraps préserve l’identité pour introspection et outils.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Différence clé entre liste et générateur :</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> le générateur produit les valeurs à la demande (paresseux)</label>
                <label><input type="radio" name="q2"> la liste est toujours infinie</label>
                <label><input type="radio" name="q2"> aucune</label>
                <div class="explain">Un générateur n’alloue pas tout en mémoire.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Que fait <code class="inline">yield from it</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> délègue l’itération à <code class="inline">it</code></label>
                <label><input type="radio" name="q3"> tue le générateur</label>
                <label><input type="radio" name="q3"> duplique <code class="inline">it</code></label>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Un context manager s’appuie sur quelle paire de méthodes ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> <code class="inline">__enter__ / __exit__</code></label>
                <label><input type="radio" name="q4"> <code class="inline">__get__ / __set__</code></label>
                <label><input type="radio" name="q4"> <code class="inline">__iter__ / __next__</code></label>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quel énoncé est vrai pour un <em>descripteur</em> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> un objet avec <code class="inline">__get__</code> / <code class="inline">__set__</code> contrôle l’accès à un attribut</label>
                <label><input type="radio" name="q5"> c’est une syntaxe pour les décorateurs</label>
                <label><input type="radio" name="q5"> c’est un alias de dict</label>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Effet direct de <code class="inline">__slots__</code> :</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> pas de <code class="inline">__dict__</code> dynamique → instances plus légères</label>
                <label><input type="radio" name="q6"> exécute plus vite tout code Python</label>
                <label><input type="radio" name="q6"> rend l’objet immuable obligatoirement</label>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) À quoi sert <code class="inline">weakref.ref(x)</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> référence qui n’empêche pas le GC de collecter <code class="inline">x</code></label>
                <label><input type="radio" name="q7"> copie profonde</label>
                <label><input type="radio" name="q7"> verrouillage du pointeur</label>
              </div>
            </div>

            <div class="q" data-q="q8">
              <div class="qhd">8) Quelle est la sortie ? <code class="inline">list(islice((i for i in range(5) if i%2), 1, None))</code></div>
              <div class="qbd">
                <label><input type="radio" name="q8" data-correct="1"> [3]</label>
                <label><input type="radio" name="q8"> [1,3]</label>
                <label><input type="radio" name="q8"> [1]</label>
                <div class="explain">Impairs → 1,3 ; on coupe à partir de l’index 1 → [3].</div>
              </div>
            </div>

            <div class="q" data-q="q9">
              <div class="qhd">9) Quel ordre d’application pour deux décorateurs empilés ?</div>
              <div class="qbd">
                <label><input type="radio" name="q9" data-correct="1"> celui le plus proche de <code class="inline">def</code> s’applique en dernier à l’exécution</label>
                <label><input type="radio" name="q9"> l’ordre est aléatoire</label>
                <label><input type="radio" name="q9"> inverse exacte</label>
                <div class="explain">@A au-dessus de @B ⇒ fn = A(B(fn)).</div>
              </div>
            </div>

            <div class="q" data-q="q10">
              <div class="qhd">10) Quel couple identifie l’API d’un itérateur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q10" data-correct="1"> <code class="inline">__iter__</code> / <code class="inline">__next__</code></label>
                <label><input type="radio" name="q10"> <code class="inline">__len__</code> / <code class="inline">__getitem__</code></label>
                <label><input type="radio" name="q10"> <code class="inline">__enter__</code> / <code class="inline">__exit__</code></label>
              </div>
            </div>

            <div class="q" data-q="q11">
              <div class="qhd">11) Pourquoi <code class="inline">finally</code> dans <code class="inline">timeit</code> est-il important ?</div>
              <div class="qbd">
                <label><input type="radio" name="q11" data-correct="1"> garantit l’affichage du temps même si la fonction lève</label>
                <label><input type="radio" name="q11"> accélère la boucle</label>
                <label><input type="radio" name="q11"> évite <code class="inline">KeyboardInterrupt</code></label>
              </div>
            </div>

            <div class="q" data-q="q12">
              <div class="qhd">12) Quel est l’intérêt de <code class="inline">@contextmanager</code> par rapport à une classe avec <code class="inline">__enter__/__exit__</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q12" data-correct="1"> écrire rapidement un manager basé sur <code class="inline">yield</code></label>
                <label><input type="radio" name="q12"> exécuter en C</label>
                <label><input type="radio" name="q12"> éviter toute exception</label>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./8.php">← Leçon précédente</a>
              <a href="./10.php">Leçon suivante →</a>
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

    // ===== Mini-jeu: Decorator Forge (simulation JS) =====
    (function(){
      const term = document.getElementById('termForge');
      const optTime = document.getElementById('optTime');
      const optCache = document.getElementById('optCache');
      const optValidate = document.getElementById('optValidate');
      const run = document.getElementById('runForge');
      const clear = document.getElementById('clearForge');
      const argN = document.getElementById('argN');

      function println(s){
        const d = document.createElement('div');
        d.className='line'; d.textContent = s;
        term.appendChild(d); term.scrollTop = term.scrollHeight;
      }
      function clearTerm(){ term.innerHTML=''; }

      // Petits "décorateurs" JS pour simuler le comportement
      function wraps(name, fn){ fn.__name__ = name; return fn; }
      function timeit(fn){
        return wraps(fn.__name__||'fn', function(...a){
          const t0 = performance.now();
          try { return fn(...a); }
          finally { println(`${fn.__name__} took ${(performance.now()-t0).toFixed(2)} ms`); }
        });
      }
      const cacheStore = new Map();
      function memoize(fn){
        return wraps(fn.__name__||'fn', function(...a){
          const key = JSON.stringify(a);
          if (cacheStore.has(key)){ println(`memoize: cache hit for ${key}`); return cacheStore.get(key); }
          const r = fn(...a); cacheStore.set(key, r); println(`memoize: store ${key}`); return r;
        });
      }
      function ensurePositive(fn){
        return wraps(fn.__name__||'fn', function(n){
          if (+n < 0) throw new Error("n doit être >= 0");
          return fn(+n);
        });
      }

      function build(){
        // fonction de base simulant compute(n)
        let compute = wraps('compute', function(n){
          // Simule du travail: somme des carrés
          let s=0; for(let i=0;i<n;i++){ s += i*i; }
          return s;
        });
        // Empilage comme en Python: @A au-dessus de @B -> A(B(fn))
        if (optValidate.checked) compute = ensurePositive(compute);
        if (optCache.checked)    compute = memoize(compute);
        if (optTime.checked)     compute = timeit(compute);
        return compute;
      }

      run.addEventListener('click', e=>{
        e.preventDefault();
        clearTerm();
        let n = parseInt(argN.value||'0',10);
        try{
          const fn = build();
          println(`__name__ préservé ? ${fn.__name__||'(anonyme)'}`);
          const r1 = fn(n); println(`Résultat: ${r1}`);
          // rejoue pour voir cache éventuel
          const r2 = fn(n); println(`Deuxième appel: ${r2}`);
        }catch(err){ println('❌ '+err.message); }
      });
      clear.addEventListener('click', e=>{ e.preventDefault(); clearTerm(); });
    })();
  </script>
</body>
</html>
