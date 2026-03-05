@extends('layouts.app')
@section('title', 'Edit Category')

@push('styles')
<style>
    :root {
        --blue:     #003087;
        --blue-mid: #1a4db3;
        --border:   rgba(255,255,255,0.07);
        --card:     rgba(255,255,255,0.03);
        --muted:    #6B7280;
        --muted-lt: #9CA3AF;
        --text:     #E8E4DC;
    }

    .back-link {
        display: inline-flex; align-items: center; gap: 7px;
        font-size: 13px; color: var(--muted); text-decoration: none;
        margin-bottom: 20px; transition: color 0.15s;
    }
    .back-link:hover { color: var(--text); }

    .form-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; max-width: 520px; }

    .form-card-header {
        padding: 20px 28px 16px; border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,0.02);
        display: flex; align-items: center; gap: 12px;
    }
    .form-card-icon { width: 38px; height: 38px; background: rgba(244,169,0,0.12); border: 1px solid rgba(244,169,0,0.25); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .form-card-title { font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; color: #fff; }
    .form-card-sub { font-size: 12px; color: var(--muted); margin-top: 1px; }

    /* Editing badge */
    .editing-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(244,169,0,0.1); border: 1px solid rgba(244,169,0,0.25);
        border-radius: 999px; padding: 3px 10px;
        font-size: 11px; font-weight: 600; color: #FCD34D;
        font-family: 'IBM Plex Mono', monospace;
        margin-left: auto; flex-shrink: 0;
    }
    .editing-dot { width: 6px; height: 6px; background: #F4A900; border-radius: 50%; }

    .form-card-body { padding: 28px; }

    .field { display: flex; flex-direction: column; gap: 7px; margin-bottom: 24px; }
    .field:last-of-type { margin-bottom: 0; }
    .field-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--muted-lt); }
    .field-input {
        background: rgba(255,255,255,0.05); border: 1px solid var(--border);
        border-radius: 10px; padding: 11px 14px; font-size: 14px;
        font-family: 'DM Sans', sans-serif; color: var(--text);
        outline: none; transition: all 0.2s; width: 100%;
    }
    .field-input:focus { border-color: rgba(244,169,0,0.45); background: rgba(244,169,0,0.04); box-shadow: 0 0 0 3px rgba(244,169,0,0.08); }
    .field-input.has-error { border-color: rgba(204,0,1,0.5); background: rgba(204,0,1,0.05); }
    .field-error { font-size: 12px; color: #FCA5A5; display: flex; align-items: center; gap: 5px; }
    .field-hint { font-size: 12px; color: var(--muted); }

    .form-actions {
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        padding-top: 20px; border-top: 1px solid var(--border); margin-top: 28px;
    }
    .btn-cancel { padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; text-decoration: none; color: var(--muted-lt); background: rgba(255,255,255,0.04); border: 1px solid var(--border); transition: all 0.15s; }
    .btn-cancel:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .btn-save { padding: 10px 22px; border-radius: 10px; font-size: 13px; font-weight: 700; font-family: 'DM Sans', sans-serif; color: #fff; background: linear-gradient(135deg, #92400E, #F4A900); border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 3px 12px rgba(244,169,0,0.25); display: inline-flex; align-items: center; gap: 7px; }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(244,169,0,0.35); }
    .btn-save:active { transform: translateY(0); }
</style>
@endpush

@section('content')

<a href="{{ route('admin.categories.index') }}" class="back-link">← Back to Categories</a>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-icon">✏️</div>
        <div>
            <div class="form-card-title">Edit Category</div>
            <div class="form-card-sub">Update the category name</div>
        </div>
        <div class="editing-badge">
            <span class="editing-dot"></span>
            {{ $category->name }}
        </div>
    </div>

    <div class="form-card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name" class="field-label">Category Name</label>
                <input type="text" name="name" id="name"
                       class="field-input {{ $errors->has('name') ? 'has-error' : '' }}"
                       value="{{ old('name', $category->name) }}"
                       autofocus required>
                @error('name')
                <div class="field-error">⚠ {{ $message }}</div>
                @enderror
                <div class="field-hint">This name appears on products and in the POS terminal.</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.categories.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">💾 Update Category</button>
            </div>
        </form>
    </div>
</div>

@endsection