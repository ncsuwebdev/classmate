<?php /* Smarty version 2.6.18, created on 2007-11-14 11:38:40
         compiled from ./application/modules/admin/views/scripts/emailqueue/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', './application/modules/admin/views/scripts/emailqueue/index.tpl', 8, false),array('function', 'jscalendar', './application/modules/admin/views/scripts/emailqueue/index.tpl', 13, false),array('function', 'cycle', './application/modules/admin/views/scripts/emailqueue/index.tpl', 53, false),array('modifier', 'date_format', './application/modules/admin/views/scripts/emailqueue/index.tpl', 13, false),array('modifier', 'capitalize', './application/modules/admin/views/scripts/emailqueue/index.tpl', 56, false),array('modifier', 'empty_alt', './application/modules/admin/views/scripts/emailqueue/index.tpl', 57, false),)), $this); ?>
<div id="adinEmailqueueIndex">
    The following are the emails that have been sent or are queued to be sent.<br /><br />

    <form method="get" action="">
        <table class="form">
            <tr>
                <td><label for="status">Status:</label></td>
                <td><?php echo smarty_function_html_options(array('name' => 'status','id' => 'status','options' => $this->_tpl_vars['statusTypes'],'selected' => $this->_tpl_vars['status']), $this);?>
</td>
            </tr>
            <tr>
                <td><label>Queue Date:</label></td>
                <td>
                    <?php echo smarty_function_jscalendar(array('name' => 'queueBeginDt','value' => ((is_array($_tmp=$this->_tpl_vars['queueBeginDt'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['dateTimeFormat']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['dateTimeFormat']))), $this);?>

                    <b>- to -</b>
                    <?php echo smarty_function_jscalendar(array('name' => 'queueEndDt','value' => ((is_array($_tmp=$this->_tpl_vars['queueEndDt'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['dateTimeFormat']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['dateTimeFormat']))), $this);?>

                </td>
            </tr>
            <tr>
                <td><label>Sent Date:</label></td>
                <td>
                    <?php echo smarty_function_jscalendar(array('name' => 'sentBeginDt','value' => ((is_array($_tmp=$this->_tpl_vars['sentBeginDt'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['dateTimeFormat']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['dateTimeFormat']))), $this);?>

                    <b>- to -</b>
                    <?php echo smarty_function_jscalendar(array('name' => 'semtEndDt','value' => ((is_array($_tmp=$this->_tpl_vars['semtEndDt'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['dateTimeFormat']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['dateTimeFormat']))), $this);?>

                </td>
            </tr>
            <tr>
                <td><label>Attribute:</label></td>
                <td>
                    <input type="text" name="attributeName" id="attributeName" value="<?php echo $this->_tpl_vars['attributeName']; ?>
" />
                    <input type="text" name="attributeId" id="attributeId" value="<?php echo $this->_tpl_vars['attributeId']; ?>
" />
                </td>
            </tr>
            <tr>
                <td><label for="callId">Call ID:</td>
                <td><input type="text" name="callId" id="callId" value="<?php echo $this->_tpl_vars['callId']; ?>
" /></td>
            </tr>
        </table>
        <input type="submit" value="Filter Results" />
    </form>
    <br /><br />

    <table class="list sortable">
    <?php $_from = $this->_tpl_vars['emails']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['emails'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['emails']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['e']):
        $this->_foreach['emails']['iteration']++;
?>
        <?php if (($this->_foreach['emails']['iteration']-1) % $this->_tpl_vars['config']['headerRowRepeat'] == 0): ?>
        <tr>
            <th width="150">To</th>
            <th width="300">Subject</th>
            <th width="90">Status</th>
            <th width="90">Call ID</th>
            <th width="50">Details</th>
        </tr>
        <?php endif; ?>
        <tr class="<?php echo smarty_function_cycle(array('values' => "row1,row2"), $this);?>
">
            <td><?php echo $this->_tpl_vars['e']['msg']['to']; ?>
</td>
            <td><?php echo $this->_tpl_vars['e']['msg']['subject']; ?>
</td>
            <td style="text-align: center"><?php echo ((is_array($_tmp=$this->_tpl_vars['e']['status'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
</td>
            <td style="text-align: center"><?php echo ((is_array($_tmp=$this->_tpl_vars['e']['callId'])) ? $this->_run_mod_handler('empty_alt', true, $_tmp, 'None') : smarty_modifier_empty_alt($_tmp, 'None')); ?>
</td>
            <td style="text-align: center">
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/emailqueue/details/?queueId=<?php echo $this->_tpl_vars['e']['queueId']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/details.png" alt="Details" /></a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td class="noResults">No Emails found</td>
        </tr>
        <?php endif; unset($_from); ?>
    </table>
</div>