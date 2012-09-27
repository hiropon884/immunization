{include file="{$TPL}parts/common_header.tpl" title="User Top" refresh="false"}
<div class="layoutMainContent">
	<h1>データ操作</h1>
	<p>各種データの操作を行う
	</p>
	<h2>患者データ操作</h2>
	<dt><a href="{$URL}pages/clinic/patient_reg.php">患者データ管理</a></dt>
	<dd>患者データの新規登録、患者データの情報更新、患者データの検索、患者データの削除を行う</dd>
	<dt><a href="{$URL}pages/clinic/patient_list.php">患者一覧表示</a></dt>
	<dd>登録された患者データを一覧表示する</dd>
	<h2>病院データ操作</h2>
	<dt><a href="{$URL}pages/clinic/statistical.php">統計データ表示</a></dt>
	<dd>各種統計データを表示する</dd>
	<dt><a href="{$URL}pages/clinic/term_setting.php">接種時期設定</a></dt>
	<dd>予防接種の推奨時期を設定する</dd>
	<dl>
	</dl>
</div>
{include file="{$TPL}parts/common_footer.tpl"}