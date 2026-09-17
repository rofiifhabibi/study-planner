<?php
$json = file_get_contents('workflow.json');
$full = json_decode($json, true);
$workflow = $full['data'];

$nodes = $workflow['nodes'];
$connections = $workflow['connections'];

// Create the new tool node 3 times (one for each agent)
function createToolNode($id, $name, $x, $y) {
    return [
        "parameters" => [
            "name" => "read_school_timetable",
            "description" => "Call this tool to get the user's weekly school timetable (Jadwal Pelajaran sekolah). It will return the subjects and times for each day. Only use this when the user asks about their school lessons or timetable.",
            "method" => "GET",
            "url" => "https://studyplanner.web.id/api/ai/tool/timetable",
            "sendHeaders" => true,
            "headerParameters" => [
                "parameters" => [
                    [
                        "name" => "X-API-KEY",
                        "value" => "studyplanner-ai-secret-2026"
                    ],
                    [
                        "name" => "X-SESSION-ID",
                        "value" => "={{ $('Webhook').item.json.body.session_id }}"
                    ]
                ]
            ],
            "sendQuery" => true,
            "queryParameters" => [
                "parameters" => [
                    [
                        "name" => "query",
                        "value" => "={{ \$fromAI('query', 'Optional filter context') }}"
                    ]
                ]
            ]
        ],
        "id" => $id,
        "name" => $name,
        "type" => "@n8n/n8n-nodes-langchain.toolHttpRequest",
        "typeVersion" => 1.1,
        "position" => [$x, $y]
    ];
}

$tool1 = createToolNode(uniqid('t1_'), "Read Timetable 1", -700, 232);
$tool2 = createToolNode(uniqid('t2_'), "Read Timetable 2", -600, 656);
$tool3 = createToolNode(uniqid('t3_'), "Read Timetable 3", -500, 1184);

$nodes[] = $tool1;
$nodes[] = $tool2;
$nodes[] = $tool3;

// Add connections to AI Agents
$connections[$tool1['name']] = ["ai_tool" => [ [ ["node" => "AI Agent1", "type" => "ai_tool", "index" => 0] ] ]];
$connections[$tool2['name']] = ["ai_tool" => [ [ ["node" => "AI Agent Groq", "type" => "ai_tool", "index" => 0] ] ]];
$connections[$tool3['name']] = ["ai_tool" => [ [ ["node" => "AI Agent Backup", "type" => "ai_tool", "index" => 0] ] ]];

// Rebuild workflow object
$newWorkflow = [
    "nodes" => $nodes,
    "connections" => $connections
];

file_put_contents('patched_workflow.json', json_encode($newWorkflow, JSON_PRETTY_PRINT));
echo "Saved patched_workflow.json";
