function add(player_name){
    $.ajax({
        type: "POST",
        url: "call.php",
        data: {
            username: player_name,
            type: "add"
        },
        dataType: 'html',
        success: function(data) {
          $('.add_output').html(data);
        }
    });
}

function del(player_name){
    $.ajax({
        type: "POST",
        url: "call.php",
        data: {
            username: player_name,
            type: "del"
        },
        dataType: 'html',
        success: function(data) {
          $('.del_output').html(data);
        }
    });
}

function ban(player_name){
    $.ajax({
        type: "POST",
        url: "call.php",
        data: {
            username: player_name,
            type: "ban"
        },
        dataType: 'html',
        success: function(data) {
          $('.ban_output').html(data);
        }
    });
}

function ren(player_name, player_name2){
    $.ajax({
        type: "POST",
        url: "call.php",
        data: {
            user1: player_name,
            user2: player_name2,
            type: "rename"
        },
        dataType: 'html',
        success: function(data) {
          $('.rename_output').html(data);
        }
    });
}


$(document).ready(function() {
    $('body').on('submit', '#add_player_form', function(e) {
        e.preventDefault();
        var player_name = $('.add_player_name').val();
        add(player_name);
    });

    $('body').on('submit', '#del_player_form', function(e) {
        e.preventDefault();
        var player_name = $('.del_player_name').val();
        del(player_name);
    });

    $('body').on('submit', '#ban_player_form', function(e) {
        e.preventDefault();
        var player_name = $('.ban_player_name').val();
        ban(player_name);
    });

    $('body').on('submit', '#rename_player_form', function(e) {
        e.preventDefault();
        var player_name = $('.rename_player_name1').val();
        var player_name2 = $('.rename_player_name2').val();
        ren(player_name, player_name2);
    });
});
