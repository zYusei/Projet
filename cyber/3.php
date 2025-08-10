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
  <title>Réseau sécurisé | FunCodeLab</title>
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

  /* Mini-jeu : Network Lab */
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
  .topo{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px}
  .cardx{background:#0f1a3b;border:1px solid var(--border);border-radius:12px;padding:10px}
  .tag{display:inline-block;margin-top:6px;border:1px solid var(--border);border-radius:999px;padding:.1rem .5rem;font-size:.75rem;color:#9cc1ff}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🌐 <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Cyber &nbsp;›&nbsp; <span style="color:var(--accent)">Réseau sécurisé</span></div>
      <span class="badge">Leçon 3 / 8</span>
    </div>

    <div class="title">
      <h1>Réseau sécurisé</h1>
      <span class="progress-pill">Niveau : Facile</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Objectif : comprendre les bons réflexes réseau — <strong>HTTPS</strong> (chiffrement web), <strong>VPN</strong> (tunnel chiffré), et <strong>segmentation</strong> (isoler pour limiter l’impact).</p>

          <h2>1) HTTPS (TLS)</h2>
          <ul>
            <li>Adresse commence par <strong>https://</strong> + <em>cadenas</em>. Le trafic est chiffré entre toi et le site.</li>
            <li>Mauvais signe : avertissement « certificat invalide » ➜ <strong>ne continue pas</strong>.</li>
            <li>Bon réflexe admin : activer <code class="inline">HSTS</code> (forcer le HTTPS) et rediriger tout le HTTP ➜ HTTPS.</li>
          </ul>

          <h2>2) VPN</h2>
          <ul>
            <li>Crée un <strong>tunnel chiffré</strong> entre ta machine et un serveur de confiance (entreprise, maison, fournisseur réputé).</li>
            <li>Utile sur réseaux publics (hôtels, cafés) ou pour accéder à des ressources internes.</li>
            <li>Ne « répare » pas un site en HTTP : si le site est HTTP, seul le tunnel jusqu’au VPN est chiffré.</li>
          </ul>

          <h2>3) Segmentation</h2>
          <ul>
            <li>On sépare le réseau en zones (ex. <em>Invités</em>, <em>IoT</em>, <em>Bureaux</em>) : un appareil compromis ne voit pas tout.</li>
            <li>Implémentation simple à la maison : Wi-Fi invités + bloquer l’accès au LAN interne.</li>
            <li>En entreprise : VLANs + règles pare-feu (autoriser seulement ce qui est nécessaire).</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> combine <em>HTTPS-only</em> + <em>VPN sur public</em> + <em>réseau invités</em> pour couvrir la plupart des risques quotidiens.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Network Lab</h3>
            <p class="rules">Active les bons réglages pour sécuriser un petit réseau. Les 4 missions passent au vert ✅</p>

            <div class="play">
              <div class="grid">
                <!-- Réglages -->
                <div class="box" aria-label="Réglages réseau">
                  <div class="row">
                    <label style="font-weight:800;min-width:160px">Forcer HTTPS</label>
                    <input type="checkbox" id="httpsOnly">
                    <span id="httpsTag" class="pill warnP">HTTP possible</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:160px">VPN quand Wi-Fi public</label>
                    <input type="checkbox" id="vpnPublic">
                    <span id="vpnTag" class="pill warnP">Non protégé</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:160px">Réseau invités</label>
                    <select id="guestNet">
                      <option value="off" selected>Désactivé</option>
                      <option value="isolated">Invités isolés du LAN</option>
                      <option value="full">Invités sur le même LAN (❌)</option>
                    </select>
                    <span id="guestTag" class="pill warnP">Désactivé</span>
                  </div>

                  <div class="row">
                    <label style="font-weight:800;min-width:160px">Pare-feu (LAN ⇄ IoT)</label>
                    <select id="fwRule">
                      <option value="allowall" selected>Tout autoriser (❌)</option>
                      <option value="denyall">Tout bloquer</option>
                      <option value="least">Accès minimal (DNS/NTP sortant)</option>
                    </select>
                    <span id="fwTag" class="pill warnP">Tout autoriser</span>
                  </div>

                  <hr style="border:none;border-top:1px solid var(--border);margin:10px 0">

                  <div class="topo">
                    <div class="cardx">
                      <strong>LAN Bureaux</strong>
                      <div class="tag">PC/serveurs</div>
                    </div>
                    <div class="cardx">
                      <strong>Réseau Invités</strong>
                      <div class="tag">Wi-Fi visiteur</div>
                    </div>
                    <div class="cardx">
                      <strong>IoT</strong>
                      <div class="tag">Caméras, TV</div>
                    </div>
                    <div class="cardx">
                      <strong>Internet</strong>
                      <div class="tag">HTTPS + VPN</div>
                    </div>
                  </div>
                </div>

                <!-- Missions -->
                <div class="box" aria-label="Missions">
                  <div class="checks">
                    <div class="check" id="c1">⬜ Mission 1 — Activer HTTPS-only/HSTS</div>
                    <div class="check" id="c2">⬜ Mission 2 — Activer le VPN sur Wi-Fi public</div>
                    <div class="check" id="c3">⬜ Mission 3 — Isoler le réseau invités</div>
                    <div class="check" id="c4">⬜ Mission 4 — Appliquer le moindre privilège entre LAN et IoT</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Indice :</strong> HSTS, VPN activé, <em>Invités isolés</em>, règle pare-feu <em>Accès minimal</em>.
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
              <div class="qhd">1) Le HTTPS sert principalement à…</div>
              <div class="qbd">
                <label><input type="radio" name="q1" data-correct="1"> Chiffrer la communication avec un site</label>
                <label><input type="radio" name="q1"> Accélérer le Wi-Fi</label>
                <label><input type="radio" name="q1"> Bloquer la pub</label>
                <div class="explain">TLS chiffre et authentifie le serveur (certificat).</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) Le VPN est surtout utile…</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Sur les réseaux publics pour créer un tunnel chiffré</label>
                <label><input type="radio" name="q2"> À la maison quand on a déjà HTTPS</label>
                <label><input type="radio" name="q2"> Pour éviter les mises à jour</label>
                <div class="explain">Le VPN protège contre l’interception locale (hôtel/café).</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Pourquoi segmenter le réseau ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Pour limiter la propagation en cas de compromission</label>
                <label><input type="radio" name="q3"> Pour avoir plus de câbles</label>
                <label><input type="radio" name="q3"> Pour augmenter la facture</label>
                <div class="explain">Isoler (Invités/IoT/Bureaux) réduit l’exposition.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Quelle règle pare-feu illustre le « moindre privilège » ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> N’autoriser que DNS/NTP sortant depuis IoT</label>
                <label><input type="radio" name="q4"> Tout autoriser entre tous les VLANs</label>
                <label><input type="radio" name="q4"> Tout bloquer y compris l’accès Internet</label>
                <div class="explain">Autoriser le strict nécessaire au fonctionnement.</div>
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

    // ===== Mini-jeu : Network Lab =====
    (function(){
      const httpsOnly = document.getElementById('httpsOnly');
      const vpnPublic = document.getElementById('vpnPublic');
      const guestNet  = document.getElementById('guestNet');
      const fwRule    = document.getElementById('fwRule');

      const httpsTag = document.getElementById('httpsTag');
      const vpnTag   = document.getElementById('vpnTag');
      const guestTag = document.getElementById('guestTag');
      const fwTag    = document.getElementById('fwTag');

      const c1=document.getElementById('c1');
      const c2=document.getElementById('c2');
      const c3=document.getElementById('c3');
      const c4=document.getElementById('c4');

      let touched = false; // rien n'est validé tant que l'utilisateur n'a pas modifié

      function setTag(el, txt, cls){
        el.textContent = txt;
        el.className = 'pill ' + cls;
      }
      function done(el, txt){ el.classList.add('done'); el.textContent='✅ '+txt; }

      function update(){
        // Tags visuels
        setTag(httpsTag, httpsOnly.checked ? 'HSTS actif' : 'HTTP possible', httpsOnly.checked ? 'ok' : 'warnP');
        setTag(vpnTag,   vpnPublic.checked ? 'Tunnel chiffré' : 'Non protégé', vpnPublic.checked ? 'ok' : 'warnP');

        if (guestNet.value==='isolated') setTag(guestTag,'Isolé','ok');
        else if (guestNet.value==='full') setTag(guestTag,'Sur le LAN (❌)','warnP');
        else setTag(guestTag,'Désactivé','warnP');

        if (fwRule.value==='least') setTag(fwTag,'Moindre privilège','ok');
        else if (fwRule.value==='denyall') setTag(fwTag,'Tout bloquer (strict)','warnP');
        else setTag(fwTag,'Tout autoriser','warnP');

        // Missions seulement après interaction
        if (!touched) return;
        if (httpsOnly.checked) done(c1,'Mission 1 — HTTPS-only/HSTS');
        else { c1.classList.remove('done'); c1.textContent='⬜ Mission 1 — Activer HTTPS-only/HSTS'; }

        if (vpnPublic.checked) done(c2,'Mission 2 — VPN sur public');
        else { c2.classList.remove('done'); c2.textContent='⬜ Mission 2 — Activer le VPN sur Wi-Fi public'; }

        if (guestNet.value==='isolated') done(c3,'Mission 3 — Invités isolés');
        else { c3.classList.remove('done'); c3.textContent='⬜ Mission 3 — Isoler le réseau invités'; }

        if (fwRule.value==='least') done(c4,'Mission 4 — Pare-feu minimaliste');
        else { c4.classList.remove('done'); c4.textContent='⬜ Mission 4 — Appliquer le moindre privilège entre LAN et IoT'; }
      }

      [httpsOnly, vpnPublic, guestNet, fwRule].forEach(el=>{
        el.addEventListener('change', ()=>{ touched = true; update(); });
      });

      // état initial neutre (rien de validé)
      update(); // met juste les tags, sans cocher de mission
    })();
  </script>
</body>
</html>
