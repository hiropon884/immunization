<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	{include file="{$TPL}parts/html_header.tpl"}
	<body id="top">
		<div class="layout">
			{include file="{$TPL}parts/site_top.tpl"}
			<div class="layoutMain">
				<table class="layoutMain" cellspacing="0" cellpadding="0">
					<tr>
						{if $menu_is_available == "true"}
							{include file="{$TPL}parts/menu.tpl"}
						{/if}
						<td valign="top" class="layoutMainCenter">
