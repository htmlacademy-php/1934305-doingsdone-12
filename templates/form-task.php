<?php
/* @var string $projectsSideTemplate
 * @var array $projects
 */

?>
<div class="content">
    <?= $projectsSideTemplate ?>
    <main class="content__main">
        <h2 class="content__main-heading">Добавление задачи</h2>

        <form class="form" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="form__row">
                <label class="form__label" for="name">Название <sup>*</sup></label>
                <?php
                $className = isset($errors["name"]) ? "form__input--error" : "" ?>
                <input class="form__input <?= $className ?>" type="text" name="name" id="name"
                       value="<?= getPostVal("name"); ?>"
                       placeholder="Введите название">
                <?php
                if (isset($errors["name"])) : ?>
                    <p class="form__message"> <?= $errors["name"] ?> </p>
                <?php
                endif ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="project">Проект <sup>*</sup></label>
                <?php
                $className = isset($errors["project_id"]) ? "form__input--error" : "" ?>
                <select class="form__input form__input--select <?= $className ?>" name="project_id" id="project">
                    <?php
                    foreach ($projects as $project) : ?>
                        <option value="<?= $project["id"] ?>"><?= $project["name"] ?></option>
                    <?php
                    endforeach; ?>
                </select>
                <?php
                if (isset($errors["project_id"])) : ?>
                    <p class="form__message"> <?= $errors["project_id"] ?> </p>
                <?php
                endif ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="date">Дата выполнения</label>
                <?php
                $className = isset($errors["end_time"]) ? "form__input--error" : "" ?>
                <input class="form__input form__input--date <?= $className ?>" type="text" name="end_time" id="date"
                       value="<?= getPostVal("end_time"); ?>"
                       placeholder="Введите дату в формате ГГГГ-ММ-ДД">
                <?php
                if (isset($errors["end_time"])) : ?>
                    <p class="form__message"> <?= $errors["end_time"] ?> </p>
                <?php
                endif ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="file">Файл</label>

                <div class="form__input-file">
                    <input class="visually-hidden" type="file" name="file" id="file" value="">

                    <label class="button button--transparent" for="file">
                        <span>Выберите файл</span>
                    </label>
                </div>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </main>
</div>
