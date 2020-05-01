<?php require VIEWS . 'mail/_top.php'; ?>
<tr>
    <td height="30" align="center">
        <h1 style="text-transform: uppercase;font-size: 24px;">Lock passcode</h1>
    </td>
</tr>
<tr>
    <td>
        <p align="justify" style="font-size:14px;line-height:28px">
            Bonjour,<br />
            Vous avez récemment souhaité verrouiller votre compte JustAuthMe suite à la perte ou le vol de votre
            appareil mobile. Afin de compléter le processus de verrouillage, nous vous invitons à entrer le code
            ci-dessous dans votre navigateur.
            Ce code n'est valable que durant 10 minutes.
            Si le code ne s'affiche pas ou si vous rencontrez des difficultés pour l'utiliser,
            contactez le support à l'adresse suivante: <a href="mailto:support@justauth.me">support@justauth.me</a>
            <br /><br />
            ATTENTION: Ce code est <strong>strictement personnel</strong> et ne dois être communiqué à personne.
            Aucun employé de JustAuthMe ne vous demandera ce code. Si vous êtes actuellement en contact avec
            quelqu'un qui prétend travailler pour JustAuthMe et souhaite que vous lui transmettiez ce code,
            <strong>ne le faites surtout pas</strong>, il s'agit d'une arnaque !
        </p>
    </td>
</tr>
<tr>
    <td height="10"></td>
</tr>
<tr>
    <td align="center">
        <h1><?= $passcode ?></h1>
    </td>
</tr>
<?php require VIEWS . 'mail/_btm.php'; ?>
