const fs = require('fs');
let content = fs.readFileSync('resources/views/progress.blade.php', 'utf8');

const oldLogic = `            if (data.status === 'success') {
                closeStartModal();
                setTimeout(() => startStudyTimer(data.session), 300);
            }`;

const newLogic = `            if (data.status === 'success') {
                closeStartModal();
                window.location.reload();
            }`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync('resources/views/progress.blade.php', content);
console.log('patched reload successfully');
