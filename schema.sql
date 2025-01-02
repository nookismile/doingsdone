INSERT INTO users(name, email, password)
VALUES
    ('hero34@mail.ru', 'Ярослав', 'secretpassw1'),
    ('asis174@mail.ru', 'Слава', 'secretpassw2');

INSERT INTO projects (title, author_id)
VALUES
    ('Входящие', 11),
    ('Учеба', 12),
    ('Работа', 11),
    ('Домашние дела', 12),
    ('Авто', 11);

INSERT INTO tasks (title, deadline, status, author_id, project_id)
VALUES
    ('Собеседование в IT компании', '01.12.2024', false, 11, 18),
    ('Выполнить тестовое задание', '25.12.2024', false, 11, 18),
    ('Сделать задание первого раздела', '21.12.2024', true, 12, 17),
    ('Встреча с другом', '22.12.2024', false, 11, 16),
    ('Купить корм для кота', null, false, 12, 19),
    ('Заказать пиццу', null, false, 12, 19);

SELECT title FROM projects WHERE author_id = 12;

SELECT title FROM tasks WHERE project_id = 18;

UPDATE tasks
SET STATUS=1
WHERE id=2;

UPDATE tasks
SET title='Сделать задание второго раздела'
WHERE id=3;

