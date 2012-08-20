{include file="{$TPL}parts/common_header.tpl" title="Patient Registration" refresh="false"}
<div class="layoutMainContent">
    <center>
        患者データ管理<P />
        {$msg}
        {if $cmd == "search"}
            <form action="{$URL}pages/patient_top.php" method="POST">
                {$search}
            </form>
        {/if}
        <form action="{$URL}pages/clinic/patient_reg.php" method="POST">
            {$table}
            {if $verify == "false"}
                <input type="radio" name="cmd" value="none" checked="checked">None
                <input type="radio" name="cmd" value="add" >新規登録
                <input type="radio" name="cmd" value="update" >更新
                <input type="radio" name="cmd" value="get" >データ取得
                <input type="radio" name="cmd" value="delete" >削除
                <input type="radio" name="cmd" value="search" >検索
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