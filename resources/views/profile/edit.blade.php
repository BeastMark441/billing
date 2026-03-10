<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white">Профиль</h2>
                <p class="text-gray-400 mt-1">Управление личной информацией и настройками безопасности</p>
            </div>
            
            @if(Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
                <div class="flex items-center gap-3 px-4 py-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl text-yellow-500">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="text-sm font-medium">Ваш Email не подтвержден</div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Profile Info -->
            <div class="space-y-8">
                <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 md:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Security & Danger Zone -->
            <div class="space-y-8">
                <div class="bg-[#0f0f13] border border-white/5 rounded-2xl p-6 md:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="bg-[#0f0f13] border border-red-500/20 rounded-2xl p-6 md:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
