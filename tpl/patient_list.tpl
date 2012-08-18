{include file="tpl/parts/common_header.tpl" title="Patient List" refresh="false"}
<div class="layoutMainContent">
	<center>
		患者データ一覧<P />
		<form action="patient_top.php" method="POST">
			{$table}
		</form>
	</center>
</div>
{include file="tpl/parts/common_footer.tpl"}