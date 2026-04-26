<?php

require_once "src/TaskManager.php";

$manager = new TaskManager("storage/tasks.json");

$command = $argv[1] ?? null;
$arg1 = $argv[2] ?? null;
$arg2 = $argv[3] ?? null;

switch ($command) {
    case "add":
        $manager->addTask($arg1, $arg2);
        break;

    case "list":
        $manager->listTasks();
        break;

    case "delete":
        $manager->deleteTask($arg1);
        break;

    case "done":
        $manager->markDone($arg1);
        break;

    case "search":
        $manager->searchTasks($arg1);
        break;

    case "clear":
        $manager->clearCompleted();
        break;

    default:
        echo "❌ Unknown command\n";
}