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
  <title>DevSecOps | FunCodeLab</title>
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#9ae6ff;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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

  /* Mini-jeu : Pipeline Hardening Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .row input,.row select,.row button{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:.15rem .6rem;font-size:.8rem;color:#cfe1ff}
  .ok{background:linear-gradient(90deg,#7cffc0,#4df1aa);color:#06121f;border-color:#164238}
  .warnP{background:linear-gradient(90deg,#ffef9f,#fbbf24);color:#2a1a06;border-color:#6a4c0b}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; DevSecOps &nbsp;›&nbsp; <span style="color:var(--accent)">CI/CD, SAST/DAST, secrets</span></div>
      <span class="badge">Leçon 4 / 8</span>
    </div>

    <div class="title">
      <h1>DevSecOps : sécuriser ton pipeline</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectif : intégrer la sécurité <strong>dans</strong> le cycle de livraison. Concrètement :
            <strong>contrôles automatiques</strong> (SAST/DAST/SCA, scans secrets, IaC/containers),
            <strong>garde-fous</strong> (branches protégées, revues, statuts requis), et <strong>chaîne d’approvisionnement</strong> (SBOM, signature des artefacts, jetons CI à privilèges minimaux).
          </p>

          <h2>1) Contrôles automatiques</h2>
          <ul>
            <li><strong>SAST</strong> : analyse statique du code avant build (fail le job si critique).</li>
            <li><strong>DAST</strong> : tests dynamiques contre l’app déployée en pré-prod.</li>
            <li><strong>SCA</strong> : audit des dépendances (CVE), pinning, MAJ régulères.</li>
            <li><strong>Secrets</strong> : détection pré-commit + pipeline (bloquer les fuites).</li>
            <li><strong>IaC & containers</strong> : scanner Terraform/K8s et images (base durcie).</li>
          </ul>

          <h2>2) Garde-fous sur le dépôt</h2>
          <ul>
            <li><strong>Branches protégées</strong> : revues requises + statuts CI obligatoires.</li>
            <li><strong>Jetons CI</strong> : <em>scopés</em>, à durée de vie courte, droits minimum.</li>
            <li><strong>Environnements</strong> : dev → staging → prod avec validations manuelles.</li>
          </ul>

          <h2>3) Chaîne d’approvisionnement</h2>
          <ul>
            <li><strong>SBOM</strong> généré à chaque build (CycloneDX/SPDX).</li>
            <li><strong>Signature</strong> des artefacts/images (ex. Sigstore cosign) + provenance.</li>
            <li><strong>Registry</strong> privé avec politique d’admission (images signées uniquement).</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> combo efficace : SAST+SCA à chaque PR, DAST en pré-prod, scans secrets, branches protégées (revue + statuts requis), jetons CI scopés, SBOM + artefacts signés.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Pipeline Hardening Lab</h3>
            <p class="rules">Active les bons réglages. Au chargement, rien n’est validé : configure le pipeline pour faire passer les 5 missions au vert ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages DevSecOps">
                  <div class="row">
                    <label style="font-weight:800;min-width:210px">SAST</label>
                    <select id="sast">
                      <option value="off" selected>Désactivé</option>
                      <option value="on">Activé sur chaque PR</option>
                    </select>
                    <span id="sastTag" class="pill warnP">Inactif</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:210px">DAST</label>
                    <select id="dast">
                      <option value="off" selected>Désactivé</option>
                      <option value="on">Pré-prod (blocking)</option>
                    </select>
                    <span id="dastTag" class="pill warnP">Inactif</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:210px">Dépendances & images</label>
                    <select id="sca">
                      <option value="none" selected>Aucun scan</option>
                      <option value="deps">SCA (dépendances)</option>
                      <option value="full">SCA + scan images</option>
                    </select>
                    <span id="scaTag" class="pill warnP">Non scanné</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:210px">Scans de secrets</label>
                    <input type="checkbox" id="secrets">
                    <span id="secretsTag" class="pill warnP">Désactivés</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:210px">Protection branche principale</label>
                    <select id="branch">
                      <option value="none" selected>Aucune</option>
                      <option value="reviews">Revues + statuts CI requis</option>
                      <option value="tight">Revues + statuts + jetons CI scopés</option>
                    </select>
                    <span id="branchTag" class="pill warnP">Faible</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:210px">SBOM & signature</label>
                    <select id="supply">
                      <option value="none" selected>Aucune</option>
                      <option value="sbom">SBOM généré</option>
                      <option value="signed">SBOM + artefacts signés</option>
                    </select>
                    <span id="supplyTag" class="pill warnP">Non tracé</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Activer SAST et DAST dans la CI</div>
                    <div class="check" id="m2">⬜ Mission 2 — Bloquer les fuites avec un scan de secrets</div>
                    <div class="check" id="m3">⬜ Mission 3 — Protéger la branche principale (revues + statuts + jetons scopés)</div>
                    <div class="check" id="m4">⬜ Mission 4 — Générer un SBOM et signer les artefacts</div>
                    <div class="check" id="m5">⬜ Mission 5 — Scanner dépendances et images</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> SAST=<em>on</em>, DAST=<em>on</em>, Secrets <em>coché</em>, Branche=<em>tight</em>, Supply=<em>signed</em>, Dépendances & images=<em>full</em>.
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
              <div class="qhd">1) SAST vs DAST : quelle différence principale ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> SAST analyse le code, DAST teste l’app en exécution</label>
                <label><input type="radio" name="q1"> SAST teste le réseau, DAST lit les logs</label>
                <label><input type="radio" name="q1"> Aucune différence</label>
                <div class="explain">SAST = statique côté code; DAST = dynamique sur l’application déployée.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Comment réduire les fuites d’API keys dans le dépôt ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Scanner les secrets en pré-commit et dans la CI</label>
                <label><input type="radio" name="q2"> Attendre la mise en prod</label>
                <label><input type="radio" name="q2"> Les renommer en <em>password123</em></label>
                <div class="explain">Un scan automatique empêche l’introduction et déclenche la rotation.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quelle protection minimale sur <code class="inline">main</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Revues obligatoires + statuts CI requis</label>
                <label><input type="radio" name="q3"> Push direct pour aller plus vite</label>
                <label><input type="radio" name="q3"> Un GIF motivant</label>
                <div class="explain">Empêcher le merge si tests/scans échouent et exiger une revue.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) À quoi sert un SBOM ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Inventorier les dépendances pour gérer les vulnérabilités</label>
                <label><input type="radio" name="q4"> Compresser l’application</label>
                <label><input type="radio" name="q4"> Remplacer la documentation</label>
                <div class="explain">Le SBOM facilite le suivi des composants et l’évaluation d’impact (CVE).</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Quel mécanisme réduit le risque d’altération des releases ?</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Signature des artefacts (ex. Sigstore) + provenance</label>
                <label><input type="radio" name="q5"> Envoyer par email le binaire</label>
                <label><input type="radio" name="q5"> Ajouter “FINAL” au nom du fichier</label>
                <div class="explain">La signature vérifie l’origine et l’intégrité; la provenance décrit la build.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./3.php">← Leçon précédente</a>
              <a href="./6.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Pipeline Hardening Lab =====
    (function(){
      const sast = document.getElementById('sast');
      const dast = document.getElementById('dast');
      const sca = document.getElementById('sca');
      const secrets = document.getElementById('secrets');
      const branch = document.getElementById('branch');
      const supply = document.getElementById('supply');

      const tags = {
        sast: document.getElementById('sastTag'),
        dast: document.getElementById('dastTag'),
        sca: document.getElementById('scaTag'),
        secrets: document.getElementById('secretsTag'),
        branch: document.getElementById('branchTag'),
        supply: document.getElementById('supplyTag')
      };

      const m1=document.getElementById('m1');
      const m2=document.getElementById('m2');
      const m3=document.getElementById('m3');
      const m4=document.getElementById('m4');
      const m5=document.getElementById('m5');

      // empêche toute validation automatique au chargement
      let touched=false;

      function tag(el, txt, cls){ el.textContent=txt; el.className='pill '+cls; }
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }
      function resetCheck(el, txt){ el.classList.remove('done'); el.textContent='⬜ '+txt; }

      function renderTags(){
        tag(tags.sast,   sast.value==='on'   ? 'SAST actif'      : 'Inactif',    sast.value==='on'   ? 'ok':'warnP');
        tag(tags.dast,   dast.value==='on'   ? 'DAST actif'      : 'Inactif',    dast.value==='on'   ? 'ok':'warnP');
        tag(tags.sca,    sca.value==='full'  ? 'Dépendances + images' : sca.value==='deps' ? 'Dépendances' : 'Non scanné', sca.value!=='none' ? 'ok':'warnP');
        tag(tags.secrets,secrets.checked     ? 'Secrets scannés' : 'Désactivés', secrets.checked ? 'ok':'warnP');
        tag(tags.branch, branch.value==='tight' ? 'Revue+statuts+tokens' : branch.value==='reviews' ? 'Revue+statuts' : 'Faible', branch.value!=='none' ? 'ok':'warnP');
        tag(tags.supply, supply.value==='signed' ? 'SBOM + signé' : supply.value==='sbom' ? 'SBOM' : 'Aucun', supply.value!=='none' ? 'ok':'warnP');
      }

      function renderMissions(){
        if (!touched) return; // pas d’auto-validation

        (sast.value==='on' && dast.value==='on')
          ? done(m1,'Mission 1 — SAST et DAST activés')
          : resetCheck(m1,'Mission 1 — Activer SAST et DAST dans la CI');

        secrets.checked
          ? done(m2,'Mission 2 — Scan de secrets actif')
          : resetCheck(m2,'Mission 2 — Bloquer les fuites avec un scan de secrets');

        branch.value==='tight'
          ? done(m3,'Mission 3 — Branche protégée au maximum')
          : resetCheck(m3,'Mission 3 — Protéger la branche principale (revues + statuts + jetons scopés)');

        supply.value==='signed'
          ? done(m4,'Mission 4 — SBOM généré + artefacts signés')
          : resetCheck(m4,'Mission 4 — Générer un SBOM et signer les artefacts');

        sca.value==='full'
          ? done(m5,'Mission 5 — Dépendances et images scannées')
          : resetCheck(m5,'Mission 5 — Scanner dépendances et images');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }

      [sast,dast,sca,secrets,branch,supply].forEach(el=>el.addEventListener('change', onChange));

      // État initial neutre (tags à jour, missions non cochées)
      renderTags();
    })();
  </script>
</body>
</html>
