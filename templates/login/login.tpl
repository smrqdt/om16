{include file="header.tpl"}
    <div class="modal-backdrop login-backdrop">
    <div class="container">
      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" name="email" class="input-block-level" placeholder="Email address">
        <input type="password" name="password" class="input-block-level" placeholder="Password">
        <label class="checkbox">
          <input type="checkbox" name="remember" value="remember-me"> Remember me
        </label>
        <button class="btn btn-large btn-primary" type="submit">Sign in</button>
      </form>

{include file="footer.tpl"}