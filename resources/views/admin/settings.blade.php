@extends('admin.layouts.app')

@section('title', 'Paramètres')

@section('styles')
<style>
  .settings-grid { display:grid; grid-template-columns:220px 1fr; gap:24px; align-items:start; }
  .settings-nav { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); overflow:hidden; position:sticky; top:24px; }
  .settings-nav-item { display:flex; align-items:center; gap:10px; padding:12px 18px; font-size:13px; font-weight:600; color:var(--adm-text2); cursor:pointer; border-left:3px solid transparent; transition:all 0.15s; text-decoration:none; }
  .settings-nav-item:hover { background:var(--adm-bg); color:var(--adm-text); }
  .settings-nav-item.active { background:var(--adm-green-soft); color:var(--adm-green); border-left-color:var(--adm-green); }
  .settings-nav-item .material-symbols-outlined { font-size:18px; }
  .settings-nav-item + .settings-nav-item { border-top:1px solid var(--adm-border); }

  .settings-section { display:none; }
  .settings-section.active { display:flex; flex-direction:column; gap:20px; }

  .settings-card { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); overflow:hidden; }
  .settings-card-header { padding:18px 22px; border-bottom:1px solid var(--adm-border); display:flex; align-items:center; gap:12px; }
  .settings-card-header-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .settings-card-header-icon .material-symbols-outlined { font-size:18px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
  .settings-card-title { font-family:'Plus Jakarta Sans',sans-serif; font-size:14.5px; font-weight:700; }
  .settings-card-subtitle { font-size:12px; color:var(--adm-text3); margin-top:1px; }
  .settings-card-body { padding:22px; }

  .settings-field { display:flex; flex-direction:column; gap:5px; margin-bottom:16px; }
  .settings-field:last-child { margin-bottom:0; }
  .settings-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); }
  .settings-input, .settings-select, .settings-textarea {
    padding:9px 13px; border:1px solid var(--adm-border); border-radius:8px;
    font-size:13px; font-family:inherit; color:var(--adm-text); background:var(--adm-bg);
    outline:none; transition:border-color 0.18s,box-shadow 0.18s; width:100%;
  }
  .settings-input:focus, .settings-select:focus, .settings-textarea:focus {
    border-color:var(--adm-green); box-shadow:0 0 0 3px rgba(27,107,58,0.08); background:#fff;
  }
  .settings-textarea { resize:vertical; min-height:90px; }
  .settings-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }

  .settings-toggle-row { display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-bottom:1px solid var(--adm-border); }
  .settings-toggle-row:last-child { border-bottom:none; padding-bottom:0; }
  .settings-toggle-label { font-size:13.5px; font-weight:600; }
  .settings-toggle-desc { font-size:12px; color:var(--adm-text3); margin-top:2px; }
  .toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; }
  .toggle-switch input { opacity:0; width:0; height:0; }
  .toggle-slider { position:absolute; cursor:pointer; top:0;left:0;right:0;bottom:0; background:#d1d5db; transition:0.25s; border-radius:24px; }
  .toggle-slider:before { position:absolute; content:''; height:18px; width:18px; left:3px; bottom:3px; background:#fff; transition:0.25s; border-radius:50%; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
  .toggle-switch input:checked + .toggle-slider { background:var(--adm-green); }
  .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }

  .danger-zone { border:1px solid #fecaca; border-radius:var(--adm-radius); overflow:hidden; }
  .danger-zone-header { background:#fff5f5; padding:16px 22px; border-bottom:1px solid #fecaca; display:flex; align-items:center; gap:10px; }
  .danger-zone-header .material-symbols-outlined { color:#e53e3e; }
  .danger-zone-header-title { font-size:14px; font-weight:700; color:#e53e3e; }
  .danger-zone-body { padding:22px; }
  .danger-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:1px solid #e53e3e; background:transparent; color:#e53e3e; transition:all 0.15s; font-family:inherit; }
  .danger-btn:hover { background:#e53e3e; color:#fff; }

  @media(max-width:768px) {
    .settings-grid { grid-template-columns:1fr; }
    .settings-nav { position:static; }
    .settings-row { grid-template-columns:1fr; }
  }
</style>
@endsection

@section('content')

  <div class="adm-page-header">
    <div>
      <h1>Paramètres de la plateforme</h1>
      <p>Configurez l'apparence, les notifications, la sécurité et les accès du dashboard.</p>
    </div>
  </div>

  <div class="settings-grid">

    {{-- NAV LATERALE --}}
    <nav class="settings-nav">
      @foreach([
        ['id'=>'general',       'label'=>'Général',         'icon'=>'tune'],
        ['id'=>'notifications', 'label'=>'Notifications',   'icon'=>'notifications'],
        ['id'=>'apparence',     'label'=>'Apparence',       'icon'=>'palette'],
        ['id'=>'securite',      'label'=>'Sécurité',        'icon'=>'security'],
        ['id'=>'avance',        'label'=>'Avancé',          'icon'=>'build'],
      ] as $s)
        <a href="#" class="settings-nav-item {{ $loop->first ? 'active' : '' }}"
           onclick="switchSection('{{ $s['id'] }}', this); return false;">
          <span class="material-symbols-outlined">{{ $s['icon'] }}</span>
          {{ $s['label'] }}
        </a>
      @endforeach
    </nav>

    {{-- CONTENU --}}
    <div>

      {{-- ── GÉNÉRAL ── --}}
      <div class="settings-section active" id="section-general">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:var(--adm-green-soft)">
                <span class="material-symbols-outlined" style="color:var(--adm-green)">store</span>
              </div>
              <div>
                <div class="settings-card-title">Informations de la plateforme</div>
                <div class="settings-card-subtitle">Nom, description et coordonnées</div>
              </div>
            </div>
            <div class="settings-card-body">
              <div class="settings-row">
                <div class="settings-field">
                  <label class="settings-label">Nom de la plateforme</label>
                  <input type="text" name="platform_name" class="settings-input" value="{{ old('platform_name', \App\Models\Setting::getValue('platform_name', 'BéninMarket')) }}">
                </div>
                <div class="settings-field">
                  <label class="settings-label">URL du site</label>
                  <input type="url" name="platform_url" class="settings-input" value="{{ old('platform_url', \App\Models\Setting::getValue('platform_url', 'https://beninmarket.bj')) }}">
                </div>
              </div>
              <div class="settings-field">
                <label class="settings-label">Description</label>
                <textarea name="platform_description" class="settings-textarea">{{ old('platform_description', \App\Models\Setting::getValue('platform_description')) }}</textarea>
              </div>
              <div class="settings-row">
                <div class="settings-field">
                  <label class="settings-label">Email de contact</label>
                  <input type="email" name="contact_email" class="settings-input" value="{{ old('contact_email', \App\Models\Setting::getValue('contact_email', 'contact@beninmarket.bj')) }}">
                </div>
                <div class="settings-field">
                  <label class="settings-label">Téléphone support</label>
                  <input type="tel" name="contact_phone" class="settings-input" value="{{ old('contact_phone', \App\Models\Setting::getValue('contact_phone', '+229 21 30 56 78')) }}">
                </div>
              </div>
              <div class="settings-row">
                <div class="settings-field">
                  <label class="settings-label">Devise par défaut</label>
                  <select name="currency" class="settings-select">
                    @foreach(['CFA (XOF)','EUR','USD'] as $cur)
                      <option value="{{ $cur }}" {{ (\App\Models\Setting::getValue('currency', 'CFA (XOF)') === $cur) ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="settings-field">
                  <label class="settings-label">Fuseau horaire</label>
                  <select name="timezone" class="settings-select">
                    @foreach(['Africa/Porto-Novo (UTC+1)','Europe/Paris (UTC+1)','UTC'] as $tz)
                      <option value="{{ $tz }}" {{ (\App\Models\Setting::getValue('timezone', 'Africa/Porto-Novo (UTC+1)') === $tz) ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div style="padding-top:4px">
                <button type="submit" class="adm-btn adm-btn-primary">
                  <span class="material-symbols-outlined">save</span>
                  Enregistrer
                </button>
              </div>
            </div>
          </div>
        </form>

        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:#fff8e6">
                <span class="material-symbols-outlined" style="color:var(--adm-gold)">payments</span>
              </div>
              <div>
                <div class="settings-card-title">Commissions & Frais</div>
                <div class="settings-card-subtitle">Taux de commission par défaut pour les vendeurs</div>
              </div>
            </div>
            <div class="settings-card-body">
              <div class="settings-row">
                <div class="settings-field">
                  <label class="settings-label">Commission standard (%)</label>
                  <input type="number" name="commission_standard" class="settings-input" value="{{ old('commission_standard', \App\Models\Setting::getValue('commission_standard', '5')) }}" min="0" max="30" step="0.5">
                </div>
                <div class="settings-field">
                  <label class="settings-label">Commission vendeur premium (%)</label>
                  <input type="number" name="commission_premium" class="settings-input" value="{{ old('commission_premium', \App\Models\Setting::getValue('commission_premium', '3')) }}" min="0" max="30" step="0.5">
                </div>
              </div>
              <div class="settings-field">
                <label class="settings-label">Frais de livraison par défaut (CFA)</label>
                <input type="number" name="delivery_fee" class="settings-input" value="{{ old('delivery_fee', \App\Models\Setting::getValue('delivery_fee', '1500')) }}">
              </div>
              <button type="submit" class="adm-btn adm-btn-primary">
                <span class="material-symbols-outlined">save</span>
                Enregistrer
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- ── NOTIFICATIONS ── --}}
      <div class="settings-section" id="section-notifications">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:#eff6ff">
                <span class="material-symbols-outlined" style="color:#2563eb">email</span>
              </div>
              <div>
                <div class="settings-card-title">Notifications Email</div>
                <div class="settings-card-subtitle">Alertes envoyées à l'administrateur</div>
              </div>
            </div>
            <div class="settings-card-body">
              @php
                $notifs = [
                  'notif_new_seller'     => ['label'=>'Nouvelle demande vendeur','desc'=>'Quand un artisan soumet sa boutique pour validation'],
                  'notif_new_product'    => ['label'=>'Nouveau produit à modérer','desc'=>'Quand un produit est soumis avec statut "en attente"'],
                  'notif_reported_topic' => ['label'=>'Signalement de discussion','desc'=>'Quand un topic est reporté par un utilisateur'],
                  'notif_new_order'      => ['label'=>'Nouvelle commande','desc'=>'Chaque nouvelle commande passée sur la plateforme'],
                  'notif_daily_report'   => ['label'=>'Rapport journalier','desc'=>'Résumé quotidien des activités de la plateforme'],
                ];
              @endphp
              @foreach($notifs as $key => $n)
                <div class="settings-toggle-row">
                  <div>
                    <div class="settings-toggle-label">{{ $n['label'] }}</div>
                    <div class="settings-toggle-desc">{{ $n['desc'] }}</div>
                  </div>
                  <input type="hidden" name="{{ $key }}" value="0">
                  <label class="toggle-switch">
                    <input type="checkbox" name="{{ $key }}" value="1" {{ \App\Models\Setting::getValue($key, '1') === '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              @endforeach
              <div style="margin-top:16px">
                <button type="submit" class="adm-btn adm-btn-primary">
                  <span class="material-symbols-outlined">save</span>
                  Enregistrer
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      {{-- ── APPARENCE ── --}}
      <div class="settings-section" id="section-apparence">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:var(--adm-green-soft)">
                <span class="material-symbols-outlined" style="color:var(--adm-green)">palette</span>
              </div>
              <div>
                <div class="settings-card-title">Thème & Apparence</div>
                <div class="settings-card-subtitle">Couleurs et préférences visuelles</div>
              </div>
            </div>
            <div class="settings-card-body">
              <div class="settings-field">
                <label class="settings-label">Couleur principale</label>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                  @foreach(['#1B6B3A','#2563eb','#7c3aed','#dc2626','#d97706','#0891b2'] as $color)
                    <div onclick="pickColor('{{ $color }}')"
                         style="width:32px;height:32px;border-radius:8px;background:{{ $color }};cursor:pointer;transition:transform 0.15s;border:3px solid {{ $color==='#1B6B3A'?'#fff':$color }};box-shadow:{{ $color==='#1B6B3A'?'0 0 0 2px #1B6B3A':'' }}"
                         onmouseenter="this.style.transform='scale(1.15)'"
                         onmouseleave="this.style.transform='scale(1)'"></div>
                  @endforeach
                  <input type="color" name="accent_color" class="settings-input" value="{{ old('accent_color', \App\Models\Setting::getValue('accent_color', '#1B6B3A')) }}" style="width:40px;height:32px;padding:2px;border-radius:8px">
                </div>
              </div>
              <div class="settings-field" style="margin-top:20px">
                <label class="settings-label">Mode d'affichage</label>
                <div style="display:flex;gap:10px">
                  @foreach(['Clair','Sombre','Automatique'] as $mode)
                    <label style="flex:1;border:1px solid var(--adm-border);border-radius:10px;padding:12px;text-align:center;cursor:pointer;font-size:13px;font-weight:600;transition:all 0.15s;{{ \App\Models\Setting::getValue('display_mode') === $mode ? 'border:2px solid var(--adm-green);color:var(--adm-green)' : '' }}"
                           onclick="document.getElementById('display_mode').value='{{ $mode }}'; this.parentElement.querySelectorAll('label').forEach(l=>l.style.cssText='flex:1;border:1px solid var(--adm-border);border-radius:10px;padding:12px;text-align:center;cursor:pointer;font-size:13px;font-weight:600;transition:all 0.15s'); this.style.cssText='flex:1;border:2px solid var(--adm-green);border-radius:10px;padding:12px;text-align:center;cursor:pointer;font-size:13px;font-weight:600;color:var(--adm-green);transition:all 0.15s'">
                      {{ $mode }}
                    </label>
                  @endforeach
                </div>
                <input type="hidden" name="display_mode" id="display_mode" value="{{ \App\Models\Setting::getValue('display_mode', 'Clair') }}">
              </div>
              <button type="submit" class="adm-btn adm-btn-primary" style="margin-top:8px">
                <span class="material-symbols-outlined">save</span>
                Appliquer
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- ── SÉCURITÉ ── --}}
      <div class="settings-section" id="section-securite">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:var(--adm-green-soft)">
                <span class="material-symbols-outlined" style="color:var(--adm-green)">key</span>
              </div>
              <div>
                <div class="settings-card-title">Changer le mot de passe admin</div>
                <div class="settings-card-subtitle">Dernière modification : jamais</div>
              </div>
            </div>
            <div class="settings-card-body">
              <div class="settings-field">
                <label class="settings-label">Mot de passe actuel</label>
                <input type="password" name="current_password" class="settings-input" placeholder="••••••••">
              </div>
              <div class="settings-row">
                <div class="settings-field">
                  <label class="settings-label">Nouveau mot de passe</label>
                  <input type="password" name="new_password" class="settings-input" placeholder="Min. 8 caractères">
                </div>
                <div class="settings-field">
                  <label class="settings-label">Confirmer</label>
                  <input type="password" name="new_password_confirmation" class="settings-input" placeholder="Répéter">
                </div>
              </div>
              <button type="submit" class="adm-btn adm-btn-primary" onclick="document.getElementById('password-form-action').value='change_password'">
                <span class="material-symbols-outlined">lock_reset</span>
                Mettre à jour
              </button>
              <input type="hidden" name="action" id="password-form-action" value="">
            </div>
          </div>
        </form>

        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:#eff6ff">
                <span class="material-symbols-outlined" style="color:#2563eb">verified_user</span>
              </div>
              <div>
                <div class="settings-card-title">Authentification 2FA</div>
                <div class="settings-card-subtitle">Double authentification via SMS ou TOTP</div>
              </div>
            </div>
            <div class="settings-card-body">
              @foreach([
                ['key'=>'enable_2fa','label'=>'Activer la double authentification','desc'=>'Requis à chaque connexion depuis un nouvel appareil'],
                ['key'=>'allow_multi_sessions','label'=>'Sessions multiples','desc'=>'Permettre la connexion depuis plusieurs appareils'],
                ['key'=>'log_access','label'=>'Journaux d\'accès','desc'=>'Enregistrer chaque connexion avec IP et heure'],
              ] as $n)
                <div class="settings-toggle-row">
                  <div>
                    <div class="settings-toggle-label">{{ $n['label'] }}</div>
                    <div class="settings-toggle-desc">{{ $n['desc'] }}</div>
                  </div>
                  <input type="hidden" name="{{ $n['key'] }}" value="0">
                  <label class="toggle-switch">
                    <input type="checkbox" name="{{ $n['key'] }}" value="1" {{ \App\Models\Setting::getValue($n['key'], '0') === '1' ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
              @endforeach
              <div style="margin-top:16px">
                <button type="submit" class="adm-btn adm-btn-primary">
                  <span class="material-symbols-outlined">save</span>
                  Enregistrer
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      {{-- ── AVANCÉ ── --}}
      <div class="settings-section" id="section-avance">
        <form method="POST" action="{{ route('admin.settings.update') }}">
          @csrf
          <div class="settings-card">
            <div class="settings-card-header">
              <div class="settings-card-header-icon" style="background:var(--adm-bg)">
                <span class="material-symbols-outlined" style="color:var(--adm-text3)">build</span>
              </div>
              <div>
                <div class="settings-card-title">Mode maintenance</div>
                <div class="settings-card-subtitle">Désactive temporairement le frontend pour les visiteurs</div>
              </div>
            </div>
            <div class="settings-card-body">
              <div class="settings-toggle-row">
                <div>
                  <div class="settings-toggle-label">Activer le mode maintenance</div>
                  <div class="settings-toggle-desc">Les visiteurs verront une page "Maintenance en cours"</div>
                </div>
                <input type="hidden" name="maintenance_mode" value="0">
                <label class="toggle-switch">
                  <input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\Setting::getValue('maintenance_mode', '0') === '1' ? 'checked' : '' }}>
                  <span class="toggle-slider"></span>
                </label>
              </div>
              <div class="settings-toggle-row">
                <div>
                  <div class="settings-toggle-label">Inscriptions vendeurs ouvertes</div>
                  <div class="settings-toggle-desc">Autoriser de nouveaux artisans à créer un compte vendeur</div>
                </div>
                <input type="hidden" name="seller_registration_open" value="0">
                <label class="toggle-switch">
                  <input type="checkbox" name="seller_registration_open" value="1" {{ \App\Models\Setting::getValue('seller_registration_open', '1') === '1' ? 'checked' : '' }}>
                  <span class="toggle-slider"></span>
                </label>
              </div>
              <div class="settings-toggle-row">
                <div>
                  <div class="settings-toggle-label">Inscriptions acheteurs ouvertes</div>
                  <div class="settings-toggle-desc">Autoriser de nouveaux utilisateurs à créer un compte</div>
                </div>
                <input type="hidden" name="buyer_registration_open" value="0">
                <label class="toggle-switch">
                  <input type="checkbox" name="buyer_registration_open" value="1" {{ \App\Models\Setting::getValue('buyer_registration_open', '1') === '1' ? 'checked' : '' }}>
                  <span class="toggle-slider"></span>
                </label>
              </div>
              <div style="margin-top:16px">
                <button type="submit" class="adm-btn adm-btn-primary">
                  <span class="material-symbols-outlined">save</span>
                  Enregistrer
                </button>
              </div>
            </div>
          </div>
        </form>

        <div class="danger-zone">
          <div class="danger-zone-header">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">warning</span>
            <div class="danger-zone-header-title">Zone dangereuse</div>
          </div>
          <div class="danger-zone-body">
            <p style="font-size:13px;color:var(--adm-text2);margin-bottom:16px">Ces actions sont irréversibles. Procédez avec extrême prudence.</p>
            <div style="display:flex;flex-wrap:wrap;gap:10px">
              <form method="POST" action="{{ route('admin.settings.update') }}" style="display:inline">
                @csrf
                <input type="hidden" name="action" value="clear_cache">
                <button type="submit" class="danger-btn">
                  <span class="material-symbols-outlined">delete_sweep</span>
                  Vider le cache
                </button>
              </form>
              <form method="POST" action="{{ route('admin.settings.update') }}" style="display:inline">
                @csrf
                <input type="hidden" name="action" value="reset_logs">
                <button type="submit" class="danger-btn">
                  <span class="material-symbols-outlined">history</span>
                  Réinitialiser les logs
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
  function switchSection(id, linkEl) {
    document.querySelectorAll('.settings-section').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(el => el.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    linkEl.classList.add('active');
  }
  function pickColor(color) {
    document.querySelector('input[name="accent_color"]').value = color;
    showToast('Couleur ' + color + ' sélectionnée.', 'info');
  }
  </script>

@endsection
