<?php
// Patch Deadline Reminder
\$deadlineFile = 'resources/views/emails/deadline_reminder.blade.php';
\$deadlineContent = file_get_contents(\$deadlineFile);

// Remove emojis
\$deadlineContent = str_replace('⚠️ Pengingat Deadline Tugas ⚠️', 'Pengingat Deadline Tugas', \$deadlineContent);
\$deadlineContent = str_replace('⏳ Tenggat:', 'Tenggat:', \$deadlineContent);

file_put_contents(\$deadlineFile, \$deadlineContent);


// Patch Daily Agenda
\$agendaFile = 'resources/views/emails/daily_agenda.blade.php';
\$agendaContent = file_get_contents(\$agendaFile);

// Remove emojis
\$agendaContent = str_replace('🎓 Jadwal Pelajaran', 'Jadwal Pelajaran', \$agendaContent);
\$agendaContent = str_replace('📅 Kegiatan & Acara', 'Kegiatan & Acara', \$agendaContent);
\$agendaContent = str_replace('☑️ Tugas Harus Selesai', 'Tugas Harus Selesai', \$agendaContent);

file_put_contents(\$agendaFile, \$agendaContent);

echo "Emails simplified.\n";
