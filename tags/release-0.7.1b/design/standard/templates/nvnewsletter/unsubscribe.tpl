{if $result.success}
    <p>{'Your email address successfully unsubscribed.'|i18n('design/nvnewsletter')}</p>
{else}
    <p>{'Email address not found or you are already unsubscribed.'|i18n('design/nvnewsletter')}</p>
    {if $result.error|eq('usercode')}
        <p>{'Check your unsubscribe link. You probably clicked non-personalized link from preview.'|i18n('design/nvnewsletter')}</p>
    {/if}
{/if}