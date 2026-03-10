<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Профиль</h2>
            <p class="text-sm text-gray-400">Управление учетной записью</p>
        </div>

        <div class="space-y-6">
            <div class="p-4 sm:p-8 bg-[#050508] border border-white/10 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-[#050508] border border-white/10 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-[#050508] border border-white/10 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
