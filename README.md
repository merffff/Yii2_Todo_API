# Yii2 Todo API

Простое RESTful API для управления задачами (to-do list), разработанное на фреймворке Yii2 с использованием MySQL (MariaDB) и Memcached для кэширования.

## Особенности

- ✅ RESTful API для управления задачами
- ✅ Кэширование с помощью Memcached (5 минут)
- ✅ Docker-контейнеры для быстрого запуска
- ✅ Миграции с тестовыми данными
- ✅ Чистый код с соблюдением ООП принципов

## Технологии

- PHP 8.2
- Yii2 Framework
- MariaDB 10.11
- Memcached 1.6
- Docker & Docker Compose
- Nginx

## Требования

- Docker
- Docker Compose

## Установка и запуск

### 1. Клонирование репозитория

```bash
git clone <url-репозитория>
cd yii2-todo-api
```

### 2. Запуск Docker контейнеров

```bash
docker-compose up -d --build
```

### 3. Установка зависимостей (если еще не установлены)

```bash
docker-compose exec app composer install
```

### 4. Выполнение миграций

```bash
docker-compose exec app php yii migrate --interactive=0
```

### 5. Проверка работы

API будет доступно по адресу: `http://localhost:8089`

## API Endpoints

### 1. Получение списка всех задач

```http
GET /task
```

**Ответ:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Разработать Yii2_Todo_API",
      "description": "Создать RESTful Yii2_Todo_API для управления задачами",
      "status": "in_progress",
    "created_at": 1704067200,
    "updated_at": 1704067200
  }
}
```

### 3. Создание новой задачи

```http
POST /task
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "title": "Новая задача",
  "description": "Описание задачи",
  "status": "pending"
}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 6,
    "title": "Новая задача",
    "description": "Описание задачи",
    "status": "pending",
    "created_at": 1704067200,
    "updated_at": 1704067200
  }
}
```

### 4. Обновление задачи

```http
PUT /task/{id}
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "title": "Обновленная задача",
  "status": "completed"
}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "title": "Обновленная задача",
    "description": "Создать RESTful Yii2_Todo_API для управления задачами",
    "status": "completed",
    "created_at": 1704067200,
    "updated_at": 1704070800
  }
}
```

### 5. Удаление задачи

```http
DELETE /task/{id}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Task deleted successfully"
}
```

## Статусы задач

- `pending` - Ожидает выполнения
- `in_progress` - В процессе
- `completed` - Завершено

## Кэширование

API использует Memcached для кэширования списка задач:

- **Кэш создается**: при первом запросе списка задач (GET /task)
- **Время жизни кэша**: 5 минут (300 секунд)
- **Кэш очищается**: при создании, обновлении или удалении задачи

## Примеры использования с curl

### Получить все задачи
```bash
curl -X GET http://localhost:8089/task
```

### Получить задачу по ID
```bash
curl -X GET http://localhost:8089/task/1
```

### Создать новую задачу
```bash
curl -X POST http://localhost:8089/task \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Новая задача",
    "description": "Описание",
    "status": "pending"
  }'
```

### Обновить задачу
```bash
curl -X PUT http://localhost:8089/task/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed"
  }'
```

### Удалить задачу
```bash
curl -X DELETE http://localhost:8089/task/1
```


## Тестирование

Для тестирования API рекомендуется использовать:
- **curl** (примеры выше)
- **Postman** - импортируйте endpoints
- **Insomnia** - REST клиент

