<?php
$boj=new Vickyedu();
$boj->testExpiredMembership();

class Vickyedu 
{   
	public function testExpiredMembership()
    { 
        $Content_JSON = file_get_contents('vickyedu_json.json');    
        $jsonObj = json_decode($Content_JSON, true);
        $state = array("current_date"=> "01/18/2020", "current_page" =>"1125", "current_user" => "13");        
        $vickyeduInfo = new Vikcyedu_Course_Details();
        echo  $result = $vickyeduInfo->getValidation($state,$jsonObj);
        //$this->assertFalse($result);  

    }
}
class Vikcyedu_Course_Details
{    
public function getValidation($state,$primeryData)
{
    
    $userInfo;
    $memberInfo;
    $pageInfo;
    $incr=0;

    $isboolUser=false;
    $isboolPage=false;
    $isboolCat=false;
    $catg_id=null;
    foreach($primeryData as $var)
    {
        $incr++;
        if($incr==1)
        {
            $userInfo=$var;
        }    
        else if($incr==2)        
        {
            $memberInfo=$var;
        }
        else if($incr==3)
        {
            $pageInfo=$var;
        }
    }
    foreach ($pageInfo as $page)
       {
        if($page["pageId"]==$state["current_page"])
                {
                    $catg_id=$page["category_id"];                
                    $isboolPage=true;                   
                    break;
                }             
        }

    foreach ($userInfo as $getuser)
        {
           if($getuser["current_user"]==$state["current_user"])
            {
                $isboolUser=true;
                if($getuser["user_role"]=="admin"||$getuser["user_role"]=="editor")
                {
                    return true;
                }
                else
                {                    
                    foreach ($memberInfo as $getmember)
                    {
                        if($getmember["cId"]==$getuser["memberships"])
                        {
                            if(!($this->calculateDuration($getmember["duration"],$state['current_date'],$getuser["activated"])))
					           {    
                                    echo "<br>Please activate Memberships<br>";
                                    return false;
                                }
                         $getmembercat=$getmember["allowed_categories"];                        
                        if($catg_id!=null)
                        {
                         foreach($getmembercat as $k=>$val)
                            {
                            if($k==$catg_id)
                                    {
                                    $isboolCat=true;                                    
                                    }
                            }
                        }
                        }
                    }
                }               
            break;
            }           
        }      
           
        if(!$isboolUser)
        {
            echo "Current User Not avalibale Please Register.";
            return false;
        }
        else if(!$isboolPage){
            echo "<br>Current Page Not avalibale<br>";
            return false;
        }
        else if(!$isboolCat)
        {
            echo "Memberships categories not avalibale";
            return false;
        }
    
}
public function calculateDuration($getdurtn,$compDuration,$activateDate)
{	
// Declare two dates 
$start_date = strtotime($activateDate); 
$end_date = strtotime($compDuration); 
$finalDate=($end_date - $start_date)/60/60/24;
	//echo "<br>".$finalDate."<br>";
	if(strpos($getdurtn,"months")!= null)
	{	
        $arr=explode(" ",$getdurtn);	
    
		if(($arr[0]*30)>=($finalDate)){
			return true;
		}else
			return false;
	}
	else if(strpos($getdurtn,"year")!= null)
	{	$arr=explode(" ",$getdurtn);	
		if(($arr[0]*365)>=$finalDate){
			return true;
		}else
			return false;
	}
	else if(strpos($getdurtn,"days")!= null)
	{		
			$arr=explode(" ",$getdurtn);		
		if($arr[0]>=$finalDate){
			return true;
		}else
			return false;
	}
	return false;
}
}
?>