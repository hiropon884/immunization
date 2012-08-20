<div class="layoutYouAreHere">
	{if $mode != "none"}
		<span class="naviYouAreHere"><a class="naviYouAreHere" href="http://localhost/immunization/index.php">トップページ</a></span>
	{else}
		<span class="naviYouAreHere">　</span>
	{/if}
	{if $mode == "admin"}
		<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" />
		<span class="naviYouAreHere"><a class="naviYouAreHere" href="http://localhost/immunization/pages/admin/admin.php">ユーザー管理</a></span>
		{if $location == "registration"}	
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">ユーザー管理</span>
		{else if $location =="user_list"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">一覧表示</span>
		{/if}
	{else if $mode == "clinic"}
		<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" />
		<span class="naviYouAreHere"><a class="naviYouAreHere" href="http://localhost/immunization/pages/user_top.php">メニュー</a></span>
		{if $location == "patient_reg"}	
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">患者データ管理</span>
		{else if $location =="patient_list"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">患者一覧表示</span>
		{else if $location =="statistical"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">統計データ</span>
		{else if $location =="setting"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">接種時期設定</span>
		{/if}
	{else if $mode == "patient"}
		<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" />
		<span class="naviYouAreHere"><a class="naviYouAreHere" href="http://localhost/immunization/pages/patient_top.php">個人メニュー</a></span>
		{if $location == "patient_reg"}	
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">個別予防接種予約</span>
		{else if $location =="patient_list"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">予防接種カレンダー</span>
		{else if $location =="statistical"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">予約一覧表示</span>
		{else if $location =="setting"}
			<img class="naviYouAreHere" src="img/youarehere.gif" alt="&#062;" /><span class="naviYouAreHere">接種履歴詳細</span>
		{/if}
	{/if}
</div>