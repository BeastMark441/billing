# Дизайн страниц (desktop-first)

## Глобально
- Layout: desktop-first, контейнер 1200–1440px, сетка CSS Grid (основная: sidebar 280px + контент), внутри секций Flexbox.
- Responsive: на <1024px sidebar сворачивается в drawer; таблицы переходят в “card list”.
- Meta (по умолчанию): title = "Billing — {Page}", description = "Панель управления платежами, интеграциями и логами"; OG: title/description совпадают, og:type=website.
- Global styles (tokens):
  - Background: #0B1220 (dark) или #FFFFFF (light), по умолчанию light.
  - Primary: #2F6BFF, Success: #22C55E, Warning: #F59E0B, Danger: #EF4444.
  - Typography: 14/16/20/28 (base/h2/h1), шрифт Inter/Roboto.
  - Buttons: primary/secondary/ghost; hover +4% яркость; disabled 60% opacity.
  - Tables: фиксированная шапка, сортировка, плотность (compact/comfortable).
  - Links: underline on hover, focus-ring 2px primary.
- Общие компоненты:
  - Topbar: название проекта, поиск (по invoiceId/correlationId), меню профиля.
  - Sidebar: Dashboard, Payments, Integrations, Logs & Reports.
  - Toasts: успех/ошибка; ошибки содержат correlation-id.

---

## Страница: /login (Вход и доступ)
- Meta: title="Вход"; description="Авторизация в панели".
- Structure: centered card (480px) + help links.
- Components:
  - Form: Email, Password, кнопка "Войти".
  - Security hints: текст про ограничения попыток; ссылка "Забыли пароль" (если поддерживается).
  - States: loading, invalid credentials, rate-limited.

## Страница: /dashboard (Дашборд)
- Meta: title="Дашборд".
- Structure: grid 2x2 виджетов + широкая лента событий.
- Sections:
  1) KPI cards: "Платежи сегодня", "Ошибки вебхуков", "Очередь уведомлений", "Синхронизация Pterodactyl".
  2) Alerts panel: список критичных проблем (красные), предупреждения (желтые) с CTA: "Открыть лог" / "Повторить".
  3) Recent activity: последние 20 событий (платеж/вебхук/уведомление/синк) с correlation-id.
  4) Quick actions: "Создать счет", "Запустить синхронизацию", "Тест SMTP", "Тест Telegram".

## Страница: /payments (Платежи)
- Meta: title="Платежи".
- Structure: верхняя панель фильтров + таблица + drawer деталей.
- Sections:
  1) Filters: статус, период, пользователь, invoiceId, provider_payment_id.
  2) Invoices table: сумма/валюта/статус/создано/действия.
  3) Invoice drawer:
     - Summary: реквизиты, текущий статус.
     - TBANK: paymentId, ссылка/QR (если применимо), timestamps.
     - Webhook timeline: список событий (валидность подписи, idempotency_key, raw preview).
     - Actions: отменить счет (если разрешено), "скопировать correlation-id".

## Страница: /integrations (Интеграции и уведомления)
- Meta: title="Интеграции".
- Structure: tabbed layout (TBANK / Email / Telegram / Pterodactyl).
- Tabs:
  1) TBANK:
     - Fields: merchant/terminal параметры, webhook secret (masked), callback URL (read-only).
     - Buttons: "Сохранить", "Сгенерировать тестовый вебхук".
  2) Email:
     - SMTP host/port/user/password (masked), fromName/fromEmail.
     - Button: "Отправить тестовое письмо" + результат.
  3) Telegram:
     - Bot token (masked), статус подключения.
     - Привязка пользователя: блок "Мой Telegram" → кнопка "Привязать" (выдает код/ссылку), поле "Подтвердить код", кнопка "Отвязать".
     - Button: "Отправить тест".
  4) Pterodactyl:
     - Panel URL, API key (masked), режим синхронизации (manual/scheduled), период.
     - Button: "Запустить синхронизацию" + прогресс.

## Страница: /logs-reports (Логи и отчетность / QA)
- Meta: title="Логи и отчеты".
- Structure: split view: фильтры сверху, контент табами (Audit / Tech Logs / QA / Reports).
- Tabs:
  1) Audit:
     - Таблица: actor, role, action, objectType/objectId, createdAt, correlation-id.
     - Экспорт CSV по текущему фильтру.
  2) Tech Logs:
     - Filters: service (payments/webhooks/notify/ptero), level, correlation-id, период.
     - Просмотр записи: JSON pretty + кнопка "копировать".
  3) QA:
     - Checklist cards: "TBANK webhook", "SMTP", "Telegram", "Pterodactyl".
     - Button: "Запустить"; вывод: passed/failed + детали.
  4) Reports:
     - Form: период, тип отчета (платежи/ошибки/SLA), формат (CSV).
     - Result: ссылка на скачивание и краткая сводка.
