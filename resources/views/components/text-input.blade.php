@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'mt-1 block w-full bg-[#0a0a0f] border border-white/10 text-white placeholder:text-gray-600 rounded-xl px-4 py-3 shadow-sm focus:border-[#a6cb40] focus:ring-2 focus:ring-[#a6cb40]/30 focus:outline-none transition-colors']) }}>
