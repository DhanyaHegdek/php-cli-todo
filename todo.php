<?php

$command = $argv[1] ?? null;
$argument = $argv[2] ?? null;

echo "Command: $command\n";
echo "Argument: $argument\n";

function loadTasks() {
    $data = file_get_contents("tasks.json");
    return json_decode($data, true);
}

function saveTasks($tasks) {
    file_put_contents("tasks.json", json_encode($tasks, JSON_PRETTY_PRINT));
}

if ($command === "add") {
    $tasks = loadTasks();

    $tasks[] = [
        "id" => count($tasks) + 1,
        "task" => $argument,
        "done" => false,
        "priority" => "high"
    ];

    saveTasks($tasks);

    echo " Task added!\n";
}

if ($command === "list") {
    $tasks = loadTasks();

    if (empty($tasks)) {
        echo "No tasks found.\n";
        exit;
    }

    foreach ($tasks as $task) {
        echo $task['id'] . ". " . $task['task'] . "\n";
    }
}

if ($command === "delete") {
    $tasks = loadTasks();

    $tasks = array_filter($tasks, function($task) use ($argument) {
        return $task['id'] != $argument;
    });

    // Reindex IDs
    $tasks = array_values($tasks);

    foreach ($tasks as $index => $task) {
        $tasks[$index]['id'] = $index + 1;
    }

    saveTasks($tasks);

    echo " Task deleted!\n";
}

if ($command === "done") {
    $tasks = loadTasks();

    foreach ($tasks as $index => $task) {
        if ($task['id'] == $argument) {
            $tasks[$index]['done'] = true;
        }
    }

    saveTasks($tasks);

    echo " Task marked as done!\n";
}

if ($command === "search") {
    $tasks = loadTasks();

    if (!$argument) {
        echo " Please provide a search keyword\n";
        exit;
    }

    $results = array_filter($tasks, function($task) use ($argument) {
        return stripos($task['task'], $argument) !== false;
    });

    if (empty($results)) {
        echo "No matching tasks found.\n";
        exit;
    }

    foreach ($results as $task) {
        $status = $task['done'] ? "✓" : " ";
        echo "[{$status}] " . $task['id'] . ". " . $task['task'] . "\n";
    }
}