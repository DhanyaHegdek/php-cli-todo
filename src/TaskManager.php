<?php

class TaskManager {
    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    private function loadTasks() {
        $data = file_get_contents($this->file);
        return json_decode($data, true) ?? [];
    }

    private function saveTasks($tasks) {
        file_put_contents($this->file, json_encode($tasks, JSON_PRETTY_PRINT));
    }

    public function addTask($task, $dueDate = null) {
        if (!$task || trim($task) === "") {
            echo " Task cannot be empty\n";
            return;
        }

        $tasks = $this->loadTasks();

        $tasks[] = [
            "id" => count($tasks) + 1,
            "task" => $task,
            "done" => false,
            "due_date" => $dueDate
        ];

        $this->saveTasks($tasks);

        echo " Task added!\n";
    }

    public function listTasks() {
        $tasks = $this->loadTasks();

        if (empty($tasks)) {
            echo "No tasks found.\n";
            return;
        }

        foreach ($tasks as $task) {
            $status = $task['done'] ? "✓" : " ";
            $due = $task['due_date'] ? " (Due: {$task['due_date']})" : "";
            echo "[{$status}] {$task['id']}. {$task['task']}{$due}\n";
        }
    }

    public function deleteTask($id) {
        if (!is_numeric($id)) {
            echo " Invalid ID\n";
            return;
        }

        $tasks = $this->loadTasks();
        $found = false;

        $tasks = array_filter($tasks, function($task) use ($id, &$found) {
            if ($task['id'] == $id) {
                $found = true;
                return false;
            }
            return true;
        });

        if (!$found) {
            echo " Task not found\n";
            return;
        }

        $tasks = array_values($tasks);

        foreach ($tasks as $index => $task) {
            $tasks[$index]['id'] = $index + 1;
        }

        $this->saveTasks($tasks);

        echo " Task deleted!\n";
    }

    public function markDone($id) {
        $tasks = $this->loadTasks();

        foreach ($tasks as $index => $task) {
            if ($task['id'] == $id) {
                $tasks[$index]['done'] = true;
            }
        }

        $this->saveTasks($tasks);

        echo " Task marked as done!\n";
    }

    public function searchTasks($keyword) {
        $tasks = $this->loadTasks();

        $results = array_filter($tasks, function($task) use ($keyword) {
            return stripos($task['task'], $keyword) !== false;
        });

        foreach ($results as $task) {
            $status = $task['done'] ? "✓" : " ";
            echo "[{$status}] {$task['id']}. {$task['task']}\n";
        }
    }

    public function clearCompleted() {
        $tasks = $this->loadTasks();

        $tasks = array_filter($tasks, function($task) {
            return !$task['done'];
        });

        $tasks = array_values($tasks);

        foreach ($tasks as $index => $task) {
            $tasks[$index]['id'] = $index + 1;
        }

        $this->saveTasks($tasks);

        echo " Completed tasks cleared!\n";
    }
}