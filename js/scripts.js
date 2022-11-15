$(document).ready(function () {
// обработка формы регистрации
    $('button[name="signup_btn"]').click(function (e) {
        e.preventDefault();
      //  console.log($('#signup_form').serialize());
        $.ajax({
            type: 'POST',
           // url: '/inc/sign.php',
            url: 'router.php',
            dataType: 'json',
            data: $('#signup_form').serialize()+ "&signUp=user",
            success: function (data) {
              //  console.log(data);
                if (data.success) {
                    // поздравляем с регистрацией.
                    $('#signup_form_success').html(data.message).show();
                    $('#signup_form_error').html(data.message).hide();

                    // очищаем форму регистрации
                    $('#signup_form')[0].reset();

                } else {
                    // выводим ошибку
                    $('#signup_form_success').html(data.message).hide();
                    $('#signup_form_error').html(data.message).show();
                }

            },
            error: function (error) {
               // console.log(  error);
            }
        });
    });
    // обработка кнопки авторизации
    $('button[name="signin_btn"]').click(function (e) {
        e.preventDefault();
         console.log('авторизация');
        $.ajax({
            type: 'POST',
      // url: '/inc/sign.php',
          url: 'router.php',
            dataType: 'json',
            data: $('#signin_form').serialize()+ "&signIn=user",
            success: function (data) {
              console.log(data);
                if (data.success) {
                   //console.log(data.success);
                  // console.log(data.message);
                    console.log(data.numberUser);
                    console.log(data.useractive);
                    //перезагрузка страницы
                    location.reload();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 5000);

                } else {
                    // выводим ошибку
                    $('#signin_form_error').html(data.message).show();
                }

            },
            error: function (error) {
                console.log(  error);
            }
        });
    });
    $('button[name="logout"]').click(function (e) {
        e.preventDefault();
        //console.log('выход');
        $.ajax({
            type: 'POST',
            url: 'router.php',
            dataType: 'json',
            data: '&logout=user',
            success: function (data) {
              //  console.log(data);
                if (data.success) {
                  //  console.log(data.success);
                    location.reload();
                    //перезагрузка страницы
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 1000);
                }
            },
            error: function (error) {
               // console.log(  error);
            }
        });
    });


});