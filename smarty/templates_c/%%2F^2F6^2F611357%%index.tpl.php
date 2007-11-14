<?php /* Smarty version 2.6.18, created on 2007-11-13 14:21:45
         compiled from ./application/modules/default/views/scripts/bug/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', './application/modules/default/views/scripts/bug/index.tpl', 17, false),array('modifier', 'truncate', './application/modules/default/views/scripts/bug/index.tpl', 20, false),array('modifier', 'date_format', './application/modules/default/views/scripts/bug/index.tpl', 25, false),array('modifier', 'capitalize', './application/modules/default/views/scripts/bug/index.tpl', 26, false),)), $this); ?>
<div id="adminSemesterIndex">
    These are the bugs which have been filed.<br /><br />

    <?php if ($this->_tpl_vars['acl']['add']): ?>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/bug/add/"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/add.png" alt="Add Semester"></a>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/bug/add/">New Bug Report</a><br /><br />
    <?php endif; ?>
    <table class="list sortable">
    <?php $_from = $this->_tpl_vars['bugs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['bugs'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['bugs']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['b']):
        $this->_foreach['bugs']['iteration']++;
?>
        <?php if (($this->_foreach['bugs']['iteration']-1) % $this->_tpl_vars['config']['headerRowRepeat'] == 0): ?>
        <tr>
            <th width="450">Bug Description</th>
            <th width="150">Submit Info</th>
            <th width="60">Status</th>
        </tr>
        <?php endif; ?>
        <tr class="<?php echo smarty_function_cycle(array('values' => "row1,row2"), $this);?>
">
            <td>
            <?php if ($this->_tpl_vars['acl']['details']): ?>
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/bug/details/?bugId=<?php echo $this->_tpl_vars['b']['bugId']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['b']['description'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 70) : smarty_modifier_truncate($_tmp, 70)); ?>
</a>
            <?php else: ?>
                <?php echo ((is_array($_tmp=$this->_tpl_vars['b']['description'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 70) : smarty_modifier_truncate($_tmp, 70)); ?>

            <?php endif; ?>
            </td>
            <td style="text-align:center"><?php echo $this->_tpl_vars['b']['submittedByUserId']; ?>
 (<?php echo ((is_array($_tmp=$this->_tpl_vars['b']['submitDt'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['dateFormat']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['dateFormat'])); ?>
)</td>
            <td style="text-align:center"><?php echo ((is_array($_tmp=$this->_tpl_vars['b']['status'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
</td>
        </tr>
    <?php endforeach; else: ?>
        <tr>
            <td class="noResults">No Bugs found</td>
        </tr>
    <?php endif; unset($_from); ?>
    </table>
</div>