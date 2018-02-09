function get_cookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return false;
}


function set_cookie(cname, cvalue, exdays, path=null) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires + (path ? "; path=" + path : '');
}


$(document).ready(function() {
	var toggle = document.getElementById("toggle321");
	var css = document.getElementById("css_theme");
	var current_theme = css.getAttribute("href");
	var theme = "light";
	var cookie = get_cookie("theme");

	/* Find current theme */
	if(cookie) {
		theme = cookie;
	}


	if(theme == "dark")
		toggle.checked = true;
	else
		toggle.checked = false;


	css.setAttribute("href", "assets/css/"+theme+".css");

	/* Detect change in theme */
	toggle.onchange = function() {
		if(this.checked) {
			set_cookie('theme', 'dark');
			theme = "dark";
			document.getElementById("css_theme").setAttribute("href", "assets/css/"+theme+".css");
		} else {
			set_cookie('theme', 'light');
			theme = "light";
			document.getElementById("css_theme").setAttribute("href", "assets/css/"+theme+".css");
		}

		console.log(theme);
	}

});
