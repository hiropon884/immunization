<div id="menu">
	{if $menu_state == 0}
	<div id="admin_menu">
		管理者メニュー
		<UI>
			<LI><a href="registrationView.php">ユーザー登録・更新・削除</a>
			<LI><a href="userInfo.php">ユーザー一覧の表示</a>
		</UI>
	</div>
	{elseif $menu_state == 1}
	<div id="user_menu">
		患者メニュー<P>
		<UI>
			<LI><a href="patient_reg.php">患者の登録・検索</a><BR>
			<LI><a href="patient_list.php">患者一覧</a><BR>
		</UI><P>
		病院側メニュー<P>
		<UI>
			<LI><a href="statistical.php">統計データ表示</a><br>
			<LI><a href="immunization_term_setting.php">接種設定</a><br>
		</UI>
	</div>
	{elseif $menu_state == 2}
	<div id="customer">
		カスタマーメニュー
		<UI>
			<LI>手動予約登録（新規登録、更新、削除）
			<LI>摂取可能予防接種リスト
			<LI>予約リスト
			<LI>完了リスト
		</UI>
	<div>
	{/if}
	<P>
<a href="logout.php">ログアウト</a><P>

問い合わせ<BR>
</div>