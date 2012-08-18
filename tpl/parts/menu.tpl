<td valign="top" class="layoutMainLeft">
	<div class="layoutVerticalNavi">
		{if $menu_state == 0}
			<div class="naviVerticalNavi_1" style="border-bottom: 1px dotted #999">
				<img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="admin.php">ユーザー管理メニュー</a>
			</div>
			<div class="naviVerticalNavi_2">
				<img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="registrationView.php">ユーザーデータ管理</a>
			</div>
			<div class="naviVerticalNavi_2">
				<img class="naviVerticalNavi_2" src="img/verticalnavi_2.gif" /><a class="naviVerticalNavi" href="user_list.php">ユーザー一覧表示</a>
			</div>
		{elseif $menu_state == 1}
			<div class="naviVerticalNavi_1" style="border-bottom: 1px dotted #999"><img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" />患者データ操作</div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="patient_reg.php">患者データ管理</a></div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="patient_list.php">患者一覧表示</a></div>
			<div class="naviVerticalNavi_1" style="border-bottom: 1px dotted #999"><img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" />病院データ操作</div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="statistical.php">統計データ表示</a></div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="immunization_term_setting.php">接種時期設定</a></div>
		{elseif $menu_state == 2}
			<div class="naviVerticalNavi_1" style="border-bottom: 1px dotted #999"><img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" />個人メニュー</div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="appointment.php">個別予防接種予約</a></div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="patient_calendar.php">予防接種カレンダー</a></div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="patient_booklist.php">予約一覧表示</a></div>
			<div class="naviVerticalNavi_2"><img class="naviVerticalNavi_2" src="img/verticalnavi_1.gif" /><a class="naviVerticalNavi" href="patient_past.php">接種履歴詳細</a></div>

		{/if}
		<div class="naviVerticalNavi_1"><img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" /><a href="logout.php">ログアウト</a></div>
		<div class="naviVerticalNavi_1"><img class="naviVerticalNavi_1" src="img/verticalnavi_1.gif" /><a href="">問い合わせ</a></div>
	</div>
</td>
