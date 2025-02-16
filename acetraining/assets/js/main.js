/*Purpose: Main java scripts*/
function closeModel() {
    document.getElementById('assignmentmodel').style.display = 'none';
}


function openModel() {
    document.getElementById('assignmentmodel').style.display = 'block';
}

function togglePasswordVisibility() {
    var passwordField = document.getElementById('password');
    var toggleCheckbox = document.getElementById('togglePassword');
    if (toggleCheckbox.checked) {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}


function getDashboardURL() {
    switch ($role) {
        case 'admin':
            return '/webpage/pages/admin_dashboard.php';
        case 'tutor':
            return '/webpage/pages/tutor_dashboard.php';
        case 'student':
            return '/webpage/pages/student_dashboard.php';
        default:
            return '';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logout-button");

    if (logoutButton) {
        logoutButton.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to log out?")) {
                event.preventDefault();
            }
        });
    }
});


function toggleDropdown() {
    document.getElementById("nav-content-dropdown").classList.toggle("show");
}
window.onclick = function (event) {
    if (!event.targets.matches('.dropdown-button')) {
        var dropdowns = document.getElementByClassName("nav-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdown[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function handleDropdown() {
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownButton = document.querySelector(".dropdown-button");
        if (dropdownButton) {
            dropdownButton.addEventListener("click", function () {
                document.getElementById("nav-content-dropdown").classList.toggle("show");
            });
        }
    });
    window.onclick = function (event) {
        if (!event.targets.matches('.dropdown-button')) {
            var dropdowns = document.getElementByClassName("nav-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdown[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
}
document.addEventListener("DOMContentLoaded", function () {
    const logoutButton = document.getElementById("logout-button");

    if (logoutButton) {
        logoutButton.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to log out?")) {
                event.preventDefault();
            }
        });
    }
    handleDropdown();
});
