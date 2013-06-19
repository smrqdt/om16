{if isset($flash['error'])}
	<div class="alert alert-error">
		{foreach from=$flash['error'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}

{if isset($flash['warn'])}
	<div class="alert">
		{foreach from=$flash['warn'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}

{if isset($flash['info'])}
	<div class="alert alert-info">
		{foreach from=$flash['info'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}

{if isset($flash['success'])}
	<div class="alert alert-success">
		{foreach from=$flash['success'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}