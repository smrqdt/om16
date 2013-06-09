{if isset($smarty.session['slim.flash']['error'])}
	<div class="alert alert-error">
		{foreach from=$smarty.session['slim.flash']['error'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}
{if isset($smarty.session['slim.flash']['warn'])}
	<div class="alert">
		{foreach from=$smarty.session['slim.flash']['warn'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}
{if isset($smarty.session['slim.flash']['info'])}
	<div class="alert alert-info">
		{foreach from=$smarty.session['slim.flash']['info'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}
{if isset($smarty.session['slim.flash']['success'])}
	<div class="alert alert-success">
		{foreach from=$smarty.session['slim.flash']['success'] item=msg}
		{$msg}<br />
		{/foreach}
	</div>
{/if}