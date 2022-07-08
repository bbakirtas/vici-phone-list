<?php
if (!empty($_GET['refresh']))
{
	$data = system('echo "rootpasswd" | sudo -u root -S perl /usr/share/empty/sipshow.pl');
	 header('Content-type: application/json');
        $fp    = 'vicidial/sipler.txt';
        $conents_arr   = file($fp,FILE_IGNORE_NEW_LINES);
        foreach($conents_arr as $key=>$value)
        {
            $conents_arr[$key]  = rtrim($value, "\r");
        }

        var_dump($conents_arr);
        $json_contents = json_encode($conents_arr);

file_put_contents("data.json", $json_contents);
   

header('Location: dashboard.php');
}

?>



<?php
if (empty($_GET['refresh']))
{
require ("vicidial/dbconnect_mysqli.php");
require("vicidial/functions.php");
$jsonitem = file_get_contents("data.json");
$json = json_decode($jsonitem);
$i = count($json);
$your_phone = $_POST['your_phone']; 
$username = $_POST['username']; 
$passwd = $_POST['passwd']; 
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="forms.css">  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">
<a href="?refresh=1" class="btn btn-outline-info">Refresh List</a><br><br>
<div class="alert alert-primary" role="alert">
 Barge and Monitor will open when you enter the form.
</div>
<form method="post" action=""  class="form-inline">

<input class="form-control form-control-sm col-md-2" type="text" name="your_phone" placeholder="Your Phone" value="<?php echo $your_phone;?>">
<input class="form-control form-control-sm col-md-2" type="text" name="username" placeholder="Admin Username" value="<?php echo $username;?>">
<input class="form-control form-control-sm col-md-2" type="password" name="passwd" placeholder="Admin Password" value="<?php echo $passwd;?>">  <button type="submit" class="btn btn-primary btn-sm">Save</button>
</form>
<div class="row">
   

 <?php
                for ($b = 1; $b <= $i; $b++) {
 
$extension_g = substr($json[$b],0,4);
$barge = "";	
$monitor = "";	
$stmt = "Select fullname,company,picture,dialplan_number,email,extension from phones where extension = '$extension_g'";
$rslt = mysql_to_mysqli($stmt, $link);
$row=mysqli_fetch_row($rslt);
$extension = $row[5];
if (!empty($extension))
{
$full_name = $row[0];
$company=$row[1];
$picture = $row[2];
$dialplan_number=$row[3];
$email=$row[4];


 $stmt_live = "select user from vicidial_live_agents where extension = 'SIP/$extension_g'";
$rslt_live = mysql_to_mysqli($stmt_live, $link);
$row_live=mysqli_fetch_row($rslt_live);
$user_active = $row_live[0];
if (empty($user_active)) {$user_status = '<button type="button" class="btn btn-warning btn-sm">User Not Active Agent Screen</button>';}
if (!empty($user_active)) {$user_status = ' <button type="button" class="btn btn-primary btn-sm">User Active Agent Screen</button>';}

if (!empty($user_active))
{
 $stmt_conf= "select conf_exten,server_ip from vicidial_conferences where extension = 'SIP/$extension_g'";
$rslt_conf = mysql_to_mysqli($stmt_conf, $link);
$row_conf =mysqli_fetch_row($rslt_conf);
$conf_exten  = $row_conf[0];
$server_ip = $row_conf[1];
$api_link_barge = "http://$server_ip/vicidial/non_agent_api.php?source=test&user=$username&pass=$passwd&function=blind_monitor&phone_login=$your_phone&session_id=$conf_exten&server_ip=$server_ip&stage=BARGE";
$api_link_monitor = "http://$server_ip/vicidial/non_agent_api.php?source=test&user=$username&pass=$passwd&function=blind_monitor&phone_login=$your_phone&session_id=$conf_exten&server_ip=$server_ip&stage=MONITOR";

if (!empty($conf_exten)) {$barge = '<a href ="'.$api_link_barge.'" target="_blank" class="btn btn-info btn-sm">Barge</a> ';
$monitor = '<a href ="'.$api_link_monitor.'" target="_blank" class="btn btn-secondary btn-sm">Monitor</a> ';}

}
if (empty($username or $passwd or $your_phone))
{
	$monitor = "";
	$barge = "";
}

if (strstr($json[$b],"OK")) { 
?>

    <div class="col-lg-4">
        <div class="card card-margin">
            <div class="card-header no-border">
                <h5 class="card-title"><?php echo $full_name;?> / <?php echo $extension_g;?></h5>
            </div>
            <div class="card-body pt-0">
                <div class="widget-49">
                    <div class="widget-49-title-wrapper">
                        <div class="widget-49-date-success">
                            <span class="widget-49-date-day">ON</span>
                            <span class="widget-49-date-month">lıne</span>
                        </div>
                        <div class="widget-49-meeting-info">
                            <span class="widget-49-pro-title"><?php echo $email;?></span>
                            <span class="widget-49-meeting-time"><?php echo $company;?></span>
                        </div>
                    </div>
                   <br>
                    <div class="widget-49-meeting">
                        <?php echo $user_status;?><BR><BR>
                    </div>
                    <div class="widget-49-meeting-action">  
                    <?php echo $barge;?> 
                    <?php echo $monitor;?>
                        <a href="sip:<?php echo $extension_g;?>" class="btn btn-sm btn-primary">Call</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
         <?php }
         else
          { ?>
          	 <div class="col-lg-4">
        <div class="card card-margin">
            <div class="card-header no-border">
                <h5 class="card-title"><?php echo $full_name;?> / <?php echo $extension_g;?></h5>
            </div>
            <div class="card-body pt-0">
                <div class="widget-49">
                    <div class="widget-49-title-wrapper">
                        <div class="widget-49-date-danger">
                            <span class="widget-49-date-day">OFF</span>
                            <span class="widget-49-date-month">lıne</span>
                        </div>
                        <div class="widget-49-meeting-info">
                            <span class="widget-49-pro-title"><?php echo $email;?></span>
                            <span class="widget-49-meeting-time"><?php echo $company;?></span>
                        </div>
                    </div>
                   <br>
                    <div class="widget-49-meeting">
                       <button type="button" class="btn btn-danger btn-sm">User Not Active Agent Screen and Phone</button>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
         	<?php 
         }
     }
 } 
 ?>

</div>
</div>

<?php }
 ?>
