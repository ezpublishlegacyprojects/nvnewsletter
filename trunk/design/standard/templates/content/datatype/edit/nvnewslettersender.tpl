<select id="selectMailSender" name="Attribute_{$attribute.id}">
{foreach $attribute.content.options as $key => $option}
  <option value="{$key}"{if eq( $attribute.content.selected, $key)} selected="selected"{/if}>{$option}</option>
{/foreach}
</select>