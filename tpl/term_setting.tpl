{include file="{$TPL}parts/common_header.tpl" title="Patient List" refresh="false"}
<div class="layoutMainContent">
	<center>
		推奨接種期間設定<P />
		<form action="{$URL}pages/clinic/term_setting.php" method="POST">
			{$table}
			<input type="submit" name="update" value="更新" />
			<input type="submit" name="reset" value="初期化" />
		</form>
	</center>
</div>
{include file="{$TPL}parts/common_footer.tpl"}