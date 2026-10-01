<?php
$f = 'resources/views/konvensional/kebun/master-tanaman.blade.php';
$c = file_get_contents($f);

// Fix JS edit action
$c = str_replace(
    "$('#formTanaman').attr('action', `/konvensional/kebun/tanaman/\${id}`);",
    "$('#formTanaman').attr('action', `/konvensional/kebun/master-tanaman/\${id}`);",
    $c
);

// Add error display to modal
$errorBlock = <<<EOF
              <div class="modal-body p-4">
                  @if(\$errors->any())
                      <div class="alert alert-danger">
                          <ul class="mb-0">
                              @foreach (\$errors->all() as \$error)
                                  <li>{{ \$error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif
EOF;
$c = str_replace('<div class="modal-body p-4">', $errorBlock, $c);

// Add script to automatically open modal if there are errors
$scriptBlock = <<<EOF
      $(document).ready(function() {
          @if(\$errors->any())
              var myModal = new bootstrap.Modal(document.getElementById('modalTanaman'));
              myModal.show();
          @endif
EOF;
$c = str_replace('$(document).ready(function() {', $scriptBlock, $c);

file_put_contents($f, $c);
echo "master-tanaman.blade.php updated.\n";
