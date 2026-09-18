<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldLogic = <<<JS
    function renderActiveChecklist() {
        const container = document.getElementById('active-checklist-container');
        const list = document.getElementById('active-steps-list');
        
        if (!currentSteps || currentSteps.length === 0) {
            container.classList.add('hidden');
            return;
        }
        
        container.classList.remove('hidden');
        list.innerHTML = '';
        
        currentSteps.forEach(step => {
            const checkedStr = step.is_completed ? 'checked' : '';
            const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
            
            list.innerHTML += `
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" \${checkedStr} onchange="toggleStep(\${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]">
                    <span class="text-xs font-semibold \${lineThrough}">\${step.title}</span>
                </label>
            `;
        });
        
        if (currentTaskId) {
            list.innerHTML += `
                <div class="flex items-center gap-2 mt-2">
                    <input type="text" id="new-active-step-input" placeholder="Tambah langkah baru..." class="flex-1 text-xs px-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-[#5B1744] focus:border-[#5B1744]" onkeypress="if(event.key === 'Enter') addActiveStep()">
                    <button type="button" onclick="addActiveStep()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
            `;
        }
    }
JS;

$newLogic = <<<JS
    function renderActiveChecklist() {
        const container = document.getElementById('active-checklist-container');
        const list = document.getElementById('active-steps-list');
        
        // Always show container if a task is selected, even if steps are empty
        if (!currentTaskId) {
            container.classList.add('hidden');
            return;
        }
        
        container.classList.remove('hidden');
        list.innerHTML = '';
        
        let completed = 0;
        let total = currentSteps ? currentSteps.length : 0;
        
        if (total > 0) {
            currentSteps.forEach(step => {
                if (step.is_completed) completed++;
                const checkedStr = step.is_completed ? 'checked' : '';
                const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
                
                list.innerHTML += `
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" \${checkedStr} onchange="toggleStep(\${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]">
                        <span class="text-xs font-semibold \${lineThrough}">\${step.title}</span>
                    </label>
                `;
            });
            
            const percentage = Math.round((completed / total) * 100);
            
            list.innerHTML = `
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5B1744]">Progress: \${percentage}%</span>
                    <span class="text-xs text-gray-400">\${completed}/\${total} selesai</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4 overflow-hidden">
                    <div class="bg-[#5B1744] h-1.5 rounded-full transition-all duration-500" style="width: \${percentage}%"></div>
                </div>
            ` + list.innerHTML;
        } else {
            list.innerHTML = `<p class="text-xs text-gray-400 italic mb-3">Belum ada langkah. Silakan tambah di bawah.</p>`;
        }
        
        // Add new step input
        list.innerHTML += `
            <div class="flex items-center gap-2 mt-3">
                <input type="text" id="new-active-step-input" placeholder="Tambah langkah baru..." class="flex-1 text-xs px-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-[#5B1744] focus:border-[#5B1744]" onkeypress="if(event.key === 'Enter') addActiveStep()">
                <button type="button" onclick="addActiveStep()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                </button>
            </div>
        `;
    }
JS;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched renderActiveChecklist.\n";
