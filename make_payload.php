<?php
$w = json_decode(file_get_contents('updated_workflow.json'), true);
$payload = [
    'workflowId' => $w['id'],
    'nodes' => $w['nodes'],
    'connections' => $w['connections'],
    'settings' => $w['settings'] ?? []
];
file_put_contents('payload.json', json_encode($payload));
