<?php

namespace App\Support;

use App\Models\AuditLog;

class AuditPresenter
{
    public static function title(AuditLog $log): string
    {
        $map = [
            'auth_login' => 'Вход в аккаунт',
            'auth_logout' => 'Выход из аккаунта',
            'auth_register' => 'Регистрация',
            'auth_password_reset' => 'Сброс пароля',
            'auth_password_changed' => 'Смена пароля',

            'profile_updated' => 'Обновление профиля',
            'profile_email_changed' => 'Смена email',

            'notification_preferences_updated' => 'Обновление настроек уведомлений',
            'telegram_link_token_created' => 'Запрос привязки Telegram',
            'telegram_unlinked' => 'Telegram отвязан',

            'ticket_created' => 'Создан тикет',
            'ticket_reply' => 'Ответ в тикете',

            'payment_create' => 'Создан платеж',
            'payment_create_failed' => 'Ошибка создания платежа',
            'payment_confirmed' => 'Платеж подтверждён',

            'order_created' => 'Оформлен заказ',
            'order_auto_renewal_toggled' => 'Автопродление изменено',
            'order_renewed' => 'Продление заказа',
            'order_renew_failed' => 'Ошибка продления заказа',
            'order_auto_renewed' => 'Автопродление заказа',
            'order_auto_renew_failed' => 'Ошибка автопродления',
            'order_suspended' => 'Услуга приостановлена',
            'order_suspend_failed' => 'Ошибка приостановки услуги',
            'order_terminated' => 'Услуга удалена',
            'order_terminate_failed' => 'Ошибка удаления услуги',

            'pterodactyl_provision_success' => 'Сервер активирован',
            'pterodactyl_provision_failed' => 'Ошибка активации сервера',

            'pterodactyl_sync_conflict' => 'Конфликт синхронизации Pterodactyl',
            'pterodactyl_sync_remote_suspended' => 'Сервер приостановлен на стороне Pterodactyl',
            'pterodactyl_sync_error' => 'Ошибка синхронизации Pterodactyl',

            'admin_ticket_updated' => 'Тикет обновлён администратором',
            'admin_ticket_reply' => 'Ответ администратора в тикете',
        ];

        return $map[$log->action] ?? self::fallbackTitle($log->action);
    }

    public static function subtitle(AuditLog $log): ?string
    {
        $meta = is_array($log->meta) ? $log->meta : [];

        switch ($log->action) {
            case 'payment_create':
                if (isset($meta['amount'])) {
                    return 'Сумма: '.number_format((float) $meta['amount'], 0, '.', ' ').' ₽';
                }

                return null;
            case 'payment_confirmed':
                if (isset($meta['payment_id'])) {
                    return 'Платёж #'.(string) $meta['payment_id'].' успешно проведён';
                }

                return null;
            case 'payment_create_failed':
                return isset($meta['error']) ? 'Причина: '.self::limit((string) $meta['error']) : null;
            case 'order_created':
                if (isset($meta['price'])) {
                    return 'Стоимость: '.number_format((float) $meta['price'], 0, '.', ' ').' ₽';
                }

                return null;
            case 'notification_preferences_updated':
                $parts = [];
                if (array_key_exists('notify_email', $meta)) {
                    $parts[] = 'Email: '.(((bool) $meta['notify_email']) ? 'включён' : 'выключен');
                }
                if (array_key_exists('notify_telegram', $meta)) {
                    $parts[] = 'Telegram: '.(((bool) $meta['notify_telegram']) ? 'включён' : 'выключен');
                }

                return count($parts) ? implode(' · ', $parts) : null;
            case 'order_auto_renewal_toggled':
                if (array_key_exists('enabled', $meta)) {
                    return ((bool) $meta['enabled']) ? 'Включено' : 'Выключено';
                }

                return null;
            case 'order_renewed':
            case 'order_auto_renewed':
                if (isset($meta['amount'])) {
                    return 'Сумма: '.number_format((float) $meta['amount'], 0, '.', ' ').' ₽';
                }

                return null;
            case 'auth_login':
                return isset($meta['ip']) ? 'IP: '.(string) $meta['ip'] : null;
            case 'auth_password_changed':
            case 'auth_password_reset':
                return isset($meta['ip']) ? 'IP: '.(string) $meta['ip'] : null;
            case 'profile_email_changed':
                if (isset($meta['to'])) {
                    return 'Новый email: '.(string) $meta['to'];
                }

                return null;
            case 'ticket_created':
                if (isset($meta['priority'])) {
                    $priorityMap = [
                        'low' => 'низкий',
                        'medium' => 'средний',
                        'high' => 'высокий',
                    ];
                    $priority = strtolower((string) $meta['priority']);

                    return 'Приоритет: '.($priorityMap[$priority] ?? (string) $meta['priority']);
                }

                return null;
            case 'pterodactyl_provision_failed':
            case 'pterodactyl_sync_error':
                return isset($meta['error']) ? 'Причина: '.self::limit((string) $meta['error']) : null;
            case 'pterodactyl_sync_conflict':
                if (isset($meta['remote'], $meta['local'])) {
                    return 'Локально: '.(string) $meta['local'].' · На сервере: '.(string) $meta['remote'];
                }

                return null;
            case 'pterodactyl_sync_remote_suspended':
                return isset($meta['reason']) ? 'Причина: '.(string) $meta['reason'] : null;
            default:
                return null;
        }
    }

    private static function fallbackTitle(string $action): string
    {
        $action = str_replace(['_', '-'], ' ', $action);
        $action = trim($action);
        if ($action === '') {
            return 'Событие';
        }

        return mb_strtoupper(mb_substr($action, 0, 1)).mb_substr($action, 1);
    }

    private static function limit(string $value, int $max = 120): string
    {
        $value = trim($value);
        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max - 1).'…';
    }
}
