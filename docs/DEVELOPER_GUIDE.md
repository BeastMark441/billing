# Руководство Разработчика NODEUM Billing

Это техническая документация по архитектуре, стеку технологий и процессам разработки NODEUM Billing.

## Содержание
1.  [Стек Технологий](#стек-технологий)
2.  [Архитектура Проекта](#архитектура-проекта)
    *   [Модели (Models)](#модели-models)
    *   [Сервисы (Services)](#сервисы-services)
    *   [Команды (Commands)](#команды-commands)
3.  [Структура Базы Данных](#структура-базы-данных)
4.  [Взаимодействие с Pterodactyl](#взаимодействие-с-pterodactyl)
5.  [Планы на Будущее (Roadmap)](#планы-на-будущее-roadmap)

---

## Стек Технологий

*   **Backend**: Laravel 12.x (PHP 8.2+)
*   **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
*   **Database**: MySQL / MariaDB
*   **API Client**: Guzzle HTTP (через Laravel Http Facade)
*   **Queue**: поддерживается (database/redis). В критических местах (webhook T‑Bank) обработка синхронная.
*   **Auth**: Laravel Breeze (Blade)
*   **PDF**: dompdf/dompdf
*   **QR**: endroid/qr-code

## Архитектура Проекта

Проект следует стандартной MVC архитектуре Laravel с выделением бизнес-логики в сервисы.

### Модели (Models)
*   `User`: Пользователь системы. Имеет баланс (`decimal`), роль (`role`), флаги уведомлений, Telegram-связку.
*   `Order`: Заказ услуги. Связан с `User` и `InfrastructureService`. Хранит `pterodactyl_server_id` и `last_error`.
    *   Статусы: `active`, `suspended`, `cancelled`, `failed`, `pending`, `paid`.
    *   `auto_renewal`: bool (автопродление).
*   `InfrastructureService`: Тарифный план. Хранит JSON-конфигурацию для Pterodactyl (`specifications`).
*   `Ticket` / `TicketMessage`: Система поддержки. Полиморфная связь с вложениями (Attachments).
*   `UserLog`: Логирование действий (Observer pattern).
*   `Payment`: Платежи T‑Bank (создание/статусы/credited_at).
*   `BalanceLog`: Финансовая история (пополнения/списания/продления/покупки).
*   `Receipt`: Чеки (PDF/Email/публичная ссылка/подпись/QR-верификация).
*   `AuditLog`: Системный аудит (действия админки, платежи, чеки).

### Сервисы (Services)
Основная логика работы с внешними API вынесена в `App\Services`.

#### `PterodactylService`
Отвечает за все взаимодействие с Pterodactyl Panel API.
*   `provisionServer(Order $order)`: Создает пользователя (если нет) и сервер.
*   `suspendServer($serverId)`: Замораживает сервер.
*   `unsuspendServer($serverId)`: Размораживает сервер.
*   `deleteServer($serverId)`: Удаляет сервер.
*   `findFreeAllocation($nodeId)`: Ищет свободный порт на узле (Node).
*   `getServerDetails($serverId)`: Получает информацию о сервере.

#### `TBankApiService`
Инициализация счета (`invoice/send`) и проверка статуса.
* `createInvoice(Payment $payment)`: Создает счет и возвращает ссылку на оплату.
* `verifyWebhook(Request $request)`: Безопасная верификация `x-signature` (RSA).

#### `TBankApiController`
Единая точка обработки платежей и вебхуков:
* `store(Request $request)`: Создание локального платежа и редирект на T-Bank.
* `webhook(Request $request)`: Прием и верификация вебхука, начисление баланса.
* при `PAID` начисляет баланс, пишет `balance_logs`, ставит `credited_at`
* создаёт чек и отправляет уведомление через `GeneralNotification`

#### `ReceiptService`
Формирование чека, PDF, QR и отправка на email.

### Команды (Commands)
Автоматизация процессов через Artisan Console.
*   `billing:check-expirations`: Ежедневная проверка сроков.
    *   Пытается продлить заказы (автопродление).
    *   Замораживает просроченные (`active` -> `suspended`).
    *   Удаляет давно просроченные (>7 дней) (`suspended` -> `cancelled` + delete server).
*   `billing:sync-status`: Синхронизация статусов.
    *   Если в панели `suspended`, а в БД `active` -> меняет на `suspended`.
    *   Если в панели `active`, а в БД `suspended` (и не просрочен) -> размораживает в БД.

*   `payments:reconcile-tbank`: Дожим pending платежей T‑Bank через `GetState`.
    *   `--minutes>0` — проверяет платежи старше N минут.
    *   `--minutes=0` — проверяет все pending платежи.

## Структура Базы Данных

Основные таблицы:
*   `users`: id, name, email, balance, role, pterodactyl_id...
*   `orders`: id, user_id, service_id, status, price, expires_at, auto_renewal, server_ip, server_port...
*   `infrastructure_services`: id, name, price, specifications (json), category_id...
*   `tickets`: id, user_id, subject, status, priority...
*   `payments`: id, user_id, amount, status, payment_id, credited_at, payload...
*   `balance_logs`: id, user_id, amount, type, description...
*   `receipts`: uuid, user_id, receipt_number, type, amount, public_token, signature, pdf_path...
*   `audit_logs`: системный аудит
*   `user_logs`: действия пользователя (логин/логаут/регистрация/и т.д.)

## Взаимодействие с Pterodactyl

Интеграция реализована через Application API Pterodactyl.
*   **Авторизация**: Bearer Token (в `.env` как `PTERODACTYL_APP_KEY`).
*   **Создание сервера**:
    1.  Проверка/Создание пользователя в Pterodactyl.
    2.  Поиск свободного порта (`allocations`).
    3.  Отправка POST запроса на создание сервера с параметрами из `InfrastructureService`.
    4.  Сохранение `server_id` и `identifier` в `orders`.

**Особенности**:
*   Мы не используем `allocation.default` (1), так как это часто вызывает ошибки. Вместо этого ищем свободный порт вручную.
*   Ошибки API сохраняются в поле `last_error` заказа для отладки.

## Планы на Будущее (Roadmap)

В следующих версиях планируется реализовать:
1.  **Надежность платежей**: улучшение диагностики доставок вебхуков, авто-reconcile по расписанию.
2.  **Смена тарифа (Server Build Update)**: Автоматическое изменение ресурсов (CPU/RAM) в Pterodactyl при смене тарифа в биллинге.
3.  **Переустановка сервера**: Возможность переустановить сервер (Reinstall) из личного кабинета.
4.  **Бекапы**: Управление резервными копиями через биллинг.
5.  **API для разработчиков**: REST API для управления заказами из сторонних приложений.
6.  **Уведомления**: развитие Email/Telegram уведомлений и шаблонов.
