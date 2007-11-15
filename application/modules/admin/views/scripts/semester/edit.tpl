<div id="adminSemesterEdit">
    Modify the semester information for {$semester.name}.<br /><br />
    <form method="POST" action="">
        <input type="hidden" name="semesterId" value="{$semester.semesterId}" />
        <table class="form">
            <tr>
                <td><label>Semester Name:</label></td>
                <td>{$semester.name}</td>
            </tr>
            <tr>
                <td><label>Start Date:</label></td>
                <td>{$semester.startDate|date_format:$config.dateTimeFormat}</td>
            </tr>
            <tr>
                <td><label for="preSemesterActivateDays">Activate Date:</label></td>
                <td><input type="text" size="10" name="preSemesterActivateDays" id="preSemesterActivateDays" value="{$semester.preSemesterActivateDays}" /> Days before the semester starts</td>
            </tr>
            <tr>
                <td><label for="gcPreSemesterAvailableDays">GC Open Date:</label></td>
                <td><input type="text" size="10" name="gcPreSemesterAvailableDays" id="gcPreSemesterAvailableDays" value="{$semester.gcPreSemesterAvailableDays}" /> Days before the semester starts</td>
            </tr>
            <tr>
                <td><label for="gcPostSemesterComtechAvailableDays">ComTech Expire Date:</label></td>
                <td><input type="text" size="10" name="gcPostSemesterComtechAvailableDays" id="gcPostSemesterComtechAvailableDays" value="{$semester.gcPostSemesterComtechAvailableDays}" /> Days after the semester starts</td>
            </tr>
        </table>
        <input type="submit" value="Save Semester" />
        <input type="button" value="Cancel" onclick="javascript:history.go(-1)" />
    </form>
</div>