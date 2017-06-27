jQuery(function($){
    /* UserList Pop Request Handler */
    $('a.add-forward').on('click', function(){
        var user_id = $(this).data('user');
        $('input[name="_userLogin"]').val(user_id);
        $.ajax({
            url: siteOptionJs.ajaxurl,
            data: {user: user_id, action: 'userList', nonce: _cnmAjaxVars._cnmgulnonc},
            type: "POST",
            beforeSend: function() {
                $('#loding_plceholdr').show();
            },
        }).done(function(res){
            $('#loding_plceholdr').hide();
            $('p.userList select').html(res);
            $('#cnmformcontainer').show();
        });
    });


    /* UserList Pop Close */
    $("._cnmClose").on('click', function(){
        $('#cnmformcontainer').hide();
    });


    /* ForwardTo Request Handling */
    $('#_cnmForm').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: siteOptionJs.ajaxurl,
            data: data,
            type: "POST",
            beforeSend: function() {
                $('#loding_plceholdr').show();
            },
        }).done(function(res){
            $('#loding_plceholdr').hide();
            location.reload(); 
        });
    });
});