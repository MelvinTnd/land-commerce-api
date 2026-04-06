@extends('admin.layouts.app')

@section('title', 'Gestion Promotions')

@section('styles')
<style>
  .promo-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
  .promo-stat { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); padding:18px 22px; display:flex; align-items:center; gap:14px; animation:fadeInUp 0.3s ease both; transition:box-shadow 0.15s; }
  .promo-stat:hover { box-shadow:var(--adm-shadow-md); }
  .promo-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .promo-stat-icon .material-symbols-outlined { font-size:22px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
  .promo-stat-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); }
  .promo-stat-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:26px; font-weight:800; margin-top:3px; }

  .promo-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; }
  .promo-card {
    background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius);
    overflow:hidden; transition:box-shadow 0.18s,transform 0.18s; animation:fadeInUp 0.3s ease both;
  }
  .promo-card:hover { box-shadow:var(--adm-shadow-md); transform:translateY(-2px); }
  .promo-card.inactive { opacity:0.65; }

  .promo-img { height:130px; background:linear-gradient(135deg,var(--adm-green) 0%,var(--adm-green-mid) 100%); position:relative; overflow:hidden; }
  .promo-img img { width:100%; height:100%; object-fit:cover; }
  .promo-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }
  .promo-img-placeholder .material-symbols-outlined { font-size:40px; color:rgba(255,255,255,0.35); font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24; }
  .promo-badge-discount { position:absolute; top:10px; left:10px; background:#EF4444; color:#fff; font-size:13px; font-weight:800; padding:4px 12px; border-radius:20px; }
  .promo-badge-status  { position:absolute; top:10px; right:10px; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; }
  .promo-badge-status.active   { background:var(--adm-green-soft); color:var(--adm-green); }
  .promo-badge-status.inactive { background:var(--adm-red-soft); color:var(--adm-red); }
  .promo-badge-status.expired  { background:#f3f4f6; color:#6b7280; }

  .promo-body { padding:14px 16px; }
  .promo-title { font-size:14.5px; font-weight:700; color:var(--adm-text); margin-bottom:4px; }
  .promo-desc { font-size:12.5px; color:var(--adm-text2); line-height:1.5; margin-bottom:10px; }
  .promo-meta { display:flex; align-items:center; gap:10px; font-size:11.5px; color:var(--adm-text3); }
  .promo-meta .material-symbols-outlined { font-size:14px; }

  .promo-footer { display:flex; align-items:center; gap:6px; padding:10px 16px; border-top:1px solid var(--adm-border); background:var(--adm-bg); }

  /* Modal */
  .adm-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; display:flex; align-items:center; justify-content:center; opacity:0; visibility:hidden; transition:all 0.2s; }
  .adm-modal-overlay.open { opacity:1; visibility:visible; }
  .adm-modal { background:var(--adm-card); border-radius:14px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; transform:translateY(20px) scale(0.97); transition:transform 0.25s; box-shadow:0 20px 60px rgba(0,0,0,0.18); margin:16px; }
  .adm-modal-overlay.open .adm-modal { transform:translateY(0) scale(1); }
  .adm-modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid var(--adm-border); }
  .adm-modal-header h3 { font-family:'Plus Jakarta Sans',sans-serif; font-size:17px; font-weight:700; }
  .adm-modal-close { background:none; border:none; cursor:pointer; color:var(--adm-text3); padding:4px; border-radius:6px; display:flex; align-items:center; transition:color 0.15s; }
  .adm-modal-close:hover { color:var(--adm-red); }
  .adm-modal-close .material-symbols-outlined { font-size:22px; }
  .adm-modal-body { padding:24px; }
  .adm-modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px; border-top:1px solid var(--adm-border); }

  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; }
  .form-group.full { grid-column:span 2; }
  .form-label { font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--adm-text3); }
  .form-input, .form-textarea, .form-select { padding:9px 12px; border:1px solid var(--adm-border); border-radius:8px; font-size:13px; font-family:inherit; color:var(--adm-text); background:var(--adm-bg); outline:none; transition:border-color 0.18s; width:100%; }
  .form-input:focus, .form-textarea:focus, .form-select:focus { border-color:var(--adm-green); background:#fff; box-shadow:0 0 0 3px rgba(27,107,58,0.08); }
  .form-textarea { resize:vertical; min-height:80px; }

  @media(max-width:700px) {
    .promo-grid { grid-template-columns:1fr; }
    .promo-stats { grid-template-columns:1fr 1fr; }
    .form-row { grid-template-columns:1fr; }
    .form-group.full { grid-column:span 1; }
  }
</style>
@endsection

@section('content')

  <div class="adm-page-header">
    <div>
      <h1>Promotions &amp; Ventes Flash</h1>
      <p>Créez et gérez des promotions — elles apparaissent directement sur <strong>/promotions</strong> du frontend.</p>
    </div>
    <button class="adm-btn adm-btn-primary" onclick="openModal('add-promo-modal')">
      <span class="material-symbols-outlined">add</span>
      Nouvelle Promotion
    </button>
  </div>

  {{-- STATS --}}
  <div class="promo-stats">
    <div class="promo-stat">
      <div class="promo-stat-icon" style="background:var(--adm-green-soft)">
        <span class="material-symbols-outlined" style="color:var(--adm-green)">local_offer</span>
      </div>
      <div>
        <div class="promo-stat-lbl">Actives</div>
        <div class="promo-stat-val">{{ $stats['active'] }}</div>
      </div>
    </div>
    <div class="promo-stat">
      <div class="promo-stat-icon" style="background:var(--adm-bg)">
        <span class="material-symbols-outlined" style="color:var(--adm-text3)">sell</span>
      </div>
      <div>
        <div class="promo-stat-lbl">Total</div>
        <div class="promo-stat-val">{{ $stats['total'] }}</div>
      </div>
    </div>
    <div class="promo-stat">
      <div class="promo-stat-icon" style="background:var(--adm-red-soft)">
        <span class="material-symbols-outlined" style="color:var(--adm-red)">event_busy</span>
      </div>
      <div>
        <div class="promo-stat-lbl">Expirées</div>
        <div class="promo-stat-val">{{ $stats['expired'] }}</div>
      </div>
    </div>
  </div>

  {{-- PROMO CARDS --}}
  @if($promotions->count() > 0)
    <div class="promo-grid">
      @foreach($promotions as $promo)
        @php
          $expired  = $promo->date_fin->isPast();
          $isActive = $promo->actif && !$expired;
          $statusClass = $expired ? 'expired' : ($promo->actif ? 'active' : 'inactive');
          $statusLabel = $expired ? 'Expirée' : ($promo->actif ? 'Active' : 'Inactive');
        @endphp
        <div class="promo-card {{ !$isActive ? 'inactive' : '' }}">
          <div class="promo-img"
               style="{{ !$promo->image ? 'background:linear-gradient(135deg,var(--adm-green) 0%,#2E8B57 100%)' : '' }}">
            @if($promo->image)
              <img src="{{ $promo->image }}" alt="{{ $promo->titre }}">
            @else
              <div class="promo-img-placeholder">
                <span class="material-symbols-outlined">local_offer</span>
              </div>
            @endif
            <span class="promo-badge-discount">-{{ number_format($promo->reduction, 0) }}%</span>
            <span class="promo-badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
          </div>

          <div class="promo-body">
            <div class="promo-title">{{ $promo->titre }}</div>
            @if($promo->description)
              <div class="promo-desc">{{ \Illuminate\Support\Str::limit($promo->description, 90) }}</div>
            @endif
            <div class="promo-meta">
              <span class="material-symbols-outlined">calendar_today</span>
              Jusqu'au {{ $promo->date_fin->format('d M Y') }}
              @if($promo->categorie)
                &bull;
                <span class="material-symbols-outlined">category</span>
                {{ $promo->categorie }}
              @endif
            </div>
          </div>

          <div class="promo-footer">
            {{-- Toggle actif --}}
            <form method="POST" action="{{ route('admin.promotions.toggle', $promo->id) }}">
              @csrf @method('PATCH')
              <button type="submit"
                      class="adm-btn adm-btn-sm {{ $promo->actif ? 'adm-btn-outline' : 'adm-btn-primary' }}"
                      title="{{ $promo->actif ? 'Désactiver' : 'Activer' }}">
                <span class="material-symbols-outlined" style="font-size:14px">
                  {{ $promo->actif ? 'pause_circle' : 'play_circle' }}
                </span>
                {{ $promo->actif ? 'Désactiver' : 'Activer' }}
              </button>
            </form>

            <button class="adm-icon-btn" title="Modifier"
                    onclick="openEditModal({{ json_encode($promo) }})">
              <span class="material-symbols-outlined">edit</span>
            </button>

            <form method="POST" action="{{ route('admin.promotions.destroy', $promo->id) }}"
                  style="margin-left:auto"
                  onsubmit="return confirmDelete(event, '{{ $promo->titre }}')">
              @csrf @method('DELETE')
              <button class="adm-icon-btn" style="color:var(--adm-red)" title="Supprimer">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="adm-card" style="padding:60px;text-align:center;color:var(--adm-text3)">
      <span class="material-symbols-outlined" style="font-size:56px;display:block;margin-bottom:12px;opacity:0.3;font-variation-settings:'FILL' 1,'wght' 300,'GRAD' 0,'opsz' 24">local_offer</span>
      <p style="font-size:15px;font-weight:600;margin-bottom:8px">Aucune promotion créée</p>
      <p style="font-size:13px;margin-bottom:20px">Créez votre première vente flash pour attirer les clients.</p>
      <button class="adm-btn adm-btn-primary" onclick="openModal('add-promo-modal')">
        <span class="material-symbols-outlined">add</span>
        Créer une promotion
      </button>
    </div>
  @endif

  {{-- ═══════════════════════════════════════
       MODAL AJOUT PROMOTION
  ═══════════════════════════════════════ --}}
  <div class="adm-modal-overlay" id="add-promo-modal">
    <div class="adm-modal">
      <div class="adm-modal-header">
        <h3>Nouvelle Promotion</h3>
        <button class="adm-modal-close" onclick="closeModal('add-promo-modal')">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.promotions.store') }}">
        @csrf
        <div class="adm-modal-body">
          <div class="form-row">
            <div class="form-group full">
              <label class="form-label">Titre *</label>
              <input type="text" name="titre" class="form-input" placeholder="Ex: Vente Flash Artisanat" required>
            </div>
            <div class="form-group full">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-textarea" placeholder="Décrivez la promotion..."></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Réduction (%) *</label>
              <input type="number" name="reduction" class="form-input" min="1" max="100" placeholder="Ex: 30" required>
            </div>
            <div class="form-group">
              <label class="form-label">Catégorie</label>
              <select name="categorie" class="form-select">
                <option value="">— Toutes catégories —</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Date de début</label>
              <input type="date" name="date_debut" class="form-input">
            </div>
            <div class="form-group">
              <label class="form-label">Date de fin *</label>
              <input type="date" name="date_fin" class="form-input" required>
            </div>
            <div class="form-group full">
              <label class="form-label">Image de couverture (URL)</label>
              <input type="url" name="image" class="form-input" placeholder="https://...">
            </div>
            <div class="form-group full">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;background:var(--adm-bg)">
                <input type="checkbox" name="actif" value="1" checked style="width:16px;height:16px;accent-color:var(--adm-green)">
                <span style="font-size:13px;font-weight:500">Activer immédiatement (visible sur le frontend)</span>
              </label>
            </div>
          </div>
        </div>
        <div class="adm-modal-footer">
          <button type="button" class="adm-btn adm-btn-outline" onclick="closeModal('add-promo-modal')">Annuler</button>
          <button type="submit" class="adm-btn adm-btn-primary">
            <span class="material-symbols-outlined">save</span>
            Créer la promotion
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL ÉDITION --}}
  <div class="adm-modal-overlay" id="edit-promo-modal">
    <div class="adm-modal">
      <div class="adm-modal-header">
        <h3>Modifier la Promotion</h3>
        <button class="adm-modal-close" onclick="closeModal('edit-promo-modal')">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      <form method="POST" id="edit-promo-form">
        @csrf @method('PUT')
        <div class="adm-modal-body">
          <div class="form-row">
            <div class="form-group full">
              <label class="form-label">Titre *</label>
              <input type="text" name="titre" id="edit-titre" class="form-input" required>
            </div>
            <div class="form-group full">
              <label class="form-label">Description</label>
              <textarea name="description" id="edit-desc" class="form-textarea"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Réduction (%) *</label>
              <input type="number" name="reduction" id="edit-reduction" class="form-input" min="1" max="100" required>
            </div>
            <div class="form-group">
              <label class="form-label">Catégorie</label>
              <input type="text" name="categorie" id="edit-categorie" class="form-input">
            </div>
            <div class="form-group">
              <label class="form-label">Date de fin *</label>
              <input type="date" name="date_fin" id="edit-date-fin" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label">Image (URL)</label>
              <input type="url" name="image" id="edit-image" class="form-input" placeholder="https://...">
            </div>
            <div class="form-group full">
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;background:var(--adm-bg)">
                <input type="checkbox" name="actif" id="edit-actif" value="1" style="width:16px;height:16px;accent-color:var(--adm-green)">
                <span style="font-size:13px;font-weight:500">Promotion active</span>
              </label>
            </div>
          </div>
        </div>
        <div class="adm-modal-footer">
          <button type="button" class="adm-btn adm-btn-outline" onclick="closeModal('edit-promo-modal')">Annuler</button>
          <button type="submit" class="adm-btn adm-btn-primary">
            <span class="material-symbols-outlined">save</span>
            Mettre à jour
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL CONFIRMATION SUPPRESSION (global dans layout) --}}

  <script>
  function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
  }
  // Fermer en cliquant hors du modal
  document.querySelectorAll('.adm-modal-overlay').forEach(el => {
    el.addEventListener('click', e => {
      if (e.target === el) closeModal(el.id);
    });
  });

  function openEditModal(promo) {
    document.getElementById('edit-promo-form').action =
      '/admin/promotions/' + promo.id;
    document.getElementById('edit-titre').value     = promo.titre || '';
    document.getElementById('edit-desc').value      = promo.description || '';
    document.getElementById('edit-reduction').value = promo.reduction || '';
    document.getElementById('edit-categorie').value = promo.categorie || '';
    document.getElementById('edit-image').value     = promo.image || '';
    document.getElementById('edit-date-fin').value  = promo.date_fin || '';
    document.getElementById('edit-actif').checked   = !!promo.actif;
    openModal('edit-promo-modal');
  }

  function confirmDelete(e, titre) {
    e.preventDefault();
    if (confirm('Supprimer la promotion « ' + titre + ' » ?\nCette action est irréversible.')) {
      e.target.closest('form').submit();
    }
  }
  </script>

@endsection
