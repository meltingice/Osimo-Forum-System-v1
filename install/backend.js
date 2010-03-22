function loadStep(num)
{
	$('#page-'+(num-1)).slideUp('slow');
	$('#page-'+num).slideDown('slow');
}

function runInstall(step)
{
	var runInstall = true;
	
	if(step=='database')
	{
		var data = "step=database";
	}
	if(step=='config')
	{
	    var siteTitle = $('#option-site-title').attr('value');
	    var siteDesc = $('#option-site-desc').attr('value');
	    var adminEmail = $('#option-admin-email').attr('value');
	    
	    if(siteTitle==''||adminEmail=='')
	    {
	    	runInstall = false;
	    }
	    else
	    {
	    	var data = "step=config&siteTitle="+escape(siteTitle)+"&siteDesc="+escape(siteDesc)+"&adminEmail="+escape(adminEmail);
	    }
	}
	if(step=='admin')
	{
		var adminName = $('#option-admin-name').attr('value');
		var adminPass1 = $('#option-admin-password1').attr('value');
		var adminPass2 = $('#option-admin-password2').attr('value');
		
		if((adminPass1!=adminPass2)||adminPass1==''||adminPass2==''||adminName=='')
		{
			runInstall = false;
			$('#admin-status').html('Check entered info!');
		}
		else
		{
			var data = "step=admin&adminName="+escape(adminName)+"&adminPass="+escape(adminPass1);
		}
	}
	
	if(runInstall)
	{
		$.ajax({
			beforeSend:function(){			
				$('#'+step+'-status').html('Running...');
			},
			type:'POST',
			url:'install.php',
			data:data,
			success:function(msg){
				if(msg=='1')
				{
				    $('#'+step+'-status').html('Completed!');
				    if(step=='database')
				    {
				    	$('#page-2>.next-step').removeAttr('disabled');
				    	$('#page-2>.generic-button').attr("disabled",'true');
				    }
				    if(step=='config')
				    {
				    	$('#page-3>.next-step').removeAttr('disabled');
				    	$('#page-3>.generic-button').attr("disabled",'true');
				    }
				    if(step=='admin')
				    {
				    	$('#page-4>.next-step').removeAttr('disabled');
				    	$('#page-4>.generic-button').attr("disabled",'true');
				    }
				}
				else
				{
				    $('#'+step+'-status').html('Error, check settings!');
				}
			}
		});		
	}
}