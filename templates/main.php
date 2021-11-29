<?php
/* @var string $projectsSideTemplate
 * @var int $showCompleteTasks
 * @var array $tasks
 */
?>

<div class="content">
    <?= $projectsSideTemplate ?>
    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>
        <form class="search-form" action="index.php" method="post" autocomplete="off">
            <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">
            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>
        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                <a href="/" class="tasks-switch__item">Повестка дня</a>
                <a href="/" class="tasks-switch__item">Завтра</a>
                <a href="/" class="tasks-switch__item">Просроченные</a>
            </nav>
            <label class="checkbox">
                <input class="checkbox__input visually-hidden show_completed" type="checkbox"
                    <?php
                    if ($showCompleteTasks) : ?>
                        checked
                    <?php
                    endif; ?>>
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>
        <table class="tasks">
            <?php
            foreach ($tasks as $task) : ?>
                <?php
                if ($showCompleteTasks === 0 && $task["is_finished"]) {
                    continue;
                }
                ?>
                <tr class="tasks__item task
                <?php
                if (isTaskImportant($task["date"], date_create())) : ?>
                    task--important
                <?php
                endif; ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1"
                                <?php
                                if ($task["is_finished"]) : ?>
                                    checked
                                <?php
                                endif; ?>
                            >
                            <span class="checkbox__text"><?= esc($task["task_name"]) ?></span>
                        </label>
                    </td>
                    <td class="task__file">
                        <a class="download-link" href="#">Home.psd</a>
                    </td>
                    <td class="task__date"><?= esc($task["date"]) ?></td>
                </tr>
            <?php
            endforeach; ?>
        </table>
    </main>
</div>
