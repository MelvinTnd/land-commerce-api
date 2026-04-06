@extends('admin.layouts.app')

@section('title', isset($article) ? 'Modifier l\'article' : 'Nouvel Article')

@section('styles')
<style>
  .form-card { background:var(--adm-card); border:1px solid var(--adm-border); border-radius:var(--adm-radius); padding:28px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .form-full  { grid-column:span 2; }
  .form-group { display:flex; flex-direction:column; gap:6px; }
  .form-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--adm-text3); }
  .form-input, .form-textarea, .form-select-full {
    padding:10px 14px; border-radius:8px;
    border:1px solid var(--adm-border); background:var(--adm-bg);
    font-size:13.5px; color:var(--adm-text); font-family:inherit;
    outline:none; transition:border-color 0.18s;
    width:100%;
  }
  .form-input:focus, .form-textarea:focus, .form-select-full:focus {
    border-color:var(--adm-green); background:#fff;
    box-shadow:0 0 0 3px rgba(27,107,58,0.08);
  }
  .form-textarea { resize:vertical; min-height:140px; }
  .form-textarea.tall { min-height:260px; }
  .form-error { font-size:11.5px; color:var(--adm-red); font-weight:600; margin-top:2px; }

  .status-radio-group { display:flex; gap:10px; flex-wrap:wrap; }
  .status-radio label {
    display:flex; align-items:center; gap:8px;
    padding:9px 16px; border-radius:8px;
    border:2px solid var(--adm-border); cursor:pointer;
    font-size:13px; font-weight:600;
    transition:all 0.15s; user-select:none;
  }
  .status-radio input[type=radio] { display:none; }
  .status-radio input:checked + label[data-value="publié"]     { border-color:var(--adm-green); background:var(--adm-green-soft); color:var(--adm-green); }
  .status-radio input:checked + label[data-value="brouillon"]  { border-color:#9ca3af; background:#f3f4f6; color:#374151; }
  .status-radio input:checked + label[data-value="en_attente"] { border-color:var(--adm-gold); background:var(--adm-gold-soft); color:var(--adm-gold); }
  .status-dot { width:8px; height:8px; border-radius:50%; }

  .image-preview { width:100%; height:150px; border-radius:8px; object-fit:cover; display:none; border:1px solid var(--adm-border); margin-top:8px; }
  .image-preview.show { display:block; }

  .form-footer { display:flex; align-items:center; justify-content:space-between; padding-top:20px; border-top:1px solid var(--adm-border); margin-top:20px; }
  .adm-flash-success { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:600; margin-bottom:16px; background:var(--adm-green-soft); color:var(--adm-green); border:1px solid #b7dfc6; }
  .adm-flash-success .material-symbols-outlined { font-size:18px; font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }
</style>
@endsection

@section('content')

  @if(session('success'))
    <div class="adm-flash-success">
      <span class="material-symbols-outlined">check_circle</span>
      {{ session('success') }}
    </div>
  @endif

  <div class="adm-page-header">
    <div>
      <h1>{{ isset($article) ? 'Modifier l\'article' : 'Nouvel Article' }}</h1>
      <p>
        @if(isset($article))
          Modifiez le contenu — passer en <strong>Publié</strong> le rend immédiatement visible sur le frontend.
        @else
          Créez un article — choisissez <strong>Publié</strong> pour le rendre visible sur le frontend.
        @endif
      </p>
    </div>
    <a href="{{ route('admin.blog') }}" class="adm-btn adm-btn-outline">
      <span class="material-symbols-outlined">arrow_back</span>
      Retour
    </a>
  </div>

  <form method="POST"
        action="{{ isset($article) ? route('admin.blog.update', $article->id) : route('admin.blog.store') }}">
    @csrf
    @isset($article) @method('PUT') @endisset

    <div class="form-card">
      <div class="form-grid">

        {{-- Titre --}}
        <div class="form-group form-full">
          <label class="form-label">Titre de l'article *</label>
          <input type="text" name="titre" class="form-input"
                 placeholder="Ex: Les secrets du tissage Kanvô..."
                 value="{{ old('titre', $article->titre ?? '') }}" required>
          @error('titre') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Description courte --}}
        <div class="form-group form-full">
          <label class="form-label">Description / Accroche</label>
          <textarea name="description" class="form-textarea"
                    placeholder="Courte description affichée dans les listings...">{{ old('description', $article->description ?? '') }}</textarea>
        </div>

        {{-- Contenu --}}
        <div class="form-group form-full">
          <label class="form-label">Contenu complet *</label>
          <textarea name="content" class="form-textarea tall"
                    placeholder="Rédigez le contenu complet de l'article ici..." required>{{ old('content', $article->content ?? '') }}</textarea>
          @error('content') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        {{-- Auteur --}}
        <div class="form-group">
          <label class="form-label">Auteur</label>
          <input type="text" name="auteur" class="form-input"
                 placeholder="Nom de l'auteur"
                 value="{{ old('auteur', $article->auteur ?? 'Heritage Admin') }}">
        </div>

        {{-- Catégorie --}}
        <div class="form-group">
          <label class="form-label">Catégorie</label>
          <input type="text" name="categorie" class="form-input"
                 list="categories-list"
                 placeholder="Ex: Artisanat, Mode, Culture..."
                 value="{{ old('categorie', $article->categorie ?? '') }}">
          <datalist id="categories-list">
            @foreach($categories as $cat)
              <option value="{{ $cat }}">
            @endforeach
          </datalist>
        </div>

        {{-- Image URL --}}
        <div class="form-group form-full">
          <label class="form-label">Image de couverture (URL)</label>
          <input type="url" name="image" id="image-url" class="form-input"
                 placeholder="https://..."
                 value="{{ old('image', $article->image ?? '') }}"
                 oninput="previewImage(this.value)">
          <img id="img-preview" class="image-preview" src="" alt="Prévisualisation">
        </div>

        {{-- Temps de lecture --}}
        <div class="form-group">
          <label class="form-label">Temps de lecture (minutes)</label>
          <input type="number" name="read_time" class="form-input"
                 min="1" max="60" placeholder="Ex: 5"
                 value="{{ old('read_time', $article->read_time ?? '') }}">
        </div>

        {{-- Tags --}}
        <div class="form-group">
          <label class="form-label">Tags (séparés par des virgules)</label>
          <input type="text" name="tags" class="form-input"
                 placeholder="artisanat, bronze, bénin"
                 value="{{ old('tags', $article->tags ?? '') }}">
        </div>

        {{-- Featured --}}
        <div class="form-group">
          <label class="form-label">Article mis en avant</label>
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 14px;border:1px solid var(--adm-border);border-radius:8px;background:var(--adm-bg)">
            <input type="checkbox" name="featured" value="1"
                   {{ old('featured', $article->featured ?? false) ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:var(--adm-green)">
            <span style="font-size:13px;font-weight:500">Afficher en priorité sur le frontend</span>
          </label>
        </div>

        {{-- Statut --}}
        <div class="form-group form-full">
          <label class="form-label">Statut de publication *</label>
          <div class="status-radio-group">
            @foreach(['publié' => ['🟢','Publié','Visible sur le frontend'], 'brouillon' => ['⚫','Brouillon','Non visible'], 'en_attente' => ['🟡','En attente','En révision']] as $val => $info)
              <div class="status-radio">
                <input type="radio" name="statut" id="s_{{ $val }}" value="{{ $val }}"
                       {{ old('statut', $article->statut ?? 'brouillon') === $val ? 'checked' : '' }}>
                <label for="s_{{ $val }}" data-value="{{ $val }}">
                  {{ $info[0] }} {{ $info[1] }}
                  <small style="display:block;font-size:10px;font-weight:400;opacity:0.7">{{ $info[2] }}</small>
                </label>
              </div>
            @endforeach
          </div>
          @error('statut') <span class="form-error">{{ $message }}</span> @enderror
        </div>

      </div>{{-- /form-grid --}}

      <div class="form-footer">
        <a href="{{ route('admin.blog') }}" class="adm-btn adm-btn-outline">Annuler</a>
        <div style="display:flex;gap:10px">
          @isset($article)
            <button type="submit" name="statut" value="brouillon"
                    class="adm-btn adm-btn-outline"
                    onclick="document.querySelector('[name=statut]:checked') || (document.getElementById('s_brouillon').checked=true)">
              Enregistrer en brouillon
            </button>
          @endisset
          <button type="submit" class="adm-btn adm-btn-primary">
            <span class="material-symbols-outlined">save</span>
            {{ isset($article) ? 'Mettre à jour' : 'Publier l\'article' }}
          </button>
        </div>
      </div>
    </div>
  </form>

  <script>
    function previewImage(url) {
      const img = document.getElementById('img-preview');
      if (url && url.startsWith('http')) {
        img.src = url;
        img.classList.add('show');
      } else {
        img.classList.remove('show');
      }
    }
    // Init preview on edit
    const existing = document.getElementById('image-url').value;
    if (existing) previewImage(existing);
  </script>

@endsection
