<?php
/* @var array $projects
 * @var mixed $projectId
 * @var string $scriptName
 */
?>

<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>
    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php
            foreach ($projects as $project) : ?>
                <li class="main-navigation__list-item
                    <?php
                    if ($projectId !== null && (int)$projectId === $project["id"]) : ?>
                        main-navigation__list-item--active
                        <?php
                    endif; ?>
                    ">
                    <a class="main-navigation__list-item-link"
                       href="<?= makeURL($scriptName, ["project_id" => $project["id"]]) ?>"><?= $project["name"] ?></a>
                    <span class="main-navigation__list-item-count"><?= $project["amount"] ?></span>
                </li>
                <?php
            endforeach; ?>
        </ul>
    </nav>
    <a class="button button--transparent button--plus content__side-button"
       href="<?=makeURL(PROJECT_SCRIPT, []) ?>" target="project_add">Добавить проект</a>
</section>
