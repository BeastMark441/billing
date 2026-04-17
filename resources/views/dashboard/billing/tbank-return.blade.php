<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-2">Подтверждение оплаты</h2>
            <p class="text-gray-400">Проверяем статус платежа в T-Bank и пополняем баланс автоматически.</p>
        </div>

        <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-gray-500 text-sm mb-1">Сумма пополнения</div>
                    <div class="text-white font-mono text-3xl font-bold">+{{ number_format((float) $payment->amount, 2, '.', ' ') }} ₽</div>
                </div>
                <div class="text-right">
                    <div class="text-gray-500 text-sm mb-1">Текущий статус</div>
                    <span id="payment-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_color }} bg-white/5">
                        <span id="payment-status-label">{{ $payment->status_label }}</span>
                    </span>
                </div>
            </div>

            <div class="mt-6 bg-[#050508] border border-white/10 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 border-2 border-white/20 border-t-[#a6cb40] rounded-full animate-spin"></div>
                    <div class="text-sm text-gray-300">
                        <span id="payment-progress-text">Ожидаем подтверждение банка…</span>
                    </div>
                </div>
                <div id="payment-error" class="hidden mt-3 text-xs text-red-300"></div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('dashboard.billing') }}" class="px-5 py-2.5 rounded-xl border border-white/10 text-gray-300 hover:text-white hover:bg-white/5 transition-colors text-sm font-medium text-center">
                    Вернуться в финансы
                </a>
                <button type="button" id="manual-check" class="px-5 py-2.5 rounded-xl bg-[#a6cb40] hover:bg-[#bbe053] text-[#0a0a0f] transition-colors text-sm font-bold">
                    Проверить сейчас
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusUrl = "{{ route('payments.tbank.status', ['payment' => $payment->id]) }}";
            const badge = document.getElementById('payment-status-badge');
            const label = document.getElementById('payment-status-label');
            const progress = document.getElementById('payment-progress-text');
            const errorBox = document.getElementById('payment-error');
            const button = document.getElementById('manual-check');
            let attempts = 0;
            let stopped = false;

            async function check() {
                if (stopped) {
                    return;
                }
                attempts++;

                try {
                    const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    if (data && data.status_label) {
                        label.textContent = data.status_label;
                    }

                    if (data && data.error) {
                        errorBox.classList.remove('hidden');
                        errorBox.textContent = data.error;
                    } else {
                        errorBox.classList.add('hidden');
                        errorBox.textContent = '';
                    }

                    if (data && data.credited) {
                        stopped = true;
                        progress.textContent = 'Баланс пополнен. Перенаправляем…';
                        window.location.href = "{{ route('dashboard.billing') }}";
                        return;
                    }

                    progress.textContent = attempts < 6
                        ? 'Подтверждаем оплату…'
                        : 'Банк обрабатывает платеж. Проверяем снова…';
                } catch (e) {
                    errorBox.classList.remove('hidden');
                    errorBox.textContent = 'Не удалось проверить статус платежа. Повторите попытку.';
                }

                if (attempts < 30) {
                    setTimeout(check, 2000);
                } else {
                    progress.textContent = 'Платёж ещё в обработке. Можно вернуться в финансы и проверить позже.';
                }
            }

            if (button) {
                button.addEventListener('click', function () {
                    attempts = 0;
                    stopped = false;
                    check();
                });
            }

            check();
        });
    </script>
</x-app-layout>

