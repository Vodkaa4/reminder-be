<button
    x-data="{ 
        theme: localStorage.getItem('theme') || 'system',
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            $dispatch('theme-changed', this.theme);
        }
    }"
    x-init="$watch('theme', val => {
        if(val === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
    })"
    x-on:click="toggleTheme()"
    type="button"
    class="flex items-center justify-center w-10 h-10 rounded-full text-gray-500 hover:bg-gray-500/10 dark:text-gray-400 dark:hover:bg-gray-500/20 transition mr-2"
>
    <x-heroicon-o-moon x-show="theme !== 'dark'" class="w-5 h-5" />
    <x-heroicon-o-sun x-show="theme === 'dark'" class="w-5 h-5" x-cloak />
</button>
