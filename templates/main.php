<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
                <?php if (isset($project)): ?>
                    <li class="main-navigation__list-item
                    <?= ((!empty($_GET['id']) && ($_GET['id'] == $project["id"]))) ? 'main-navigation__list-item--active' : ''; ?>"
                    >
                        <a class="main-navigation__list-item-link" href="?id=<?=$project['id']; ?>">
                            <?= htmlspecialchars($project["title"]) ?>
                        </a>
                        <span class="main-navigation__list-item-count">
                            <?= get_tasks_count_by_project($all_tasks, $project); ?>
                            </span>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
    <a class="button button--transparent button--plus content__side-button"
       href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>
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
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= (($show_complete_tasks) ? "checked" : ""); ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>
    <table class="tasks">
        <?php foreach ($tasks as $key => $task): ?>
            <?php if (isset($task)): ?>
                <?php if (!($show_complete_tasks) && ($task["status"])) { continue;} ?>
                <tr class="tasks__item task
                <?= ($task["status"]) ? 'task--completed' : ''; ?>
                <?= (compare_dates($task["deadline"] <= 24)) ? 'task--important' : ''; ?>
                ">
                <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1" <?= (($task["status"]) ? 'checked' : '');?>>
                            <span class="checkbox__text"><?= htmlspecialchars($task['title']); ?></span>
                        </label>
                    </td>
                    <td class="task__file">
                        <a class="download-link" href="#">Home.psd</a>
                    </td>
                    <td class="task__date"><?= htmlspecialchars($task['deadline']); ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</main>

