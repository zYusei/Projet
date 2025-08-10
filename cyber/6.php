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
  <title>Attacking & Defending AWS | FunCodeLab</title>
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

  /* Mini-jeu : AWS Attack / Defense */
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
  .check{display:block;white-space:normal;padding:4px 0;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">☁️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Cloud Security &nbsp;›&nbsp; <span style="color:var(--accent)">Attacking &amp; Defending AWS</span></div>
      <span class="badge">Leçon 6 / 8</span>
    </div>

    <div class="title">
      <h1>Attacking &amp; Defending AWS : IAM, S3 &amp; Attack Paths</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectif : comprendre les chemins d’attaque communs (clés exposées, rôles trop larges, S3 publics) et mettre en place des défenses <strong>simples et bloquantes</strong> : <strong>least privilege</strong> sur IAM, blocage public S3, chiffrement, journalisation/alerting (CloudTrail/GuardDuty), et séparation des comptes avec <strong>SCP</strong>.</p>

          <h2>1) Surfaces d’attaque typiques</h2>
          <ul>
            <li><strong>Clés d’accès</strong> commitées → prise de compte, pivot via <code class="inline">sts:AssumeRole</code>.</li>
            <li><strong>Rôles IAM permissifs</strong> (ex. <code class="inline">iam:PassRole</code> ou <code class="inline">iam:CreateAccessKey</code>) → élévation.</li>
            <li><strong>Bucket S3</strong> public ou policy mal fichue → exfiltration.</li>
            <li><strong>Absence de logs</strong> → attaques invisibles; pas d’alertes temps réel.</li>
          </ul>

          <h2>2) Défenses rapides</h2>
          <ul>
            <li><strong>Least privilege</strong> : policies minimales; conditions; pas de <code class="inline">*:* </code>.</li>
            <li><strong>Blocage public S3</strong> (compte + bucket) + <em>Bucket Policy</em> restrictive.</li>
            <li><strong>Chiffrement</strong> par défaut (SSE-KMS) + gestion des clés (rotation/ACL).</li>
            <li><strong>CloudTrail</strong> dans tous les régions + <strong>GuardDuty</strong> activé.</li>
            <li><strong>Séparation des comptes</strong> (prod/stage) avec <strong>SCP</strong> pour interdire actions risquées.</li>
            <li><strong>MFA</strong> pour le root et accès sensibles; clés à durée de vie courte via roles.</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> bloque le public S3 au niveau compte, active CloudTrail &amp; GuardDuty, chiffre S3 avec KMS, impose des rôles <em>scopés</em> et utilise des <strong>SCP</strong> pour empêcher les <em>wildcards</em> et la création de clés persistantes.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : AWS Attack / Defense Lab</h3>
            <p class="rules">Active les bons réglages. Rien n’est validé au départ : configure la plateforme pour faire passer les 6 missions au vert ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages AWS">
                  <div class="row">
                    <label style="font-weight:800;min-width:240px">S3 — Block Public Access (compte + bucket)</label>
                    <select id="s3bpa">
                      <option value="off" selected>Désactivé</option>
                      <option value="on">Activé (complet)</option>
                    </select>
                    <span id="s3bpaTag" class="pill warnP">Ouverts au public</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">S3 — Chiffrement par défaut</label>
                    <select id="s3enc">
                      <option value="none" selected>Aucun</option>
                      <option value="sse">SSE-S3</option>
                      <option value="kms">SSE-KMS (recommandé)</option>
                    </select>
                    <span id="s3encTag" class="pill warnP">Non chiffré</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">IAM — Least privilege</label>
                    <select id="iamlp">
                      <option value="wide" selected>Politiques larges (*:*)</option>
                      <option value="lp">Droits minimaux + conditions</option>
                    </select>
                    <span id="iamlpTag" class="pill warnP">Trop permissif</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">CloudTrail &amp; GuardDuty</label>
                    <select id="detect">
                      <option value="off" selected>Désactivés</option>
                      <option value="on">Activés (toutes régions)</option>
                    </select>
                    <span id="detectTag" class="pill warnP">Pas de visibilité</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">SCP (Organisation) anti-<em>wildcards</em></label>
                    <select id="scp">
                      <option value="none" selected>Aucune</option>
                      <option value="deny">SCP qui refuse <code>*:*</code> &amp; création de clés</option>
                    </select>
                    <span id="scpTag" class="pill warnP">Aucune barrière</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:240px">Egress S3 via VPC Endpoint</label>
                    <select id="vpce">
                      <option value="internet" selected>Sortie Internet</option>
                      <option value="endpoint">Endpoint S3 + policies</option>
                    </select>
                    <span id="vpceTag" class="pill warnP">Chemin d’exfiltration</span>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Empêcher l’exposition publique des buckets</div>
                    <div class="check" id="m2">⬜ Mission 2 — Forcer le chiffrement des objets</div>
                    <div class="check" id="m3">⬜ Mission 3 — Réduire le risque d’élévation via IAM</div>
                    <div class="check" id="m4">⬜ Mission 4 — Avoir logs &amp; détections actifs</div>
                    <div class="check" id="m5">⬜ Mission 5 — Empêcher les politiques <code class="inline">*:*</code> au niveau orga</div>
                    <div class="check" id="m6">⬜ Mission 6 — Couper l’exfiltration directe d’S3</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> BPA=<em>on</em>, Chiffrement=<em>kms</em>, IAM=<em>lp</em>, Détection=<em>on</em>, SCP=<em>deny</em>, Egress=<em>endpoint</em>.
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
              <div class="qhd">1) Quelle est la meilleure façon d’éviter un bucket S3 public accidentel ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Activer “Block Public Access” au niveau compte et bucket</label>
                <label><input type="radio" name="q1"> Mettre un nom compliqué au bucket</label>
                <label><input type="radio" name="q1"> Compter sur la chance</label>
                <div class="explain">BPA bloque ACL/policies publiques même si elles sont ajoutées par erreur.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) CloudTrail + GuardDuty servent principalement à…</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Journaliser les appels API et alerter sur activités suspectes</label>
                <label><input type="radio" name="q2"> Réduire la facture EC2</label>
                <label><input type="radio" name="q2"> Remplacer IAM</label>
                <div class="explain">CloudTrail trace; GuardDuty détecte (IOC, anomalies, IAM findings).</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Une policy <code class="inline">iam:PassRole</code> trop large peut mener à…</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Une élévation de privilèges via lancement de services avec rôles puissants</label>
                <label><input type="radio" name="q3"> Une baisse de latence réseau</label>
                <label><input type="radio" name="q3"> Rien de particulier</label>
                <div class="explain">Pouvoir passer n’importe quel rôle permet d’endosser des permissions supérieures.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Quelle option protège <em>à l’échelle de l’organisation</em> contre des politiques dangereuses ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Des SCP qui refusent <code class="inline">*:*</code> et la création de clés</label>
                <label><input type="radio" name="q4"> Un README sévère</label>
                <label><input type="radio" name="q4"> Des emojis</label>
                <div class="explain">Les SCP appliquent des garde-fous sur tous les comptes/fils.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Pour chiffrer S3 avec contrôle fin des accès aux clés, on choisit…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> SSE-KMS avec policies de clé</label>
                <label><input type="radio" name="q5"> Aucun chiffrement</label>
                <label><input type="radio" name="q5"> Zipper les objets</label>
                <div class="explain">KMS permet audit, rotation et contrôle d’usage des clés.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./5.php">← Leçon précédente</a>
              <a href="./7.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : AWS Attack / Defense =====
    (function(){
      const s3bpa = document.getElementById('s3bpa');
      const s3enc = document.getElementById('s3enc');
      const iamlp = document.getElementById('iamlp');
      const detect = document.getElementById('detect');
      const scp = document.getElementById('scp');
      const vpce = document.getElementById('vpce');

      const tags = {
        s3bpa: document.getElementById('s3bpaTag'),
        s3enc: document.getElementById('s3encTag'),
        iamlp: document.getElementById('iamlpTag'),
        detect: document.getElementById('detectTag'),
        scp: document.getElementById('scpTag'),
        vpce: document.getElementById('vpceTag'),
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
        tag(tags.s3bpa, s3bpa.value==='on' ? 'BPA activé' : 'Ouverts au public', s3bpa.value==='on' ? 'ok' : 'warnP');
        tag(tags.s3enc, s3enc.value==='kms' ? 'SSE-KMS' : s3enc.value==='sse' ? 'SSE-S3' : 'Non chiffré', s3enc.value!=='none' ? 'ok' : 'warnP');
        tag(tags.iamlp, iamlp.value==='lp' ? 'Least privilege' : 'Trop permissif', iamlp.value==='lp' ? 'ok' : 'warnP');
        tag(tags.detect, detect.value==='on' ? 'Trail+GuardDuty' : 'Pas de visibilité', detect.value==='on' ? 'ok' : 'warnP');
        tag(tags.scp, scp.value==='deny' ? 'SCP restrictives' : 'Aucune barrière', scp.value==='deny' ? 'ok' : 'warnP');
        tag(tags.vpce, vpce.value==='endpoint' ? 'Endpoint + policy' : 'Internet', vpce.value==='endpoint' ? 'ok' : 'warnP');
      }

      function renderMissions(){
        if (!touched) return;
        (s3bpa.value==='on')
          ? done(m1,'Mission 1 — Exposition publique empêchée')
          : resetCheck(m1,'Mission 1 — Empêcher l’exposition publique des buckets');

        (s3enc.value==='kms')
          ? done(m2,'Mission 2 — Chiffrement SSE-KMS appliqué')
          : resetCheck(m2,'Mission 2 — Forcer le chiffrement des objets');

        (iamlp.value==='lp')
          ? done(m3,'Mission 3 — IAM en least privilege')
          : resetCheck(m3,'Mission 3 — Réduire le risque d’élévation via IAM');

        (detect.value==='on')
          ? done(m4,'Mission 4 — Logs & détections actifs')
          : resetCheck(m4,'Mission 4 — Avoir logs & détections actifs');

        (scp.value==='deny')
          ? done(m5,'Mission 5 — Garde-fous orga en place')
          : resetCheck(m5,'Mission 5 — Empêcher les politiques *:* au niveau orga');

        (vpce.value==='endpoint')
          ? done(m6,'Mission 6 — Exfiltration Internet coupée')
          : resetCheck(m6,'Mission 6 — Couper l’exfiltration directe d’S3');
      }

      function onChange(){ touched=true; renderTags(); renderMissions(); }

      [s3bpa,s3enc,iamlp,detect,scp,vpce].forEach(el=>el.addEventListener('change', onChange));
      renderTags(); // état initial neutre
    })();
  </script>
</body>
</html>
