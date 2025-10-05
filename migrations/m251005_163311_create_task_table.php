<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 */
class m251005_163311_create_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'status' => $this->string(50)->notNull()->defaultValue('pending'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-tasks-status',
            '{{%tasks}}',
            'status'
        );

        $time = time();
        $this->batchInsert('{{%tasks}}', ['title', 'description', 'status', 'created_at', 'updated_at'], [
            [
                'Разработать Yii2_Todo_API',
                'Создать RESTful Yii2_Todo_API для управления задачами',
                'in_progress',
                $time,
                $time
            ],
            [
                'Настроить Docker',
                'Подготовить docker-compose для проекта',
                'completed',
                $time - 3600,
                $time - 1800
            ],
            [
                'Написать документацию',
                'Создать README.md с инструкциями',
                'pending',
                $time,
                $time
            ],
            [
                'Добавить кэширование',
                'Реализовать кэширование с Memcached',
                'completed',
                $time - 7200,
                $time - 3600
            ],
            [
                'Провести тестирование',
                'Протестировать все endpoints Yii2_Todo_API',
                'pending',
                $time,
                $time
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tasks}}');
    }

}
