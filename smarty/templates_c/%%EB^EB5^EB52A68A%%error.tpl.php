<?php /* Smarty version 2.6.18, created on 2007-11-13 14:19:58
         compiled from ./application/views/scripts/error/error.tpl */ ?>
<div class="error">
<b><?php echo $this->_tpl_vars['messageTitle']; ?>
</b><br />
<ul>
<?php if ($this->_tpl_vars['message'] == ''): ?>
    <li>No error message passed</li>
<?php else: ?>
    <li><?php echo $this->_tpl_vars['message']; ?>
</li>
<?php endif; ?>
</ul>
<br />
<form id="groupForm">
    <input type="button" value="Back" onclick="history.go(-1);" />
</form>
</div>
<span id="error"></span>