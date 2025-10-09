<?php

namespace app\services;

use app\models\Task;
use Yii;
use yii\caching\CacheInterface;
use yii\web\NotFoundHttpException;
use yii\base\Exception;
use yii\db\Exception as DbException;

class TaskService
{
    public const CACHE_KEY = 'tasks_list';
    public const CACHE_DURATION = 300; // 5 минут

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function list(): array
    {
        $tasks = $this->cache->get(self::CACHE_KEY);
        if ($tasks === false) {
            $tasks = Task::find()->asArray()->all();
            $this->cache->set(self::CACHE_KEY, $tasks, self::CACHE_DURATION);
        }
        return $tasks;
    }

    public function get(int|string $id): Task
    {
        $task = Task::findOne($id);
        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }
        return $task;
    }

    public function create(array $data): Task
    {
        $task = new Task();
        $task->load($data, '');

        if (!$task->validate()) {
            throw new Exception(json_encode($task->errors, JSON_UNESCAPED_UNICODE));
        }
        if (!$task->save(false)) {
            throw new DbException('Failed to save new task');
        }

        $this->clearCache();
        return $task;
    }

    public function update(int|string $id, array $data): Task
    {
        $task = $this->get($id);
        $task->load($data, '');

        if (!$task->validate()) {
            throw new Exception(json_encode($task->errors, JSON_UNESCAPED_UNICODE));
        }
        if (!$task->save(false)) {
            throw new DbException('Failed to update task');
        }

        $this->clearCache();
        return $task;
    }

    public function delete(int|string $id): void
    {
        $task = $this->get($id);
        if ($task->delete() === false) {
            throw new DbException('Failed to delete task');
        }
        $this->clearCache();
    }

    public function clearCache(): void
    {
        $this->cache->delete(self::CACHE_KEY);
    }
}
