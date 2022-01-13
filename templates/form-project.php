<?php
/* @var string $projectsSideTemplate
 */

?>

<div class="content">
    <?= $projectsSideTemplate ?>
    <main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form" action="" method="post" autocomplete="off">
            <div class="form__row">
                <label class="form__label" for="project_name">Название <sup>*</sup></label>

                <input class="form__input" type="text" name="project_name" id="project_name" value=""
                       placeholder="Введите название проекта">
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </main>
</div>
