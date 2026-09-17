<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldStep = "                <div class=\"flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100\">
                    <i class=\"fa-regular fa-circle-check \${step.is_completed ? 'text-green-500' : 'text-gray-300'}\"></i>
                    <span class=\"text-xs font-medium \${step.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}\">\${step.title}</span>
                </div>";
                
$newStep = "                <div class=\"flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm\">
                    <i class=\"fa-solid fa-circle-check \${step.is_completed ? 'text-green-500' : 'text-gray-200'}\"></i>
                    <span class=\"text-sm font-semibold \${step.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}\">\${step.title}</span>
                </div>";

$content = str_replace($oldStep, $newStep, $content);
file_put_contents($file, $content);
echo "Patched step list UI.\n";
