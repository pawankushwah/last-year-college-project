<div class="absolute top-2 left-2 z-50 bg-white text-black dark:text-white dark:bg-gray-900 rounded-full opacity-20 hover:opacity-100">
    <label class="swap swap-rotate p-2">
        <input id="theme-toggle" type="checkbox" class="theme-controller" />

        <i class="swap-on text-lg sm:text-xl lg:text-2xl fill-current fa-solid fa-sun"></i>
        <i class="swap-off text-lg sm:text-xl lg:text-2xl fill-current fa-solid fa-moon"></i>
    </label>
</div>

<script>
    const themes = ["light", "dark"];
    const checkbox = document.getElementById("theme-toggle");

    if (!localStorage.theme || !themes.includes(localStorage.theme)) {
        localStorage.setItem("theme", themes[0]);
    }
    // if(localStorage.theme === themes[1]) {
    document.documentElement.classList.add(localStorage.theme);
    document.documentElement.setAttribute("data-theme", localStorage.theme);
    // }
    localStorage.theme === themes[1] ? checkbox.checked = false : checkbox.checked = true;

    checkbox.onclick = () => {
        localStorage.theme = localStorage.theme === themes[0] ? themes[1] : themes[0];
        document.documentElement.classList = '';
        document.documentElement.classList.add(localStorage.theme);
        document.documentElement.setAttribute("data-theme", localStorage.theme);
    }
</script>