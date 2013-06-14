  <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="{$path}">Tapeshop</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              {if $user['logged_in']}
              	  Logged in as {$user['email']} <a href="{$path}index.php/logout" id="logoutButton" class="btn btn-small btn-inverse navbar-link">Logout</a>
              {else}
	              <a href="{$path}index.php/signup" id="signupButton" class="btn btn-small btn-inverse navbar-link">Signup</a>
	              <a href="{$path}index.php/login" id="loginButton" class="btn btn-small btn-inverse navbar-link">Login</a>
              {/if}
            </p>
            <ul class="nav">
              <li><a href="{$path}">Home</a></li>
              <li><a href="{$path}index.php/cart">Cart <span class="badge">{$noCartItems}</span></a></li>
              <li><a href="#contact">Contact</a></li>
              {if isset($smarty.session['auth_user']) && $smarty.session['auth_user']['admin']}
              	  <li><a href="{$path}index.php/admin">Admin</a></li>
              	  <li><a href="{$path}index.php/admin/items">Items</a></li>
              {/if}
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
{include file="flash.tpl"}
    