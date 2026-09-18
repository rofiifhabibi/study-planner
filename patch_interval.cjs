const fs = require('fs');
let content = fs.readFileSync('resources/views/progress.blade.php', 'utf8');

const oldLogic = `        tick();
        studyTimerInterval = setInterval(tick, 1000);`;

const newLogic = `        tick();
        if (studyTimerInterval) clearInterval(studyTimerInterval);
        studyTimerInterval = setInterval(tick, 1000);`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync('resources/views/progress.blade.php', content);
console.log('patched interval successfully');
