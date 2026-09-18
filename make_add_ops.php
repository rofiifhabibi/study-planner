<?php
$ops = [];
$nodes = json_decode(file_get_contents("/root/.gemini/antigravity-cli/brain/57d837f3-fc8b-43d9-b335-80a2648b9f5a/n8n_tools_custom.json"), true)['nodes'];
foreach ($nodes as $n) {
    $ops[] = [
        'type' => 'addNode',
        'node' => $n
    ];
}
// Add connections to the 3 agents
$agents = ['AI Agent1', 'AI Agent Groq', 'AI Agent Backup'];
foreach ($nodes as $n) {
    foreach ($agents as $agent) {
        $ops[] = [
            'type' => 'addConnection',
            'source' => $n['name'],
            'sourceType' => 'ai_tool',
            'sourceIndex' => 0,
            'target' => $agent,
            'targetType' => 'ai_tool',
            'targetIndex' => 0
        ];
    }
}
echo json_encode(['id' => 'ifMZ21KCdFbY2IjH', 'operations' => $ops], JSON_PRETTY_PRINT);
