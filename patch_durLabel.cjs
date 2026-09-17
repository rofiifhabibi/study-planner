const fs = require('fs');
let content = fs.readFileSync('resources/views/progress.blade.php', 'utf8');

const oldLogic = `                    $dur = $s->duration_seconds ?? 0;
                    $hours = floor($dur / 3600);
                    $mins = floor(($dur % 3600) / 60);
                    $durLabel = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";`;

const newLogic = `                    $dur = $s->duration_seconds ?? 0;
                    $hours = str_pad(floor($dur / 3600), 2, '0', STR_PAD_LEFT);
                    $mins = str_pad(floor(($dur % 3600) / 60), 2, '0', STR_PAD_LEFT);
                    $secs = str_pad($dur % 60, 2, '0', STR_PAD_LEFT);
                    $durLabel = "{$hours}:{$mins}:{$secs}";`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync('resources/views/progress.blade.php', content);
console.log('patched durLabel successfully');
