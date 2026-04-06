@extends('admin.layouts.app')

@section('title', 'Gestion Utilisateurs')

@section('styles')
<style>
  .users-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:24px; }
  .user-stat { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); padding:18px 20px; display:flex; align-items:center; gap:14px; transition:box-shadow 0.15s; animation:fadeInUp 0.3s ease both; }
  .user-stat:hover { box-shadow:var(--adm-shadow-md); }
  .user-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .user-stat-icon .material-symbols-outlined { font-size:22px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
  .user-stat-icon.green  { background:var(--adm-green-soft); color:var(--adm-green); }
  .user-stat-icon.blue   { background:#eff6ff; color:#2563eb; }
  .user-stat-icon.orange { background:var(--adm-orange-soft); color:var(--adm-orange); }
  .user-stat-icon.red    { background:var(--adm-red-soft); color:var(--adm-red); }
  .user-stat-lbl { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); }
  .user-stat-val { font-family:'Plus Jakarta Sans',sans-serif; font-size:24px; font-weight:800; margin-top:2px; }

  .user-av { width:38px; height:38px; border-radius:50%; background:var(--adm-green-soft); color:var(--adm-green); font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .user-name { font-size:13.5px; font-weight:700; }
  .user-email { font-size:12px; color:var(--adm-text3); margin-top:1px; }

  .role-badge { padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; }
  .role-admin  { background:#eff6ff; color:#2563eb; }
  .role-seller { background:var(--adm-green-soft); color:var(--adm-green); }
  .role-buyer  { background:var(--adm-bg); color:var(--adm-text2); border:1px solid var(--adm-border); }

  .search-bar { display:flex; gap:10px; margin-bottom:18px; align-items:center; }
  .search-bar input { flex:1; max-width:320px; padding:9px 14px; border:1px solid var(--adm-border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; background:var(--adm-card); }
  .search-bar input:focus { border-color:var(--adm-green); }

  @media(max-width:900px) { .users-stats { grid-template-columns:repeat(2,1fr); } }
</style>
@endsection

@section('content')

  <div class="adm-page-header">
    <div>
      <h1>Gestion Utilisateurs</h1>
      <p>Gérez les comptes, rôles et accès des membres de la plateforme BéninMarket.</p>
    </div>
    <button class="adm-btn adm-btn-primary">
      <span class="material-symbols-outlined">person_add</span>
      Inviter un utilisateur
    </button>
  </div>

  {{-- Stat cards --}}
  <div class="users-stats">
    <div class="user-stat">
      <div class="user-stat-icon green">
        <span class="material-symbols-outlined">group</span>
      </div>
      <div>
        <div class="user-stat-lbl">Total Membres</div>
        <div class="user-stat-val">{{ number_format($stats['total']) }}</div>
      </div>
    </div>
    <div class="user-stat">
      <div class="user-stat-icon blue">
        <span class="material-symbols-outlined">admin_panel_settings</span>
      </div>
      <div>
        <div class="user-stat-lbl">Admins</div>
        <div class="user-stat-val">{{ $stats['admins'] }}</div>
      </div>
    </div>
    <div class="user-stat">
      <div class="user-stat-icon orange">
        <span class="material-symbols-outlined">storefront</span>
      </div>
      <div>
        <div class="user-stat-lbl">Vendeurs</div>
        <div class="user-stat-val">{{ $stats['sellers'] }}</div>
      </div>
    </div>
    <div class="user-stat">
      <div class="user-stat-icon red">
        <span class="material-symbols-outlined">new_releases</span>
      </div>
      <div>
        <div class="user-stat-lbl">Nouveaux (30j)</div>
        <div class="user-stat-val">{{ $stats['newMonth'] }}</div>
      </div>
    </div>
  </div>

  {{-- Search --}}
  <form method="GET" action="{{ route('admin.users') }}" class="search-bar">
    <input type="text" name="search" placeholder="Rechercher par nom ou email…"
           value="{{ request('search') }}">
    <select name="role" class="adm-select-sm"
            onchange="this.form.submit()"
            style="padding:9px 28px 9px 12px;border-radius:8px;border:1px solid var(--adm-border);background:var(--adm-card);font-size:13px;font-family:inherit;outline:none;">
      <option value="">Tous les rôles</option>
      <option value="admin"  {{ request('role')=='admin'  ? 'selected':'' }}>Admins</option>
      <option value="seller" {{ request('role')=='seller' ? 'selected':'' }}>Vendeurs</option>
      <option value="buyer"  {{ request('role')=='buyer'  ? 'selected':'' }}>Acheteurs</option>
    </select>
    <button class="adm-btn adm-btn-outline adm-btn-sm">
      <span class="material-symbols-outlined">search</span>
      Rechercher
    </button>
  </form>

  {{-- Table --}}
  <div class="adm-card">
    <div class="adm-table-wrap">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Utilisateur</th>
            <th>Rôle</th>
            <th>Boutique</th>
            <th>Inscrit le</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="user-av">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                  </div>
                </div>
              </td>
              <td>
                @php
                  $role = $user->role ?? ($user->shop ? 'seller' : 'buyer');
                  $roleMap   = ['admin'=>'role-admin','seller'=>'role-seller','buyer'=>'role-buyer'];
                  $roleLabel = ['admin'=>'Admin','seller'=>'Vendeur','buyer'=>'Acheteur'];
                @endphp
                <span class="role-badge {{ $roleMap[$role] ?? 'role-buyer' }}">
                  {{ $roleLabel[$role] ?? 'Acheteur' }}
                </span>
              </td>
              <td style="font-size:13px;color:var(--adm-text2)">
                @if($user->shop)
                  <a href="{{ route('admin.sellers.show', $user->shop->id) }}"
                     style="color:var(--adm-green);font-weight:600">
                    {{ $user->shop->name }}
                  </a>
                @else
                  <span style="color:var(--adm-text3)">—</span>
                @endif
              </td>
              <td style="font-size:12.5px;color:var(--adm-text3);white-space:nowrap">
                {{ $user->created_at->format('d M Y') }}
              </td>
              <td>
              <div class="adm-actions-cell">
                  {{-- Suspendre / Réactiver --}}
                  <form method="POST"
                        action="{{ route('admin.users.toggle', $user->id) }}"
                        style="display:inline">
                    @csrf @method('PATCH')
                    <button class="adm-icon-btn"
                            title="{{ $user->is_active ? 'Suspendre' : 'Réactiver' }}"
                            style="{{ !$user->is_active ? 'color:var(--adm-green)' : 'color:var(--adm-gold)' }}">
                      <span class="material-symbols-outlined">{{ $user->is_active ? 'block' : 'check_circle' }}</span>
                    </button>
                  </form>

                  {{-- Supprimer --}}
                  @if($user->role !== 'admin')
                    <form method="POST"
                          action="{{ route('admin.users.destroy', $user->id) }}"
                          onsubmit="return false"
                          data-confirm-title="Supprimer {{ addslashes($user->name) }} ?"
                          data-confirm-msg="Cette action est irréversible. La boutique et les produits seront également supprimés.">
                      @csrf @method('DELETE')
                      <button class="adm-icon-btn"
                              style="color:var(--adm-red)"
                              title="Supprimer"
                              onclick="confirmAction(this.closest('form'), this.closest('form').dataset.confirmTitle, this.closest('form').dataset.confirmMsg)">
                        <span class="material-symbols-outlined">delete</span>
                      </button>
                    </form>
                  @else
                    <span class="adm-icon-btn" title="Admin protégé" style="opacity:0.3;cursor:default">
                      <span class="material-symbols-outlined">shield</span>
                    </span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center;padding:40px;color:var(--adm-text3)">
                <span class="material-symbols-outlined"
                      style="font-size:40px;display:block;margin-bottom:8px;opacity:0.4">
                  group
                </span>
                Aucun utilisateur trouvé.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid var(--adm-border);font-size:12.5px;color:var(--adm-text3)">
        <span>{{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ number_format($users->total()) }} utilisateurs</span>
        <div style="display:flex;gap:4px">
          @if(!$users->onFirstPage())
            <a href="{{ $users->previousPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif
          @foreach(range(1, min($users->lastPage(),5)) as $p)
            <a href="{{ $users->url($p) }}"
               class="adm-page-btn {{ $users->currentPage()===$p?'active':'' }}">{{ $p }}</a>
          @endforeach
          @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="adm-page-btn">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          @endif
        </div>
      </div>
    @endif
  </div>

  <style>
    .adm-page-btn { width:32px;height:32px;border-radius:8px;border:1px solid var(--adm-border);background:var(--adm-card);display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:600;cursor:pointer;color:var(--adm-text2);transition:all 0.15s;text-decoration:none; }
    .adm-page-btn:hover { border-color:var(--adm-green);color:var(--adm-green); }
    .adm-page-btn.active { background:var(--adm-green);border-color:var(--adm-green);color:#fff; }
    .adm-page-btn .material-symbols-outlined { font-size:16px; }
  </style>

@endsection
