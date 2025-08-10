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
  <title>Mots de passe & manager | FunCodeLab</title>
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
  .q label{
    display:flex;
    gap:10px;
    align-items:flex-start;
    line-height:1.4;
  }
  .q label input{
    margin-top:4px; /* centre le rond sur la première ligne */
    flex-shrink:0;
  }
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

  /* Mini-jeu Password Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}

  .pw-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.pw-grid{grid-template-columns:1fr}}

  .pw-box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .pw-row{display:flex;gap:8px;align-items:center}
  .pw-row input[type="password"], .pw-row input[type="text"]{flex:1;background:#0e1534;border:1px solid var(--border);color:#fff;padding:.6rem .7rem;border-radius:10px}
  .pw-meter{height:10px;border-radius:999px;background:#1a2247;overflow:hidden;border:1px solid var(--border);margin-top:10px}
  .pw-meter-bar{height:100%;width:0%}
  .pw-meter.weak  .pw-meter-bar{background:#ff6b6b}
  .pw-meter.fair  .pw-meter-bar{background:#fbbf24}
  .pw-meter.good  .pw-meter-bar{background:#4df1aa}
  .pw-meter.strong .pw-meter-bar{background:#10d49b}

  .tips{margin-top:10px;color:var(--muted)}
  .tips li{margin:4px 0}
  .gen{display:flex;gap:8px;margin-top:10px}
  .gen input{width:140px;background:#0e1534;border:1px solid var(--border);color:#fff;padding:.5rem .6rem;border-radius:10px}
  .good-badge{display:inline-block;font-weight:800;color:#06121f;background:linear-gradient(90deg,#7cffc0,#4df1aa);padding:.15rem .5rem;border-radius:999px;border:1px solid #164238;margin-left:6px}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Mots de passe & manager</span></div>
      <span class="badge">Leçon 3 / 8</span>
    </div>

    <div class="title">
      <h1>Mots de passe & manager</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectif : savoir créer un <strong>mot de passe solide</strong> et utiliser un <strong>manager de mots de passe</strong> (coffre-fort) pour ne rien oublier.</p>

          <h2>1) La méthode « passphrase »</h2>
          <ul>
            <li>Assemble 4–5 mots faciles à retenir mais <em>sans lien</em> : <code class="inline">cactus|ballon|neige|piano</code></li>
            <li>Ajoute 1–2 chiffres et un symbole : <code class="inline">cactusBallon-neige9!Piano</code></li>
            <li>Évite : dates, prénoms, « azerty123 », « password »…</li>
          </ul>

          <h2>2) Un mot de passe par site</h2>
          <ul>
            <li>Si un site fuit, les autres restent protégés.</li>
            <li>C’est le rôle du <strong>manager</strong> (1 coffre-fort + remplissage automatique).</li>
          </ul>

          <h2>3) Le manager (coffre-fort)</h2>
          <ul>
            <li>Un <strong>mot de passe maître</strong> fort (passphrase) ouvre le coffre.</li>
            <li>Active la <strong>double authentification (2FA)</strong> pour ton compte du coffre.</li>
            <li>Sauvegarde la <strong>clé de secours</strong>/codes de récupération en lieu sûr.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce sécurité :</strong> si un email parle de « connexion suspecte », ne clique pas : va directement sur le site officiel et change ton mot de passe là-bas.
          </div>

          <!-- MINI-JEU : Password Lab -->
          <div class="mini">
            <h3>Mini-jeu : Teste ton mot de passe</h3>
            <p class="rules">Tape un mot de passe (ou une passphrase). Regarde la jauge et les conseils. Clique « Suggérer une passphrase » pour générer une idée.</p>

            <div class="play">
              <div class="pw-grid">
                <!-- Zone test -->
                <div class="pw-box" aria-label="Testeur de mot de passe">
                  <div class="pw-row">
                    <input id="pw" type="password" placeholder="Tape ton mot de passe…">
                    <button id="togglePw" class="btn-ghost" type="button">Afficher</button>
                  </div>
                  <div id="meter" class="pw-meter" aria-hidden="false" aria-label="Force du mot de passe">
                    <div id="bar" class="pw-meter-bar"></div>
                  </div>
                  <p id="label" style="margin:8px 0 0;font-weight:800">Force : —</p>

                  <ul id="advice" class="tips">
                    <li>Utilise au moins <strong>14 caractères</strong>.</li>
                    <li>Mélange <strong>minuscules/MAJUSCULES</strong>, <strong>chiffres</strong> et <strong>symboles</strong>.</li>
                    <li>Évite les mots courants (password, azerty, 123456…)</li>
                  </ul>
                </div>

                <!-- Générateur -->
                <div class="pw-box" aria-label="Générateur de passphrase">
                  <strong>Suggérer une passphrase</strong>
                  <div class="gen">
                    <input id="wordsCount" type="number" min="3" max="6" value="4" aria-label="Nombre de mots">
                    <button id="btnSuggest" class="btn-accent" type="button">Suggérer</button>
                    <button id="btnUse" class="btn-ghost" type="button">Utiliser</button>
                  </div>
                  <p id="suggest" style="margin-top:10px;color:#dfe6ff"></p>
                  <small class="tips">Astuce : ajoute un symbole ou un chiffre personnel pour te l’approprier <span class="good-badge">+fort</span></small>
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
              <div class="qhd">1) Pourquoi utiliser un manager de mots de passe ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Pour avoir un mot de passe différent sur chaque site</label>
                <label><input type="radio" name="q1"> Pour réutiliser le même mot de passe partout</label>
                <label><input type="radio" name="q1"> Pour que les pirates aient plus facile</label>
                <div class="explain">Un coffre-fort permet d’avoir un mot de passe unique par service.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Qu’est-ce qu’une « passphrase » ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Un mot très court</label>
                <label><input type="radio" name="q2" data-correct="1"> Une phrase/montage de plusieurs mots faciles à retenir</label>
                <label><input type="radio" name="q2"> Un code PIN de 4 chiffres</label>
                <div class="explain">Une passphrase est longue et mémorisable ⇒ plus résistante.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Lequel est le plus sûr ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> azerty123</label>
                <label><input type="radio" name="q3"> 01012010</label>
                <label><input type="radio" name="q3" data-correct="1"> cactusBallon-neige9!Piano</label>
                <div class="explain">Longueur + diversité des caractères + mots sans lien.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Quoi activer sur ton compte de coffre-fort ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4"> Rien, ça suffit</label>
                <label><input type="radio" name="q4" data-correct="1"> La double authentification (2FA)</label>
                <label><input type="radio" name="q4"> Le partage public des mots de passe</label>
                <div class="explain">La 2FA ajoute une seconde barrière en cas de fuite.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) On te demande un changement urgent via un lien d’email : que faire ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5"> Cliquer sans réfléchir</label>
                <label><input type="radio" name="q5" data-correct="1"> Ouvrir le site officiel soi-même et vérifier depuis là</label>
                <label><input type="radio" name="q5"> Donner le mot de passe par téléphone</label>
                <div class="explain">Ça évite le phishing (faux liens).</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./2.php">← Leçon précédente</a>
              <a href="./4.php">Leçon suivante →</a>
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

    // ===== Password Lab =====
    (function(){
      const pw   = document.getElementById('pw');
      const meter= document.getElementById('meter');
      const bar  = document.getElementById('bar');
      const label= document.getElementById('label');
      const advice= document.getElementById('advice');
      const toggle = document.getElementById('togglePw');

      const wordsCount = document.getElementById('wordsCount');
      const btnSuggest = document.getElementById('btnSuggest');
      const btnUse = document.getElementById('btnUse');
      const suggest = document.getElementById('suggest');

      const common = ['password','azerty','qwerty','123456','letmein','admin','welcome','monpass','motdepasse','abc123','iloveyou'];

      const wordlist = ['cactus','piano','neige','ballon','galaxie','lama','pastèque','nuage','puzzle','vortex',
                        'lune','banquise','café','koala','biscuit','moka','sonate','mangue','citron','pixel',
                        'tortue','mistral','volcan','ivoire','joueur','panda','bambou','grenade','saphir','piment'];

      function scorePassword(s){
        if (!s) return {score:0,label:'—',cls:''};

        let score = 0, tips = [];

        // Longueur
        if (s.length >= 16) score += 4;
        else if (s.length >= 12) score += 3;
        else if (s.length >= 10) score += 2;
        else if (s.length >= 8) score += 1;
        else tips.push('Augmente la longueur (14+).');

        // Variété
        const hasLower = /[a-z]/.test(s);
        const hasUpper = /[A-Z]/.test(s);
        const hasDigit = /[0-9]/.test(s);
        const hasSym   = /[^A-Za-z0-9]/.test(s);
        [hasLower,hasUpper,hasDigit,hasSym].forEach(x=>{ if(x) score+=1; });
        if (!(hasLower && hasUpper)) tips.push('Mélange MAJ/min.');
        if (!hasDigit) tips.push('Ajoute un chiffre.');
        if (!hasSym)   tips.push('Ajoute un symbole.');

        // Répétitions & séquences simples
        if (/(.)\1{2,}/.test(s)) { score -= 1; tips.push('Évite les répétitions (aaa, 111).'); }
        if (/abcdefghijklmnopqrstuvwxyz|qwertyuiop|azertyuiop|0123456789/i.test(s)) { score -= 1; tips.push('Évite les suites clavier.'); }

        // Mots communs
        const low = s.toLowerCase();
        if (common.some(w=>low.includes(w))) { score = Math.max(0, score-2); tips.push('Évite les mots trop connus.'); }

        // Plafond & label
        score = Math.max(0, Math.min(10, score));
        let pct = (score/10)*100;
        let labelTxt = 'Faible', cls='weak';
        if (score >= 8){ labelTxt='Excellent'; cls='strong'; }
        else if (score >= 6){ labelTxt='Bon'; cls='good'; }
        else if (score >= 4){ labelTxt='Correct'; cls='fair'; }

        return {score, pct, label:labelTxt, cls, tips};
      }

      function render(){
        const {pct,label:lbl,cls,tips} = scorePassword(pw.value);
        bar.style.width = (pct||0)+'%';
        meter.className = `pw-meter ${cls||''}`;
        label.textContent = 'Force : ' + lbl;
        advice.innerHTML = (tips && tips.length)
          ? tips.map(t=>`<li>${t}</li>`).join('')
          : `<li>Très bien ! Utilise un coffre-fort pour le mémoriser.</li>`;
      }

      pw.addEventListener('input', render);
      render();

      toggle.addEventListener('click', ()=>{
        pw.type = (pw.type === 'password' ? 'text' : 'password');
        toggle.textContent = (pw.type === 'password' ? 'Afficher' : 'Masquer');
      });

      // Générateur de passphrase
      function randomWord(){ return wordlist[Math.floor(Math.random()*wordlist.length)]; }
      function suggestPassphrase(n=4){
        const words = Array.from({length:n}, ()=>randomWord());
        // petite touche perso
        const pass = words.map((w,i)=> i%2 ? w : w.charAt(0).toUpperCase()+w.slice(1)).join('-') + Math.floor(Math.random()*10);
        return pass;
      }

      btnSuggest.addEventListener('click', ()=>{
        const n = Math.max(3, Math.min(6, parseInt(wordsCount.value||'4',10)));
        suggest.textContent = suggestPassphrase(n);
      });

      btnUse.addEventListener('click', ()=>{
        if (suggest.textContent.trim().length){
          pw.value = suggest.textContent.trim() + '!';
          render();
        }
      });
    })();
  </script>
</body>
</html>
