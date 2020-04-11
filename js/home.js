function openNav() {
    if (openside) {
        document.getElementById("mySidebar").style.width = "250px";
        document.getElementById("main").style.marginLeft = "250px";
        openside = false;
    } else {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("main").style.marginLeft = "0";
        openside = true;
    }
}

/***********************************************************************************************************************/

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}

/***********************************************************************************************************************/

function keepopen() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

/***********************************************************************************************************************/
$('#Tree').on('ready.jstree', function() {
    $("#Tree").jstree("open_all");
});
/***********************************************************************************************************************/
$('#Tree').jstree({


    'core': {
        'data': Treeview
    },

    "search": {
        "case_insensitive": true,
        "show_only_matches": false

    },
    plugins: ["search", "html_data"]

});
//, "noclose"
$('#Search').keyup(function() {
    $('#Tree').jstree('search', $(this).val());
});