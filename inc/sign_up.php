<form action="" method="post" id="signup_form">
    <input type="text" class="form-control" name="signUpForm[login]" id="login" placeholder="Введите логин" required><br>
    <input type="password" class="form-control" name="signUpForm[password]" id="psw" placeholder="Введите пароль" required autocomplete="on"><br>
    <input type="password" class="form-control" name="signUpForm[confirm_password]" id="conf_psw" placeholder="Повторите пароль" required autocomplete="on"><br>
    <input type="email" class="form-control" name="signUpForm[email]" id="email" placeholder="Введите Email" required><br>
    <input type="text" class="form-control" name="signUpForm[name]" id="name" placeholder="Введите имя" required><br>
    <div class=" " id="signup_form_error"></div>
    <div class=" " id="signup_form_success"></div>
    <button class="btn btn-success" name="signup_btn" type="submit">Зарегистрировать</button>
</form>