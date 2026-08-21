@if($count > 0)
    <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); background: #fff7ed; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <h3 style="margin:0; font-size: 1rem; font-weight: 700; color: #9a3412;">🎉 Siap Panen! ({{ $count }} lubang)</h3>
        <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: var(--text-muted);">Tanaman yang sudah melewati batas waktu tumbuh</p>
    </div>
    <div style="max-height: 300px; overflow-y: auto; padding: 1rem; background: #fff7ed;">
        <div style="display: flex; gap: 0.5rem; flex-direction: column;">
            @foreach($readyGroups as $plantName => $holes)
                @php
                    $plantCount = count($holes);
                    $firstHole  = $holes->first();
                    $sampleGh   = $firstHole['gh_name'];
                    $sampleRack = $firstHole['rack_name'];
                    $timeAgo    = \Carbon\Carbon::parse($firstHole['planted_at'])->diffForHumans();
                @endphp
                <div style="background: var(--card-bg); border: 1px solid #fed7aa; border-radius: 8px; padding: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #fed7aa; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                        <strong style="color: #9a3412; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="ph ph-plant"></i> {{ $plantName }}
                        </strong>
                        <span style="background: #ea580c; color: white; padding: 0.15rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">
                            {{ $plantCount }} lubang
                        </span>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.3rem;">
                        <div><strong>{{ $sampleGh }}</strong> &middot; {{ $sampleRack }} ({{ $plantCount }})</div>
                        <div style="color: #b45309; display: flex; align-items: center; gap: 0.3rem;">
                            <i class="ph ph-clock"></i> Tanam sejak {{ $timeAgo }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div style="padding: 0.75rem; border-top: 1px solid var(--border-color); text-align: center; background: #fff7ed; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
        <button onclick="markNotificationsRead()" style="background: none; border: none; color: #ea580c; font-weight: 600; font-size: 0.85rem; cursor: pointer; text-decoration: underline;">
            Tandai sudah dibaca
        </button>
    </div>
@else
    <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
        <i class="ph ph-bell-slash" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 0.5rem;"></i>
        <p style="margin: 0; font-size: 0.9rem;">Belum ada notifikasi baru</p>
    </div>
@endif
