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
  <title>Hygiène des mots de passe | FunCodeLab</title>
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#9ae6b4;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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
  code.inline{background:rgba(90,161,255,.12);border:1px solid var(--border);padding:.12rem .35rem;border-radius:6px}

  /* Quiz */
  .q{border:1px solid var(--border);background:var(--panel-2);border-radius:12px;margin:14px 0}
  .q .qhd{padding:14px 16px;border-bottom:1px solid var(--border);font-weight:800}
  .q .qbd{padding:16px; display:flex; flex-direction:column; gap:10px}
  .q label{display:flex;gap:10px;align-items:flex-start;line-height:1.4}
  .q label input{margin-top:4px;flex-shrink:0}
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
  .footer-nav a{color:var(--muted);text-decoration:none} .footer-nav a:hover{color:#88b6ff}

  /* Mini-jeu Policy Builder */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .row input,.row select,.row button{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .meter{height:10px;border-radius:999px;background:#1a2247;overflow:hidden;border:1px solid var(--border);margin-top:10px}
  .meter>div{height:100%;width:0%}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:.15rem .6rem;font-size:.8rem;color:#cfe1ff}
  .ok{background:linear-gradient(90deg,#7cffc0,#4df1aa);color:#06121f;border-color:#164238}
  .bad{background:linear-gradient(90deg,#ff9f9f,#ff6b6b);color:#1a0c0c;border-color:#4a1a1a}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🔐 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Cyber &nbsp;›&nbsp; <span style="color:var(--accent)">Hygiène des mots de passe</span></div>
      <span class="badge">Leçon 2 / 8</span>
    </div>

    <div class="title">
      <h1>Hygiène des mots de passe</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>On met en place des règles simples et efficaces : <strong>politiques</strong> (longueur/complexité), <strong>stockage</strong> sécurisé côté serveur, et <strong>MFA</strong>.</p>

          <h2>1) Politique recommandée</h2>
          <ul>
            <li>Longueur d’abord : <strong>≥ 14 caractères</strong> (passphrase conseillée).</li>
            <li>Mélanger types : minuscules/MAJUSCULES, chiffres, symboles—sans imposer des règles absurdes.</li>
            <li>Bloquer les <strong>mots/patterns courants</strong> (azerty, 123456, saison+année, prénom+date).</li>
            <li>Limiter les tentatives (anti-bruteforce) + 2FA pour les comptes sensibles.</li>
          </ul>

          <h2>2) Stockage côté serveur</h2>
<pre><code>&lt;?php
// Hacher un mot de passe (PHP &gt; 7.2)
$hash = password_hash($pwd, PASSWORD_ARGON2ID); // ou PASSWORD_BCRYPT
// Vérifier à la connexion
if (password_verify($pwdSaisi, $hash)) { /* OK */ }
?&gt;</code></pre>
          <ul>
            <li>Utilise <strong>Argon2id</strong> (ou <strong>bcrypt</strong> si indisponible) : <em>jamais</em> de MD5/SHA-1 simple.</li>
            <li>Les fonctions gèrent <strong>sel</strong> & paramètres (coût/mémoire) pour toi.</li>
          </ul>

          <h2>3) MFA (2 facteurs)</h2>
          <ul>
            <li>Application TOTP (Authy/Google Authenticator/Microsoft Authenticator) ou clé FIDO2.</li>
            <li>Codes de secours stockés hors-ligne ; rappeler de regénérer après perte.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> autorise les <em>pastes</em>/gestionnaires au champ mot de passe (UX) mais bloque les <strong>pastes</strong> sur codes OTP si ta menace est locale.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Policy Builder</h3>
            <p class="rules">Configure une politique saine et vérifie un exemple de mot de passe. Valide les 4 missions ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Zone réglages -->
                <div class="box" aria-label="Réglages de politique">
                  <div class="row">
                    <label style="font-weight:800">Longueur minimale</label>
                    <!-- Défauts NE VALIDENT PAS de mission -->
                    <select id="minLen">
                      <option selected>10</option><option>12</option><option>14</option><option>16</option>
                    </select>
                  </div>

                  <div class="row">
                    <label style="font-weight:800">Exiger</label>
                    <label><input type="checkbox" id="reqLower" checked> minuscules</label>
                    <label><input type="checkbox" id="reqUpper"> MAJUSCULES</label>
                    <label><input type="checkbox" id="reqDigit"> chiffres</label>
                    <label><input type="checkbox" id="reqSymbol"> symboles</label>
                  </div>

                  <div class="row">
                    <label style="font-weight:800">Bloquer patterns communs</label>
                    <select id="denyList">
                      <option value="none" selected>aucun</option>
                      <option value="basic">basique (123456, azerty, saison+année)</option>
                      <option value="strict">strict (+ prénoms, clubs, villes)</option>
                    </select>
                  </div>

                  <div class="row">
                    <label style="font-weight:800">Stockage serveur</label>
                    <select id="hashAlgo">
                      <option value="argon2id" selected>Argon2id</option>
                      <option value="bcrypt">bcrypt</option>
                      <option value="md5">MD5 (interdit)</option>
                    </select>
                    <span id="hashTag" class="pill">Argon2id 👍</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800">MFA requis pour admin</label>
                    <input type="checkbox" id="mfa">
                  </div>

                  <hr style="border:none;border-top:1px solid var(--border);margin:10px 0">
                  <div class="row">
                    <input id="candidate" type="text" placeholder="Teste un mot de passe (ex: Cactus-neige9Piano)" style="flex:1">
                    <!-- id unique pour éviter le conflit avec le quiz -->
                    <button id="pwEvaluate" class="btn-accent" type="button">Évaluer</button>
                  </div>
                  <div class="meter" aria-label="Force"><div id="bar"></div></div>
                  <p id="label" style="margin:8px 0 0;font-weight:800">Force : —</p>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Min. 14 caractères + mix de caractères</div>
                    <div class="check" id="c2">⬜ Mission 2 — Bloquer les patterns communs</div>
                    <div class="check" id="c3">⬜ Mission 3 — Choisir un hash sécurisé (Argon2id/bcrypt)</div>
                    <div class="check" id="c4">⬜ Mission 4 — MFA requis pour les admins</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> 14+, minuscules+MAJ+chiffres (+symbole facultatif), “denylist basique/strict”, <em>Argon2id</em>, MFA activé.
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
            <div class="q" data-q="q1">
              <div class="qhd">1) Quel est le facteur le plus important pour la robustesse d’un mot de passe ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> La longueur</label>
                <label><input type="radio" name="q1"> Les caractères spéciaux uniquement</label>
                <label><input type="radio" name="q1"> Le thème (film préféré)</label>
                <div class="explain">La longueur augmente l’espace de recherche bien plus vite.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Quel algorithme de stockage éviter absolument ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2"> Argon2id</label>
                <label><input type="radio" name="q2"> bcrypt</label>
                <label><input type="radio" name="q2" data-correct="1"> MD5 simple</label>
                <div class="explain">MD5/SHA-1 sans sel/itérations est insuffisant.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) La meilleure façon d’empêcher la réutilisation de “azerty2024” ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3"> Laisser faire</label>
                <label><input type="radio" name="q3" data-correct="1"> Mettre une denylist de patterns communs</label>
                <label><input type="radio" name="q3"> Forcer un symbole au début</label>
                <div class="explain">Le blocage de patterns connus réduit les choix trivials.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Pourquoi exiger la MFA pour les comptes admin ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Parce que l’impact d’une compromission est maximal</label>
                <label><input type="radio" name="q4"> Pour rendre l’UX impossible</label>
                <label><input type="radio" name="q4"> Pour éviter de changer son mot de passe</label>
                <div class="explain">La MFA ajoute une barrière forte contre le vol de mot de passe.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quelle politique est la plus “saine” ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> 14+ chars, mix raisonnable, denylist, MFA</label>
                <label><input type="radio" name="q5"> 8 chars, symbole obligatoire en 3e position</label>
                <label><input type="radio" name="q5"> 6 chars si on change chaque semaine</label>
                <div class="explain">On privilégie la longueur, du bon sens et la MFA.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./1.php">← Leçon précédente</a>
              <a href="./3.php">Leçon suivante →</a>
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
        let total=0, ok=0;
        document.querySelectorAll('.q').forEach(q=>{
          total++;
          q.classList.remove('correct','wrong');
          const name=q.dataset.q;
          const checked=form.querySelector(`input[name="${name}"]:checked`);
          const explain=q.querySelector('.explain');
          if (explain) explain.style.display='block';
          if (checked && checked.hasAttribute('data-correct')){
            ok++; q.classList.add('correct');
          } else { q.classList.add('wrong'); }
        });
        scoreEl.style.display='inline-block';
        scoreEl.textContent=`Score : ${ok}/${total} • ${Math.round((ok/total)*100)}%`;
        scoreEl.scrollIntoView({behavior:'smooth',block:'center'});
      }
      function resetQuiz(){
        form.reset();
        document.querySelectorAll('.q').forEach(q=>{
          q.classList.remove('correct','wrong');
          const ex=q.querySelector('.explain'); if(ex) ex.style.display='none';
        });
        scoreEl.style.display='none';
      }
      btnCheck.addEventListener('click', evaluate);
      btnReset.addEventListener('click', resetQuiz);
    })();

    // ===== Mini-jeu : Policy Builder =====
    (function(){
      const minLen=document.getElementById('minLen');
      const reqLower=document.getElementById('reqLower');
      const reqUpper=document.getElementById('reqUpper');
      const reqDigit=document.getElementById('reqDigit');
      const reqSymbol=document.getElementById('reqSymbol');
      const denyList=document.getElementById('denyList');
      const hashAlgo=document.getElementById('hashAlgo');
      const hashTag=document.getElementById('hashTag');
      const mfa=document.getElementById('mfa');
      const cand=document.getElementById('candidate');
      const pwEvaluate=document.getElementById('pwEvaluate'); // id unique
      const bar=document.getElementById('bar');
      const label=document.getElementById('label');

      const c1=document.getElementById('c1');
      const c2=document.getElementById('c2');
      const c3=document.getElementById('c3');
      const c4=document.getElementById('c4');

      function done(el,txt){ el.classList.add('done'); el.textContent='✅ '+txt; }

      function updateHashTag(){
        if (hashAlgo.value==='md5'){ hashTag.textContent='MD5 ❌'; hashTag.className='pill bad'; }
        else if (hashAlgo.value==='bcrypt'){ hashTag.textContent='bcrypt 👍'; hashTag.className='pill ok'; }
        else { hashTag.textContent='Argon2id 👍'; hashTag.className='pill ok'; }
      }
      hashAlgo.addEventListener('change', ()=>{
        updateHashTag();
        if (hashAlgo.value!=='md5') done(c3,'Mission 3 — Hash sécurisé choisi');
      });
      updateHashTag();

      function scorePassword(p){
        let score=0;
        if (!p) return 0;
        const length = p.length;
        if (length>=parseInt(minLen.value,10)) score+=30; else score+=length*1.5;

        const lower=/[a-z]/.test(p), upper=/[A-Z]/.test(p), digit=/\d/.test(p), sym=/[^A-Za-z0-9]/.test(p);
        const kinds = [lower,upper,digit,sym].filter(Boolean).length;
        score+=kinds*10;

        // Bonus diversité
        score+=Math.min(20, new Set(p).size);

        // Malus patterns
        const common = /(password|azerty|qwerty|admin|12345|202[0-9]|printemps|ete|été|hiver|autome|automne)/i;
        if (denyList.value!=='none' && common.test(p)) score-=25;

        return Math.max(0, Math.min(100, score));
      }

      function applyMissions(){
        // M1 : 14+ + mix (au moins 3 catégories cochées)
        const needed = [reqLower.checked, reqUpper.checked, reqDigit.checked, reqSymbol.checked].filter(Boolean).length;
        if (parseInt(minLen.value,10)>=14 && needed>=3) {
          done(c1,'Mission 1 — 14+ & mix de caractères');
        }
        // M2 : denylist activée
        if (denyList.value!=='none') done(c2,'Mission 2 — Patterns communs bloqués');
        // M4 : MFA requis
        if (mfa.checked) done(c4,'Mission 4 — MFA activé pour admin');
      }

      [minLen, reqLower, reqUpper, reqDigit, reqSymbol, denyList, mfa].forEach(el=>{
        el.addEventListener('change', applyMissions);
      });
      // ⚠️ Pas de applyMissions() au chargement → rien n'est vert après refresh

      pwEvaluate.addEventListener('click', ()=>{
        const s = scorePassword(cand.value.trim());
        bar.style.width = s+'%';
        bar.style.background = s<40 ? '#ff6b6b' : s<70 ? '#fbbf24' : '#4df1aa';
        label.textContent = 'Force : ' + (s<40 ? 'faible' : s<70 ? 'moyenne' : 'forte') + ` (${s}%)`;
      });
    })();
  </script>
</body>
</html>
