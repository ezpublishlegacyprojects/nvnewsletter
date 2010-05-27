{if $attribute.content.selected|ne('')}
  {$attribute.content.options[$attribute.content.selected]|trim('&nbsp;')}
{/if}