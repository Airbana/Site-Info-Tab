function toggle(type) {
	$.ajax({
		type : 'POST',
		url : "ajax.php?querytype=update_metadata&tid=" + gCan[0] + "&type="
				+ type,
		data : 'sid=' + gCan[2],
		dataType : 'html',
		success : function(data) {
			if (type == 'reviews') {
				$('#reviews').toggleClass("special", 1000);
				var str = $("#reviews").text();
				if (str == 'Reviews Disabled') {
					$("#reviews").text("Reviews Enabled");
				} else {
					$("#reviews").text("Reviews Disabled");
				}
			} else if (type == 'contact') {
				$('#contact').toggleClass("special", 1000);
				var str = $("#contact").text();
				if (str == 'Contact Details Disabled') {
					$("#contact").text("Contact Details Enabled");
				} else {
					$("#contact").text("Contact Details Disabled");
				}
			} else if (type == 'events') {
				$('#events').toggleClass("special", 1000);
				var str = $("#events").text();
				if (str == 'Upcoming Events Disabled') {
					$("#events").text("Upcoming Events Enabled");
				} else {
					$("#events").text("Upcoming Events Disabled");
				}
			}

		},
		error : function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
}
