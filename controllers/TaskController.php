<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Task;

class TaskController extends Controller
{
    const CACHE_KEY = 'tasks_list';
    const CACHE_DURATION = 300; // 5 минут

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Получение списка всех задач
     * GET /tasks
     *
     * @return array
     */
    public function actionIndex()
    {
        $cache = Yii::$app->cache;

        $tasks = $cache->get(self::CACHE_KEY);

        if ($tasks === false) {

            $tasks = Task::find()->asArray()->all();

            $cache->set(self::CACHE_KEY, $tasks, self::CACHE_DURATION);
        }

        return [
            'success' => true,
            'data' => $tasks,
        ];
    }

    /**
     * Получение конкретной задачи
     * GET /tasks/{id}
     *
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $task = $this->findModel($id);

        return [
            'success' => true,
            'data' => $task->toArray(),
        ];
    }

    /**
     * Создание новой задачи
     * POST /tasks
     *
     * @return array
     */
    public function actionCreate()
    {
        $task = new Task();
        $task->load(Yii::$app->request->getBodyParams(), '');

        if ($task->save()) {

            $this->clearCache();

            Yii::$app->response->setStatusCode(201);
            return [
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task->toArray(),
            ];
        }

        Yii::$app->response->setStatusCode(422);
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $task->errors,
        ];
    }

    /**
     * Обновление задачи
     * PUT /tasks/{id}
     *
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $task = $this->findModel($id);
        $task->load(Yii::$app->request->getBodyParams(), '');

        if ($task->save()) {

            $this->clearCache();

            return [
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task->toArray(),
            ];
        }

        Yii::$app->response->setStatusCode(422);
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $task->errors,
        ];
    }

    /**
     * Удаление задачи
     * DELETE /tasks/{id}
     *
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $task = $this->findModel($id);

        if ($task->delete()) {

            $this->clearCache();

            Yii::$app->response->setStatusCode(204);
            return [
                'success' => true,
                'message' => 'Task deleted successfully',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to delete task',
        ];
    }

    /**
     * Поиск по ID
     *
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Task not found');
    }

    /**
     * Очистка кэша задач
     *
     * @return bool
     */
    protected function clearCache()
    {
        return Yii::$app->cache->delete(self::CACHE_KEY);
    }
}
