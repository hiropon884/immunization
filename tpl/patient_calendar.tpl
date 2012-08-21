{include file="{$TPL}parts/common_header.tpl" title="CALENDAR" refresh="false"}
<div class="layoutMainContent">
	<center>
		予約リスト<P />
		
		<form action="{$URL}pages/patient/appointment.php" method="POST">
		{$table}
		</form>
	</center>
</div>
{include file="{$TPL}parts/common_footer.tpl"}