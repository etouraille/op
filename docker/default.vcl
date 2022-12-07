vcl 4.0;
backend default {
  .host = "web";
  .port = "80";
}

sub vcl_backend_response {
	# Don't cache 404 responses
	if ( beresp.status == 404 ) {
		set beresp.ttl = 120s;
		set beresp.uncacheable = true;
		return (deliver);
	}
	set beresp.http.x-url = bereq.url;
    set beresp.http.x-host = bereq.http.host;
}



sub vcl_recv {


    if (req.method == "BAN") {
            if (!req.http.x-invalidate-pattern) {
                return (purge);
            }
            ban("obj.http.x-url ~ " + req.http.x-invalidate-pattern
                + " && obj.http.x-host == " + req.http.host);
            return (synth(200,"Ban added"));
        }

    if (req.method != "GET" && req.method != "HEAD" && req.method != "BAN") {
            /* We only deal with GET and HEAD by default */
            return (pass);
     }

    if (req.http.X-Forwarded-Proto == "https" ) {
        set req.http.X-Forwarded-Port = "443";
    } else {
        set req.http.X-Forwarded-Port = "80";
    }

    // Remove all cookies except the session ID.
    if (req.http.Cookie) {
        set req.http.Cookie = ";" + req.http.Cookie;
        set req.http.Cookie = regsuball(req.http.Cookie, "; +", ";");
        set req.http.Cookie = regsuball(req.http.Cookie, ";(PHPSESSID)=", "; \1=");
        set req.http.Cookie = regsuball(req.http.Cookie, ";[^ ][^;]*", "");
        set req.http.Cookie = regsuball(req.http.Cookie, "^[; ]+|[; ]+$", "");

        if (req.http.Cookie == "") {
            // If there are no more cookies, remove the header to get the page cached.
            unset req.http.Cookie;
        }
    }



}


sub vcl_deliver {
  # Display hit/miss info
  if (obj.hits > 0) {
    set resp.http.V-Cache = "HIT";
  }
  else {
    set resp.http.V-Cache = "MISS";
  }
  unset resp.http.x-url;
      unset resp.http.x-host;
}