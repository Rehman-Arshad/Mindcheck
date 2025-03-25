function validate_email(user_email){
	
	let result = /^[A-Za-z0-9._%+-]+@ue\.edu\.pk$/.test(user_email.value);
	if(result){
		$("#email-error").html("");
		$("input[type='submit']").css('background-color', '#0A76D8');

		return result;
	}
	else{
		$("input[type='submit']").prop('disabled', true);
		$("input[type='submit']").css('background-color', 'grey');
		$("#email-error").html("Login email should be under the domain of University of Education , Lahore");
		return result;
	}
}