{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{* nvNewsletter - receiver group view *}

{def $base_uri=concat('/nvnewsletter/view_receiver_group/', $group.id)
     $page_limit = $limit
     $group_members_count = $group.members_count
     $group_members_unsubscribed_count = $group.members_unsubscribed_count}

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Receiver group <%group_name>'|i18n('design/nvnewsletter',, hash('%group_name', $group.group_name ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

    <div class="block float-break">

    {* Name *}
    <div class="block float-break">
        <label>{"Name"|i18n('design/nvnewsletter')}:</label> {$group.group_name|wash}
    </div>

    {* Description *}
    <div class="block float-break">
        <label>{"Description"|i18n('design/nvnewsletter')}:</label> {$group.group_description|wash}
    </div>

    <div class="break"></div>

</div>

{* DESIGN: Content END *}</div></div></div>
    {* Buttons. *}
    <div class="controlbar" >
    
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
        <div class="block left">
        {* Edit *}
        <form name="edit_receiver_group" method="post" action={concat('/nvnewsletter/edit_receiver_group/', $group.id)|ezurl} style="display:inline">
        <input class="button" type="submit" value="{'Edit'|i18n('design/nvnewsletter')}" title="{'Edit this receiver group.'|i18n('design/nvnewsletter')}" />
        </form>

        <form name="import_receivers" method="post" action={concat('/nvnewsletter/import_receivers/', $group.id)|ezurl} style="display:inline">
        <input class="button" type="submit" value="{'Import CSV'|i18n('design/nvnewsletter')}" title="{'Import receivers to this group from CSV.'|i18n('design/nvnewsletter')}" />
        </form>
        
        <form name="receivers" method="post" action={'/nvnewsletter/list_receivers/'|ezurl} style="display:inline">
        <input class="button" type="submit" name="CreateReceiverButton" value="{'New receiver'|i18n( 'design/nvnewsletter' )}" title="{'Create a new receiver.'|i18n( 'design/nvnewsletter' )}" />
        </form>
        
        </div>
    </div>


{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>

<form name="search_receivers" method="get" action={concat('/nvnewsletter/search_receivers/' )|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">
    {'Search receivers from group'|i18n('design/nvnewsletter')|wash}
</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

    <div class="block float-break">

        <div class="block float-break">
            <label for="SearchText">{'Search'|i18n('design/nvnewsletter')}:</label>
            <input type="text" name="SearchText" id="SearchText" class="halfbox" value="{$searchText|wash}" />
        </div>
        
        <div class="block float-break">
            <label>{'Search from'|i18n('design/nvnewsletter')}:</label>
            <label class="checkbox"><input type="radio" name="SearchFrom" value="email"{if or( $search_from|eq( 'email' ), is_set( $search_from )|not )} checked="checked"{/if} /> Email</label>
            <label class="checkbox"><input type="radio" name="SearchFrom" value="fields"{if $search_from|eq( 'fields' )} checked="checked"{/if} /> User fields</label>
        </div>
        
        <div class="block float-break">
            {def $wildcard    = ezini( 'SearchSettings', 'AllowWildcard', 'nvnewsletter.ini' )
                 $wildcardPre = ezini( 'SearchSettings', 'AllowWildcardPre', 'nvnewsletter.ini' )}
            {if $wildcardPre|eq('enabled')}
                <strong>Note: </strong>{'Wildcard search with *keyword* is enabled.'}
            {elseif $wildcard|eq('enabled')}
                <strong>Note: </strong>{'Wildcard search with keyword* is enabled.'}
            {/if}
        </div>
        
    </div>

    <div class="break"></div>

</div>

{* DESIGN: Content END *}</div></div></div>
    {* Buttons. *}
    <div class="controlbar" >
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
        <div class="block">
            <input type="hidden" name="SearchGroup" value="{$group.id}" />
            <input class="button" type="submit" value="{'Search'|i18n('design/nvnewsletter')}" title="{'Search'|i18n('design/nvnewsletter')}" />
        </div>
    </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</form>

<form name="group_receivers" method="post" action={$base_uri|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Group members'|i18n( 'design/nvnewsletter' )} [{$group_members_count}]</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>

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

{* Group member table. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n('design/nvnewsletter')}" title="{'Invert selection.'|i18n('design/nvnewsletter')}" onclick="ezjs_toggleCheckboxes( document.group_receivers, 'ReceiverIDArray[]' ); return false;" /></th>
    <th class="tight">{'ID'|i18n('design/nvnewsletter')}</th>
    <th>{'Email'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'eZ User'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'Subscribe status'|i18n( 'design/nvnewsletter' )}</th>
</tr>

{def $members = fetch('nvnewsletter', 'group_members', hash('group_id', $group.id, 'offset', $view_parameters.offset, 'limit', $page_limit))
     $member_status_array = array()
     $member_status = 0
     $ezuser = false()}

{foreach $members as $member
         sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td><input type="checkbox" name="ReceiverIDArray[]" value="{$member.id}" title="{'Select receiver for removal.'|i18n( 'design/nvnewsletter' )}" /></td>
    <td class="number">{$member.id}</td>
    <td><a href={concat( '/nvnewsletter/view_receiver/', $member.id, '/' )|ezurl}>{$member.email_address|wash}</a></td>
    <td>
        {set $ezuser = $member.ezuser}
        {if $ezuser}
           <a href={$ezuser.contentobject.main_node.url_alias|ezurl}>{$ezuser.contentobject.name}</a>
        {/if}
    </td>
    <td>
    {set $member_status_array = $member.groups_status
         $member_status = $member_status_array[$group.id]}
    {switch match=$member_status}
        {case match=1} 
        {'pending'|i18n( 'design/nvnewsletter' )}
        {/case}
        {case match=2} 
        {'confirmed'|i18n( 'design/nvnewsletter' )}
        {/case}
        {case match=3} 
        {'approved'|i18n( 'design/nvnewsletter' )}
        {/case}
        {case}
        {/case}
    {/switch}
    </td>
</tr>
{/foreach}
</table>

{* Navigator. *}
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=$base_uri
         view_parameters=$view_parameters
         item_count=$group_members_count
         item_limit=$page_limit}
</div>

{* DESIGN: Content END *}</div></div></div>

{* Buttons. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="RemoveReceiverButton" value="{'Remove selected'|i18n( 'design/nvnewsletter' )}" title="{'Remove selected receivers.'|i18n( 'design/nvnewsletter' )}" />
<input class="button" type="submit" name="UnsubscribeReceiver" value="{'Unsubscribe from group'|i18n('design/nvnewsletter')}" title="{'Unsubscribe this receiver.'|i18n('design/nvnewsletter')}" />
{if $members}<a href={concat('/nvnewsletter/export_receivers_group/', $group.id)|ezurl} class="button">{'Export CSV'|i18n( 'design/nvnewsletter' )}</a>{/if}
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div></div>

</form>

<form name="group_unsubscribed" method="post" action={$base_uri|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Group unsubscribed members'|i18n( 'design/nvnewsletter' )} [{$group_members_unsubscribed_count}]</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>

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

{* Group member table. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n('design/nvnewsletter')}" title="{'Invert selection.'|i18n('design/nvnewsletter')}" onclick="ezjs_toggleCheckboxes( document.group_unsubscribed, 'ReceiverIDArray[]' ); return false;" /></th>
    <th class="tight">{'ID'|i18n('design/nvnewsletter')}</th>
    <th>{'Email'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'eZ User'|i18n( 'design/nvnewsletter' )}</th>
    <th>{'Unsubscribed by'|i18n( 'design/nvnewsletter' )}</th>
</tr>

{def $members_unsubscribed = fetch('nvnewsletter', 'group_members_unsubscribed', hash('group_id', $group.id, 'offset', $view_parameters.offset2, 'limit', $page_limit))}

{foreach $members_unsubscribed as $member
         sequence array( bglight, bgdark ) as $seq}

<tr class="{$seq}">
    <td><input type="checkbox" name="ReceiverIDArray[]" value="{$member.id}" title="{'Select receiver for removal.'|i18n( 'design/nvnewsletter' )}" /></td>
    <td class="number">{$member.id}</td>
    <td><a href={concat( '/nvnewsletter/view_receiver/', $member.id, '/' )|ezurl}>{$member.email_address|wash}</a></td>
    <td>
        {set $ezuser = $member.ezuser}
        {if $ezuser}
           <a href={$ezuser.contentobject.main_node.url_alias|ezurl}>{$ezuser.contentobject.name}</a>
        {/if}
    </td>
    <td>
    {set $member_status_array = $member.groups_unsubscribed_status
         $member_status = $member_status_array[$group.id]}
    {switch match=$member_status}
        {case match=11} 
        {'user'|i18n( 'design/nvnewsletter' )}
        {/case}
        {case match=12} 
        {'admin'|i18n( 'design/nvnewsletter' )}
        {/case}
        {case}
        {/case}
    {/switch}
    </td>
</tr>
{/foreach}
</table>

{* Navigator. *}
<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google2.tpl'
         page_uri=$base_uri
         view_parameters=$view_parameters
         item_count=$group_members_unsubscribed_count
         item_limit=$page_limit}
</div>

{* DESIGN: Content END *}</div></div></div>

{* Buttons. *}
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">
<input class="button" type="submit" name="RemoveReceiverButton" value="{'Remove selected'|i18n( 'design/nvnewsletter' )}" title="{'Remove selected receivers.'|i18n( 'design/nvnewsletter' )}" />
<input class="button" type="submit" name="SubscribeReceiver" value="{'Subscribe to group'|i18n('design/nvnewsletter')}" title="{'Unsubscribe this receiver.'|i18n('design/nvnewsletter')}" />
{if $members_unsubscribed}<a href={concat('/nvnewsletter/export_receivers_group/', $group.id, '/0')|ezurl} class="button">{'Export CSV'|i18n( 'design/nvnewsletter' )}</a>{/if}
</div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div></div>

</form>