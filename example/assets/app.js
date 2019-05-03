// rlx

$("document").ready(function() {
    $('.testsys').click(function() {
        var data;
        data = { "rlx": 1, "test": "ok" }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "ajax.php",
            data: data,
            success: function(res) {
                console.log(res)
            }
        })
    });
});