document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme");

    if (!themeToggle) {
        console.error("Elemento #theme non trovato!");
        return;
    }

    const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
    const savedTheme = getCookie("theme");

    function applyTheme(theme) {
        document.documentElement.className = theme;
        themeToggle.className = `fa-solid ${theme === "dark" ? "fa-moon" : "fa-sun"}`;
        reloadCSS();
    }

    function reloadCSS() {
        const links = document.querySelectorAll("link[rel='stylesheet']");
        links.forEach(link => {
            const newLink = link.cloneNode();
            newLink.href = link.href.split("?")[0] + "?v=" + new Date().getTime();
            newLink.onload = () => link.remove();
            link.parentNode.insertBefore(newLink, link.nextSibling);
        });
    }

    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        const defaultTheme = prefersDarkScheme.matches ? "dark" : "light";
        applyTheme(defaultTheme);
        setCookie("theme", defaultTheme, 365);
    }

    themeToggle.addEventListener("click", function () {
        const currentTheme = document.documentElement.className;
        const newTheme = currentTheme === "dark" ? "light" : "dark";
        applyTheme(newTheme);
        setCookie("theme", newTheme, 365);
        console.log(`Tema cambiato: ${newTheme}`);
    });

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = `${name}=${value}; path=/;${expires}`;
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
        }
        return null;
    }
});
