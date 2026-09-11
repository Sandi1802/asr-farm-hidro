@extends('admin.layout')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="margin: 0;">Kotak Masuk Pesan</h1>
            <p style="color: #7f8c8d; margin: 0.25rem 0 0 0;">Pesan yang masuk dari halaman kontak website.</p>
        </div>
        @php $unread = $messages->where('is_read', false)->count(); @endphp
        @if($unread > 0)
            <span style="background: #e74c3c; color: white; padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">
                {{ $unread }} Pesan Baru
            </span>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @if($messages->count() === 0)
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <p style="color: #aaa; font-size: 1.1rem;">Belum ada pesan yang masuk.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($messages as $message)
            <div style="background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 1.5rem; border-left: 4px solid {{ $message->is_read ? '#bdc3c7' : '#27ae60' }};">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <strong style="font-size: 1.05rem;">{{ $message->name }}</strong>
                            @if(!$message->is_read)
                                <span style="background: #27ae60; color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; font-weight: bold;">BARU</span>
                            @endif
                        </div>
                        <div style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 0.5rem;">
                            <span>{{ $message->email }}</span> &bull; <span>{{ $message->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 0.75rem;">
                            Subjek: {{ $message->subject }}
                        </div>
                        <div style="color: #555; line-height: 1.65; white-space: pre-wrap;">{{ $message->message }}</div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end; min-width: 150px;">
                        @if(!$message->is_read)
                            <form action="/admin/messages/{{ $message->id }}/read" method="POST">
                                @csrf
                                <button type="submit" style="background: #3498db; color: white; border: none; padding: 0.4rem 0.9rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem; width: 100%;">Tandai Sudah Dibaca</button>
                            </form>
                        @else
                            <span style="color: #bdc3c7; font-size: 0.8rem; font-style: italic;">Sudah dibaca</span>
                        @endif
                        <a href="mailto:{{ $message->email }}" style="background: #27ae60; color: white; text-decoration: none; padding: 0.4rem 0.9rem; border-radius: 4px; font-size: 0.85rem; text-align: center; width: 100%; box-sizing: border-box;">Balas via Email</a>
                        <form action="/admin/messages/{{ $message->id }}/delete" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                            @csrf
                            <button type="submit" style="background: none; border: 1px solid #e74c3c; color: #e74c3c; padding: 0.4rem 0.9rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem; width: 100%;">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
