<?php
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$app_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', realpath(__DIR__.'/../..')));
$assets_path = $base_url . $app_path . '/assets/images/';
?>
<style>
     .admin-footer {
            background-color: var(--virlanie-dark);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: 50px;
        }
        
        .admin-footer p {
            margin-top: 10px;
            margin-bottom: 6px;
        }
</style>

<footer class="admin-footer">
    <div class="container">
        <img src="<?php echo $assets_path; ?>virlanie-logo.png" alt="Virlanie Foundation" style="height: 60px; margin-bottom: 5px;">
        <p>Empowering children through education and skills development</p>
        <p>&copy; <?php echo date('Y'); ?> Virlanie Foundation, Inc. All rights reserved.</p>
    </div>
</footer>