<?php /* Smarty version 2.6.18, created on 2007-11-14 11:18:59
         compiled from ./application/modules/admin/views/scripts/acl/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', './application/modules/admin/views/scripts/acl/edit.tpl', 15, false),array('modifier', 'capitalize', './application/modules/admin/views/scripts/acl/edit.tpl', 40, false),)), $this); ?>
<div id="aclIndexAdd">
    Select the name that you would like to call the role.  This system also allows
    roles to inherit permission from an existing role.  Inhertance is optional
    but is useful in implementing a tiered access system.<br /><br />

    <form method="POST" action="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/<?php echo $this->_tpl_vars['action']; ?>
">
        <input type="hidden" name="originalRoleName" id="originalRoleName" value="<?php echo $this->_tpl_vars['originalRoleName']; ?>
" />
        <table class="form">
            <tr>
                <td><label for="roleName">Role Name:</label></td>
                <td><input type="text" class="required" name="roleName" id="roleName" value="<?php echo $this->_tpl_vars['roleName']; ?>
" size="30" maxlength="126" /></td>
            </tr>
            <tr>
                <td><label for="role">Inherit From:</label></td>
                <td><?php echo smarty_function_html_options(array('name' => 'inheritRoleName','id' => 'inheritRoleName','options' => $this->_tpl_vars['roles'],'selected' => $this->_tpl_vars['inheritRoleName']), $this);?>

                <input type="button" value="Pre-Populate" onclick="if (confirm('You will lose any changes you have made.')) location.href='<?php echo $this->_tpl_vars['sitePrefix']; ?>
/admin/acl/<?php echo $this->_tpl_vars['action']; ?>
/?roleName=' + document.getElementById('roleName').value + '&originalRoleName=' + document.getElementById('originalRoleName').value + '&inheritRoleName=' + document.getElementById('inheritRoleName').value; return false;" /></td>
            </tr>
        </table><br /><br />

        <?php if ($this->_tpl_vars['action'] == 'edit' && count ( $this->_tpl_vars['children'] ) != 0): ?>
        <table class="form highlight">
            <tr>
                <td><b>CAUTION!</b><br /><br />
                Making changes to this role will affect the following roles which are
                inherited (directly or indirectly) from this role:
                <ul>
                <?php $_from = $this->_tpl_vars['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
                <li><a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/acl/details/?originalRoleName=<?php echo $this->_tpl_vars['c']['name']; ?>
"><?php echo $this->_tpl_vars['c']['from']; ?>
</li>
                <?php endforeach; endif; unset($_from); ?>
                </ul>
                </td>
            </tr>
        </table><br /><Br />
        <?php endif; ?>
        <div id="accessList">
            <?php $_from = $this->_tpl_vars['resources']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['module'] => $this->_tpl_vars['controllers']):
?>
            <div class="aclSection">
                <table class="list">
                    <tr class="module">
                        <td colspan="3" width="460"><b><?php echo ((is_array($_tmp=$this->_tpl_vars['module'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
</b></td>
                    </tr>
                    <?php $_from = $this->_tpl_vars['controllers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['controller'] => $this->_tpl_vars['actions']):
?>
                    <tr class="controller">
                        <td class="td1" width="300"><?php echo ((is_array($_tmp=$this->_tpl_vars['controller'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>

                        </td>
                        <td width="80"><input type="radio" value="allow" onclick="toggle(this);" name="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][all]" id="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][all]"<?php if ($this->_tpl_vars['actions']['all']['access']): ?> checked="checked"<?php endif; ?> /> Allow All</td>
                        <td width="80"><input type="radio" value="deny" onclick="toggle(this);" name="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][all]" id="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][all]"<?php if (! $this->_tpl_vars['actions']['all']['access']): ?> checked="checked"<?php endif; ?> /> Deny All</td>
                    </tr>
                        <?php $_from = $this->_tpl_vars['actions']['part']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['action'] => $this->_tpl_vars['access']):
?>
                    <tr class="action">
                        <td class="td1"><?php echo ((is_array($_tmp=$this->_tpl_vars['action'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>

                        </td>
                        <td><input type="radio" value="allow" name="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][part][<?php echo $this->_tpl_vars['action']; ?>
]" id="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][part][<?php echo $this->_tpl_vars['action']; ?>
]"<?php if ($this->_tpl_vars['access']['access']): ?> checked="checked"<?php endif; ?>/> Allow</td>
                        <td><input type="radio" value="deny" name="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][part][<?php echo $this->_tpl_vars['action']; ?>
]" id="<?php echo $this->_tpl_vars['module']; ?>
[<?php echo $this->_tpl_vars['controller']; ?>
][part][<?php echo $this->_tpl_vars['action']; ?>
]"<?php if (! $this->_tpl_vars['access']['access']): ?> checked="checked"<?php endif; ?>/> Deny</td>
                    </tr>
                        <?php endforeach; endif; unset($_from); ?>
                    <?php endforeach; endif; unset($_from); ?>
                </table>
            </div>
            <?php endforeach; endif; unset($_from); ?>

        </div>
        <input type="submit" value="Set Permission" />
        <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>