
<?php 
		
	error_reporting(1);
	if(!isset($_SESSION))session_start();
	
	// confirm user login first 
	
	require "../media/php/Users.php";
	require "../media/php/Course.php";
	require "../media/php/timecoder.php"; 
	
	$user = new User("users");	
	
	if(!$user->confirmLogin('exmUser')) $user->logout('exmUser','../'); 
	 /*************************************************************************/	
	 $myInfo = $user->getAll(array("user_id"=>$_SESSION['exmUser']));
	 
		if(isset($_POST['logout']))
		{
			$user->logout("exmUser","../");  
		}
	/************************************************************************/
	// fetching course schedule  
	
	$dbm = new DbTool(); 

	$readyCos = $dbm->getFields($dbm->select_Multi_Distinct(
		array("code","qtype","year"),"course_schedule",array("state"=>"ready")),array("code","qtype","year"));	
	 
	
	
	// clicking any button to start assessment 
	
	if(isset($_POST['start_paper']))
		{
			// $_SESSION['prevNewStart'] = true; $_SESSION['delay_details'] = $mySchedules['code'][0]."_".$mySchedules['qtype'][0]."_".$mySchedules['year'][0];
			if($_SESSION['prevNewStart'] == true && !empty($_SESSION['delay_details'])){				
				$_SESSION['alt'] = "error";  $criterials = explode("_",$_SESSION['delay_details']);
				$_SESSION['stgMsg'] = "You Have Not Completed Your ". $criterials[0]." &nbsp;". $criterials[1]." <br/> <br/>  Kindly Complete The Paper Before You Commence New One!";				
			}
			else {  
			
			 $dbm = new DbTool();
			 
			 $criterials = explode("_",$_POST['start_paper']);
			 $_SESSION['code'] = $criterials[0]; 
			 $_SESSION['qtype'] = $criterials[1]; 
			 $_SESSION['year'] =  $criterials[2]; 
			
			// fetch the information 
			$myCond = array("user_id"=>$_SESSION['exmUser'], "code"=>$_SESSION['code'], "qtype"=>$_SESSION['qtype'], "year"=>$_SESSION['year']);
			
			$myDatas = array("sn","user_id","code","year","total_sec","level","totalmark", "unitmark", "totalscore", "point", 
			"grade","qtype","paperlogintime", "paperlogouttime", "paper_signal","percent","sec_used","bus_stop","total_qtn"); 
			
			$_SESSION['myReports'] = $myReports = $dbm->getFields($dbm->select("users_result",$myCond),$myDatas);			
			$_SESSION['tot_qtn'] = $myReports['total_qtn'][0];
			$_SESSION['qtnNo'] =  empty($myReports['bus_stop'][0])?1:$myReports['bus_stop'][0];
			
			$_SESSION['init_sec_used'] = 0; #$myReports['sec_used'][0];
			$_SESSION['total_sec'] = $myReports['total_sec'][0];
 			$_SESSION['myStatus'] = "process";
			$_SESSION['started'] = true;
			
			unset($_SESSION['pasted']); // use this to auto create quetions and saave to database
			 
			// save some infos about login 
			$logintym = date('D jS M, Y - g:i s A');		 		
			
			$data = array("paperlogintime"=>$logintym,'currently'=>"on","paper_signal"=>"process");
			
			$dbm->updateTb("users_result",$data,$myCond);
			
			header("Location:report.php");
			
			}
		
		}
	// clicking any button to redo assessment 
	
	if(isset($_POST['redo_paper']))
		{
			 $dbm = new DbTool();
			 
			 $criterials = explode("_",$_POST['redo_paper']);
			 $_SESSION['code'] = $criterials[0]; 
			 $_SESSION['qtype'] = $criterials[1]; 
			 $_SESSION['year'] =  $criterials[2]; 
			
			// fetch the information 
			$myCond = array("user_id"=>$_SESSION['exmUser'], "code"=>$_SESSION['code'], "qtype"=>$_SESSION['qtype'], "year"=>$_SESSION['year']);
			
			$myDatas = array("sn","user_id","code","year","total_sec","level","totalmark", "unitmark", "totalscore", "point", 
			"grade","qtype","paperlogintime", "paperlogouttime", "paper_signal","percent","sec_used","bus_stop","total_qtn"); 
			
			$_SESSION['myReports'] = $myReports = $dbm->getFields($dbm->select("users_result",$myCond),$myDatas);			
			$_SESSION['tot_qtn'] = $myReports['total_qtn'][0];
			$_SESSION['qtnNo'] =  empty($myReports['bus_stop'][0])?1:$myReports['bus_stop'][0];
			
			$_SESSION['init_sec_used'] = $myReports['sec_used'][0];
			$_SESSION['total_sec'] = $myReports['total_sec'][0];
 			$_SESSION['myStatus'] = "process";
			$_SESSION['restarted'] = true;
			
			$_SESSION['pasted'] = true; // use this to auto create quetions and saave to database
			 
			// save some infos about login 
			$logintym = date('D jS M, Y - g:i s A');		 		
			
			$data = array("paperlogintime"=>$logintym,'currently'=>"on","paper_signal"=>"process");
			
			$dbm->updateTb("users_result",$data,$myCond);
			
			header("Location:report.php");
		
	}
		
	/**********************************************************/
	if(isset($_POST['complete_paper'])){
		$_SESSION['alt'] = "ok"; 
		$_SESSION['stgMsg'] = " &nbsp; &nbsp; Yes <br/> <br/> Your Paper Has Been Completed!  <br/> <br/> You Can Signout Now or Do Other Paper Available "; 
	}
	
	/// when users has completed paper 
	if($_SESSION['paperFinished'] == true){
		 for($i=1; $i<=$_SESSION['tot_qtn']; $i++){
		 unset($_SESSION['picked1'.$i]);
		 }
	}
	
?>

<!DOCTYPE html>
<html lang="en">

<head>
    
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
    
    <title> Kwara State College of Education Technology | Lafiagi </title>    
	
	<link href="../media/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../media/css/custom.css" rel="stylesheet"/>
    <link href="../media/fonts/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="../media/css/animate.min.css" rel="stylesheet" />   
	<link href="../media/images/laflogo.png" rel="shortcut icon" /> 
	
	     <!-- jQuery -->
    <script src="../media/js/jquery.min.js"></script>
 
   
</head>

<body style="background:url(../media/images/logo.PNG) repeat;">
		
				
<!--********************* MODALS ************************************************ -->
 <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
      <div class="modal-dialog modal-sm" >
        <div class="modal-content">
          <div class="modal-header">
            <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-close red "></i> </button>
            <label class="modal-title" id="myModalLabel"> ATTENTION. </label>
          </div>
          <?php if($_SESSION['alt']== "error") {?>
		  <div class="modal-body red" style="text-align:center;">
			 <i class="fa fa-warning fa-2x"> </i> &nbsp; 
			<?php echo $_SESSION['stgMsg'] ; ?> 
			<p> &nbsp;  </p>
			<center>
			 <button type="button" class="dismiss-btn btn btn-default green" data-dismiss="modal">Ok</button> 
			 </center>
			 <p> &nbsp;  </p>
          </div>
		  <?php } else if($_SESSION['alt']=="ok"){ ?>
		  <div class="modal-body green" style="text-align:center;">
			 <i class="glyphicon glyphicon-ok fa-2x"  ></i>
			<?php echo $_SESSION['stgMsg'] ; ?> 
			<p> &nbsp;  </p>
			<center>
			 <button type="button"   class="dismiss-btn btn btn-default green" data-dismiss="modal">Ok</button> 
			 </center>
			 <p> &nbsp;  </p>
          </div>
		  <?php }?>
           
		  
        </div>
      </div>
    </div>
<!--**************************************************************************************************************** -->

		
	<form method="post">
	
	<!-- Page Content -->
    <div class="body container"> 
	
	<div class="main_container">
	 
		   <br/> 
		 <!-- Content Row -->
        <div class="row">
             <div class="col-lg-10 col-md-10 col-sm-10 col-sm-offset-1">
                    <div class="panel panel-info text-center">
                        <div class="panel-heading black">
                      <h3>  <img src="../media/images/laflogo.png" style="max-height:60px; max-width:120px;" /> &nbsp; 
					  <b>    KWARA  STATE COLLEGE OF  EDUCATION (TECHNICAL), LAFIAGI </b> </h3>
                        </div>
                        <!-- /.panel-heading -->
                             
                        </div> 
                    </div>
                 
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row --> 
	 
		<div class="row">
		
			<div class="col-lg-10 col-md-10 col-sm-10 col-sm-offset-1">
				<div class="x_panel">
					
					 <div class="x_title">
                                    <h2 class="big"> <b> Welcome : <?php echo $myInfo['surname'][0]."&nbsp;".$myInfo['firstname'][0];?>  </b> </h2>   
										<div class="nav navbar-right panel_toolbox">
												  <button type="submit" onclick="return confirm('Are You Logging Out Now ? ')" class="btn btn-danger" name="logout" data-toggle="tooltip" data-placement="top" 
													title="Logout" > <i class="glyphicon glyphicon-off big"></i> </button>
										</div>		
										 
                                    <div class="clearfix"></div>
                     </div> <!-- /. x_title -->
					 
					 <div class="x_content">

                                    <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
									<div class="profile_img">

                                            <!-- end of image cropping -->
                                            <div id="crop-avatar">
                                                <!-- Current avatar -->
                                                <div class="avatar-view" title="<?php echo $allUser['surname'][0]."&nbsp;".$allUser['firstname'][0]."&nbsp;".$allUser['midname'][0] ; ?>">                                                     <img src="<?php echo "../media/user_imgs/".$myInfo['passport'][0];?>" alt="<?php echo "../media/user_imgs/".$myInfo['user_id'][0];?>">
                                                </div>  
											</div>
                                            <!-- end of image cropping -->
                                        </div>
										
										<h4 class="text-uppercase"> <b>
										<i class="fa fa-user"></i>&nbsp; <?php echo $_SESSION['exmUser'];?> </b> 
										</h4>
										   <ul class="list-unstyled user_data">
                                            <li><i class="fa fa-signal user-profile-icon"></i>  Level &nbsp; <?php echo $myInfo['level'][0]; ?>
                                            </li>
											
                                            <li>
                                                <i class="fa fa-briefcase user-profile-icon"></i> Dept. &nbsp;  <?php echo $myInfo['department'][0]; ?>
                                            </li>
											<li>
                                                <i class="fa fa-briefcase user-profile-icon"></i> Faculty. &nbsp;  <?php echo $myInfo['faculty'][0]; ?>
                                            </li>
                                        </ul>
									
									</div> <!-- /. col-sm-3 -->
									
									<div class="col-md-9 col-sm-9 col-xs-12">

											<div class="profile_title">
												<div class="col-md-6">
													<h4> <b>Course Assessment Schedule </b> </h4>
												</div>   
												
											</div>
											
											<div  class="tab_content">	
												
											 <br/>
											 
											 <!-- .. fetch all the courses available for schedule currently -->
											<?php 
												/** count all the available courses first */
												if(count($readyCos['code'])==0){ 
												/*** no course is available for assessment currently **/
												?>
												<table class="table table-responsive "> <!-- table-bordered table-stripped -->
												 
												<tbody>
												
													
													<tr>
														<td colspan="6"> 
														<p>&nbsp;</p>

														<div class="alert alert text-uppercase text-center">
															
																<h2 style="line-height:32px;"> <i class="fa fa-warning fa-2x"></i> &nbsp; <br/> it seems there is no any available course for assessment currently,
																you can check back later!
																</h2>
															 
															</div>
														</td>
														 
													</tr>
													  
													  
												</tbody>
											
											</table> 
												
												<?php }
												else {
												/*** display all available course and check for their state  **/ 												 
												 ?>
												<div class=" ">
													<h2> Please select any of the underlisted papers for your assessment </h2>
													   <br/>
												</div>
											
												
												<table class="table table-responsive table-stripped "> <!-- table-bordered table-stripped -->
												 <thead style="background:#000; color:#FFF; ">
													<tr>
														<th> S/N </th>
														<th> Course Title</th>
														<th> Course Code</th>
														<th> Type</th>
														<th> Time Allowed  </th>
														<th> Time Left .</th>
														<th> Status </th>														
													</tr>
												
												</thead> 
												
												<tbody>
													
													<?php  // display all users schedule  	?>
													<?php 
													/****************************************/
											// fetch the assessment that the user is doing now from results 
											$n = 0; foreach($readyCos['code'] as $scode) { 
																							
												## check the student's result for confirmation 
												$dbm = new DbTool(); 
												$courses = new Course();   $cosInfo = $courses->getAll(array("code"=>$scode));										
												
												$cond = array("user_id"=>$_SESSION['exmUser'], "code"=>$scode, "qtype"=>$readyCos['qtype'][$n], "year"=>$readyCos['year'][$n]);
												$datas = array("sn", "user_id", "code", "year", "total_qtn", "total_sec", "level",
																"totalmark", "unitmark", "totalscore", "point", "grade","qtype",
																"paperlogintime", "paperlogouttime", "paper_signal", "percent","sec_used","bus_stop"); 
												$mySchedules = $dbm->getFields($dbm->select("users_result",$cond),$datas);
											
												?>
												
													<tr>
														<td> <?php echo ($n+1); ?> </td>
														<td> <?php echo $cosInfo['name'][0]; ?> </td>
														<td> <?php echo $scode;  ?> </td>
														<td> <b> <?php echo $readyCos['qtype'][$n]; ?> </b> </td>
														<td> <p> <?php 
															echo readTime($mySchedules['total_sec'][0]);
															 
															?>  </p> 
														</td>
														
														<!-- corrolate two fields -->
														 <?php 
																	
															if($mySchedules['paper_signal'][0]=="normal"){ 
														$dbm = new DbTool();
															$rem = ($mySchedules['total_sec'][0]-$mySchedules['sec_used'][0]); 
																		$cent = ($rem / $mySchedules['total_sec'][0])*100;?>
																<td> <p> <?php 
																		echo readTime($rem);   ?>  
																	 </p>															
																		
																		<div class="progress  right progress_sm ">
																			<div class="<?php  $dbm->readColor($cent);?>  " role="progressbar" data-transitiongoal="<?php echo $cent;?>"></div>
																		</div> 
																</td>
														
																<td> 
																	<button class="btn btn-success" name = "start_paper" value="<?php echo $mySchedules['code'][0]."_".$mySchedules['qtype'][0]."_".$mySchedules['year'][0]; ?>"> Start .. </button> 														
																</td>	
																 	
																<?php  } 
																 
																	else if($mySchedules['paper_signal'][0]=="process"){
																		$_SESSION['prevNewStart'] = true; $_SESSION['delay_details'] = $mySchedules['code'][0]."_".$mySchedules['qtype'][0]."_".$mySchedules['year'][0];
																		 
																		$dbm = new DbTool();																		
																		$rem = ($mySchedules['total_sec'][0] - $mySchedules['sec_used'][0]); 
																		$cent = ($rem / $mySchedules['total_sec'][0])*100;
																	?>
																	<td> <p> <?php 
																		echo readTime($rem);  ?>  </p>															
																		
																		<div class="progress  right progress_sm">
																			<div class="<?php echo $dbm->readColor($cent);?>" role="progressbar" data-transitiongoal="<?php echo $cent;?>"></div>
																		</div> 
																	</td>
														
																<td>
																<button class="btn btn-warning" name = "redo_paper" value="<?php echo $mySchedules['code'][0]."_".$mySchedules['qtype'][0]."_".$mySchedules['year'][0]; ?>"> Continue.. </button> 
																	
																</td> 
																<?php 	 
																	}
																	else if($mySchedules['paper_signal'][0]=="done"){ 
																	$dbm = new DbTool(); ?>
																		
																		<td> <p> <?php 
																			echo readTime(0); $cent = 0; ?>  </p>															
																			
																			<div class="progress  right progress_sm">
																				<div class="<?php echo $dbm->readColor($cent);?>" role="progressbar" data-transitiongoal="<?php echo $cent;?>"></div>
																			</div> 
																		</td>
																		<td> 
																			<button class="btn btn-primary" name = "complete_paper" value="completed"> Completed .. </button> 														
																		</td> 
																	<?php 	 
																	}
																	// when the user has not been scheduled 
																	else if(empty($mySchedules['user_id'])) { ?>
																		
																		<td colspan="2">
																			<p class="red text-uppercase"> You are not scheduled for the <?php echo $readyCos['qtype'][$n]?>, Check Back Later! </p>
																		</td>
																		
																	<?php } 	?>
														 
														<!-- end corrolate two fields -->
														
													</tr>
													  
													<?php 
											$n++;
										}		// all courses fetched for user		
									?>
													
												</tbody>
											
											</table>
											
												
												<?php }
											?>
											 
											</div>
											
                                     </div>
									
									</div> <!-- /. col-sm-9 -->
									
					
					</div> <!-- /. x_content  -->
					 
				</div>
			</div>
		 
		
		</div>
	 
					<div class="row text-center" style="display:none;">										
						<a href="#" id = "showPop" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#basicModal">Click to open Modal</a>
					</div>
					<input type="hidden" name="stg1Alert" id="stg1Alert" value="<?php echo $_SESSION['stgMsg'];?>" />
					
		<!-- footer content -->
                <footer style="background:#EEEFFF;">
                    <div class="" style="color:#222;">
                        <p class="pull-right"> <center> Copyright &copy; 2016,  Hamdala Komputer Konsults.  |
                            <span class="lead"> <i class="fa fa-paw"></i> HKK!</span> </center>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
		 
		</div>	<!-- /. main_container-->
	</div> <!-- /. body container -->
	<?php   unset($_SESSION['stgMsg']); ?>
	</form>
</body>

		<script>
			
			$(function(){
					 // auto click showPop	
					stg1Pop = $('#stg1Alert').val(); 
					if(stg1Pop !="")	$('#showPop').click();	
				/************************************/
				/****************************************/
			// detecting click  buttons on keyboard 
		$(document).keydown(function(event){
			
		/***************************************/						 
		// alert(event.which); 
		
			if(event.which == 32 || event.which == 27 || event.which == 13 ) 
				{
				// press Next button 
					$('.dismiss-btn').click(); 
				}  
				});
		/****************************************/
				});
		</script>

	
 <script src="../media/js/html5shiv.js"></script>
    <script src="../media/js/respond.1.4.2.min.js"></script>	 
	 
	<script src="../media/js/bootstrap.min.js"></script>
    <!-- bootstrap progress js -->
    <script src="../media/js/progressbar/bootstrap-progressbar.min.js"></script>
    
     <script src="../media/js/custom.js"></script>
	
</html>