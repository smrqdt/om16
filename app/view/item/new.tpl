{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">

	<div class="span8">
		<form method="post" action="{$path}index.php/items/create"
			class="form-horizontal">
			{include file="item/itemform.tpl"}
		</form>
	</div>

</div>
