<?php
$ops = [
    [
        'type' => 'patchNodeField',
        'nodeName' => 'Get User Calendar',
        'fieldPath' => 'parameters.url',
        'patches' => [
            ['find' => 'https://studyplanner.web.id/api/ai/tool/calendar', 'replace' => 'https://studyplanner.web.id/api/ai/tool/calendar?query={{$fromAI("query", "optional search query or just leave empty")}}']
        ]
    ],
    [
        'type' => 'patchNodeField',
        'nodeName' => 'Get User Tasks',
        'fieldPath' => 'parameters.url',
        'patches' => [
            ['find' => 'https://studyplanner.web.id/api/ai/tool/tasks', 'replace' => 'https://studyplanner.web.id/api/ai/tool/tasks?query={{$fromAI("query", "optional search query or just leave empty")}}']
        ]
    ]
];
echo json_encode(['id' => 'ifMZ21KCdFbY2IjH', 'operations' => $ops], JSON_PRETTY_PRINT);
