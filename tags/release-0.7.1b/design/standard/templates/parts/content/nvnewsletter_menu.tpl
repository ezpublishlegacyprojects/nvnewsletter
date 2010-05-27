

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Newsletter'|i18n( 'design/nvnewsletter' )}</h4>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<ul>
    <li><div><a href={'nvnewsletter/create_newsletter'|ezurl}>{'Create newsletter'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_senders'|ezurl}>{'Senders'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_receiver_groups'|ezurl}>{'Receiver groups'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_receivers'|ezurl}>{'Receivers'|i18n('design/nvnewsletter')}</a></div></li>
    <li><div><a href={'nvnewsletter/list_receiver_fields'|ezurl}>{'Receiver fields'|i18n('design/nvnewsletter')}</a></div></li>
</ul>

{* DESIGN: Content END *}</div></div></div></div></div></div>

{if is_set($module_result.newsletter_menu)}
    {include uri=$module_result.newsletter_menu}
{/if}
