{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* nvNewsletter - receiver list *}
{def $receiverCount=fetch('nvnewsletter', 'receiver_count')
     $page_limit = $limit}
     
{include uri='design:parts/search_receivers_form.tpl'}

<form name="receivers" method="post" action={'/nvnewsletter/list_receivers/'|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Newsletter receivers'|i18n( 'design/nvnewsletter' )} [{$receiverCount}]</h1>

{* DESIGN: Mainline *}<div class="header-subline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

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
<div class="button-right"></div>
<div class="float-break"></div>
</div>

<div>
{* Newsletter receiver table. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n('design/nvnewsletter')}" title="{'Invert selection.'|i18n('design/nvnewsletter')}" onclick="ezjs_toggleCheckboxes( document.receivers, 'ReceiverIDArray[]' ); return false;" /></th>
    <th>{'Email'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'eZ User'|i18n( 'design/nvnewsletter' )}</th>
    <th class="tight">{'ID'|i18n( 'design/nvnewsletter' )}</th>
    <th class="tight">&nbsp;</th>
</tr>
{def $ezuser = false()}
{if $receiverCount|lt(1)}
<tr><td>&nbsp;</td><td colspan="2">{'No receivers'|i18n( 'design/nvnewsletter' )}</td><td>&nbsp;</td></tr>
{else}
{foreach $receiver_array as $receiver
         sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td><input type="checkbox" name="ReceiverIDArray[]" value="{$receiver.id}" title="{'Select receiver for removal.'|i18n( 'design/nvnewsletter' )}" /></td>
    <td>{'newsletter'|icon( 'small', 'Newsletter'|i18n( 'design/nvnewsletter' ) )}&nbsp;<a href={concat( '/nvnewsletter/view_receiver/', $receiver.id, '/' )|ezurl}>{$receiver.email_address|wash}</a></td>
    <td>
        {set $ezuser = $receiver.ezuser}
        {if $ezuser}
           <a href={$ezuser.contentobject.main_node.url_alias|ezurl}>{$ezuser.contentobject.name}</a>
        {/if}
    </td>
    <td class="number" align="right">{$receiver.id}</td>
    <td><a href={concat( '/nvnewsletter/edit_receiver/', $receiver.id, '/' )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/nvnewsletter' )}" title="{'Edit the <%receiver_email> receiver.'|i18n( 'design/nvnewsletter',, hash( '%receiver_email', $receiver.email_address ) )|wash}" /></a></td>
    </tr>
{/foreach}
{/if}
</table>
</div>

{* Navigator. *}
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/nvnewsletter/list_receivers'
         item_count=$receiverCount
         view_parameters=$view_parameters
         item_limit=$page_limit}
</div>

{* DESIGN: Content END *}</div></div></div>

{* Buttons. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="RemoveReceiverButton" value="{'Remove selected'|i18n( 'design/nvnewsletter' )}" title="{'Remove selected receivers.'|i18n( 'design/nvnewsletter' )}" />
<input class="button" type="submit" name="CreateReceiverButton" value="{'New receiver'|i18n( 'design/nvnewsletter' )}" title="{'Create a new receiver.'|i18n( 'design/nvnewsletter' )}" />
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>
</form>
