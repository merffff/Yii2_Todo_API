<?php

namespace app\controllers;

use app\components\ResponseHelper;
use app\services\TaskService;
use Yii;
use Throwable;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TaskController extends Controller
{
    use ResponseHelper;

    private TaskService $service;

    public function __construct($id, $module, TaskService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $b = parent::behaviors();
        $b['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        $b['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index'  => ['GET'],
                'view'   => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
            ],
        ];
        return $b;
    }

    public function actionIndex()
    {
        try {
            $tasks = $this->service->list();
            return $this->jsonResponse(true, '', ['tasks' => $tasks]);
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return $this->jsonResponse(false, 'Failed to load tasks', [], 500);
        }
    }

    public function actionView($id)
    {
        try {
            $task = $this->service->get($id);
            return $this->jsonResponse(true, '', $task->toArray());
        } catch (NotFoundHttpException $e) {
            return $this->jsonResponse(false, 'Task not found', [], 404);
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return $this->jsonResponse(false, 'Error loading task', [], 500);
        }
    }

    public function actionCreate()
    {
        try {
            $task = $this->service->create(Yii::$app->request->getBodyParams());
            return $this->jsonResponse(true, 'Task created successfully', $task->toArray(), 201);
        } catch (Throwable $e) {
            Yii::warning($e->getMessage(), __METHOD__);
            return $this->jsonResponse(false, 'Validation failed', $this->decodeErrors($e), 422);
        }
    }

    public function actionUpdate($id)
    {
        try {
            $task = $this->service->update($id, Yii::$app->request->getBodyParams());
            return $this->jsonResponse(true, 'Task updated successfully', $task->toArray(), 200);
        } catch (NotFoundHttpException $e) {
            return $this->jsonResponse(false, 'Task not found', [], 404);
        } catch (Throwable $e) {
            Yii::warning($e->getMessage(), __METHOD__);
            return $this->jsonResponse(false, 'Validation failed', $this->decodeErrors($e), 422);
        }
    }

    public function actionDelete($id)
    {
        try {
            $this->service->delete($id);
            return $this->jsonResponse(true, 'Task deleted successfully', [], 204);
        } catch (NotFoundHttpException $e) {
            return $this->jsonResponse(false, 'Task not found', [], 404);
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return $this->jsonResponse(false, 'Failed to delete task', [], 500);
        }
    }

    private function decodeErrors(Throwable $e): array
    {
        $msg = $e->getMessage();
        $decoded = json_decode($msg, true);
        return is_array($decoded) ? $decoded : ['_error' => $msg];
    }
}


