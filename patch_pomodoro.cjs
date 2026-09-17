const fs = require('fs');
let content = fs.readFileSync('resources/views/progress.blade.php', 'utf8');

const oldLogic = `        function tick() {
            const now = Date.now();
            const elapsed = baseSeconds + Math.floor((now - startTs) / 1000);
            document.getElementById('study-timer-display').textContent = formatStudyTime(elapsed);
        }`;

const newLogic = `        function tick() {
            const now = Date.now();
            const elapsed = baseSeconds + Math.floor((now - startTs) / 1000);
            document.getElementById('study-timer-display').textContent = formatStudyTime(elapsed);

            // Pengingat waktu belajar efektif (Setiap 25 menit = 1500 detik)
            if (elapsed > 0 && elapsed % 1500 === 0) {
                const mins = Math.floor(elapsed / 60);
                showToast(\`Kerja bagus! Kamu sudah fokus selama \${mins} menit. Ambil jeda istirahat 5 menit ya!\`, 'success');
                
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification('Waktunya Istirahat! ☕', {
                        body: \`Kamu sudah belajar fokus selama \${mins} menit. Istirahatkan matamu sebentar agar tetap efektif!\`
                    });
                }
            }
        }`;

content = content.replace(oldLogic, newLogic);

const oldInit = `    document.addEventListener('DOMContentLoaded', () => {
        checkActiveStudySession();
    });`;

const newInit = `    document.addEventListener('DOMContentLoaded', () => {
        checkActiveStudySession();
        
        // Request browser notification permission for study alerts
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });`;

content = content.replace(oldInit, newInit);

fs.writeFileSync('resources/views/progress.blade.php', content);
console.log('patched pomodoro successfully');
