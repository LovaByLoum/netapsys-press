(function($){
	$.jCache = {
        /* Version*/
        version: "1.0",
        set: function(key, value, expired){
        	$.ajax({
		        url: wpu_ajax.url,
		        type: 'post',
		        dataType: 'json',
		        async: true,
		        data: {
		        	action: 'jcache',
		        	method: 'set',
		        	key: key,
		        	expired:expired,
		        	value:value
		        },
		        cache: false
		    });
        },
        get: function(key){
        	var result;
        	$.ajax({
		        url: wpu_ajax.url,
		        type: 'post',
		        dataType: 'json',
		        async: false,
		        data: {
		        	action: 'jcache',
		        	method: 'get',
		        	key: key
		        },
		        cache: false,
		        success: function(data) {
		            result = data;
		        }
		    });
		    return result;
        }
	};
})(window.jQuery || window.$);