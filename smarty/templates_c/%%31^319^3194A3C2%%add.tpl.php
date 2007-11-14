<?php /* Smarty version 2.6.18, created on 2007-11-13 15:41:43
         compiled from ./application/modules/default/views/scripts/bug/add.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', './application/modules/default/views/scripts/bug/add.tpl', 8, false),)), $this); ?>
<div id="bugAdd">
    If you are experiencing an issue with Aerial, you can file a bug report here.  If you
    have a feature request, you should contact your administrator directly.<br /><br />
    <form method="POST" action="">
    <table class="form">
        <tr>
            <td><label>Reproducibility:</label></td>
            <td><?php echo smarty_function_html_options(array('class' => 'required','name' => 'reproducibility','id' => 'reproducibility','options' => $this->_tpl_vars['reproducibilityTypes']), $this);?>
</td>
        </tr>
        <tr>
            <td><label>Severity:</label></td>
            <td><?php echo smarty_function_html_options(array('class' => 'required','name' => 'severity','id' => 'severity','options' => $this->_tpl_vars['severityTypes']), $this);?>
</td>
        </tr>
        <tr>
            <td><label>Priority:</label></td>
            <td><?php echo smarty_function_html_options(array('class' => 'required','name' => 'priority','id' => 'priority','options' => $this->_tpl_vars['priorityTypes']), $this);?>
</td>
        </tr>
        <tr>
            <td><label>Description:</label></td>
            <td><textarea rows="5" class='required' cols="100" name="description"></textarea></td>
        </tr>
    </table>
    <input type="submit" value="File Bug Report" />
    <input type="button" value="Cancel" onclick="history.go(-1);" />
    </form>
</div>