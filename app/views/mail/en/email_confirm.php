<?php require VIEWS . 'mail/_top.php'; ?>
<table  border="0" cellpadding="0" cellspacing="0"
        style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: separate !important; border-radius: 4px; width: 100%; overflow: hidden; border: 1px solid #dee2e6;"
        bgcolor="#ffffff">
    <tbody>
    <tr>
        <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0;"
            align="left">
            <div style="border-top-width: 5px; border-top-color: #3498DB; border-top-style: solid;">
                <table  border="0" cellpadding="0" cellspacing="0"
                        style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
                    <tbody>
                    <tr>
                        <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;"
                            align="left">
                            <div>
                                <h4 class=""
                                    style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 24px; line-height: 28.8px;"
                                    align="left">E-mail address confirmation</h4>
                                <table  border="0" cellpadding="0"
                                        cellspacing="0" style="width: 100%;">
                                    <tbody>
                                    <tr>
                                        <td height="16"
                                            style="border-spacing: 0px; border-collapse: collapse; line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;"
                                            align="left">
                                             
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <p class=""
                                   style="line-height: 24px; font-size: 14px; margin: 0;"
                                   align="left">
                                    Hello,<br />
                                    You recently created a JustAuthMe account on your smartphone.
                                    In order to complete the registration process, we invite you to validate
                                    your e-mail address by clicking the button below.
                                    Beware, this link is only valid for 24h!
                                    If the button does not display or if you encounters issues while using it, simply copy
                                    this link in your web browser:<br />
                                    <span style="color:blue"><?= $confirm_link ?></span>
                                </p>
                                <table  border="0" cellpadding="0"
                                        cellspacing="0" style="width: 100%;">
                                    <tbody>
                                    <tr>
                                        <td height="16"
                                            style="border-spacing: 0px; border-collapse: collapse; line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;"
                                            align="left">
                                             
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>


                                <table  border="0" cellpadding="0"
                                        cellspacing="0" style="width: 100%;">
                                    <tbody>
                                    <tr>
                                        <td height="8"
                                            style="border-spacing: 0px; border-collapse: collapse; line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;"
                                            align="left">
                                             
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                    <table
                                            align="center" border="0" cellpadding="0"
                                            cellspacing="0"
                                            style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: separate !important; border-radius: 4px; margin: 0 auto;">
                                        <tbody>
                                        <tr>
                                            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-radius: 4px; margin: 0;"
                                                align="center" bgcolor="#3498DB">
                                                <a href="<?= $confirm_link ?>"
                                                   style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 4.8px; line-height: 30px; display: inline-block; font-weight: normal; white-space: nowrap; background-color: #3498DB; color: #ffffff; padding: 8px 16px; border: 1px solid #3498DB;">
                                                    Confirm my e-mail address
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
<?php require VIEWS . 'mail/en/_btm.php'; ?>