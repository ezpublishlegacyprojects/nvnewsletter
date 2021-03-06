{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* nvNewsletter - sender list *}
{def $senderCount=fetch('nvnewsletter', 'sender_count')
     $page_limit = $limit}
<form name="senders" method="post" action={'/nvnewsletter/list_senders/'|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Newsletter senders'|i18n( 'design/nvnewsletter' )} [{$senderCount}]</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content overflow">

{* Items per page selector. *}
<div class="context-toolbar">
<div class="button-left">
    <p class="table-preferences">
        {switch match=$page_limit}
        {case match=25}
        <a href={'/user/preferences/set/admin_list_limit/1'|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
        <span class="current">25</span>
        <a href={'/user/preferences/set/admin_list_limit/3'|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
        {/case}

        {case match=50}
        <a href={'/user/preferences/set/admin_list_limit/1'|ezurl} title="{'Show 10 items per page.'|i18n( 'design/admin/node/view/full' )}">10</a>
        <a href={'/user/preferences/set/admin_list_limit/2'|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
        <span class="current">50</span>
        {/case}

        {case}
        <span class="current">10</span>
        <a href={'/user/preferences/set/admin_list_limit/2'|ezurl} title="{'Show 25 items per page.'|i18n( 'design/admin/node/view/full' )}">25</a>
        <a href={'/user/preferences/set/admin_list_limit/3'|ezurl} title="{'Show 50 items per page.'|i18n( 'design/admin/node/view/full' )}">50</a>
        {/case}
        {/switch}
    </p>
</div>
<div class="float-break"></div>
</div>

{* Newsletter sender table. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n('design/nvnewsletter')}" title="{'Invert selection.'|i18n('design/nvnewsletter')}" onclick="ezjs_toggleCheckboxes( document.senders, 'SenderIDArray[]' ); return false;" /></th>
    <th>{'Name'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'Email'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'Reply-to'|i18n( 'design/nvnewsletter' )}</th>
    <th class="tight">{'ID'|i18n( 'design/nvnewsletter' )}</th>
    <th class="tight">&nbsp;</th>
</tr>
{if $senderCount|lt(1)}
<tr><td>&nbsp;</td><td colspan="3">{'No senders defined'|i18n( 'design/nvnewsletter' )}</td><td>&nbsp;</td><td>&nbsp;</td></tr>
{else}
{foreach $sender_array as $sender
         sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td><input type="checkbox" name="SenderIDArray[]" value="{$sender.id}" title="{'Select sender for removal.'|i18n( 'design/nvnewsletter' )}" /></td>
    <td>{'newsletter'|icon( 'small', 'Newsletter'|i18n( 'design/nvnewsletter' ) )}&nbsp;<a href={concat( '/nvnewsletter/view_sender/', $sender.id, '/' )|ezurl}>{$sender.sender_name|wash}</a></td>
    <td>{$sender.sender_email|wash}</a></td>
    <td>{$sender.reply_to|wash}</a></td>
    <td class="number" align="right">{$sender.id}</td>
    <td><a href={concat( '/nvnewsletter/edit_sender/', $sender.id, '/' )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/nvnewsletter' )}" title="{'Edit the <%sender_name> sender.'|i18n( 'design/nvnewsletter',, hash( '%sender_name', $sender.sender_name ) )|wash}" /></a></td>
    </tr>
{/foreach}
{/if}
</table>

{* Navigator. *}
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/nvnewsletter/list_senders'
         item_count=$senderCount
         view_parameters=$view_parameters
         item_limit=$page_limit}
</div>

{* DESIGN: Content END *}</div></div></div>

{* Buttons. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="RemoveSenderButton" value="{'Remove selected'|i18n( 'design/nvnewsletter' )}" title="{'Remove selected senders.'|i18n( 'design/nvnewsletter' )}" />
<input class="button" type="submit" name="CreateSenderButton" value="{'New sender'|i18n( 'design/nvnewsletter' )}" title="{'Create a new sender.'|i18n( 'design/nvnewsletter' )}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
</form>
