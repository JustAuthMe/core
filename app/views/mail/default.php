<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1-strict.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width; initial-scale:1.0; maximum-scale=1.0;" />
    </head>
    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" border="0" ceilpadding="0" ceilspacing="0" style="font-family:Helvetica,Verdana,sans-serif">
        <table bgcolor="#ffffff" width="600" align="center">
            <tbody>
                <tr>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="80" align="center">
                        <img height="80" src="https://static.justauth.me/TXT_ONLY_BLUE_80.png" alt="JustAuth.Me" />
                    </td>
                </tr>
                <tr>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="70" style="border-top: 1px solid #555"></td>
                </tr>
                <tr>
                    <td height="30" align="center">
                        <h1 style="text-transform: uppercase;font-size: 24px;"><?= $subject ?></h1>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p align="justify" style="font-size:14px;line-height:28px">
                            <?= $body ?>
                        </p>
                    </td>
                </tr>
                <?php if (isset($call_to_action)): ?>
                    <tr>
                        <td height="10"></td>
                    </tr>
                    <tr>
                        <td align="center">
                            <a href="<?= $call_to_action->link ?>" style="color:white;text-decoration:none;">
                                <table width="200" bgcolor="#3598db" style="border-radius:20px">
                                    <tr color="white">
                                        <td height="25" align="center" style="font-size: 12px;text-transform: uppercase; color: white;" color="white"><?= $call_to_action->title ?></td>
                                    </tr>
                                </table>
                            </a>
                        </td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td height="70" style="border-bottom: 1px solid #555"></td>
                </tr>
                <tr>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td>
                        <p align="center" style="font-size: 13px;">
                            <b>JustAuthMe SAS</b><br />
                            12 Rue Anselme - 93400 Saint-Ouen
                        </p>
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td align="center">
                        <a href="https://twitter.com/justauthme"><img src="https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-twitter-s.png" /></a>
                        &nbsp;&nbsp;
                        <a href="https://www.facebook.com/justauthme"><img src="https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-facebook-s.png" /></a>
                        &nbsp;&nbsp;
                        <a href="https://instagram.com/justauthme"><img src="https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-instagram-s.png" /></a>
                    </td>
                </tr>
                <tr>
                    <td height="10"></td>
                </tr>
                <tr>
                    <td>
                        <table width="600" align="center">
                            <tbody>
                            <tr>
                                <td width="30"></td>
                                <td>
                                    <p align="center" style="font-size:13px;color:#555;line-height:20px;">
                                        Vous recevez cet E-Mail car votre adresse E-Mail est associée à un
                                        compte <a href="https://justauth.me">JustAuth.Me</a>.<br />
                                        S'il ne s'agit pas de vous, merci de nous le faire savoir en répondant à cet
                                        E-Mail. Sachez néanmoins que la sécurité de votre compte E-Mail ou de vos
                                        informations personnelles n'a pas été compromise.
                                    </p>
                                </td>
                                <td width="30"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!--<tr>
                    <td>
                        <table width="660" align="center">
                            <tbody>
                            <tr align="center" style="font-size:13px">
                                <td width="220">
                                    <a href="https://justauth.me">https://justauth.me</a>
                                </td>
                                <td width="220">
                                    <a href="mailto:support@justauth.me">support@justauth.me</a>
                                </td>
                                <td width="220">
                                    JustAuthMe SAS<br />
                                    SAS au capital de 1000,00€<br />
                                    12 Rue Anselme, 93400 Saint-Ouen
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>-->
            </tbody>
        </table>
    </body>
</html>