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
            case 'payment_create_failed':
                return isset($meta['error']) ? 'Причина: '.self::limit((string) $meta['error']) : null;
            case 'order_created':
                if (isset($meta['price'])) {
                    return 'Стоимость: '.number_format((float) $meta['price'], 0, '.', ' ').' ₽';
                }
                return null;
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
                    return 'Приоритет: '.(string) $meta['priority'];
                }
                return null;
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

