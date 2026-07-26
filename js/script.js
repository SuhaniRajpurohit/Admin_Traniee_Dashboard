function confirmDeleteTrainee(name) {
    return confirm("Are you sure you want to delete trainee " + name + "?");
}

function confirmDeleteTask() {
    return confirm("Are you sure you want to delete this task?");
}

function validateTraineeForm(event) {
    var fullName = document.getElementById("full_name").value.trim();
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var joiningDate = document.getElementById("joining_date").value;
    if (fullName === "" || email === "" || phone === "" || joiningDate === "") {
        alert("Please fill in all required fields.");
        event.preventDefault();
        return false;
    }
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        event.preventDefault();
        return false;
    }
    return true;
}

function validateTaskForm(event) {
    var title = document.getElementById("task_title").value.trim();
    var assignedDate = document.getElementById("assigned_date").value;
    var dueDate = document.getElementById("due_date").value;
    if (title === "" || assignedDate === "" || dueDate === "") {
        alert("Please fill in all required fields.");
        event.preventDefault();
        return false;
    }
    if (new Date(dueDate) < new Date(assignedDate)) {
        alert("Due Date cannot be before Assigned Date.");
        event.preventDefault();
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", function() {
    var traineeForm = document.getElementById("traineeForm");
    if (traineeForm) {
        traineeForm.addEventListener("submit", validateTraineeForm);
    }
    var taskForm = document.getElementById("taskForm");
    if (taskForm) {
        taskForm.addEventListener("submit", validateTaskForm);
    }
});
