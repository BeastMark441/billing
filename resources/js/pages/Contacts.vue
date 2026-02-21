<template>
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <h1 class="text-4xl font-bold mb-8 text-center">Контакты</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-xl font-bold text-primary mb-2">Техническая поддержка</h3>
                    <p class="text-gray-400">Мы работаем круглосуточно, 24/7 без выходных.</p>
                    <a href="mailto:support@nodeum.ru" class="text-white hover:text-primary transition">support@nodeum.ru</a>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold text-primary mb-2">Отдел продаж</h3>
                    <p class="text-gray-400">По вопросам сотрудничества и индивидуальных тарифов.</p>
                    <a href="mailto:sales@nodeum.ru" class="text-white hover:text-primary transition">sales@nodeum.ru</a>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-primary mb-2">Социальные сети</h3>
                    <div class="flex gap-4">
                        <a href="#" class="bg-gray-800 p-3 rounded hover:bg-blue-600 transition">Telegram</a>
                        <a href="#" class="bg-gray-800 p-3 rounded hover:bg-blue-800 transition">VKontakte</a>
                        <a href="#" class="bg-gray-800 p-3 rounded hover:bg-purple-600 transition">Discord</a>
                    </div>
                </div>
            </div>

            <!-- Feedback Form -->
            <div class="bg-gray-900 p-8 rounded-xl border border-gray-800">
                <h3 class="text-xl font-bold mb-6">Написать нам</h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1 text-gray-400">Ваше имя</label>
                        <input type="text" class="w-full bg-gray-800 border border-gray-700 rounded p-2 focus:border-primary focus:outline-none" placeholder="Иван Иванов">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-400">Email для ответа</label>
                        <input type="email" class="w-full bg-gray-800 border border-gray-700 rounded p-2 focus:border-primary focus:outline-none" placeholder="example@mail.ru">
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-gray-400">Сообщение</label>
                        <textarea rows="4" class="w-full bg-gray-800 border border-gray-700 rounded p-2 focus:border-primary focus:outline-none" placeholder="Опишите ваш вопрос..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-black font-bold py-2 rounded hover:opacity-90 transition">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Contacts',
    methods: {
        submitForm() {
            alert('Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.');
        }
    }
}
</script>
