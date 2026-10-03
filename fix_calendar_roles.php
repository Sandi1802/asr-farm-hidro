<?php
$file = 'resources/views/konvensional/dashboard.blade.php';
$content = file_get_contents($file);

$target = '<div class="responsive-grid-cal">';
$replacement = <<<HTML
    @if(in_array(Auth::user()?->role_agri, ['it_admin', 'produksi', 'produksi_gh', 'produksi_konvensional', 'kepala_produksi', 'produksi_paprika']))
<div class="responsive-grid-cal">
HTML;

$target2 = <<<HTML
                </div>
            </div>
        </div>
    </div>



        {{-- GRAFIK TOP TANAMAN --}}
HTML;
$replacement2 = <<<HTML
                </div>
            </div>
        </div>
    </div>
    @endif



        {{-- GRAFIK TOP TANAMAN --}}
HTML;

$content = str_replace($target, $replacement, $content);
$content = str_replace($target2, $replacement2, $content);
file_put_contents($file, $content);
