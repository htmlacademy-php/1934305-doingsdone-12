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
                <?php
                $className = isset($errors["project_name"]) ? "form__input--error" : "" ?>
                <input class="form__input <?= $className ?>" type="text" name="project_name" id="project_name" value=""
                       placeholder="Введите название проекта">
                <?php
                if (isset($errors["project_name"])) : ?>
                    <p class="form__message"> <?= $errors["project_name"] ?> </p>
                    <?php
                endif ?>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </main>
</div>
