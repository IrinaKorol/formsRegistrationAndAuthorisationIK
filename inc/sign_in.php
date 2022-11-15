<form action="" method="post" id="signin_form">
    <input type="text" class="form-control" name="signInForm[login]" placeholder="Введите логин" required><br>
    <input type="password" class="form-control" name="signInForm[password]" placeholder="Введите пароль" required autocomplete="on"><br>
    <div class=" " id="signin_form_error"></div>
    <div class="form-group">
        <input type="checkbox"  name="check" id="exampleCheck1">
        <label  for="exampleCheck1">запомнить</label>
    </div>
    <button class="btn btn-success" name="signin_btn" type="submit">Войти</button>
</form>