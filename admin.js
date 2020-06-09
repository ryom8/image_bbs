
$(function(){

    var i = '';

    $('#nocheck').click(function(){
        $('[name="delete"]:checked').prop('checked',false);
    });

    $('[name="delete"]').change(function(){
        var chk = [];
        $('[name="delete"]:checked').each(function(){
            chk.push($(this).val());
        });

        i = chk;
    });
    

    $('#ad_del').click(function(){
        if(i != ''){
            if(confirm('チェックした記事を削除します')) {

            var postData = { 'did':i };

            $.ajax({
                url:'delete_ad.php',
                type:'POST',
                data:postData,
                success:function(data){alert(data);}
            });

            }
        }
    });

});

