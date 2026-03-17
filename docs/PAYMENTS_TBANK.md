# Платежи T‑Bank

Документ описывает поток пополнения баланса через T‑Bank, webhook, статусы и команды диагностики.

## Поток пополнения

1. Пользователь создаёт платеж в личном кабинете (форма пополнения).
2. Система создаёт запись `payments` со статусом `pending`.
3. Система вызывает T‑Bank `Init` и делает редирект на `PaymentURL`.
4. После оплаты:
   - пользователь возвращается на `SuccessURL` / `FailURL`;
   - банк отправляет webhook на `NotificationURL` при смене статуса.

Важно:
* Редирект на `SuccessURL` не является источником истины о зачислении.
* Истина — это статус у банка (webhook или `GetState`).

## Endpoints

* `POST /payments/create` — создание платежа.
* `GET /payments/success` — возврат пользователя после оплаты.
* `GET /payments/failed` — возврат пользователя при неуспехе.
* `POST /payments/webhook` — webhook от банка (CSRF исключён).

## Статусы

Локально в `payments.status` сохраняется статус, который пришёл от провайдера (в нижнем регистре).

Зачисление баланса происходит только при `CONFIRMED` и один раз (через `credited_at`).

## Переменные окружения

Обязательные:
* `TBANK_TERMINAL_KEY`
* `TBANK_PASSWORD`
* `TBANK_API_URL` (обычно `https://securepay.tinkoff.ru/v2/`)

Рекомендуемые:
* `TBANK_WEBHOOK_URL` — внешний публичный URL для `NotificationURL`.
  Полезно, если `APP_URL` в окружении не совпадает с публичным доменом.

## Локальная разработка

Для приема webhook нужен публичный HTTPS URL.

Пример через ngrok:
```bash
ngrok http 8000
```

После запуска:
* выставить `APP_URL` и `TBANK_WEBHOOK_URL` на HTTPS URL ngrok,
* убедиться, что `POST {ngrok}/payments/webhook` доступен снаружи.

## Диагностика и команды

### Дожать pending платежи

Команда опрашивает `GetState` и применяет статус к `payments` (а при `CONFIRMED` — начисляет баланс и создаёт `balance_logs`).

* Проверить все pending платежи:
```bash
php artisan payments:reconcile-tbank --minutes=0 --limit=50
```

* Проверить только платежи старше 5 минут:
```bash
php artisan payments:reconcile-tbank --minutes=5 --limit=50
```

### Что проверять, если платежи не зачисляются

* Проверь, что `NotificationURL` реально указывает на публичный `/payments/webhook`.
* Проверь, что приложение отвечает банку `2XX`.
* Посмотри `AuditLog` по событиям `payment_*` и логи приложения.

