
$(document).ready(function()
{
	
	$('form').submit(function(e)
	{
		$("#loading-div-background").css({ opacity: 0.8 });

		$("#loading-div-background").show();
		e.preventDefault();
		e.stopImmediatePropagation();
		// START A LOADING SPINNER HERE
		var formDiv = $(this);
	
		var file = ($(this).find('.fileInput').prop('files')[0]);
		
		var data = new FormData();
		data.append(0, file);
		
		$.ajax({
			url: 'submit.php?files',
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false, // Don't process the files
			contentType: false, // Set content type to false as jQuery will tell the server its a query string request
			success: function(data, textStatus, jqXHR)
			{
				if(typeof data.error === 'undefined')
				{
					// Success so call function to process the form
					if(data.files.length)
						submitForm(e, data, formDiv);
					else
					{
						hideLoading();
						error('ERRORS: upload error, please try again.');
					}
				}
				else
				{
					// Handle errors here
					hideLoading();
					error('ERRORS: ' + data.error);
				}
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				// Handle errors here
				hideLoading();
				error('ERRORS: ' + textStatus);
			}
		});
		
		return false;
	});
});
function error(t)
{
	alert(t);
}
function hideLoading()
{
	$("#loading-div-background").hide();
}
function submitForm(event, data, formDiv)
{
   $form = $(event.target);
	
	// Serialize the form data
    var formData = $form.serialize();
	console.log('formData',formData);
   
	$.each(data.files, function(key, value)
    {
        formData = formData + '&filenames[]=' + value;
    });
	var formats = formDiv.find('.iconFormats').val();
	var type = formDiv.find('input[name=iconType]:checked').val();
	var inputtype = formDiv.find('input[name=inputtype]').val();
	formData += '&formats=' + formats;
	formData += '&type=' + type;
	
    $.ajax({
        url: 'submit.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(data, textStatus, jqXHR)
        {
            if(typeof data.error === 'undefined')
            {
                // Success so call function to process the form
                console.log('SUCCESS: ' + data.success);
				hideLoading();
				window.location.href = 'download.php?file='+data.zip+'&filename='+data.realname;
            }
            else
            {
                // Handle errors here
				hideLoading();
                error('ERRORS: ' + data.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            error('ERRORS: ' + textStatus);
        },
        complete: function()
        {
            hideLoading();
			// STOP LOADING SPINNER
        }
    });
}