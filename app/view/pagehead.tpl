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
              {if isset($smarty.session['auth_user']['logged_in']) && $smarty.session['auth_user']['logged_in']}
              	  Logged in as {$user->email} <a href="{$path}logout" id="logoutButton" class="btn btn-small btn-inverse navbar-link">Logout</a>
              {/if}
            </p>
            <ul class="nav">
              <li><a href="{$path}">Home</a></li>
              <li><a href="{$path}cart">Cart {if isset($noCartItems) && $noCartItems}<span class="badge">{$noCartItems}</span>{/if}</a></li>
              <li><a href="#contact">Contact</a></li>
              {if isset($smarty.session['auth_user']['logged_in']) && $smarty.session['auth_user']['logged_in'] && $user->admin}
              	<li class="divider-vertical"></li>
              	<li><a href="{$path}admin">Users</a></li>
              	<li><a href="{$path}admin/items">Items</a></li>
              	<li><a href="{$path}admin/orders">Orders</a></li>
              {/if}
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
{include file="flash.tpl"}
    