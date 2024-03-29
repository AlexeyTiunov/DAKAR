<?
$MESS ['CES_ERROR_NO_FILE'] = "Не указан файл экспорта.";
$MESS ['CES_ERROR_NO_ACTION'] = "Не указано действие над файлом экспорта.";
$MESS ['CES_ERROR_FILE_NOT_EXIST'] = "Файл экспорта не найден:";
$MESS ['CES_ERROR_NOT_AGENT'] = "Этот профиль не может использоваться в агентах, так как он является профилем \"по умолчанию\" и для текущего экспортера определен файл настроек.";
$MESS ['CES_ERROR_ADD_PROFILE'] = "Ошибка добавления профиля.";
$MESS ['CES_ERROR_NOT_CRON'] = "Этот профиль не может использоваться на cron, так как он является профилем \"по умолчанию\" и для текущего экспортера определен файл настроек.";
$MESS ['CES_ERROR_ADD2CRON'] = "Ошибка установки конфигурационного файла в cron:";
$MESS ['CES_ERROR_UNKNOWN'] = "неизвестная ошибка.";
$MESS ['CES_ERROR_NO_PROFILE1'] = "Профиль #";
$MESS ['CES_ERROR_NO_PROFILE2'] = "не найден.";
$MESS ['CES_ERROR_SAVE_PROFILE'] = "Ошибка сохранения профиля экспорта.";
$MESS ['CES_ERROR_NO_SETUP_FILE'] = "Файл настроек не найден.";
$MESS ['TITLE_EXPORT_PAGE'] = "Настройка экспорта";
$MESS ['CES_ERRORS'] = "Ошибки при выполнении операции:";
$MESS ['CES_SUCCESS'] = "Операция успешно завершена.";
$MESS ['CES_EXPORT_FILE'] = "Файл экспорта:";
$MESS ['CES_EXPORTER'] = "Экспортер";
$MESS ['CES_ACTIONS'] = "Действия";
$MESS ['CES_PROFILE'] = "Профиль";
$MESS ['CES_IN_MENU'] = "Меню";
$MESS ['CES_IN_AGENT'] = "Агент";
$MESS ['CES_IN_CRON'] = "Cron";
$MESS ['CES_USED'] = "Использован";
$MESS ['CES_ADD_PROFILE_DESCR'] = "Добавить новый профиль экспорта";
$MESS ['CES_ADD_PROFILE'] = "Добавить профиль";
$MESS ['CES_DEFAULT'] = "По умолчанию";
$MESS ['CES_NO'] = "Нет";
$MESS ['CES_YES'] = "Да";
$MESS ['CES_RUN_INTERVAL'] = "Интервал между запусками (часов):";
$MESS ['CES_SET'] = "Установить";
$MESS ['CES_DELETE'] = "Удалить";
$MESS ['CES_CLOSE'] = "Закрыть";
$MESS ['CES_OR'] = "или";
$MESS ['CES_RUN_TIME'] = "Время запуска:";
$MESS ['CES_PHP_PATH'] = "Путь к php:";
$MESS ['CES_AUTO_CRON'] = "Установить автоматически:";
$MESS ['CES_AUTO_CRON_DEL'] = "Удалить автоматически:";
$MESS ['CES_RUN_EXPORT_DESCR'] = "Начать экспорт данных";
$MESS ['CES_RUN_EXPORT'] = "Экспортировать";
$MESS ['CES_TO_LEFT_MENU_DESCR'] = "Вынести пункт в левое меню";
$MESS ['CES_TO_LEFT_MENU_DESCR_DEL'] = "Удалить пункт из левого меню";
$MESS ['CES_TO_LEFT_MENU'] = "Добавить в меню";
$MESS ['CES_TO_LEFT_MENU_DEL'] = "Удалить из меню";
$MESS ['CES_TO_AGENT_DESCR'] = "Создать агента автоматического выполнения";
$MESS ['CES_TO_AGENT_DESCR_DEL'] = "Удалить агента автоматического выполнения";
$MESS ['CES_TO_AGENT'] = "Создать агента";
$MESS ['CES_TO_AGENT_DEL'] = "Удалить агента";
$MESS ['CES_TO_CRON_DESCR'] = "Привязать к cron для автоматического выполнения";
$MESS ['CES_TO_CRON_DESCR_DEL'] = "Удалить из cron";
$MESS ['CES_TO_CRON'] = "Привязать к cron";
$MESS ['CES_TO_CRON_DEL'] = "Удалить из cron";
$MESS ['CES_SHOW_VARS_LIST_DESCR'] = "Показать список переменных этого профиля экспорта";
$MESS ['CES_SHOW_VARS_LIST'] = "Список переменных";
$MESS ['CES_DELETE_PROFILE_DESCR'] = "Удалить профиль";
$MESS ['CES_DELETE_PROFILE_CONF'] = "Вы уверены, что хотите удалить этот профиль?";
$MESS ['CES_DELETE_PROFILE'] = "Удалить профиль";
$MESS ['CES_NOTES1'] = "Агенты - это PHP-функции, которые запускаются с определенной периодичностью. В самом начале загрузки каждой страницы система автоматически проверяет, есть ли агент, который нуждается в запуске, и в случае необходимости исполняет его. Не рекомендуется создавать агентов для длительных по времени выгрузок. Для этих случаев лучше использовать cron.";
$MESS ['CES_NOTES2'] = "Утилита cron доступна только на хостингах, работающих под операционными системами семейства UNIX.";
$MESS ['CES_NOTES3'] = "Утилита cron работает в фоновом режиме и выполняет указанные задачи в указанное время. Для включения экспорта в список задач необходимо установить конфигурационный файл";
$MESS ['CES_NOTES4'] = "в cron. Этот файл содержит инструкции на выполнение указанных вами экспортов. После изменения набора экспортов, установленных на cron, необходимо заново установить конфигурационный файл.";
$MESS ['CES_NOTES5'] = "Для установки конфигурационного файла необходимо соединиться с вашим сайтом по SSH (SSH2) или какому-либо другому аналогичному протоколу, поддерживаемому вашим провайдером для удаленного доступа. В строке ввода нужно выполнить комманду";
$MESS ['CES_NOTES6'] = "Для просмотра списка установленных задач нужно выполнить комманду";
$MESS ['CES_NOTES7'] = "Для удаления списка установленных задач нужно выполнить комманду";
$MESS ['CES_NOTES8'] = "Текущий список установленных на cron задач:";
$MESS ['CES_NOTES10'] = "Внимание! Если у вас установлены на cron задачи, которых нет в конфигурационном файле, то при применении этого файла такие задачи будут удалены.";
$MESS ['CES_NOTES11'] = "Оболочкой для выполнения задач на cron является файл";
$MESS ['CES_NOTES12'] = "Убедитесь, что в нем прописаны правильные пути к php и корню сайта.";
$MESS ['export_setup_cat'] = "Скрипты экспорта находятся в каталоге:";
$MESS ['export_setup_script'] = "Скрипт экспорта";
$MESS ['export_setup_name'] = "Название";
$MESS ['export_setup_file'] = "Файл";
$MESS ['export_setup_begin'] = "Начать экспорт данных";
?>