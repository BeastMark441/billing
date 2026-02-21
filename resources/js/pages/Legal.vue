<template>
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <h1 class="text-4xl font-bold mb-8 text-center">Юридическая информация</h1>
        
        <div class="bg-gray-900 p-8 rounded-xl border border-gray-800 space-y-8">
            <section>
                <h2 class="text-2xl font-bold text-primary mb-4">Публичная оферта</h2>
                <div class="text-gray-400 space-y-2 text-sm">
                    <p>1. Общие положения</p>
                    <p>1.1. Настоящая оферта является официальным предложением сервиса Nodeum...</p>
                    <p>1.2. Акцептом оферты является регистрация на сайте и оплата услуг...</p>
                    <p>...</p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-primary mb-4">Политика конфиденциальности</h2>
                <div class="text-gray-400 space-y-2 text-sm">
                    <p>1. Сбор данных</p>
                    <p>Мы собираем только необходимые данные для предоставления услуг хостинга: email, имя...</p>
                    <p>2. Хранение данных</p>
                    <p>Все данные хранятся в зашифрованном виде и не передаются третьим лицам...</p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-primary mb-4">Реквизиты</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-400">
                    <div>
                        <p class="font-bold text-white">Наименование:</p>
                        <p>ИП "Nodeum Hosting"</p>
                    </div>
                    <div>
                        <p class="font-bold text-white">ИНН:</p>
                        <p>000000000000</p>
                    </div>
                    <div>
                        <p class="font-bold text-white">ОГРНИП:</p>
                        <p>000000000000000</p>
                    </div>
                    <div>
                        <p class="font-bold text-white">Адрес:</p>
                        <p>г. Москва, ул. Примерная, д. 1</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Legal'
}
</script>
