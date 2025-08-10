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
  <title>Red Team Operations | FunCodeLab</title>
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
  .title .progress-pill{font-weight:800;color:#1b0a0a;background:linear-gradient(90deg,#ffb4b4,#ff6b6b);padding:.35rem .65rem;border-radius:999px;border:1px solid #5a1d1d}

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

  /* Mini-jeu : Red Team Ops Lab */
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
  .check{display:block;white-space:normal;padding:4px 0;color:var(--muted)} /* 1 mission = 1 ligne */
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🎯 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Red Team &nbsp;›&nbsp; <span style="color:var(--accent)">Red Team Operations</span></div>
      <span class="badge">Leçon 10 / 10</span>
    </div>

    <div class="title">
      <h1>Red Team Operations : intrusion, persistance &amp; évasion</h1>
      <span class="progress-pill">Niveau : Difficile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Les opérations Red Team se font <strong>avec autorisation</strong> et <strong>dans le cadre (ROE)</strong>. L’objectif : tester les défenses <em>de bout en bout</em> tout en respectant la sécurité des systèmes et des personnes. On privilégie des techniques <strong>peu destructives</strong>, <strong>réversibles</strong> et <strong>traçables</strong>, avec un plan de <strong>cleanup</strong> et une gestion stricte des données.</p>

          <h2>Phases (très haut niveau)</h2>
          <ul>
            <li><strong>Initial access</strong> (dans le scope et validé) → <strong>C2</strong> contraint par l’egress.</li>
            <li><strong>Mouvement / élévation</strong> via mauvaises config et identités mal protégées (pas d’exploits destructifs).</li>
            <li><strong>Persistance</strong> légère et réversible, <strong>OPSEC</strong> pour minimiser le bruit.</li>
            <li><strong>Objectifs</strong> simulés (échantillons, données factices), <strong>exfil</strong> chiffrée vers un sink approuvé.</li>
          </ul>

          <div class="callout warn">
            <strong>Règles d’or :</strong> ROE signées, scope clair, preuves minimales & chiffrées, pas de données réelles hors périmètre, <em>cleanup</em> & réversibilité garantis, rapport final orienté défense.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Red Team Ops Lab</h3>
            <p class="rules">Paramètre l’opération (dans le cadre légal) pour valider les 6 missions ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages RTO">
                  <div class="row">
                    <label style="font-weight:800;min-width:240px">ROE &amp; scope</label>
                    <select id="roe">
                      <option value="loose" selected>Vagues / non validés</option>
                      <option value="strict">ROE signées + scope clair</option>
                    </select>
                    <span id="roeTag" class="pill warnP">Cadre insuffisant</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Accès initial</label>
                    <select id="initial">
                      <option value="out" selected>Hors-scope / non approuvé</option>
                      <option value="in">Méthode in-scope &amp; approuvée</option>
                    </select>
                    <span id="initialTag" class="pill warnP">Risque légal</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">C2 / egress</label>
                    <select id="c2">
                      <option value="raw" selected>Direct bruyant vers Internet</option>
                      <option value="redir">HTTPS via redirector + egress contrôlé</option>
                    </select>
                    <span id="c2Tag" class="pill warnP">C2 détectable</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Élévation &amp; mouvement</label>
                    <select id="privesc">
                      <option value="risky" selected>Exploit destructif</option>
                      <option value="misconf">Abus de mauvaises config / identités</option>
                    </select>
                    <span id="privescTag" class="pill warnP">Dangereux</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Persistance</label>
                    <select id="persist">
                      <option value="heavy" selected>Intrusive / difficile à retirer</option>
                      <option value="light">Légère &amp; réversible (utilisateur)</option>
                    </select>
                    <span id="persistTag" class="pill warnP">Non réversible</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Exfiltration &amp; données</label>
                    <select id="exfil">
                      <option value="wild" selected>Serveur inconnu / clair</option>
                      <option value="sink">Chiffrée vers sink approuvé (données factices)</option>
                    </select>
                    <span id="exfilTag" class="pill warnP">Non conforme</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Opérer sous ROE signées et scope défini</div>
                    <div class="check" id="m2">⬜ Mission 2 — Obtenir un accès initial in-scope et approuvé</div>
                    <div class="check" id="m3">⬜ Mission 3 — Établir un C2 discret via redirector</div>
                    <div class="check" id="m4">⬜ Mission 4 — Élever le niveau via mauvaises config (non destructif)</div>
                    <div class="check" id="m5">⬜ Mission 5 — Installer une persistance légère et réversible</div>
                    <div class="check" id="m6">⬜ Mission 6 — Exfiltrer des artefacts factices vers un sink chiffré</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> ROE=<em>strict</em>, Accès=<em>in</em>, C2=<em>redir</em>, Privesc=<em>misconf</em>, Persistance=<em>light</em>, Exfil=<em>sink</em>.
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
              <div class="qhd">1) La première exigence avant toute opération Red Team est…</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Des ROE signées et un scope approuvé</label>
                <label><input type="radio" name="q1"> Un logo cool</label>
                <label><input type="radio" name="q1"> Un VPN public gratuit</label>
                <div class="explain">Le cadre légal et la sécurité priment, sinon on s’arrête.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Pourquoi utiliser un redirector/proxy pour le C2 ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Pour contrôler l’egress, cloisonner l’infra et réduire l’empreinte</label>
                <label><input type="radio" name="q2"> Pour payer plus cher</label>
                <label><input type="radio" name="q2"> Pour casser TLS</label>
                <div class="explain">Le redirector sépare opérateurs et C2 et s’adapte aux politiques réseau.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Quelle approche d’élévation est acceptable en priorité ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Exploiter des mauvaises configurations/identités</label>
                <label><input type="radio" name="q3"> Utiliser un 0-day destructif</label>
                <label><input type="radio" name="q3"> Éteindre l’antivirus</label>
                <div class="explain">On évite les techniques à fort risque opérationnel.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Une bonne persistance Red Team devrait être…</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Discrète, réversible et documentée pour le cleanup</label>
                <label><input type="radio" name="q4"> Permanente et profonde</label>
                <label><input type="radio" name="q4"> Impossible à retirer</label>
                <div class="explain">On minimise l’impact et facilite la restauration.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Pour les données d’objectif en Red Team, on privilégie…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Des échantillons factices, chiffrés et envoyés vers un sink approuvé</label>
                <label><input type="radio" name="q5"> Des archives clients réelles par email perso</label>
                <label><input type="radio" name="q5"> Un upload FTP en clair</label>
                <div class="explain">On protège la confidentialité et respecte la politique de données.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./9.php">← Leçon précédente</a>
              <a href="../parcours.php">Fin du module →</a>
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

    // ===== Mini-jeu : Red Team Ops Lab =====
    (function(){
      const roe = document.getElementById('roe');
      const initial = document.getElementById('initial');
      const c2 = document.getElementById('c2');
      const privesc = document.getElementById('privesc');
      const persist = document.getElementById('persist');
      const exfil = document.getElementById('exfil');

      const tags = {
        roe: document.getElementById('roeTag'),
        initial: document.getElementById('initialTag'),
        c2: document.getElementById('c2Tag'),
        privesc: document.getElementById('privescTag'),
        persist: document.getElementById('persistTag'),
        exfil: document.getElementById('exfilTag'),
      };

      const m1=document.getElementById('m1');
      const m2=document.getElementById('m2');
      const m3=document.getElementById('m3');
      const m4=document.getElementById('m4');
      const m5=document.getElementById('m5');
      const m6=document.getElementById('m6');

      let touched=false;

      function tag(el, txt, cls){ el.textContent=txt; el.className='pill '+cls; }
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }
      function resetCheck(el, txt){ el.classList.remove('done'); el.textContent='⬜ '+txt; }

      function renderTags(){
        tag(tags.roe, roe.value==='strict' ? 'ROE & scope validés' : 'Cadre insuffisant', roe.value==='strict' ? 'ok' : 'warnP');
        tag(tags.initial, initial.value==='in' ? 'Accès in-scope' : 'Hors-scope', initial.value==='in' ? 'ok' : 'warnP');
        tag(tags.c2, c2.value==='redir' ? 'C2 via redirector' : 'Direct bruyant', c2.value==='redir' ? 'ok' : 'warnP');
        tag(tags.privesc, privesc.value==='misconf' ? 'Non destructif' : 'Risque élevé', privesc.value==='misconf' ? 'ok' : 'warnP');
        tag(tags.persist, persist.value==='light' ? 'Réversible' : 'Persistant lourd', persist.value==='light' ? 'ok' : 'warnP');
        tag(tags.exfil, exfil.value==='sink' ? 'Sink chiffré, données factices' : 'Non conforme', exfil.value==='sink' ? 'ok' : 'warnP');
      }

      function renderMissions(){
        if (!touched) return;

        (roe.value==='strict')
          ? done(m1,'Mission 1 — ROE & scope validés')
          : resetCheck(m1,'Mission 1 — Opérer sous ROE signées et scope défini');

        (initial.value==='in')
          ? done(m2,'Mission 2 — Accès initial in-scope')
          : resetCheck(m2,'Mission 2 — Obtenir un accès initial in-scope et approuvé');

        (c2.value==='redir')
          ? done(m3,'Mission 3 — C2 discret opérationnel')
          : resetCheck(m3,'Mission 3 — Établir un C2 discret via redirector');

        (privesc.value==='misconf')
          ? done(m4,'Mission 4 — Élévation non destructive')
          : resetCheck(m4,'Mission 4 — Élever le niveau via mauvaises config (non destructif)');

        (persist.value==='light')
          ? done(m5,'Mission 5 — Persistance réversible')
          : resetCheck(m5,'Mission 5 — Installer une persistance légère et réversible');

        (exfil.value==='sink')
          ? done(m6,'Mission 6 — Exfil conforme (sink chiffré)')
          : resetCheck(m6,'Mission 6 — Exfiltrer des artefacts factices vers un sink chiffré');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }
      [roe,initial,c2,privesc,persist,exfil].forEach(el=>el.addEventListener('change', onChange));
      renderTags(); // état initial neutre
    })();
  </script>
</body>
</html>
