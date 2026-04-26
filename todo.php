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

    if (!$argument || trim($argument) === "") {
        echo " Task cannot be empty\n";
        exit;
    }
    $tasks = loadTasks();

    $tasks[] = [
        "id" => count($tasks) + 1,
        "task" => $argument,
        "done" => false,
        "priority" => "high",
        "due_date" => $dueDate
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
        $status = $task['done'] ? "✓" : " ";
        $due = $task['due_date'] ? " (Due: {$task['due_date']})" : "";

        echo "[{$status}] {$task['id']}. {$task['task']}{$due}\n";
    }

    $today = date("Y-m-d");

    if ($task['due_date'] && $task['due_date'] < $today && !$task['done']) {
        echo "⚠️ ";
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

if ($command === "clear") {
    $tasks = loadTasks();

    // Keep only tasks that are NOT done
    $tasks = array_filter($tasks, function($task) {
        return $task['done'] === false;
    });

    // Reindex + reset IDs
    $tasks = array_values($tasks);

    foreach ($tasks as $index => $task) {
        $tasks[$index]['id'] = $index + 1;
    }

    saveTasks($tasks);

    echo " Completed tasks cleared!\n";
}