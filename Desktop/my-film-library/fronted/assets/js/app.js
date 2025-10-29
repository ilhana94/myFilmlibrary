$(document).ready(function () {
    var app = $.spapp({
        defaultView: "home", // stranica koja se prva učitava
        templateDir: "views/" // folder gdje čuvaš sve HTML fajlove
    });

    // 🔹 Definicije ruta
    app.route({
        view: "home",
        load: "home.html"
    });
    app.route({
        view: "about",
        load: "about.html"
    });

    app.route({
        view: "services",
        load: "services.html"
    });

    app.route({
        view: "portfolio",
        load: "portfolio.html"
    });

    app.route({
        view: "contact",
        load: "contact.html"
    });

    app.route({
        view: "login",
        load: "login.html"
    });

    app.route({
        view: "register",
        load: "register.html"
    });

    app.route({
        view: "profile",
        load: "profile.html"
    });

    app.run();
});
function checkUserRole() {
    const token = localStorage.getItem("jwt");
    if (!token) {
        // ako nije logovan
        document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
        return;
    }

    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        const role = payload.role;

        if (role !== "admin") {
            document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
        }
    } catch (e) {
        console.error("Invalid token:", e);
        document.querySelectorAll(".admin-only").forEach(el => el.style.display = "none");
    }
}

checkUserRole();
