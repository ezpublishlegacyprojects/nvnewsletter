{def $status = ezini( 'SubscribeSettings', 'StatusViewParameter', 'nvnewsletter.ini' )
     $groups  = ezini( 'SubscribeSettings', 'GroupsSubscribe', 'nvnewsletter.ini' )
     $status_param = $#view_parameters.$status}

{if $status_param}
    <div id="newsletterSubscriptionStatus">
    {if $status_param|eq('failed')}
        <p class="error">{'Email address is already registered.'|i18n('design/nvnewsletter')}</p>
    {elseif $status_param|eq('emailfailed')}
        <p class="error">{'Your email address seems to be invalid.'|i18n('design/nvnewsletter')}</p>
    {elseif $status_param|eq('groupfailed')}
        <p class="error">{'Newsletter group is missing.'|i18n('design/nvnewsletter')}</p>
    {elseif $status_param|eq('success')}
        <p class="success">{'Newsletter successfully subscribed!'|i18n('design/nvnewsletter')}</p>
    {/if}
    </div>
{/if}

<form action={"nvnewsletter/subscribe"|ezurl} method="post">
    <legend>{'Subscribe newsletter'|i18n('design/nvnewsletter')}</legend>
    <label for="email">{'Email'|i18n('design/newsletter')}</label>
    <input type="text" name="nvNewsletterEmail" id="email" value="" />
    {if gt( $groups|count, 1 )}
        {foreach $groups as $groupID => $groupName}
    <div>
        <label><input type="checkbox" name="nvNewsletterGroupID[]" value="{$groupID}" /> {$groupName}</label>
        <label><input type="radio" name="nvNewsletterGroupType[{$groupID}]" value="1" /> HTML</label> <label><input type="radio" name="nvNewsletterGroupType[{$groupID}]" value="0" /> Text</label>
    </div>
        {/foreach}
    {else}
        {foreach $groups as $groupID => $groupName}
    <label><input type="radio" name="nvNewsletterGroupType[{$groupID}]" value="1" /> HTML</label> <label><input type="radio" name="nvNewsletterGroupType[{$groupID}]" value="0" /> Text</label>
    <input type="hidden" name="nvNewsletterGroupID[]" value="{$groupID}" />
        {/foreach}
    {/if}
    <input type="hidden" name="NodeID" value="{$#node.node_id}" />
    <input type="submit" name="subscribeNewsletter" value="{'Subscribe'|i18n('design/nvnewsletter')}" />
</form>