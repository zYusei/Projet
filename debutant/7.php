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
  <title>Wi-Fi public en sécurité | FunCodeLab</title>
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
  .badge{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--panel-2);color:#cdbb7a;padding:.3rem .55rem;border-radius:999px;font-size:.8rem}

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

  /* Mini-jeu Hotspot Lab */
  .mini{margin-top:22px}
  .mini .rules{color:var(--muted);margin:6px 0 10px}
  .mini .play{background:var(--panel-2); border:1px solid var(--border); border-radius:12px; padding:14px}
  .hs-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
  @media(max-width:900px){.hs-grid{grid-template-columns:1fr}}
  .box{background:#0e1534;border:1px solid var(--border);border-radius:12px;padding:14px}
  .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .row input,.row select,.row button{background:#0e1534;border:1px solid var(--border);color:#fff;padding:.55rem .7rem;border-radius:10px}
  .pill{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:.15rem .6rem;font-size:.8rem;color:#cfe1ff}
  .ok{background:linear-gradient(90deg,#7cffc0,#4df1aa);color:#06121f;border-color:#164238}
  .warnTag{background:linear-gradient(90deg,#ff9f9f,#ff6b6b);color:#1a0c0c;border-color:#4a1a1a}
  .checks{margin-top:10px;display:flex;flex-direction:column;gap:6px}
  .check{display:flex;align-items:center;gap:8px;color:var(--muted)}
  .check.done{color:var(--good);font-weight:800}
  .hint{color:var(--muted);font-size:.92rem;margin-top:8px}
  .status{margin-top:10px;border:1px dashed var(--border);border-radius:10px;padding:10px;color:#cfe1ff}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="topbar">
      <div class="crumbs">🛡️ <a href="../parcours.php">Parcours</a> &nbsp;›&nbsp; Débutant &nbsp;›&nbsp; <span style="color:var(--accent)">Wi-Fi public en sécurité</span></div>
      <span class="badge">Leçon 7 / 8</span>
    </div>

    <div class="title">
      <h1>Wi-Fi public en sécurité</h1>
      <span class="progress-pill">Niveau : Intermédiaire</span>
    </div>

    <div class="layout">
      <!-- COURS -->
      <section class="card">
        <div class="hd">Leçon</div>
        <div class="bd lesson">
          <p>Sur un Wi-Fi public, n’importe qui peut être sur le même réseau que toi. Pour réduire les risques :</p>
          <ul>
            <li><strong>Évite les réseaux ouverts</strong> (sans cadenas). Préfère WPA2/WPA3 avec mot de passe.</li>
            <li><strong>Utilise un VPN</strong> : ton trafic devient chifré entre toi et le serveur VPN.</li>
            <li><strong>Vérifie le HTTPS</strong> (cadenas) et refuse les versions <code class="inline">http://</code>.</li>
            <li><strong>Désactive le partage</strong> (partage de fichiers, AirDrop “Tout le monde”, hotspot auto…).</li>
          </ul>

          <div class="callout warn">
            <strong>Astuce :</strong> attention aux réseaux “jumeaux maléfiques” (ex. <em>FreeWifi</em> vs <em>FreeWiFi-Secure</em>). Le nom est ressemblant, mais la sécurité n’est pas la même.
          </div>

          <!-- MINI-JEU -->
          <div class="mini">
            <h3>Mini-jeu : Hotspot Lab</h3>
            <p class="rules">Objectif : te connecter <strong>prudemment</strong>. Choisis le bon réseau, <strong>active le VPN</strong>, <strong>visite un site en HTTPS</strong> et <strong>désactive le partage</strong>.</p>

            <div class="play">
              <div class="hs-grid">
                <!-- Zone d’actions -->
                <div class="box">
                  <div class="row">
                    <label style="font-weight:800">Réseau Wi-Fi</label>
                    <select id="ssid">
                      <option value="coffee-open">Coffee_Free (ouvert)</option>
                      <option value="mall-open-twin">Mall-WiFi (ouvert — clone)</option>
                      <option value="hotel-wpa2">HotelSecure (WPA2)</option>
                      <option value="airport-wpa3">Airport-WPA3</option>
                    </select>
                    <button id="btnJoin" class="btn-accent" type="button">Se connecter</button>
                  </div>

                  <div class="status" id="netStatus">Pas connecté.</div>

                  <hr style="border:0;border-top:1px solid var(--border);margin:12px 0">

                  <div class="row">
                    <label style="font-weight:800">VPN</label>
                    <button id="btnVpn" type="button" class="btn-ghost">Activer VPN</button>
                    <span id="vpnTag" class="pill">VPN : OFF</span>
                  </div>

                  <div class="row" style="margin-top:10px">
                    <label style="font-weight:800">Visiter</label>
                    <select id="site">
                      <option value="http://banque.exemple">http://banque.exemple</option>
                      <option value="https://banque.exemple">https://banque.exemple</option>
                      <option value="https://journal.exemple">https://journal.exemple</option>
                    </select>
                    <button id="btnVisit" class="btn-ghost" type="button">Ouvrir</button>
                    <span id="tlsTag" class="pill">HTTPS : ?</span>
                  </div>

                  <div class="row" style="margin-top:10px">
                    <label style="font-weight:800">Partage</label>
                    <button id="btnShare" class="btn-ghost" type="button">Désactiver le partage</button>
                    <span id="shareTag" class="pill">Partage : ON</span>
                  </div>

                  <p class="hint">Indice : WPA2/WPA3 + VPN + HTTPS + partage désactivé = combo gagnant 🔒</p>
                </div>

                <!-- Missions -->
                <div class="box">
                  <div class="checks">
                    <div class="check" id="m1">⬜ Mission 1 — Éviter le réseau ouvert / clone, choisir un réseau sécurisé (WPA2/3)</div>
                    <div class="check" id="m2">⬜ Mission 2 — Activer le VPN</div>
                    <div class="check" id="m3">⬜ Mission 3 — Visiter un site en <strong>HTTPS</strong></div>
                    <div class="check" id="m4">⬜ Mission 4 — Désactiver le partage</div>
                  </div>
                  <div class="callout" style="margin-top:10px">
                    <strong>Note :</strong> même en HTTPS, le réseau voit que tu vas sur <em>banque.exemple</em> (nom de domaine), pas le contenu. Le VPN cache aussi ce domaine au Wi-Fi local.
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
              <div class="qhd">1) Quel réseau privilégier dans un café ?</div>
              <div class="qbd">
                <label><input type="radio" name="q1"> Réseau ouvert sans mot de passe</label>
                <label><input type="radio" name="q1" data-correct="1"> Réseau WPA2/WPA3 connu (fourni au comptoir)</label>
                <label><input type="radio" name="q1"> Réseau au nom ressemblant mais inconnu</label>
                <div class="explain">Un Wi-Fi protégé (WPA2/3) limite les attaques locales et évite les clones.</div>
              </div>
            </div>

            <div class="q" data-q="q2">
              <div class="qhd">2) À quoi sert un VPN sur Wi-Fi public ?</div>
              <div class="qbd">
                <label><input type="radio" name="q2" data-correct="1"> Chiffrer ton trafic entre toi et le serveur VPN</label>
                <label><input type="radio" name="q2"> Accélérer YouTube</label>
                <label><input type="radio" name="q2"> Remplacer l’antivirus</label>
                <div class="explain">Le VPN empêche les curieux du réseau local de lire ton trafic.</div>
              </div>
            </div>

            <div class="q" data-q="q3">
              <div class="qhd">3) Pourquoi refuser un lien qui force <code class="inline">http://</code> ?</div>
              <div class="qbd">
                <label><input type="radio" name="q3" data-correct="1"> Car non chiffré : interception ou altération possibles</label>
                <label><input type="radio" name="q3"> Parce que c’est lent</label>
                <label><input type="radio" name="q3"> Parce que le site est toujours faux</label>
                <div class="explain">Sans HTTPS, le contenu peut être lu ou modifié sur le trajet.</div>
              </div>
            </div>

            <div class="q" data-q="q4">
              <div class="qhd">4) Que faire du “partage de fichiers/AirDrop” sur un Wi-Fi public ?</div>
              <div class="qbd">
                <label><input type="radio" name="q4" data-correct="1"> Le désactiver / limiter aux contacts</label>
                <label><input type="radio" name="q4"> Le laisser ouvert à tous</label>
                <label><input type="radio" name="q4"> Peu importe</label>
                <div class="explain">Réduis la surface d’attaque sur le réseau local.</div>
              </div>
            </div>

            <div class="q" data-q="q5">
              <div class="qhd">5) Un “Evil Twin” c’est…</div>
              <div class="qbd">
                <label><input type="radio" name="q5" data-correct="1"> Un faux point d’accès avec un nom quasi identique</label>
                <label><input type="radio" name="q5"> Un bug de ton PC</label>
                <label><input type="radio" name="q5"> Un répéteur officiel</label>
                <div class="explain">Le but est de te faire connecter au mauvais réseau.</div>
              </div>
            </div>

            <div class="q" data-q="q6">
              <div class="qhd">6) Avec HTTPS mais sans VPN, le Wi-Fi public peut voir…</div>
              <div class="qbd">
                <label><input type="radio" name="q6" data-correct="1"> Le nom de domaine (ex: banque.exemple)</label>
                <label><input type="radio" name="q6"> Ton mot de passe</label>
                <label><input type="radio" name="q6"> Le contenu des pages</label>
                <div class="explain">TLS masque le contenu, pas la destination (SNI/DoH selon cas). Le VPN masque aussi la destination au réseau local.</div>
              </div>
            </div>

            <div class="controls">
              <button type="button" class="btn-accent" id="btnCheck">Valider mes réponses</button>
              <button type="button" class="btn-ghost" id="btnReset">Réinitialiser</button>
            </div>
            <div id="score" class="score" style="display:none"></div>

            <div class="footer-nav">
              <a href="./6.php">← Leçon précédente</a>
              <a href="./8.php">Leçon suivante →</a>
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

    // ===== Mini-jeu : Hotspot Lab =====
    (function(){
      const ssid = document.getElementById('ssid');
      const btnJoin = document.getElementById('btnJoin');
      const netStatus = document.getElementById('netStatus');

      const btnVpn = document.getElementById('btnVpn');
      const vpnTag = document.getElementById('vpnTag');

      const site = document.getElementById('site');
      const btnVisit = document.getElementById('btnVisit');
      const tlsTag = document.getElementById('tlsTag');

      const btnShare = document.getElementById('btnShare');
      const shareTag = document.getElementById('shareTag');

      const m1 = document.getElementById('m1');
      const m2 = document.getElementById('m2');
      const m3 = document.getElementById('m3');
      const m4 = document.getElementById('m4');

      let connected = null; // 'open' | 'wpa2' | 'wpa3' | 'clone'
      let vpn = false;
      let share = true;

      function mark(el, txt){
        el.classList.add('done'); el.textContent = '✅ ' + txt;
      }
      function updateVPN(){
        vpnTag.textContent = 'VPN : ' + (vpn ? 'ON' : 'OFF');
        vpnTag.className = 'pill ' + (vpn ? 'ok' : '');
      }
      function updateShare(){
        shareTag.textContent = 'Partage : ' + (share ? 'ON' : 'OFF');
        shareTag.className = 'pill ' + (share ? 'warnTag' : 'ok');
      }
      function updateTLS(url){
        const https = url.startsWith('https://');
        tlsTag.textContent = 'HTTPS : ' + (https ? 'OK' : 'NON');
        tlsTag.className = 'pill ' + (https ? 'ok' : 'warnTag');
      }

      btnJoin.addEventListener('click', ()=>{
        const v = ssid.value;
        if (v === 'coffee-open') connected = 'open';
        if (v === 'mall-open-twin') connected = 'clone';
        if (v === 'hotel-wpa2') connected = 'wpa2';
        if (v === 'airport-wpa3') connected = 'wpa3';

        let txt = 'Connexion à ';
        if (connected==='open') txt += 'Coffee_Free (ouvert) — ⚠️ non chiffré';
        if (connected==='clone') txt += 'Mall-WiFi (ouvert — possible clone) — ⚠️';
        if (connected==='wpa2') txt += 'HotelSecure (WPA2) — ✅ chiffré';
        if (connected==='wpa3') txt += 'Airport-WPA3 — ✅ chiffré';
        netStatus.textContent = txt;

        if (connected==='wpa2' || connected==='wpa3'){
          mark(m1, 'Mission 1 — Réseau sécurisé choisi');
        }
      });

      btnVpn.addEventListener('click', ()=>{
        vpn = !vpn;
        btnVpn.textContent = vpn ? 'Désactiver VPN' : 'Activer VPN';
        updateVPN();
        if (vpn) mark(m2, 'Mission 2 — VPN activé');
      });

      btnVisit.addEventListener('click', ()=>{
        const url = site.value;
        updateTLS(url);
        if (url.startsWith('https://')) mark(m3, 'Mission 3 — Site en HTTPS visité');
      });

      btnShare.addEventListener('click', ()=>{
        share = !share;
        btnShare.textContent = share ? 'Désactiver le partage' : 'Activer le partage';
        updateShare();
        if (!share) mark(m4, 'Mission 4 — Partage désactivé');
      });

      // init
      updateVPN(); updateShare(); updateTLS(site.value);
    })();
  </script>
</body>
</html>
