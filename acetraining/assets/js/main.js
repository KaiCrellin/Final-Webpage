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

// determine the dashboard URL based on the user role
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
// Toggle Create Assignment
function showCourseCreate() {
    const showFormButton = document.getElementById('showFormButton');
    const assignmentForm = document.getElementById('assignmentForm');

    if (showFormButton && assignmentForm) {
        showFormButton.addEventListener('click', function () {
            if (assignmentForm.style.display === 'none' || assignmentForm.style.display === '') {
                assignmentForm.style.display = 'block';
            } else {
                assignmentForm.style.display = 'none';
            }
        });
    }
}

// navigation dropdown toggle
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
// navigation handle dropdown
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
// handle logout button
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
    showCourseCreate();
});

document.addEventListener("DOMContentLoaded", function () {
    handleDropdown();
    showCourseCreate();
});

//toggle course blocks
function toggleCourseContent(button) {
    const courseBlock = button.closest('.course-block');
    const content = courseBlock.querySelector('.course-content');
    const isHidden = content.classList.contains('hidden');


    content.classList.toggle('hidden');


    button.classList.toggle('active');


    if (!isHidden) return;

    const allContents = document.querySelectorAll('.course-content:not(.hidden)');
    const allButtons = document.querySelectorAll('.block-dropdown-button.active');

    allContents.forEach(item => {
        if (item !== content) {
            item.classList.add('hidden');
        }
    });

    allButtons.forEach(btn => {
        if (btn !== button) {
            btn.classList.remove('active');
        }
    });
}


