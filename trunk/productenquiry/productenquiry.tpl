{capture name=path}{l s='Product Enquiry' mod='productenquiry'}{/capture}
{include file=$tpl_dir./breadcrumb.tpl}<script type="text/javascript"><!--	var baseDir = '{$base_dir_ssl}';--></script>{if isset($js_files)}	{foreach from=$js_files item=js_uri}	<script type="text/javascript" src="{$js_uri}"></script>	{/foreach}{/if}<script type="text/javascript">// <![CDATA[idSelectedCountry = {if isset($visitor.country)}{$visitor.country|intval}{else}false{/if};countries = new Array();{foreach from=$countries item='country'}	{if isset($country.states)}		countries[{$country.id_country|intval}] = new Array();		{foreach from=$country.states item='state' name='states'}			countries[{$country.id_country|intval}]['{$state.id_state|intval}'] = '{$state.name}';		{/foreach}	{/if}{/foreach}$(function(){ldelim}	$('.id_state option[value={if isset($visitor.state)}{$visitor.state}{else}{$visitor.state|escape:'htmlall':'UTF-8'}{/if}]').attr('selected', 'selected');{rdelim});//]]></script>

<h2>{l s='Product Enquiry' mod='productenquiry'}</h2>

<p class="bold">{l s='Enquire about a product.' mod='productenquiry'}.</p>
{include file=$tpl_dir./errors.tpl}

{if $confirm}
	<p class="success">{$confirm}</p>
{else}
	<form method="post" action="{$request_uri}" class="std">
		<fieldset>
			<h3>{l s='Product Enquiry' mod='productenquiry'}</h3>
		
			<p class="align_center">
				<a href="{$productLink}"><img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'small')}" alt="" title="{$cover.legend}" /></a><br/>
				<a href="{$productLink}">{$product->name}</a>
			</p>
			
			<p>				<input type="hidden" id="friend-name" name="name" value="To whom to be sent" />			</p>			
			<p>				<input type="hidden" id="friend-address" name="email" value="enquiry@example.com" />			</p>						<p class="required text">				<label for="visitor-name">{l s='Name:' mod='sendtoafriend'}</label>				<input type="text" id="visitor-name" name="visitorname" value="{if isset($visitor.fullname)}{$visitor.fullname|escape:'htmlall'|stripslashes}{/if}" />				<sup>*</sup>			</p>						<p class="required text">				<label for="visitor-email">{l s='Email:' mod='sendtoafriend'}</label>				<input type="text" id="visitor-email" name="visitoremail" value="{if isset($visitor.email)}{$visitor.email|escape:'htmlall'|stripslashes}{/if}" />				<sup>*</sup>			</p>						<p class="required text">				<label for="visitor-phone">{l s='Phone:' mod='sendtoafriend'}</label>				<input type="text" id="visitor-phone" name="visitorphone" value="{if isset($visitor.mobile)}{$visitor.mobile|escape:'htmlall'|stripslashes}{/if}" />				<sup>*</sup>			</p>						<p class="required select">				<label for="id_country">{l s='Country'}</label>				<select id="id_country" name="id_country">{$countries_list}</select>				<sup>*</sup>			</p>			<p class="required id_state select">				<label for="id_state">{l s='State'}</label>				<select name="id_state" id="id_state">					<option value="">-</option>				</select>				<sup>*</sup>			</p>						<p>				<label for="enquiry-text">{l s='Product Enquiry:' mod='productenquiry'}</label>				<textarea rows="7" cols="35" id="enquiry-text" name="enquiry">{if isset($smarty.post.enquiry)}{$smarty.post.enquiry|escape:'htmlall'|stripslashes}{/if}</textarea>			</p>
			
			<p class="submit">
				<input type="submit" name="submitAddtoafriend" value="{l s='send' mod='productenquiry'}" class="button" />
			</p>
		</fieldset>
	</form>
{/if}

<ul class="footer_links">
	<li><a href="{$productLink}" class="button_large">{l s='Back to' mod='productenquiry'} {$product->name}</a></li>
</ul>
