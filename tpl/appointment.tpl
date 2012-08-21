{include file="{$TPL}parts/common_header.tpl" title="Appointment" refresh="false"}
<div class="layoutMainContent">
	<center>
		予約データ管理<P />
		{$msg}
		<form action="{$URL}pages/patient/appointment.php" method="POST">
			{$table}
			{if $verify == "false"}
				<input type="radio" name="cmd" value="none" {$check.none}>None
				<input type="radio" name="cmd" value="add" {$check.add}>新規登録
				<input type="radio" name="cmd" value="update" {$check.update}>更新
				
				<input type="radio" name="cmd" value="delete" {$check.delete}>削除
			{/if}
			<P />
			{if $is_submit == "true"}
			{else if $verify == "true"}
				{if $cmd == "get"}
					<input type="submit" name="cancel" value="戻る" />
				{else}
					<input type="hidden" name="cmd" value="{$cmd}"/>
					<input type="submit" name="submit" value="実行" />
					<input type="submit" name="cancel" value="キャンセル" />
				{/if}
			{else}
				<input type="submit" name="verify" value="確認" />
				<input type="submit" name="reset" value="リセット" />
			{/if}
		</form>
	</center>
</div>
{include file="{$TPL}parts/common_footer.tpl"}