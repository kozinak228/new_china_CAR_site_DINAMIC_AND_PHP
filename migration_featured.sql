-- Миграция: добавить поле featured для управления каруселью
ALTER TABLE cars ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0;
