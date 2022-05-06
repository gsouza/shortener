async function get(url, allData, success_cb) {

	let data = {
		url: url,
		data: allData,
		method: 'GET',
		success_cb: success_cb,
	}
	return await _ajax(data);
}

async function post(url, allData, success_cb) {

	let data = {
		url : url,
		args: allData,
		method: 'POST',
		success_cb : success_cb,
	}
	return await _ajax(data);
}

function _ajax (_data) {
	return new Promise(function(resolve, reject) {
		$.ajax({
			json: true,
			url: _data.url,
			method: _data.method,
			data: _data.args,
			cache: false ,
			headers: { "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content') },
			success: function (recData) {
        if (_data.success_cb)
          _data.success_cb(recData);

        return resolve(true);
			},
			error: function (error) {
				console.log(`Error on ${_data.url}:`, error);
				return reject(true);
			}
	 	});
 });
}