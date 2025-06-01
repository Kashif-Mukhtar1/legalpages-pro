document.addEventListener("DOMContentLoaded", function () {
    if (!localStorage.getItem("cookiesAccepted")) {
        const banner = document.createElement("div");
        banner.innerHTML = `
            <div id="cookie-banner" style="position: fixed; bottom: 0; width: 100%; background: #000; color: #fff; padding: 15px; text-align: center; z-index: 9999;">
                🍪 We use cookies to improve your experience.
                <a href="${legalpages_vars.cookies_link}" style="color: #ddd; text-decoration: underline; margin-left: 8px;">
                    Read our Cookies Policy
                </a>
                <button id="accept-cookies" style="margin-left: 15px; padding: 6px 14px; background: #eee; border: none; color: #000; border-radius: 4px; cursor: pointer;">
                    Accept
                </button>
            </div>
        `;
        document.body.appendChild(banner);
        document.getElementById("accept-cookies").addEventListener("click", function () {
            localStorage.setItem("cookiesAccepted", true);
            document.getElementById("cookie-banner").remove();
        });
    }
});