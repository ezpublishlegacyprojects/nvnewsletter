{foreach $attribute.content.options as $key => $option}
  {if eq($attribute.content.selected.$key,'1')}
    {$option}<br />
  {/if}
{/foreach}