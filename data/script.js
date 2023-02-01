document.getElementById('edit-toggle').onclick = function() {
    var edit = document.getElementById('edit-area');
    if(edit.style.display != "block") {
        edit.style.display = "block";
     } else {
         edit.style.display = "none";
     }
}