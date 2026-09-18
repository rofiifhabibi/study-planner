<?php
$w = json_decode(file_get_contents("/root/.gemini/antigravity-cli/brain/57d837f3-fc8b-43d9-b335-80a2648b9f5a/.system_generated/steps/218/output.txt"), true);
$nodes = $w['data']['nodes'];
$ops = [];
foreach ($nodes as $n) {
    if (preg_match('/^(View Calendar|Create Event|View Tasks|Create Task)/', $n['name'])) {
        $ops[] = [
            'type' => 'removeNode',
            'nodeName' => $n['name']
        ];
    }
}
echo json_encode(['id' => 'ifMZ21KCdFbY2IjH', 'operations' => $ops], JSON_PRETTY_PRINT);
