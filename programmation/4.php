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
  <title>JavaScript — DOM & événements | FunCodeLab</title>
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
button{appearance:none;border:1px solid var(--border);background:linear-gradient(180deg,#1a244a,#111937);color:var(--text);font-weight:800;border-radius:10px;padding:.6rem 1rem;cursor:pointer}
button:hover{border-color:#2b3c75}
.btn-accent{background:linear-gradient(90deg,var(--accent),#88b6ff);color:#fff;border:0}  /* texte blanc */
.btn-ghost{background:transparent;color:#fff}                                            /* texte blanc */
.score{margin-top:14px;font-weight:800;color:#071421;background:linear-gradient(90deg,#7cffc0,#4df1aa);display:inline-block;padding:.45rem .8rem;border-radius:999px;border:1px solid #164238}

.footer-nav{display:flex;justify-content:space-between;gap:12px;margin-top:16px}
.footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:var(--accent)}

/* Mini-jeu DOM lab (intégré dans la leçon) */
.mini{margin-top:22px}
.mini .rules{color:var(--muted);margin:6px 0 10px}
.mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
.lab{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
@media(max-width:900px){.lab{grid-template-columns:1fr}}
.sandbox{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
.sandbox .cardx{border:1px solid var(--border);border-radius:12px;padding:12px;background:#0f1a3b;transition:.2s ease}
.sandbox .cardx.highlight{outline:2px solid var(--accent); box-shadow:0 0 0 4px rgba(90,161,255,.18)}
.sandbox ul{padding-left:18px}
.sandbox li{margin:4px 0}
.tools{display:flex;flex-direction:column;gap:10px}
.tools input, .tools button{background:#0e1534;border:1px solid var(--border);color:var(--text);padding:.55rem .7rem;border-radius:10px}
/* texte blanc pour le bouton "Appliquer" dans la zone outils */
.tools button.btn-accent{color:#fff}
.checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
/* Missions : police un peu plus petite et ligne plus compacte */
.check {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.92rem;
  line-height: 1.4;
}

.check.done{color:var(--good);font-weight:800}
.check .keyword {
  font-weight: 600;
  color: var(--accent);
}
.check code.inline {
  background: none;
  border: none;
  padding: 0;
  border-radius: 0;
  color: inherit;
  font-family: inherit;
  font-size: inherit;
}

  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">💻 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Programmation &nbsp;›&nbsp; <span style="color:var(--accent)">JavaScript — DOM & événements</span></div>
      <span class="badge">Leçon 4 / 11</span>
    </div>

    <div class="title">
      <h1>JavaScript — DOM & événements</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS (gauche) -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Découvrons comment <strong>sélectionner</strong> des éléments, <strong>écouter</strong> des événements et <strong>manipuler</strong> le DOM.</p>

          <h2>Sélectionner</h2>
<pre><code>// Sélections modernes
const title = document.querySelector('#title');     // premier match
const items = document.querySelectorAll('.item');   // NodeList (itérable)

// Classiques
document.getElementById('app');
document.getElementsByClassName('item');  // HTMLCollection</code></pre>

          <h2>Écouter des événements</h2>
<pre><code>const btn = document.querySelector('#btn');
btn.addEventListener('click', (e) => {
  console.log('clic !', e.target);
});</code></pre>

          <h2>Empêcher un comportement par défaut</h2>
<pre><code>form.addEventListener('submit', (e) => {
  e.preventDefault(); // pas d'envoi / rechargement
});</code></pre>

          <h2>Délégation d’événements</h2>
<pre><code>// Un seul écouteur sur le parent
list.addEventListener('click', (e) => {
  if (e.target.matches('li')) {
    e.target.classList.toggle('selected');
  }
});</code></pre>

          <h2>Modifier le contenu & classes</h2>
<pre><code>title.textContent = 'Nouveau titre'; // texte brut (sécurisé)
box.innerHTML = '<strong>HTML</strong>'; // insère du HTML
box.classList.add('highlight');
box.classList.toggle('open');</code></pre>

          <div class="callout warn">
            <strong>Attention :</strong> <code class="inline">innerHTML</code> insère du HTML interprété. Préfère <code class="inline">textContent</code> pour du texte utilisateur (sécurité XSS).
          </div>

          <!-- MINI-JEU intégré sous la leçon -->
          <div class="mini">
            <h3>Mini-jeu : DOM Lab</h3>
            <p class="rules">
              <strong>Objectif :</strong> accomplis les 3 missions en manipulant le DOM (clics & formulaire).
              Les coches passent au vert quand c’est réussi ✅
            </p>
            <div class="play">
              <div class="lab">
                <!-- Aire de jeu -->
                <div class="sandbox" id="sandbox">
                  <div class="cardx" id="box">
                    <h3 id="labTitle">Titre de la carte</h3>
                    <p id="labText">Clique la carte pour <em>basculer</em> la classe <code class="inline">highlight</code>.</p>
                    <ul id="labList">
                      <li class="item">Apprendre querySelector</li>
                      <li class="item">Écouter des événements</li>
                    </ul>
                  </div>

                  <form id="labForm" style="margin-top:12px">
                    <label for="newItem" style="display:block;font-weight:800;margin-bottom:6px">Ajouter un item</label>
                    <input id="newItem" type="text" placeholder="Ex : Manipuler classList" required />
                    <button class="btn-accent" style="margin-left:6px">Ajouter</button>
                    <small style="color:var(--muted);display:block;margin-top:6px">
                      Astuce : le formulaire n’actualise pas la page (on utilise <code class="inline">preventDefault()</code>).
                    </small>
                  </form>
                </div>

                <!-- Outils & missions -->
                <div class="tools">
                  <div>
                    <label style="font-weight:800;display:block;margin-bottom:6px">Changer le titre</label>
                    <input id="setTitleInput" type="text" placeholder="Nouveau titre…" />
                    <button id="applyTitle" class="btn-accent" style="margin-top:6px">Appliquer</button>
                  </div>

                  <div class="checks">
                 <div class="check" id="c1">⬜ Mission 1 — Toggle la classe highlight en cliquant la carte</div>
                    <div class="check" id="c2">⬜ Mission 2 — Changer le titre via le champ ci-dessus</div>
                    <div class="check" id="c3">⬜ Mission 3 — Ajouter un item dans la liste via le formulaire</div>
                  </div>
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
              <div class="qhd">1) Quelle méthode renvoie le <em>premier</em> élément correspondant à un sélecteur CSS ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> document.querySelector()</label>
                <label><input type="radio" name="q1"> document.querySelectorAll()</label>
                <label><input type="radio" name="q1"> document.getElementsByTagName()</label>
                <div class="explain">querySelector renvoie le premier match ; querySelectorAll renvoie une NodeList de tous les matchs.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Comment ajouter un écouteur de clic moderne ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> element.onclick = handler</label>
                <label><input type="radio" name="q2" data-correct="1"> element.addEventListener('click', handler)</label>
                <label><input type="radio" name="q2"> document.on('click', handler)</label>
                <div class="explain">addEventListener est recommandé (plusieurs handlers possibles, options capture, etc.).</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) À quoi sert <code class="inline">event.preventDefault()</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> À empêcher l’action par défaut du navigateur</label>
                <label><input type="radio" name="q3"> À stopper la propagation</label>
                <label><input type="radio" name="q3"> À supprimer l’élément</label>
                <div class="explain">Ex : empêcher qu’un formulaire se soumette ou qu’un lien suive son href.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) La délégation d’événements consiste à…</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Attacher un handler sur chaque enfant</label>
                <label><input type="radio" name="q4" data-correct="1"> Attacher un handler sur le parent et tester <code class="inline">event.target</code></label>
                <label><input type="radio" name="q4"> Utiliser uniquement <code class="inline">onclick</code></label>
                <div class="explain">On écoute un parent et on réagit selon l’élément cliqué (utile pour les listes dynamiques).</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle propriété ajoute/retire une classe facilement ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> element.style</label>
                <label><input type="radio" name="q5" data-correct="1"> element.classList.toggle('x')</label>
                <label><input type="radio" name="q5"> element.addClass('x')</label>
                <div class="explain"><code class="inline">classList</code> fournit <code class="inline">add</code>, <code class="inline">remove</code>, <code class="inline">toggle</code>, <code class="inline">contains</code>.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Quelle option est la plus sûre pour afficher du texte utilisateur ?</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> textContent</label>
                <label><input type="radio" name="q6"> innerHTML</label>
                <label><input type="radio" name="q6"> outerHTML</label>
                <div class="explain"><code class="inline">textContent</code> échappe automatiquement le HTML (évite l’exécution).</div>
              </div>
            </div>

            <div class="q" data-q="q7">
              <div class="qhd">7) Comment stopper la propagation d’un événement ?</div>
              <div class="qbd">
                <label><input type="radio" name="q7" data-correct="1"> event.stopPropagation()</label>
                <label><input type="radio" name="q7"> event.preventDefault()</label>
                <label><input type="radio" name="q7"> event.stopDefault()</label>
                <div class="explain">stopPropagation empêche l’événement de remonter (bubbling) vers les parents.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>
          </form>

          <div class="footer-nav">
            <a href="./3.php">← Leçon précédente</a>
            <a href="./5.php">Leçon suivante →</a>
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

    // ===== Mini-jeu DOM Lab =====
    (function(){
      const box = document.getElementById('box');
      const labTitle = document.getElementById('labTitle');
      const labList = document.getElementById('labList');
      const labForm = document.getElementById('labForm');
      const newItem = document.getElementById('newItem');

      const setTitleInput = document.getElementById('setTitleInput');
      const applyTitle = document.getElementById('applyTitle');

      const c1 = document.getElementById('c1');
      const c2 = document.getElementById('c2');
      const c3 = document.getElementById('c3');

      // Mission 1 : toggle class en cliquant la carte
      box.addEventListener('click', () => {
        box.classList.toggle('highlight');
        if (box.classList.contains('highlight')) {
          c1.classList.add('done'); c1.textContent = '✅ Mission 1 — Classe "highlight" activée';
        }
      });

      // Mission 2 : changer le titre
      applyTitle.addEventListener('click', () => {
        const txt = setTitleInput.value.trim();
        if (!txt) return;
        labTitle.textContent = txt;       // textContent => safe
        c2.classList.add('done'); c2.textContent = '✅ Mission 2 — Titre changé';
      });

      // Mission 3 : ajouter un item via le formulaire
      labForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const value = newItem.value.trim();
        if (!value) return;
        const li = document.createElement('li');
        li.className = 'item';
        li.textContent = value;
        labList.appendChild(li);
        newItem.value = '';
        c3.classList.add('done'); c3.textContent = '✅ Mission 3 — Item ajouté';
      });

      // Délégation : cliquer un <li> le sélectionne
      labList.addEventListener('click', (e) => {
        if (e.target.matches('li')) {
          e.target.classList.toggle('selected');
        }
      });
    })();
  </script>
</body>
</html>
