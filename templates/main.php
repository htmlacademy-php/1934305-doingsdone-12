<?php
/* @var string $projectsSideTemplate
 * @var int $showCompleteTasks
 * @var array $tasks
 * @var string $scriptName
 * @var $btnActive
 */

?>

<div class="content">
    <?= $projectsSideTemplate ?>
    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>
        <form class="search-form" action="" method="get" autocomplete="off">
            <input class="search-form__input" type="text" name="query" value="" placeholder="Поиск по задачам">
            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>
        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="/" class="tasks-switch__item
                <?php
                if ($btnActive["all_tasks"] ?? null) : ?>
                tasks-switch__item--active
                <?php
                endif; ?>">
                    Все задачи</a>
                <a href="<?= makeURL($scriptName, ["current_day" => 1]) ?>" class="tasks-switch__item
            <?php
                if ($btnActive["current_day"] ?? null) : ?>
                tasks-switch__item--active
                <?php
                endif; ?>">Повестка дня</a>
                <a href="<?= makeURL($scriptName, ["tomorrow" => 1]) ?>" class="tasks-switch__item
 <?php
                if ($btnActive["tomorrow"] ?? null) : ?>
                tasks-switch__item--active
                <?php
                endif; ?>">Завтра</a>
                <a href="<?= makeURL($scriptName, ["overdue" => 1]) ?>" class="tasks-switch__item
<?php
                if ($btnActive["overdue"] ?? null) : ?>
                tasks-switch__item--active
                <?php
                endif; ?>
">Просроченные</a>
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
            if ($tasks === null) : ?>
                <p>Ничего не найдено по вашему запросу</p>
            <?php
            else : ?>
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
                                <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                                       value="<?= $task["task_id"] ?>"
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
                            <?php
                            if ($task["file"]) : ?>
                                <a class="download-link" href="<?= esc($task["file"]) ?>"><?= str_replace(
                                        "uploads/",
                                        "",
                                        esc($task["file"])
                                    ) ?></a>
                            <?php
                            endif; ?>
                        </td>
                        <td class="task__date"><?= esc($task["date"]) ?></td>
                    </tr>
                <?php
                endforeach; ?>
            <?php
            endif; ?>
        </table>
    </main>
</div>
