<h2>Newsletter</h2>

{def $newsletter_draft_count=fetch( 'nvnewsletter', 'drafts_newsletter_count' )
     $newsletter_sent_count=fetch( 'nvnewsletter', 'sent_newsletter_count' )
     $newsletter_failed_count=fetch( 'nvnewsletter', 'failed_newsletter_count' )
     $newsletter_in_progress_count=fetch( 'nvnewsletter', 'in_progress_newsletter_count' )
     $newsletter_groups_count=fetch( 'nvnewsletter', 'receiver_group_count' )
     $newsletter_receivers_count=fetch( 'nvnewsletter', 'receiver_count' )
     $newsletter_unsubscribed_count=fetch( 'nvnewsletter', 'unsubscribed_count' )
     $senderFieldID=ezini( 'ContentClassSettings', 'SenderFieldIdentifier', 'nvnewsletter.ini' )
     $groupFieldID=ezini( 'ContentClassSettings', 'GroupsFieldIdentifier', 'nvnewsletter.ini' )
     $page_limit = $limit}

<table class="list" cellspacing="0">
<tr>
    <th>{'Newsletter statistics'|i18n( 'design/nvnewsletter' )}</th>
    <th>&nbsp;</th>
</tr>
<tr class="bglight">
    <td><a href={'nvnewsletter/list_sent'|ezurl}>{'Sent'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_sent_count}</td>
</tr>
<tr class="bgdark">
    <td><a href={'nvnewsletter/list_in_progress'|ezurl}>{'In progress'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_in_progress_count}</td>
</tr>
<tr class="bglight">
    <td><a href={'nvnewsletter/list_draft'|ezurl}>{'Drafts'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_draft_count}</td>
</tr>
<tr class="bgdark">
    <td><a href={'nvnewsletter/list_failed'|ezurl}>{'Failed'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_failed_count}</td>
</tr>
<tr class="bglight">
    <td><a href={'nvnewsletter/list_receiver_groups'|ezurl}>{'Groups'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_groups_count}</td>
</tr>
<tr class="bgdark">
    <td><a href={'nvnewsletter/list_receivers'|ezurl}>{'Receivers'|i18n( 'design/nvnewsletter' )}</a></td>
    <td>{$newsletter_receivers_count}</td>
</tr>
<tr class="bglight">
    <td>{'Unsubscribed'|i18n( 'design/nvnewsletter' )}</td>
    <td>{$newsletter_unsubscribed_count}</td>
</tr>
</table>