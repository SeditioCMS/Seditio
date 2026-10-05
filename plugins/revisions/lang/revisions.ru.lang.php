<?php

/* ====================
Seditio - Website engine
Copyright (c) Seditio Team
https://seditio.org

[BEGIN_SED]
File=plugins/revisions/lang/revisions.ru.lang.php
Version=186
Updated=2026-oct-01
Type=Plugin
Author=Seditio Team
Description=Russian language file for revisions plugin
[END_SED]
==================== */

if (!defined('SED_CODE')) {
	die('Wrong URL.');
}

$L['Revisions'] = 'История версий';
$L['rev_title'] = 'Контрольные точки и версии';
$L['rev_revision'] = 'Ревизия';
$L['rev_version'] = 'Версия';
$L['rev_date'] = 'Дата и время';
$L['rev_author'] = 'Автор';
$L['rev_comment'] = 'Комментарий';
$L['rev_actions'] = 'Действия';
$L['rev_diff'] = 'Сравнить';
$L['rev_restore'] = 'Откатить';
$L['rev_restore_confirm'] = 'Вы уверены, что хотите восстановить эту версию? Текущее состояние будет автоматически сохранено как новая контрольная точка.';
$L['rev_restored_success'] = 'Объект успешно восстановлен к версии #%1$s';
$L['rev_auto_edit_comment'] = 'Автоматическая контрольная точка перед сохранением';
$L['rev_auto_restore_comment'] = 'Снимок перед откатом к версии #%1$s';
$L['rev_clean_older_than'] = 'Очистить ревизии старше %1$s дней';
$L['rev_total_count'] = 'Всего контрольных точек в базе';
$L['rev_total_size'] = 'Общий объём данных';
$L['rev_no_revisions'] = 'Нет сохраненных версий';
$L['rev_all_revisions'] = 'Ревизии';
$L['rev_maintenance'] = 'Очистка';
$L['rev_prune_custom_title'] = 'Очистить устаревшие контрольные точки';
$L['rev_prune_custom_desc'] = 'Удалить сохранённые версии старше указанного количества дней';
$L['rev_prune_older_than_days'] = 'Старше (дней)';
$L['rev_prune_btn'] = 'Очистить';
$L['rev_prune_quick_title'] = 'Быстрая очистка по интервалам';
$L['rev_prune_quick_desc'] = 'Удаление контрольных точек старше 30, 60 или 90 дней';
$L['rev_optimize_title'] = 'Оптимизация и сжатие таблицы БД';
$L['rev_optimize_desc'] = 'Дефрагментация таблицы и освобождение физического дискового пространства после удаления записей';
$L['rev_optimize_btn'] = 'Оптимизировать';
$L['rev_wipeall_title'] = 'Полная очистка всех ревизий';
$L['rev_wipeall_desc'] = 'Безвозвратно удалить все сохранённые контрольные точки для всех объектов';
$L['rev_wipeall_confirm'] = 'ВНИМАНИЕ: Будет полностью удалена вся история версий для всех объектов. Продолжить?';
$L['rev_diff_title'] = 'Сравнение версии #%1$s с текущей версией';
$L['rev_diff_old_version'] = 'Версия #%1$s (%2$s)';
$L['rev_diff_current_version'] = 'Текущее состояние';
$L['rev_diff_no_changes'] = 'В выбранных полях различий не обнаружено.';
$L['rev_diff_legend_del'] = 'Удалённый фрагмент';
$L['rev_diff_legend_ins'] = 'Добавленный фрагмент';
$L['rev_item_id'] = 'ID объекта';
$L['rev_entity_type'] = 'Тип сущности';
$L['rev_filter_type'] = 'Фильтр по типу';
$L['rev_filter_author'] = 'Фильтр по автору';
$L['rev_delete_confirm'] = 'Удалить эту контрольную точку?';
$L['rev_prune_success'] = 'Удалено устаревших контрольных точек: %1$s';
$L['rev_deleted_success'] = 'Контрольная точка #%1$s успешно удалена';
$L['rev_back_to_edit'] = 'Вернуться к редактированию';
$L['rev_back_to_list'] = 'Вернуться к списку версий';
$L['rev_view_details'] = 'Подробнее';
$L['rev_current_label'] = 'текущая';
$L['rev_size'] = 'Размер данных';
