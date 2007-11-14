<?php /* Smarty version 2.6.18, created on 2007-11-14 11:17:31
         compiled from ./application/modules/admin/views/scripts/acl/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', './application/modules/admin/views/scripts/acl/index.tpl', 27, false),)), $this); ?>
<div id="aclIndexIndex">
    Access Roles provide a simple approach to managing user access within the application.
    Roles are created by any user with access, allowing them to grand and revoke access to certain
    resources within the application.  Resources are defined as accessible functions
    within the application.<br /><br />

    Below are all active roles that are available to be assigned to users:<br /><br />

    <?php if ($this->_tpl_vars['acl']['add']): ?>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/add/"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/add.png" alt="Add Access Role"></a>
    <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/add/">Add New Access Role</a><br /><br />
    <?php endif; ?>
    <table class="list sortable">
    <?php $_from = $this->_tpl_vars['roles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['roles'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['roles']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['r']):
        $this->_foreach['roles']['iteration']++;
?>
        <?php if (($this->_foreach['roles']['iteration']-1) % $this->_tpl_vars['config']['headerRowRepeat'] == 0): ?>
        <tr>
            <th width="200">Role Name</th>
            <th width="200">Inherited From</th>
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
            <td><a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/details/?originalRoleName=<?php echo $this->_tpl_vars['r']['name']; ?>
"><?php echo $this->_tpl_vars['r']['name']; ?>
</a></td>
            <td align="center"><?php echo $this->_tpl_vars['r']['inherit']; ?>
</td>
            <?php if ($this->_tpl_vars['acl']['edit']): ?>
            <td align="center">
                <?php if ($this->_tpl_vars['r']['editable']): ?>
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/edit/?originalRoleName=<?php echo $this->_tpl_vars['r']['name']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/edit.png" alt="Edit <?php echo $this->_tpl_vars['r']['name']; ?>
" /></a>
                <?php else: ?>
                <img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/editDisabled.png" alt="Edit Disabled for <?php echo $this->_tpl_vars['r']['name']; ?>
" />
                <?php endif; ?>
            </td>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['acl']['delete']): ?>
            <td align="center">
                <?php if ($this->_tpl_vars['r']['editable']): ?>
                <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/delete/?originalRoleName=<?php echo $this->_tpl_vars['r']['name']; ?>
"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/delete.png" alt="Delete <?php echo $this->_tpl_vars['r']['name']; ?>
" /></a>
                <?php else: ?>
                <img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/deleteDisabled.png" alt="Delete Disabled for <?php echo $this->_tpl_vars['r']['name']; ?>
" />
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td class="noResults">No Roles found</td>
        </tr>
        <?php endif; unset($_from); ?>
    </table>
</div>