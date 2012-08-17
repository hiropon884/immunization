{include file="tpl/common_header.tpl" title="login form"}

<div id="inner">
		<div id="mainWrap">
			{if $person_state == "NG"}
                            <font color="red">システムエラー:</font>
                            選択された患者データデータが存在しません。
                         {else}
                                推奨予防接種（2ヶ月分）<P>
<H3>カレンダーから予防接種予約を入れる</H3>
                        {/if}

		</div>
	
		<div id="sideWrap">
			{include file="tpl/menu.tpl"}
		</div>
</div>
<P>
	
{include file="tpl/common_footer.tpl"}