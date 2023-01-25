var buttons = document.querySelectorAll('.edit-toggle');
buttons.forEach(function(button) {
    button.onclick = function() {
        var edit = document.getElementById(button.dataset.toggle);
        if(edit.style.display != "block") {
           edit.style.display = "block";
        } else {
            edit.style.display = "none";
        }

    };
});