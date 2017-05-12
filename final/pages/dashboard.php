<?php
include_once "common/header.php";
include_once "../database/projects.php";
include_once "../database/files.php";
include_once "../database/users.php";

$projectName = getProjectName($_SESSION['project_id']);
$projectDescription = getProjectDescription($_SESSION['project_id']);
$files = getLastThreeUploadedFiles($_SESSION['project_id']);


$smarty->assign('files',$files);
$smarty->assign('projectName',$projectName);
$smarty->display($BASE_DIR . 'templates/dashboard.tpl');

?>

<?php
include_once "common/footer.php";
?>


