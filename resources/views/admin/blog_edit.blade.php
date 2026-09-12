@extends('admin.layout')

@section('content')
<div>
    <h1>Edit Artikel Blog: {{ $post->title }}</h1>
    
    <div class="card" style="padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form method="POST" action="/admin/blog/{{ $post->id }}/update" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Judul Artikel</label>
                <input type="text" name="title" value="{{ $post->title }}" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Upload Gambar Cover (Atau isi URL di bawahnya)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 0.5rem;">
                <input type="text" name="image" value="{{ $post->image }}" placeholder="URL Gambar Cover (Opsional jika sudah upload)" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Isi Artikel</label>
                <textarea name="content" rows="10" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">{{ $post->content }}</textarea>
            </div>

            <button type="submit" style="background: var(--color-primary); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 4px; font-weight: bold; cursor: pointer;">Update Artikel</button>
            <a href="/admin/blog" style="margin-left: 1rem; color: #666; text-decoration: none;">Batal</a>
        </form>
    </div>
</div>

<!-- Summernote Editor (No API Key Required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
  $(document).ready(function() {
      $('textarea[name="content"]').summernote({
          height: 400,
          toolbar: [
              ['font', ['bold', 'italic', 'underline', 'clear']],
              ['para', ['ul', 'ol', 'paragraph']],
              ['insert', ['link']],
              ['view', ['fullscreen', 'codeview']]
          ]
      });
  });
</script>
@endsection
