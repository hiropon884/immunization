{include file="tpl/parts/common_header.tpl" title="Patient Top" refresh="false"}
<div class="layoutMainContent">
	{if $person_state == "NG"}
		<font color="red">システムエラー:</font>
			選択された患者データデータが存在しません。
	{else}
		<h1>個人メニュー</h1>
		<p>	</p>
		<h2>個人メニュー</h2>
		<dt><a href="">個別予防接種予約</a></dt>
		<dd>予防接種の予約・実績を入力するページ</dd>
		<dt><a href="">予防接種カレンダー</a></dt>
		<dd>誕生日を基準に推奨予防接種時期が過ぎている予防接種のリストを一覧表示する</dd>
		<dt><a href="">予約一覧表示</a></dt>
		<dd>予約済みの予防接種一覧を表示する</dd>
		<dt><a href="">接種履歴詳細</a></dt>
		<dd>接種が完了した過去の予防接種一覧を表示する</dd><dl>
		</dl>
	{/if}
</div>
{include file="tpl/parts/common_footer.tpl"}