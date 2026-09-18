<?php
$json = json_decode(file_get_contents('workflow.json'), true);
$workflow = $json['data'];

$toolCalendarId = 'd82b0f4a-9351-4f70-b183-f8d9b1c7a421';
$toolTasksId = 'e2b3c4d5-6f7a-8b9c-0d1e-2f3a4b5c6d7e';

// Create nodes
$calendarNode = [
    'parameters' => [
        'operation' => 'getAll',
        'calendar' => 'primary',
        'limit' => 10,
    ],
    'type' => 'n8n-nodes-base.googleCalendarTool',
    'typeVersion' => 1.2,
    'position' => [0, -200],
    'id' => $toolCalendarId,
    'name' => 'Google Calendar Tool'
];

$tasksNode = [
    'parameters' => [
        'operation' => 'getAll',
        'taskList' => '@default',
        'limit' => 10,
    ],
    'type' => 'n8n-nodes-base.googleTasksTool',
    'typeVersion' => 1,
    'position' => [200, -200],
    'id' => $toolTasksId,
    'name' => 'Google Tasks Tool'
];

$workflow['nodes'][] = $calendarNode;
$workflow['nodes'][] = $tasksNode;

// We need to connect them to the 3 agents.
// The agents are:
// "230cb54f-b061-41d8-8499-6f6a30b8bb38" (AI Agent1)
// "916cd916-8859-4839-81eb-ac920d224958" (AI Agent Groq)
// "6293c46f-9a27-4711-9b1d-6db5093e24f6" (AI Agent Backup)

$agentNames = [
    'AI Agent1',
    'AI Agent Groq',
    'AI Agent Backup'
];

if (!isset($workflow['connections']['Google Calendar Tool'])) {
    $workflow['connections']['Google Calendar Tool'] = ['ai_tool' => [[]]];
}
if (!isset($workflow['connections']['Google Tasks Tool'])) {
    $workflow['connections']['Google Tasks Tool'] = ['ai_tool' => [[]]];
}

foreach ($agentNames as $agentName) {
    // Add to Google Calendar Tool
    $workflow['connections']['Google Calendar Tool']['ai_tool'][0][] = [
        'node' => $agentName,
        'type' => 'ai_tool',
        'index' => 0
    ];
    // Add to Google Tasks Tool
    $workflow['connections']['Google Tasks Tool']['ai_tool'][0][] = [
        'node' => $agentName,
        'type' => 'ai_tool',
        'index' => 0
    ];
}

file_put_contents('updated_workflow.json', json_encode($workflow, JSON_PRETTY_PRINT));
