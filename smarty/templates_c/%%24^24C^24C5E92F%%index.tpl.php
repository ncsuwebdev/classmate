<?php /* Smarty version 2.6.18, created on 2007-11-13 14:21:47
         compiled from ./application/modules/admin/views/scripts/user/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', './application/modules/admin/views/scripts/user/index.tpl', 26, false),)), $this); ?>
<div id="userIndexIndex">
    Here you can add users and grant them roles.  Roles are defined by the applicaiton
    administrator and can be set <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/">here</a>.<br /><br />

    <?php if ($this->_tpl_vars['acl']['add']): ?>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/user/add/"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/add.png" alt="Add User"></a>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/user/add/">Add New User</a><br /><br />
    <?php endif; ?>
    <table class="list sortable">
    <?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['users'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['users']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['u']):
        $this->_foreach['users']['iteration']++;
?>
        <?php if (($this->_foreach['users']['iteration']-1) % $this->_tpl_vars['config']['headerRowRepeat'] == 0): ?>
        <tr>
            <th width="70">User ID</th>
            <th width="200">Access Role</th>
            <?php if ($this->_tpl_vars['acl']['log']): ?>
            <th width="50">Logs</th>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['acl']['edit']): ?>
            <th width="50">Edit</th>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['acl']['delete']): ?>
            <th width="50">Delete</th>
            <?php endif; ?>
        </tr>
        <?php endif; ?>
        <tr class="<?php echo smarty_function_cycle(array('values' => "row1,row2"), $this);?>
">
            <td><?php echo $this->_tpl_vars['u']['userId']; ?>
</td>
            <td align="center"><?php echo $this->_tpl_vars['u']['role']; ?>
</td>
            <?php if ($this->_tpl_vars['acl']['log']): ?>
            <td align="center">
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/log/?userId=<?php echo $this->_tpl_vars['u']['userId']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/log.png" alt="Logs for <?php echo $this->_tpl_vars['u']['userId']; ?>
" /></a>
            </td>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['acl']['edit']): ?>
            <td align="center">
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/user/edit/?userId=<?php echo $this->_tpl_vars['u']['userId']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/edit.png" alt="Edit <?php echo $this->_tpl_vars['u']['userId']; ?>
" /></a>
            </td>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['acl']['delete']): ?>
            <td align="center">
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/user/delete/?userId=<?php echo $this->_tpl_vars['u']['userId']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/delete.png" alt="Delete <?php echo $this->_tpl_vars['u']['userId']; ?>
" /></a>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; else: ?>
        <tr>
            <td class="noResults">No Users found</td>
        </tr>
    <?php endif; unset($_from); ?>
    </table>
</div>