@extends('layouts.app')

@section('content')
<div class="content-container">
    <div class="header-action" style="margin-bottom: 2rem;">
        <h2 class="page-title">Profil Saya</h2>
    </div>

    @if(session('success'))
    <div style="background: rgba(22, 163, 74, 0.1); color: var(--asr-green); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(22, 163, 74, 0.2); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="ph ph-check-circle" style="font-size: 1.5rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="profile-layout" style="display: flex; gap: 2rem; max-width: 1000px; margin: 0 auto; flex-wrap: wrap;">
        
        <!-- Left Sidebar: Profile Info -->
        <div class="form-card" style="flex: 1; min-width: 250px; background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 1.5rem; box-shadow: var(--shadow-sm); text-align: center; height: fit-content;">
            @if($user->avatar)
                <div style="width: 90px; height: 90px; border-radius: 50%; margin: 0 auto 1rem; box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.3); overflow: hidden; border: 3px solid var(--card-bg);">
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @else
                <div style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, var(--asr-green), #14b8a6); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; margin: 0 auto 1rem; box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.3);">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif
            <h3 style="margin: 0; color: var(--text-main); font-size: 1.25rem; font-weight: 600;">{{ $user->name }}</h3>
            <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                <span style="display:inline-block; padding: 0.2rem 0.6rem; border-radius: 50px; background: {{ $user->roleBadgeColor() }}22; color: {{ $user->roleBadgeColor() }}; font-weight:600;">
                    {{ $user->roleLabel() }}
                </span>
            </div>
            
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color); text-align: left; font-size: 0.85rem;">
                <div style="margin-bottom: 0.75rem;">
                    <strong style="color: var(--text-main); display: block; margin-bottom: 0.1rem;">NIP</strong>
                    <span style="color: var(--text-muted);">{{ $user->nip ?? '-' }}</span>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <strong style="color: var(--text-main); display: block; margin-bottom: 0.1rem;">Username</strong>
                    <span style="color: var(--text-muted);">{{ $user->username }}</span>
                </div>
                <div>
                    <strong style="color: var(--text-main); display: block; margin-bottom: 0.1rem;">Email</strong>
                    <span style="color: var(--text-muted);">{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <!-- Right Content: Form -->
        <div class="form-card" style="flex: 2; min-width: 350px; background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 1.5rem; box-shadow: var(--shadow-sm);">
            <h3 style="margin-top: 0; margin-bottom: 1.25rem; color: var(--text-main); font-size: 1.15rem;">Pengaturan Akun</h3>
            
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">Foto Profil <span style="font-size:0.75rem; font-weight:normal;">(Opsional)</span></label>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg" 
                           style="width: 100%; padding: 0.5rem; border-radius: var(--radius-md); border: 1px dashed var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.85rem;">
                    @error('avatar') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">Nama Lengkap</label>
                    <div style="position: relative;">
                        <i class="ph ph-user" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.05rem;"></i>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.875rem;">
                    </div>
                    @error('name') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">NIP <span style="font-size:0.75rem; font-weight:normal;">(Opsional)</span></label>
                    <div style="position: relative;">
                        <i class="ph ph-identification-card" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.05rem;"></i>
                        <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="Masukkan NIP Anda"
                               style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.875rem;">
                    </div>
                    @error('nip') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">Username</label>
                    <div style="position: relative;">
                        <i class="ph ph-at" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.05rem;"></i>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                               style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.875rem;">
                    </div>
                    @error('username') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                </div>

                <div style="border-top: 1px dashed var(--border-color); margin: 1.5rem 0;"></div>

                <h4 style="margin: 0 0 1rem 0; font-size: 1rem; color: var(--text-main);">Keamanan Akun</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">Password Baru <span style="font-size:0.75rem; font-weight:normal;">(Opsional)</span></label>
                        <div style="position: relative;">
                            <i class="ph ph-lock-key" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.05rem;"></i>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" autocomplete="new-password"
                                   style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.875rem;">
                        </div>
                        @error('password') <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.35rem; font-weight: 500;">Konfirmasi Password Baru</label>
                        <div style="position: relative;">
                            <i class="ph ph-lock-key" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.05rem;"></i>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password"
                                   style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-main); outline: none; transition: all 0.2s; font-family: inherit; font-size: 0.875rem;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem; font-size: 1rem; font-weight: 600;">
                    <i class="ph ph-floppy-disk"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    input:focus {
        border-color: var(--asr-green) !important;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
</style>
@endsection
