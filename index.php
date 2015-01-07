<?php

define('ROOT', dirname(__FILE__) . '/');
define('ROOT_CORE', ROOT . 'core/');
define('ROOT_CLASS', ROOT_CORE . 'class/');

require_once(ROOT_CLASS . 'Config.class.php');
require_once(ROOT_CLASS . 'T411API.class.php');
require_once(ROOT_CLASS . 'MyTorrent.class.php');
require_once(ROOT_CORE . 'functions.php');

if (Config::$debug)
{
    error_reporting(0);
    set_error_handler("myErrorHandler");
}

$id = isset($_POST['id']) && is_string($_POST['id']) ? $_POST['id'] : "";
$torrentFile = isset($_FILES['file']) ? $_FILES['file'] : "";
$getMagnet = isset($_POST['getMagnet']) ? true : false;
$getTorrent = isset($_POST['getTorrent']) ? true : false;

try
{
    if (!empty($id))
    {
        $T411API = new T411API(Config::$t411Account["username"], Config::$t411Account["password"]);
        $T411API->connectionRequest();
        $pathFile = $T411API->downloadTorrentRequest($id);
        $torrent = new MyTorrent($pathFile);
        $stepTwo = true;
    }
    else if (!empty($torrentFile))
    {
        $torrentPath = uploadTorrent($torrentFile);
        $torrent = new MyTorrent($torrentPath);
        $stepTwo = true;
    }

    if (isset($stepTwo))
    {
        if ($getTorrent)
        {
            die($torrent->send());
        }
        else if ($getMagnet)
        {
            echo "<script>prompt('Copiez/Collez le magnet', '".$torrent->magnet()."');</script>";
        }
    }
} catch(MyException $e)
{
    $error = $e->getMessage();
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>T411UNLIMITED</title>
        <link href="./static/css/style.css" rel="stylesheet" media="all" type="text/css"> 
        <script type="text/javascript" href="./static/js/script.js"></script>
    </head>
<body>

<?php 
if (!checkT411TrackerStatusFromCache())
{
    echo "<p style='font-size: 18px; color: red; font-weight: bold'>/!\ : Le tracker T411 semble être hors ligne, T411Unlimited l'est par conséquent !</p>";
}
?>

<section id="leftContent">
    <h1>T411UNLIMITED</h1>

    <p>
    T411UNLIMITED permet de télécharger sur le tracker T411.me sans se soucier de son ratio !<br />
    Récupérez le torrent téléchargé sur T411 ou son ID (voir tutoriel ci dessous).<br />
    </p>

    <h2>Entrez l'id/torrent ci dessous</h2>

    <form method="POST" enctype="multipart/form-data" action="">
        <label for="id">ID :</label>
        <input type="text" id="id" name="id" placeholder="Ex : 5174565" />
        <label for="file"> <span style='font-weight:bold;'>ou</span> Torrent :</label>
        <input type="file" id="file" name="file" accept=".torrent" />
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
        <br />
        <input type="submit" name="getTorrent" value="Télécharger le torrent" />
        <!--<input type="submit" name="getMagnet" value="Récupérer le lien magnet" />-->
    </form>

    <?php 
    if (isset($error))
    {
        echo "<strong style='color:red;margin-top:10px;display:inline-block;'>Erreur : $error</strong>";
    }
    ?>

    <h2>Tutoriel</h2>

    <a href="img/tuto.png" target="_blank"><img src="static/img/tuto.png" style="width:500px" /></a>
    <p><em>Exemples :</em>
        <ul>
            <li>https://www.t411.me/t/<strong>5174565</strong></li>
            <li>http://www.t411.me/torrents/download/?id=<strong>5174565</strong></li>
            <li>https://www.t411.me/t/<strong>5174565</strong></li>
    </p>
</section>

<section id="rightContent">
    <h1>NEWS</h1>
    <a class="twitter-timeline" href="https://twitter.com/t411unlimited" data-widget-id="551813034373824512">Tweets de @t411unlimited</a>
    <br />
<div class="fb-like" data-href="https://www.facebook.com/pages/T411Unlimited/465844353556674" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
</section>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-58172575-1', 'auto');
  ga('send', 'pageview');
</script>

</body>
</html>