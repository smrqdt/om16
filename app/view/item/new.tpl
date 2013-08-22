{include file="header.tpl"} {include file="pagehead.tpl"}

<div class="row-fluid">

	<div class="span8">
		<form method="post" action="{$path}items/create"
			class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="{$csrf_key}" value="{$csrf_token}">
			{include file="item/itemform.tpl"}
		</form>
	</div>

</div>

{include file="footer.tpl"}