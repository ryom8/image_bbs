
$(function(){

    $('.display').change(function(){
        var display = $('.display').val();
        window.location.href = 'index.php?display=' + display;
        $('.display').val(display);        
    });

    $('.del').click(function(){
        var dpass = prompt('削除パスワードを入力してください');

        if(confirm('本当に削除してもよろしいですか？')) {
            if(dpass != ''){
                var did = $(this).attr('data-article');
                var img = $(this).attr('data-img');

                var postData = { 'did':did,'dpass':dpass,'img':img };

                $.ajax({
                    url:'delete.php',
                    type:'POST',
                    data:postData,
                    success:function(data){alert(data);}
                });

            }else{
                alert("パスワードが入力されていません！");
            }
        }

    });

});

