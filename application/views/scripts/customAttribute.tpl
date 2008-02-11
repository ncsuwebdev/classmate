{if $tempAttribute.required}
{assign var=required value=" (Required)"}
{assign var=label value=$tempAttribute.label|cat:" <span class='requiredNotification'>(Required)</span>"}
{else}
{assign var=label value=$tempAttribute.label}
{/if}
<tr>
    <td{if strlen($label) >= 20} colspan="2"{/if}><label for="{$tempAttribute.attributeId}">{$label}:</label></td>
    <td>{if strlen($tempAttribute.label) < 20}{$tempAttribute.formField}{/if}</td>
</tr>
{if strlen($tempAttribute.label) >= 20}
<tr>
    <td colspan="2">{$tempAttribute.formField}</td>
</tr>
{/if}
