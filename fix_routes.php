<?php
$file = 'resources/views/dashboard.blade.php';
$content = file_get_contents($file);

// The bottom one in Quick Actions doesn't have $task
$brokenQuickAction = '<a href="{{ route(\'progress\') . \'?start_task=\' . urlencode($task->title) }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">';
$fixedQuickAction = '<a href="{{ route(\'progress\') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">';
$content = str_replace($brokenQuickAction, $fixedQuickAction, $content);

// The "Ke Halaman Track Progress" button at bottom of widget
$brokenWidgetButton = '<a href="{{ route(\'progress\') . \'?start_task=\' . urlencode($task->title) }}" class="w-full mt-4 py-2.5 rounded-xl bg-[#FAF6F0] text-[#5B1744] border border-[#E7C8DB]/50 text-xs font-bold text-center hover:bg-[#F4E7EF] transition shadow-xs">';
$fixedWidgetButton = '<a href="{{ route(\'progress\') }}" class="w-full mt-4 py-2.5 rounded-xl bg-[#FAF6F0] text-[#5B1744] border border-[#E7C8DB]/50 text-xs font-bold text-center hover:bg-[#F4E7EF] transition shadow-xs">';
$content = str_replace($brokenWidgetButton, $fixedWidgetButton, $content);

file_put_contents($file, $content);
echo "Fixed routes.\n";
